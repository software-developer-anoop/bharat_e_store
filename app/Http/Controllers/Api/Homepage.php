<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Homepage extends Controller
{
    public function index(Request $request)
    {
        checkHeaders();

        $record = DB::table('websetting')->select('banner')->first();

        $banner = !empty($record->banner)?json_decode($record->banner):[];
        if (empty($banner)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($banner as $key => $value) {
            $return['image'] = url('uploads/' . $value->image);
            array_push($returnData, $return);
        }
        return response()->json([
            'status' => true,
            'banner' => $returnData,
            'message' => 'API Accessed Successfully'
        ]);
    }

    public function categoryList(){
        checkHeaders();
        $category = DB::table('categories')->where('status','Active')->select('category_name','category_image','id')->get();
        if (empty($category)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($category as $key => $value) {
            $return['category_id'] = (string)$value->id;
            $return['category_name'] = (string)$value->category_name;
            $return['category_image'] = url('uploads/' . $value->category_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function subcategoryList(){
        $post = checkPayload();
        $category_id = trim($post['category_id']??'');
        $where=[];
        $where['status']='Active';
        $where['category']=$category_id;
        $subcategory = DB::table('subcategories')->where($where)->select('subcategory_name','subcategory_image','id')->get();
        if (empty($subcategory)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($subcategory as $key => $value) {
            $return['subcategory_id'] = (string)$value->id;
            $return['category_id'] = (string)$value->category;
            $return['subcategory_name'] = (string)$value->subcategory_name;
            $return['subcategory_image'] = url('uploads/' . $value->subcategory_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function trendingProducts(){
        $post = checkPayload();
        $condition = trim($post['condition']??'');
        $where=[];
        $where['status']='Active';
        $where['is_trending']='yes';
        if($condition && $condition !== "all"){
            $response['status'] = false;
            $response['message'] = "Invalid Condition";
            return response()->json($response);
        }
        $query = DB::table('products')->where($where);

        if (empty($condition)) {
            $query->limit(8);
        }

        $products = $query->get();

        if (empty($products)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($products as $key => $value) {
            $return['product_id'] = (string)$value->id;
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
}
