<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Address extends Controller
{
    public function index(){
        $page_name = 'Address List';
        $data = DB::table('addresses')->get();
        return view('backend.address-list',compact('page_name','data'));
    }
    public function addAddress($id=null){
        $data = $id?DB::table('addresses')->where('id',$id)->first():'';
        $page_name = $id?'Edit Address':'Add Address';
        $customers = DB::table('customers')->select('customer_name','id')->where('profile_status', 'Active')->where('email_status','Verified')->get();
        return view('backend.add-address',compact('data','page_name','customers'));
    }
    public function saveAddress(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        
        $saveData['customer_id'] = $data['customer_id']?trim($data['customer_id']):'';
        $saveData['name'] = $data['name']?trim($data['name']):'';
        $saveData['email'] = $data['email']?trim($data['email']):'';
        $saveData['phone'] = $data['phone']?trim($data['phone']):'';
        $saveData['address'] = $data['address']?trim($data['address']):'';
        $saveData['pincode'] = $data['pincode']?trim($data['pincode']):'';
        $saveData['address_type'] = $data['address_type']?trim($data['address_type']):'';
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('addresses')->insert($saveData);
            $msg = 'Address Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('addresses')->where('id',$id)->update($saveData);
            $msg = 'Address Updated Successfully';
        }
        return redirect(route('admin.address-list'))->with('success',$msg);
    }
}
