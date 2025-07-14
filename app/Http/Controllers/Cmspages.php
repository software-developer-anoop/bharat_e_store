<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Cmspages extends Controller
{
    public function index(){
        $page = DB::table('cms_pages')->where('page_name','Contact Us')->select('page_name','description')->first();
        return view('cms_page',compact('page'));
    }
    public function refund_policy(){
        $page = DB::table('cms_pages')->where('page_name','Refund Policy')->select('page_name','description')->first();
        return view('cms_page',compact('page'));
    }
    public function t_and_c(){
        $page = DB::table('cms_pages')->where('page_name','Terms And Conditions')->select('page_name','description')->first();
        return view('cms_page',compact('page'));
    }
    public function deleteAccountPage(){
        $countries = DB::table('country')->where('status','Active')->select('country_name')->get();
        return view('delete-account',compact('countries'));
    }
    public function deleteAccount(Request $request)
    {
        $country = $request->input('country');
        $email = $request->input('email');
        $mobile = $request->input('mobile');

        // Validate input based on country
        if ($country === "India" && empty($mobile)) {
            return back()->with('error', 'Please enter your mobile number.');
        } elseif ($country !== "India" && empty($email)) {
            return back()->with('error', 'Please enter your email address.');
        }

        // Build search conditions
        $where = ['country_name' => $country];

        if ($country === "India") {
            $where['customer_phone'] = $mobile;
        } else {
            $where['customer_email'] = $email;
        }

        // Check if customer exists
        $customer = DB::table('customers')->where($where)->first();

        if (!$customer) {
            return back()->with('error', 'No account found with the provided details.');
        }

        // Delete the account
        DB::table('customers')->where('id', $customer->id)->delete();

        return redirect(route('delete.account.page'))->with('success', 'Account Deleted Successfully');
    }
}
