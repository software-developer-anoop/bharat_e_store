<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class Authentication extends Controller
{
    public function index(){
        return view('backend.login');
    }
    public function authenticate(Request $request){
        $credentials = $request->only('userName', 'userPassword');
        if (empty($credentials['userName'])) {
            // return back()->with('error','Please Enter The Email');
            return response()->json(['status' => false, 'msg' => 'Please Enter The Email']);
        }
        if (empty($credentials['userPassword'])) {
            //return back()->with('error','Please Enter The Password');
            return response()->json(['status' => false, 'msg' => 'Please Enter The Password']);
        }
        
        // Attempt to authenticate the user
        if (Auth::attempt(['user_name' => $credentials['userName'], 'password' => $credentials['userPassword']])) {
            $request->session()->regenerate();
            //return redirect(route('admin.dashboard'))->with('success','Logged In Successfully');
            return response()->json(['status' => true, 'msg' => 'Logged In Successfully', 'url' => route('admin.dashboard') ]);
        }
        //return back()->with('error','Invalid Login Credentials');
        return response()->json(['status' => false, 'msg' => 'Invalid Login Credentials']);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login')); // Redirect to login page
    }
}
