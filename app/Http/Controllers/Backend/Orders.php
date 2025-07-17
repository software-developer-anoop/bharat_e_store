<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Orders extends Controller
{
    public function index(Request $request){
        $page_name = 'Orders List';
        $key = 'total';
        $query = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Use leftJoin to avoid errors if no coupon is used
            ->select(
                'orders.*',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id'
                'orders.created_at as order_created_at'
            );

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $query->where('orders.order_status', $_GET['order_status']);
        }

        $data = $query->orderBy('order_created_at', 'desc')->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function pendingOrders(Request $request)
    {
        $page_name = 'Pending Orders List';
        $key = 'pending';
        $data = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Safe if no coupon used
            ->select(
                'orders.*',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title'
            )
            ->where('orders.order_status', 'pending')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function placedOrders(Request $request)
    {
        $page_name = 'Placed Orders List';
        $key = 'placed';
        $data = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Safe if no coupon used
            ->select(
                'orders.*',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title'
            )
            ->where('orders.order_status', 'placed')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function shippedOrders(Request $request)
    {
        $page_name = 'Shipped Orders List';
        $key = 'shipped';
        $data = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Safe if no coupon used
            ->select(
                'orders.*',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title'
            )
            ->where('orders.order_status', 'shipped')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function deliveredOrders(Request $request)
    {
        $page_name = 'Delivered Orders List';
        $key = 'delivered';
        $data = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Safe if no coupon used
            ->select(
                'orders.*',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title'
            )
            ->where('orders.order_status', 'delivered')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function cancelledOrders(Request $request)
    {
        $page_name = 'Cancelled Orders List';
        $key = 'cancelled';
        $data = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('coupons', 'orders.coupon_id', '=', 'coupons.id') // Safe if no coupon used
            ->select(
                'orders.*',
                'orders.id as order_tbl_id',
                'orders.customer_id as order_customer_id',
                'customers.customer_name',
                'addresses.address',
                'addresses.pincode',
                'coupons.coupon_title'
            )
            ->where('orders.order_status', 'cancelled')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('backend.orders', compact('page_name', 'data','key'));
    }
    public function orderHistory($orderID){
        $page_name = 'Order History ';
        $data = DB::table('order_history')
            ->where('order_table_id', $orderID)
            ->get();
        return view('backend.order-history', compact('page_name', 'data'));
    }

}
