<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class Websetting extends Controller
{
    public function index(){
        $page_name= 'Web Setting';
        return view('backend.web-setting',compact('page_name'));
    }
    public function saveWebSetting(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $saveData['site_name'] = $data['site_name']?trim($data['site_name']):'';
        $saveData['mobile_number'] = $data['mobile_number']?trim($data['mobile_number']):'';
        $saveData['email'] = $data['email']?trim($data['email']):'';
        // $saveData['facebook_link'] = $data['facebook_link']?trim($data['facebook_link']):'';
        // $saveData['twitter_link'] = $data['twitter_link']?trim($data['twitter_link']):'';
        // $saveData['instagram_link'] = $data['instagram_link']?trim($data['instagram_link']):'';
        // $saveData['linkedin_link'] = $data['linkedin_link']?trim($data['linkedin_link']):'';
        $saveData['address'] = $data['address']?trim($data['address']):'';
        // $saveData['meta_title'] = $data['meta_title']?trim($data['meta_title']):'';
        // $saveData['meta_description'] = $data['meta_description']?trim($data['meta_description']):'';
        // $saveData['meta_keyword'] = $data['meta_keyword']?trim($data['meta_keyword']):'';
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_logo']){
                    removeImage($data['old_logo']);
                }
                if($data['old_logo_webp']){
                    removeImage($data['old_logo_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['logo'] = $filename;
                $saveData['logo_webp'] = $webp_filename;
            }
        }
        if ($file = $request->file('favicon')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_favicon']))) {
                    @unlink(public_path('uploads/' . $data['old_favicon']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['favicon'] = $filename;
            }
        }
        if ($file = $request->file('banner')) {
            if ($file->isValid()) {
                $filename = $file->hashName();
                if (is_file(public_path('uploads/' . $data['old_banner']))) {
                    @unlink(public_path('uploads/' . $data['old_banner']));
                }
                $file->move(public_path('uploads/'), $filename);
                $saveData['banner'] = $filename;
            }
        }
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('websetting')->insert($saveData);
            $msg = 'Websetting Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('websetting')->where('id',$id)->update($saveData);
            $msg = 'Websetting Updated Successfully';
        }
        return redirect(route('admin.web-setting'))->with('success',$msg);
    }
}
