<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Notification extends Controller
{
    public function index(){
        $page_name = 'Notification List';
        $data = DB::table('notifications')->get();
        return view('backend.notification-list',compact('page_name','data'));
    }
    public function addNotification($id=null){
        $data = $id?DB::table('notifications')->where('id',$id)->first():'';
        $page_name = $id?'Edit Notification':'Add Notification';
        return view('backend.add-notification',compact('data','page_name'));
    }
    public function saveNotification(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['title'] = trim($data['title']);
        $duplicate = DB::table('notifications')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }

        if ($file = $request->file('image')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_image']))) {
                    @unlink(public_path('uploads/' . $data['old_image']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['image'] = $filename;
            }
        }
        
        $saveData['title'] = $data['title']?trim($data['title']):'';
        $saveData['description'] = $data['description']?trim($data['description']):'';

        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('notifications')->insert($saveData);
            $msg = 'Notification Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('notifications')->where('id',$id)->update($saveData);
            $msg = 'Notification Updated Successfully';
        }
        return redirect(route('admin.notification-list'))->with('success',$msg);
    }
    public function pushNotification(Request $request)
    {
        $id = trim($request->input('id', ''));
        $start = (int)trim($request->input('start', 0));
        $limit = (int)trim($request->input('limit', 10));

        if (empty($id)) {
            return response()->json(['error' => 'ID is blank'], 400);
        }

        $notificationData = DB::table('notifications')->find($id);
        if (!$notificationData) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $data = [
            'status' => false,
            'id' => (string) $id,
            'start' => (string) ($start + 1),
            'limit' => (string) $limit,
            'ids' => '',
        ];

        $serverkey = env('FIREBASE_API_KEY');

        $where = [
            ['fcm_token', '!=', ''],
            ['profile_status', '=', 'Active'],
            ['email_status', '=', 'Verified']
        ];

        $offset = $start * $limit;
        $customers = DB::table('customers')
            ->where($where)
            ->select('id', 'fcm_token')
            ->orderBy('id', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $msgarray = [
            'title' => ucwords($notificationData->title),
            'message' => !empty($notificationData->descrption) ? $notificationData->descrption : $notificationData->title,
            'image' => !empty($notificationData->image) ? url('uploads/' . $notificationData->image) : ''
        ];

        if ($customers->isNotEmpty()) {
            $data['status'] = true;
            $saveRecords = [];
            $tokens = [];

            foreach ($customers as $customer) {
                if (!empty($customer->fcm_token) && strlen($customer->fcm_token) > 30) {
                    $tokens[] = $customer->fcm_token;

                    $saveRecords[] = [
                        'customer_id' => $customer->id,
                        'notification_id' => $id,
                        'title' => $msgarray['title'],
                        'description' => $msgarray['message'],
                        'image_path' => $msgarray['image'],
                        'created_at' => now(),
                    ];
                }
            }

            if (!empty($saveRecords)) {
                DB::table('push_notifications')->insert($saveRecords);
            }

            if (!empty($tokens)) {
                $firebaseids = implode(',', $tokens);
                pushnotifications($firebaseids, $msgarray, $serverkey);
            }
        }

        return response()->json($data);
    }

}
