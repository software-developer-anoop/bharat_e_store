<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Homepage extends Controller
{
    public function index(Request $request){
        checkHeaders();
        $banner = webSetting('banner');
        return response()->json([
            'status'=>true,
            'banner'=>url('uploads/' . $banner->banner),
            'message'=>'Api Accessed Successfully']);
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
        $subcategory = DB::table('subcategories')->where('status','Active')->select('subcategory_name','subcategory_image','id')->get();
        if (empty($subcategory)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($subcategory as $key => $value) {
            $return['subcategory_id'] = (string)$value->id;
            $return['category_id'] = (string)$value->id;
            $return['subcategory_name'] = (string)$value->subcategory_name;
            $return['subcategory_image'] = url('uploads/' . $value->subcategory_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
}
