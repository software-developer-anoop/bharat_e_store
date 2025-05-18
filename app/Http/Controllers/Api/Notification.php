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
        $per_page_limit = intval($post['per_page_limit']??10); // Default to 10
        $page_no = intval($post['page_no']??1); // Default to 1
        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id Is Blank']);
        }
        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }
        if ($customer->profile_status == "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }
        $offset = ($page_no - 1) * $per_page_limit;
        $notificationList = DB::table('push_notifications')
            ->where('customer_id', $customer_id)
            ->select('id', 'notification_id', 'customer_id', 'title', 'description', 'image')
            ->offset($offset)
            ->limit($per_page_limit)
            ->get();
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
