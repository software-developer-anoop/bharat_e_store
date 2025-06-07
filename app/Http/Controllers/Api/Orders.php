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
}
