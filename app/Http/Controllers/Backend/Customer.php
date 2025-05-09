<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Customer extends Controller
{
    public function index(){
        $page_name = 'Customer List';
        $data = DB::table('customers')->get();
        return view('backend.customer-list',compact('page_name','data'));
    }
    public function addCustomer($id=null){
        $data = $id?DB::table('customers')->where('id',$id)->first():'';
        $page_name = $id?'Edit Customer':'Add Customer';
        return view('backend.add-customer',compact('data','page_name'));
    }
    public function saveCustomer(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['customer_email'] = trim($data['customer_email']);
        $checkData['customer_phone'] = trim($data['customer_phone']);
        $duplicate = DB::table('customers')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        $saveData = $checkData;
        $saveData['customer_name'] = $data['customer_name']?trim($data['customer_name']):'';
        $saveData['customer_address'] = $data['customer_address']?trim($data['customer_address']):'';
        $saveData['customer_gender'] = $data['customer_gender']?trim($data['customer_gender']):'';
        $saveData['referral_code'] = $data['referral_code']?trim($data['referral_code']):'';
        
        // if ($request->hasFile('Customer_image')) {
        //     $file = $request->file('Customer_image');
        //     if ($file->isValid()) { 
        //         $filename = $file->hashName();
        //         $file->move(public_path('uploads'), $filename);
        //         if($data['old_Customer_image']){
        //             removeImage($data['old_Customer_image']);
        //         }
        //         if($data['old_Customer_image_webp']){
        //             removeImage($data['old_Customer_image_webp']);
        //         }
        //         $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        //         $webp_path = public_path('uploads/' . $webp_filename);
        //         $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
        //         $saveData['Customer_image'] = $filename;
        //         $saveData['Customer_image_webp'] = $webp_filename;
        //     }
        // }
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            if(!empty(trim($data['referrer_code']))){
            $saveData['referrer_code'] = $data['referrer_code'] ? trim($data['referrer_code']) : '';
            }else{
            $saveData['referrer_code'] = random_alphanumeric_string(10);
            }
            $id = DB::table('customers')->insertGetId($saveData);
            $msg = 'Customer Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('customers')->where('id',$id)->update($saveData);
            $msg = 'Customer Updated Successfully';
        }
        return redirect(route('admin.customer-list'))->with('success',$msg);
    }
}
