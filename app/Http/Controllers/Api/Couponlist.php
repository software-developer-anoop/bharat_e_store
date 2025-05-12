<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Couponlist extends Controller
{
    public function index() {
        $response = [];
        checkHeaders();
        $couponlist = DB::table('coupons')->where('status','Active')->select('coupon_title','coupon_description','coupon_code','id','coupon_type','coupon_value')->get();
        if (empty($couponlist)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($couponlist as $key => $value) {
            $return['coupon_id'] = (string)$value->id;
            $return['coupon_title'] = (string)$value->coupon_title;
            $return['coupon_description'] = (string)$value->coupon_description;
            $return['coupon_code'] = (string)$value->coupon_code;
            $return['coupon_type'] = (string)$value->coupon_type;
            $return['coupon_value'] = (string)$value->coupon_value;
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
}
