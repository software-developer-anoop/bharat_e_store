<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Notification extends Controller
{
    public function index()
    {
        $post = checkPayload();
        $customer_id = trim($post['customer_id'] ?? '');
        $per_page_limit = intval($post['per_page_limit'] ?? 10); // Default to 10
        $page_no = intval($post['page_no'] ?? 1); // Default to 1

        if (empty($customer_id)) {
            return response()->json(['status' => false, 'message' => 'Customer Id is blank']);
        }

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found']);
        }

        if ($customer->profile_status === "Inactive") {
            return response()->json(['status' => false, 'message' => 'Your profile is currently inactive']);
        }

        $offset = ($page_no - 1) * $per_page_limit;

        $notificationList = DB::table('push_notifications')
            ->where('customer_id', $customer_id)
            ->select('id', 'notification_id', 'customer_id', 'title', 'description', 'image','created_at')
            ->offset($offset)
            ->limit($per_page_limit)
            ->get();

        if ($notificationList->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No records found']);
        }

        $returnData = $notificationList->map(function ($value) {
            return [
                'notify_id'       => (string) $value->id,
                'customer_id'     => (string) $value->customer_id,
                'notification_id' => (string) $value->notification_id,
                'title'           => (string) $value->title,
                'description'     => (string) $value->description,
                'image'           => (string) $value->image,
                'date'            => Carbon::parse($value->created_at)->format('Y-m-d'),
                'time'            => Carbon::parse($value->created_at)->format('H:i'),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'API accessed successfully!',
            'data'    => $returnData
        ]);
    }

    public function deleteMyNotification(){
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
        $notification = DB::table('push_notifications')->find($notify_id);
        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found']);
        }
        $where=[];
        $where['customer_id']=$customer_id;
        $where['id']=$notify_id;
        DB::table('push_notifications')->where($where)->delete();
        return response()->json(['status' => true, 'message' => 'Notification Deleted Successfully']);
    }

}
