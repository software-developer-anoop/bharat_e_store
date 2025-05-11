<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Wishlist extends Controller
{
    public function index(){
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $product_id = trim($post['product_id']??'');

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product Id Is Blank']);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }
        $saveData = [];
        $saveData['customer_id']=$customer_id;
        $saveData['product_id']=$product_id;
        $saveData['created_at']=Carbon::now();

        DB::table('wishlist')->insert($saveData);
        return response()->json(['status' => true, 'message' => 'Added To Wishlist']);
    }
    public function myWishlist(){
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $products = DB::table('products')
                ->join('wishlist', 'products.id', '=', 'wishlist.product_id')
                ->select('products.id as product_id','products.category_id as category_id','products.subcategory_id as subcategory_id','products.product_name as product_name','products.product_rating as product_rating','products.product_image as product_image',)
                ->get();
        if (empty($products)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($products as $key => $value) {
            $return['product_id'] = (string)$value->product_id;
            $return['category_id'] = (string)$value->category_id;
            $return['subcategory_id'] = (string)$value->subcategory_id;
            $return['product_name'] = (string)$value->product_name;
            $return['product_rating'] = (string)$value->product_rating;
            $return['product_image'] = url('uploads/' . $value->product_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function removeFromWishlist(){
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        $product_id = trim($post['product_id']??'');

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        if (empty($product_id)) {
            return response()->json(['status' => false, 'message' => 'Product Id Is Blank']);
        }
        $customer = DB::table('customers')->find($customer_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }
        $where=[];
        $where['customer_id']=$customer_id;
        $where['product_id']=$product_id;
        DB::table('wishlist')->where($where)->delete();
        return response()->json(['status' => true, 'message' => 'Removed From Wishlist']);
    }
}
