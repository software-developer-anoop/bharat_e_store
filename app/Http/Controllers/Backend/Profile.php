<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class Profile extends Controller {
    public function index() {
        $page_name = 'My Profile';
        $user = DB::table('users')->where('id', Auth::id())->first();
        $countries = DB::table('country')->where('status', 'Active')->select('country_name')->get();
        return view('backend.profile', compact('page_name', 'user', 'countries'));
    }
    public function saveProfile(Request $request) {
        $data = $request->all();
        $saveData = [];
        $saveData['name'] = $data['name'] ? trim($data['name']) : '';
        $saveData['phone'] = $data['phone'] ? trim($data['phone']) : '';
        $saveData['email'] = $data['email'] ? trim($data['email']) : '';
        $saveData['user_name'] = $data['user_name'] ? trim($data['user_name']) : '';
        $saveData['dob'] = $data['dob'] ? trim($data['dob']) : '';
        $saveData['address'] = $data['address'] ? trim($data['address']) : '';
        $saveData['country'] = $data['country'] ? trim($data['country']) : '';
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
        $saveData['updated_at'] = Carbon::now();
        DB::table('users')->where('id', Auth::id())->update($saveData);
        $msg = 'Profile Updated Successfully';
        return redirect(route('admin.my-profile'))->with('success', $msg);
    }
}
