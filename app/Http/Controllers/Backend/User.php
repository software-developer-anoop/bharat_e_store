<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class User extends Controller
{
    public function index(){
        $page_name = 'User List';
        $data = DB::table('users')->where('role','!=','super_admin')->get();
        return view('backend.user-list',compact('page_name','data'));
    }
    public function addUser($id=null){
        $data = $id?DB::table('users')->where('id',$id)->first():'';
        $page_name = $id?'Edit User':'Add User';
        $countries = DB::table('country')->select('country_name')->where('status', 'Active')->get();
        $roles = DB::table('roles')->get();
        return view('backend.add-user',compact('data','page_name','countries','roles'));
    }
    public function saveUser(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $saveData['name'] = $data['name'] ? trim($data['name']) : '';
        $saveData['phone'] = $data['phone'] ? trim($data['phone']) : '';
        $saveData['email'] = $data['email'] ? trim($data['email']) : '';
        $saveData['user_name'] = $data['user_name'] ? trim($data['user_name']) : '';
        $saveData['gender'] = $data['gender'] ? trim($data['gender']) : '';
        $saveData['dob'] = $data['dob'] ? trim($data['dob']) : '';
        $saveData['address'] = $data['address'] ? trim($data['address']) : '';
        $saveData['country'] = $data['country'] ? trim($data['country']) : '';
        $saveData['role'] = $data['role'] ? trim($data['role']) : '';
        if(!empty(trim($data['password']))){
            $saveData['password'] =  Hash::make(trim($data['password']));
        }
        
        if ($file = $request->file('profile_image')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_profile_image']))) {
                    @unlink(public_path('uploads/' . $data['old_profile_image']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['profile_image'] = $filename;
            }
        }
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('users')->insert($saveData);
            $msg = 'User Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('users')->where('id',$id)->update($saveData);
            $msg = 'User Updated Successfully';
        }
        return redirect(route('admin.user-list'))->with('success',$msg);
    }
}
