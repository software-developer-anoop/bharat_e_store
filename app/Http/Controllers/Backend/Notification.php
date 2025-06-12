<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Notification extends Controller {
    public function index() {
        $page_name = 'Notification List';
        $data = DB::table('notifications')->get();
        return view('backend.notification-list', compact('page_name', 'data'));
    }
    public function addNotification($id = null) {
        $data = $id ? DB::table('notifications')->where('id', $id)->first() : '';
        $page_name = $id ? 'Edit Notification' : 'Add Notification';
        return view('backend.add-notification', compact('data', 'page_name'));
    }
    public function saveNotification(Request $request) {
        $data = $request->all();
        $saveData = [];
        $id = $data['id'] ? trim($data['id']) : '';
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
        $saveData['title'] = $data['title'] ? trim($data['title']) : '';
        $saveData['description'] = $data['description'] ? trim($data['description']) : '';
        $saveData['notification_type'] = $data['notification_type'] ? trim($data['notification_type']) : '';
        if (empty($id)) {
            $saveData['created_at'] = Carbon::now();
            DB::table('notifications')->insert($saveData);
            $msg = 'Notification Added successfully';
        } else {
            $saveData['updated_at'] = Carbon::now();
            DB::table('notifications')->where('id', $id)->update($saveData);
            $msg = 'Notification Updated Successfully';
        }
        return redirect(route('admin.notification-list'))->with('success', $msg);
    }
    public function pushNotification(Request $request) {
        $id = trim($request->input('id', ''));
        $start = (int)$request->input('start', 0);
        $limit = (int)$request->input('limit', 10);
        if (empty($id)) {
            return response()->json(['error' => 'ID is blank'], 400);
        }
        $notification = DB::table('notifications')->find($id);
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
        $data = ['status' => false, 'id' => (string)$id, 'start' => (string)($start + 1), 'limit' => (string)$limit, 'ids' => '', ];
        $msg = ['title' => ucwords($notification->title), 'message' => !empty($notification->description) ? $notification->description : $notification->title, 'image' => !empty($notification->image) ? url('uploads/' . $notification->image) : '','notification_type'=>$notification->notification_type];
        $where = [['fcm_token', '!=', ''], ['profile_status', '=', 'Active'], ['email_status', '=', 'Verified']];
        $offset = $start * $limit;
        $customers = DB::table('customers')->where($where)->select('id', 'fcm_token')->orderByDesc('id')->offset($offset)->limit($limit)->get();
        if ($customers->isEmpty()) {
            return response()->json($data);
        }
        $data['status'] = true;
        $notificationLog = [];
        $tokens = [];
        foreach ($customers as $customer) {
            if (!empty($customer->fcm_token) && strlen($customer->fcm_token) > 30) {
                $tokens[] = $customer->fcm_token;
                $notificationLog[] = ['customer_id' => $customer->id, 
                                      'notification_id' => $id, 
                                      'title' => $msg['title'], 
                                      'description' => $msg['message'],
                                      'notification_type' => $msg['notification_type'], 
                                      'image' => $msg['image'], 
                                      'created_at' => now(), ];
            }
        }
        if (!empty($notificationLog)) {
            DB::table('push_notifications')->insert($notificationLog);
        }
        $jsonPath = realpath('../firebase_credentials.json');

        // 🔥 Send notifications
        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                // You should pass token + message content here
                $response = sendPushNotification([
                    'message' => [
                        'to' => $token, // or 'topic' => 'news'
                        'notification' => [
                            'title' => $msg['title'],
                            'body'  => $msg['message'],
                            'image' => $msg['image'] ?? null, // optional image
                        ],
                        'data' => [
                            'notification_id'   => $id,
                            'title'             => $msg['title'],
                            'body'              => $msg['message'],
                            'notification_type' => $msg['notification_type'],
                            'image'             => $msg['image'] ?? '', // optional image
                            'click_action' => $msg['notification_type']=="normal"?'OPEN_NOTIFICATION':"OPEN_SCRATCH"

                        ],
                        'android' => [
                            'notification' => [
                                'click_action' => $msg['notification_type']=="normal"?'OPEN_NOTIFICATION':"OPEN_SCRATCH"
                            ]
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'category' => 'NEW_MESSAGE_CATEGORY'
                                ]
                            ]
                        ]
                    ]
                ], $jsonPath);


            }
        }
        return response()->json($data);
    }
}
