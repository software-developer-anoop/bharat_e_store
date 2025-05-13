<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Notification extends Controller
{
    public function index(){
        $post = checkPayload();
        $customer_id = trim($post['customer_id']??'');
        if (empty($countryCode)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $notificationlist = DB::table('push_notifications')->where('customer_id',$customer_id)->select('customer_id','notification_id','title','id','description','image')->get();
        if (empty($notificationlist)) {
            $response['status'] = false;
            $response['message'] = "No Records Found";
            return response()->json($response);
        }
        $returnData = [];
        foreach ($notificationlist as $key => $value) {
            $return['notify_id'] = (string)$value->id;
            $return['customer_id'] = (string)$value->customer_id;
            $return['notification_id'] = (string)$value->notification_id;
            $return['title'] = (string)$value->title;
            $return['description'] = (string)$value->description;
            $return['image'] = (string)$value->image;
            array_push($returnData, $return);
        }
        $response['status'] = true;
        $response['data'] = $returnData;
        $response['message'] = "API Accessed Successfully!";
        return response()->json($response);
    }
}
