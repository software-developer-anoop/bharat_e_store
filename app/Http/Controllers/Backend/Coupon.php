<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Coupon extends Controller
{
    public function index(){
        $page_name = 'Coupon List';
        $data = DB::table('coupons')->get();
        return view('backend.coupon-list',compact('page_name','data'));
    }
    public function addCoupon($id=null){
        $data = $id?DB::table('coupons')->where('id',$id)->first():'';
        $page_name = $id?'Edit Coupon':'Add Coupon';
        return view('backend.add-coupon',compact('data','page_name'));
    }
    public function saveCoupon(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['coupon_code'] = ucwords(trim($data['coupon_code']));
        $duplicate = DB::table('coupons')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }
        
        $saveData = $checkData;
        $saveData['coupon_title'] = $data['coupon_title']?trim($data['coupon_title']):'';
        $saveData['coupon_description'] = $data['coupon_description']?trim($data['coupon_description']):'';
        $saveData['coupon_type'] = $data['coupon_type']?trim($data['coupon_type']):'';
        $saveData['coupon_value'] = $data['coupon_value']?trim($data['coupon_value']):'';
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('coupons')->insert($saveData);
            $msg = 'Coupon Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('coupons')->where('id',$id)->update($saveData);
            $msg = 'Coupon Updated Successfully';
        }
        return redirect(route('admin.coupon-list'))->with('success',$msg);
    }
}
