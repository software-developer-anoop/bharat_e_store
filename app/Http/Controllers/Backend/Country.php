<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Country extends Controller
{
    public function index(){
        $page_name = 'Country List';
        $data = DB::table('country')->get();
        return view('backend.country-list',compact('page_name','data'));
    }
    public function addCountry($id=null){
        $data = $id?DB::table('country')->where('id',$id)->first():'';
        $page_name = $id?'Edit Country':'Add Country';
        return view('backend.add-country',compact('data','page_name'));
    }
    public function saveCountry(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['country_name'] = ucwords(trim($data['country_name']));
        $duplicate = DB::table('country')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }

        $filename = $data['old_flag_image'];
        if ($request->hasFile('flag_image')) {
            $file = $request->file('flag_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_flag_image']){
                    removeImage($data['old_flag_image']);
                }
                if($data['old_flag_image_webp']){
                    removeImage($data['old_flag_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['flag_image'] = $filename;
                $saveData['flag_image_webp'] = $webp_filename;
            }
        }
        
        $saveData['country_name'] = $data['country_name']?trim($data['country_name']):'';
        $saveData['country_code'] = $data['country_code']?trim($data['country_code']):'';
        $saveData['country_currency_symbol'] = $data['country_currency_symbol']?trim($data['country_currency_symbol']):'';
        $saveData['flag_image_alt'] = $data['flag_image_alt']?trim($data['flag_image_alt']):'';
      
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('country')->insert($saveData);
            $msg = 'Country Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('country')->where('id',$id)->update($saveData);
            $msg = 'Country Updated Successfully';
        }
        return redirect(route('admin.country-list'))->with('success',$msg);
    }
}
