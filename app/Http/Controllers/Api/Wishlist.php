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

        $wishlistProduct = DB::table('wishlist')->where($saveData)->first();
        if ($wishlistProduct) {
            return response()->json(['status' => false, 'message' => 'Already In Wishlist']);
        }

        $saveData['created_at']=Carbon::now();

        DB::table('wishlist')->insert($saveData);
        DB::table('products')->where('id', $product_id)->update(['added_to_wishlist' => 'true']);
        return response()->json(['status' => true, 'message' => 'Added To Wishlist']);
    }
    public function myWishlist()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');

        if (empty($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Customer Id is blank'
            ]);
        }

        $customer = DB::table('customers')->find($customer_id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ]);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json([
                'status' => false,
                'message' => 'Your profile is currently inactive'
            ]);
        }

        $products = DB::table('wishlist')
            ->where('wishlist.customer_id', $customer_id)
            ->leftJoin('products', 'products.id', '=', 'wishlist.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
            ->select(
                'products.id as product_id',
                'products.category_id',
                'products.subcategory_id',
                'products.product_name',
                'products.product_rating',
                'products.product_image',
                'products.added_to_wishlist',
                'products.product_selling_price',
                'products.product_cost_price',
                'categories.category_name',
                'subcategories.subcategory_name',
                'wishlist.id as wishlist_id'
            )
            ->get();
        
        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No records found'
            ]);
        }

        $customerCurrency = getUserCurrency($customer_id);
        $returnData = [];

        foreach ($products as $value) {
            $isInWishlist = DB::table('wishlist')
            ->where('customer_id', $customer_id)
            ->where('product_id', $value->product_id)
            ->exists();
            $images = $value->product_image ? json_decode($value->product_image, true) : [];
            $firstImageUrl = !empty($images[0]['image']) ? url('uploads/' . $images[0]['image']) : null;

            $returnData[] = [
                'wishlist_id'           => (string) $value->wishlist_id,
                'product_id'            => (string) $value->product_id,
                'category_id'           => (string) $value->category_id,
                'subcategory_id'        => (string) $value->subcategory_id,
                'product_name'          => (string) $value->product_name,
                'product_rating'        => (string) $value->product_rating,
                'product_image'         => $firstImageUrl,
                'added_to_wishlist'     => $isInWishlist,
                'product_selling_price' => $customerCurrency .' '. (string) $value->product_selling_price,
                'product_cost_price'    => $customerCurrency .' '. (string) $value->product_cost_price,
                'category_name'         => (string) $value->category_name,
                'subcategory_name'      => (string) $value->subcategory_name,
            ];
        }

        return response()->json([
            'status'  => true,
            'data'    => $returnData,
            'message' => 'API accessed successfully!'
        ]);
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
        DB::table('products')->where('id', $product_id)->update(['added_to_wishlist' => 'false']);
        return response()->json(['status' => true, 'message' => 'Removed From Wishlist']);
    }
}
