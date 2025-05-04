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
}
