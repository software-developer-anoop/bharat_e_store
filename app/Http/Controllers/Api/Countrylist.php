<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Countrylist extends Controller
{
    public function index() {
        $response = [];
        checkHeaders();
        $countrylist = DB::table('country')->where('status','Active')->select('country_name','country_code','flag_image','id')->get();
        if ($countrylist->isEmpty()) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($countrylist as $key => $value) {
            $return['country_id'] = (string)$value->id;
            $return['country_name'] = (string)$value->country_name;
            $return['country_code'] = (string)$value->country_code;
            $return['flag_image'] = url('uploads/' . $value->flag_image);
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function stateList() {
        $response = [];
        $post = checkPayload();
        $country_id = trim($post['country_id']??'');
        if (empty($country_id)) {
            $response['status'] = false;
            $response['message'] = "Country Id Is Blank";
            return response()->json($response);
        }
        $where = [];
        $where['country']=$country_id;
        $where['status']='Active';
        $statelist = DB::table('states')->where($where)->select('state_name','country','id')->get();
        if ($statelist->isEmpty()) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($statelist as $key => $value) {
            $return['state_id'] = (string)$value->id;
            $return['state_name'] = (string)$value->state_name;
            $return['country_id'] = (string)$value->country;
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
    public function cityList() {
        $response = [];
        $post = checkPayload();
        $country_id = trim($post['country_id']??'');
        $state_id = trim($post['state_id']??'');
        if (empty($country_id)) {
            $response['status'] = false;
            $response['message'] = "Country Id Is Blank";
            return response()->json($response);
        }
        if (empty($state_id)) {
            $response['status'] = false;
            $response['message'] = "State Id Is Blank";
            return response()->json($response);
        }
        $where = [];
        $where['country']=$country_id;
        $where['state']=$state_id;
        $where['status']='Active';
        $citylist = DB::table('cities')->where($where)->select('state','country','id','city_name','locality')->get();
        if ($citylist->isEmpty()) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($citylist as $key => $value) {
            $return['city_id'] = (string)$value->id;
            $return['city_name'] = (string)$value->city_name;
            $return['country_id'] = (string)$value->country;
            $return['state_id'] = (string)$value->state;
            $return['locality'] = (string)$value->locality;
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
}
