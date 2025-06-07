<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Orders extends Controller {
    public function index() {
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
        $where = [];
        $where['customer_id'] = $customer_id;
        if(!empty($filter)){
        $where['order_status'] = $filter;   
        }
        $orders = DB::table('orders')
            ->where($where)
            ->orderBy('id', 'desc')
            ->limit($per_page_limit)
            ->offset($offset)
            ->get();

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
                    'product_selling_price'
                ) // Exclude created_at, updated_at
                ->get();

            $returnData[] = [
                'order_table_id'  => (string)$value->id,
                'product_id'      => (string)$firstHistory->product_id,
                'product_name'    => (string)$firstHistory->product_name,
                'product_color'   => (string)$firstHistory->product_color,
                'product_size'    => (string)$firstHistory->product_size,
                'amount'          => $customerCurrency . ' ' . (string)$value->amount,
                'image'           => (string)$firstHistory->image,
                'payment_status'  => (string)$value->status,
                'order_status'    => (string)$value->order_status,
                'order_history'   => $allHistory // Full history
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
