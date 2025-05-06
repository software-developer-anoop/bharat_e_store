<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Countrylist extends Controller
{
    public function index() {
        $response = [];
         checkHeaders();
        $countrylist = DB::table('country')->where('status','Active')->select('country_name','country_code','flag_image','id')->get();
        if (empty($countrylist)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($countrylist as $key => $value) {
            $return['id'] = (string)$value->id;
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
}
