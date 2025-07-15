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
                ->where('order_id', $value->order_id)
                ->orderBy('id', 'asc')
                ->first();

            if (!$firstHistory) {
                continue; // skip if no order_history found
            }

            $transactionHistory = DB::table('transactions')
                ->where('order_id', $value->order_id)
                ->select('transaction_id', 'payment_gateway')
                ->first();

            $returnData[] = [
                'order_table_id'  => (string)$value->id,
                'order_id'        => (string)$value->order_id,
                'product_id'      => (string)($firstHistory->product_id ?? ''),
                'product_name'    => (string)($firstHistory->product_name ?? ''),
                'product_color'   => (string)($firstHistory->product_color ?? ''),
                'product_size'    => (string)($firstHistory->product_size ?? ''),
                'amount'          => $customerCurrency . ' ' . (string)$value->amount,
                'image'           => (string)($firstHistory->image ?? ''),
                'quantity'        => (string)($firstHistory->quantity ?? ''),
                'payment_status'  => (string)($value->status ?? ''),
                'order_status'    => (string)($value->order_status ?? ''),
                'payment_mode'    => (string)($value->payment_mode ?? ''),
                'transaction_id'  => (string)($transactionHistory->transaction_id ?? ''),
                'payment_gateway' => (string)($transactionHistory->payment_gateway ?? ''),
                'billing_address' => (string)($value->address ?? ''),
                'delivery_address'=> (string)($value->address ?? ''),
                'order_date'      => (string)($value->created_at ?? '')
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
                SUM(CASE WHEN order_status = 'placed' THEN 1 ELSE 0 END) as total_orders,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            ")
            ->where('customer_id', $customer_id)
            ->first();

        $totalOrders     = $stats->total_orders ?? 0;
        $deliveredOrders = $stats->delivered_orders ?? 0;
        $cancelledOrders = $stats->cancelled_orders ?? 0;

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
    public function orderHistory()
    {
        $post = checkPayload();
        $order_table_id = trim($post['order_table_id'] ?? '');

        if (empty($order_table_id)) {
            return response()->json([
                'status' => false,
                'message' => "Order ID is blank"
            ]);
        }

        $order = DB::table('orders')
            ->where('orders.id', $order_table_id)
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select('orders.created_at', 'orders.amount', 'orders.customer_id', 'addresses.address', 'orders.payment_mode')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => "No order found"
            ]);
        }

        $orderHistory = DB::table('order_history')
            ->where('order_table_id', $order_table_id)
            ->get();

        if ($orderHistory->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "No record found"
            ]);
        }

        $customerCurrency = getUserCurrency($order->customer_id) ?? '';
        $returnData = []; // Initialize the array to avoid undefined variable warning

        foreach ($orderHistory as $value) {
            $returnData[] = [
                'order_history_id'    => (string)($value->id ?? ''),
                'order_table_id'      => (string)($value->order_table_id ?? ''),
                'order_id'            => (string)($value->order_id ?? ''),
                'product_id'          => (string)($value->product_id ?? ''),
                'product_name'        => (string)($value->product_name ?? ''),
                'product_color'       => (string)($value->product_color ?? ''),
                'product_size'        => (string)($value->product_size ?? ''),
                'product_selling_price' => $customerCurrency . ' ' . (string)($value->product_selling_price ?? '0'),
                'image'               => (string)($value->image ?? ''),
                'quantity'            => (string)($value->quantity ?? '1'),
                'order_date'          => (string)($order->created_at ?? ''),
                'order_total'         => (string)($order->amount ?? '0'),
                'delivery_address'    => (string)($order->address ?? ''),
                'billing_address'     => (string)($order->address ?? ''),
                'payment_mode'        => (string)($order->payment_mode ?? ''),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => "Order history fetched successfully",
            'data' => $returnData
        ]);
    }
    public function cancelOrder(){
        $post = checkPayload();
        $order_table_id = trim($post['order_table_id'] ?? '');
        $customer_id = trim($post['customer_id'] ?? '');
        $listed_reason = trim($post[''] ?? '');
        $explain_reason = trim($post[''] ?? '');

        if (empty($order_table_id)) {
            return response()->json([
                'status' => false,
                'message' => "Order ID is blank"
            ]);
        }
        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => "Customer ID is blank"
            ]);
        }
        if (empty($listed_reason)) {
            return response()->json([
                'status' => false,
                'message' => "Reason is blank"
            ]);
        }
        if (empty($explain_reason)) {
            return response()->json([
                'status' => false,
                'message' => "Reason description is blank"
            ]);
        }
        $saveData = ['order_table_id'=>$order_table_id,
                     'customer_id'=>$customer_id,
                     'listed_reason'=>$listed_reason,
                     'explain_reason'=>$explain_reason,
                     'created_at'=>Carbon::now()];
        DB::table('cancelled_orders')->insert($saveData);
        return response()->json([
                'status' => true,
                'message' => "Reason Added Successfully"
            ]);
    }

}
