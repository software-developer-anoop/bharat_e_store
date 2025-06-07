<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Orders extends Controller {
    public function index()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $per_page_limit = intval($post['per_page_limit'] ?? 10); // Default to 10
        $page_no = intval($post['page_no'] ?? 1); // Default to 1
        $filter = trim($post['filter'] ?? '');

        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => "Customer ID is blank"
            ]);
        }

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ]);
        }

        $offset = ($page_no - 1) * $per_page_limit;

        $query = DB::table('orders')
            ->where('orders.customer_id', $customer_id)
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->orderBy('orders.id', 'desc')
            ->limit($per_page_limit)
            ->offset($offset)
            ->select('orders.*', 'addresses.address');

        if (!empty($filter)) {
            $query->where('order_status', $filter);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "No orders found"
            ]);
        }

        $customerCurrency = getUserCurrency($customer_id) ?? '';
        $returnData = [];

        foreach ($orders as $value) {
            $firstHistory = DB::table('order_history')
                ->where('order_table_id', $value->id)
                ->orderBy('id', 'asc')
                ->first();

            if (!$firstHistory) {
                continue; // skip if no order_history found
            }

            $transactionHistory = DB::table('transactions')
                ->where('order_id', $value->order_id)
                ->select('transaction_id', 'payment_gateway')
                ->first();

            $allHistory = DB::table('order_history')
                ->where('order_table_id', $value->id)
                ->orderBy('id', 'asc')
                ->select(
                    'id',
                    'order_table_id',
                    'product_id',
                    'product_name',
                    'product_color',
                    'product_size',
                    'image',
                    'quantity',
                    'product_selling_price'
                )
                ->get();

            $returnData[] = [
                'order_table_id'  => (string)$value->id,
                'order_id'        => (string)$value->order_id,
                'product_id'      => (string)($firstHistory->product_id ?? ''),
                'product_name'    => (string)($firstHistory->product_name ?? ''),
                'product_color'   => (string)($firstHistory->product_color ?? ''),
                'product_size'    => (string)($firstHistory->product_size ?? ''),
                'amount'          => $customerCurrency . ' ' . (string)$value->amount,
                'image'           => (string)($firstHistory->image ?? ''),
                'payment_status'  => (string)($value->status ?? ''),
                'order_status'    => (string)($value->order_status ?? ''),
                'payment_mode'    => (string)($value->payment_mode ?? ''),
                'transaction_id'  => (string)($transactionHistory->transaction_id ?? ''),
                'payment_gateway' => (string)($transactionHistory->payment_gateway ?? ''),
                'address'         => (string)($value->address ?? ''),
                'order_date'      => (string)($value->created_at ?? ''),
                'order_history'   => $allHistory
            ];
        }

        return response()->json([
            'status' => true,
            'message' => "Order list fetched successfully",
            'data' => $returnData
        ]);
    }

    public function summary() {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');

        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => "Customer ID is blank"
            ]);
        }

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ]);
        }

        $stats = DB::table('orders')
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            ")
            ->where('customer_id', $customer_id)
            ->first();

        $totalOrders     = $stats->total_orders ?? 0;
        $deliveredOrders = $stats->delivered_orders ?? 0;
        $cancelledOrders = $stats->cancelled_orders ?? 0;

        if ($totalOrders == 0 && $deliveredOrders == 0 && $cancelledOrders == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No record found'
            ]);
        }

        $returnData = [
            'orders'    => (string)$totalOrders,
            'delivered' => (string)$deliveredOrders,
            'cancelled' => (string)$cancelledOrders,
            'coins'     => (string)($customer->wallet_points ?? 0),
        ];

        return response()->json([
            'status'  => true,
            'message' => 'API Accessed Successfully',
            'data'    => $returnData
        ]);
    }
}
