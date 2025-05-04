<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class Homesetting extends Controller
{
    public function index(){
        $page_name= 'Home Setting';
        return view('backend.home-setting',compact('page_name'));
    }
    public function saveHomeSetting(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $saveData['main_heading'] = $data['main_heading']?trim($data['main_heading']):'';
        $saveData['main_description'] = $data['main_description']?trim($data['main_description']):'';
        $saveData['about_main_heading'] = $data['about_main_heading']?trim($data['about_main_heading']):'';
        $saveData['about_sub_heading'] = $data['about_sub_heading']?trim($data['about_sub_heading']):'';
        $saveData['about_description'] = $data['about_description']?trim($data['about_description']):'';
        $saveData['services_main_heading'] = $data['services_main_heading']?trim($data['services_main_heading']):'';
        $saveData['services_sub_heading'] = $data['services_sub_heading']?trim($data['services_sub_heading']):'';
        $saveData['services_description'] = $data['services_description']?trim($data['services_description']):'';
        $saveData['work_main_heading'] = $data['work_main_heading']?trim($data['work_main_heading']):'';
        $saveData['work_sub_heading'] = $data['work_sub_heading']?trim($data['work_sub_heading']):'';
        $saveData['work_description'] = $data['work_description']?trim($data['work_description']):'';
        $saveData['expertise_main_heading'] = $data['expertise_main_heading']?trim($data['expertise_main_heading']):'';
        $saveData['expertise_sub_heading'] = $data['expertise_sub_heading']?trim($data['expertise_sub_heading']):'';
        $saveData['expertise_description'] = $data['expertise_description']?trim($data['expertise_description']):'';
        $saveData['achievement_main_heading'] = $data['achievement_main_heading']?trim($data['achievement_main_heading']):'';
        $saveData['achievement_sub_heading'] = $data['achievement_sub_heading']?trim($data['achievement_sub_heading']):'';
        $saveData['achievement_description'] = $data['achievement_description']?trim($data['achievement_description']):'';
        $saveData['testimonial_main_heading'] = $data['testimonial_main_heading']?trim($data['testimonial_main_heading']):'';
        $saveData['testimonial_sub_heading'] = $data['testimonial_sub_heading']?trim($data['testimonial_sub_heading']):'';
        $saveData['blog_main_heading'] = $data['blog_main_heading']?trim($data['blog_main_heading']):'';
        $saveData['blog_sub_heading'] = $data['blog_sub_heading']?trim($data['blog_sub_heading']):'';
        $saveData['meta_title'] = $data['meta_title']?trim($data['meta_title']):'';
        $saveData['meta_description'] = $data['meta_description']?trim($data['meta_description']):'';
        $saveData['meta_keyword'] = $data['meta_keyword']?trim($data['meta_keyword']):'';
        $saveData['about_us_yt_link'] = $data['about_us_yt_link']?trim($data['about_us_yt_link']):'';
        $saveData['footer_description'] = $data['footer_description']?trim($data['footer_description']):'';
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_main_image']){
                    removeImage($data['old_main_image']);
                }
                if($data['old_main_image_webp']){
                    removeImage($data['old_main_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['main_image'] = $filename;
                $saveData['main_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('second_image')) {
            $file = $request->file('second_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_second_image']){
                    removeImage($data['old_second_image']);
                }
                if($data['old_second_image_webp']){
                    removeImage($data['old_second_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['second_image'] = $filename;
                $saveData['second_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('third_image')) {
            $file = $request->file('third_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_third_image']){
                    removeImage($data['old_third_image']);
                }
                if($data['old_third_image_webp']){
                    removeImage($data['old_third_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['third_image'] = $filename;
                $saveData['third_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('fourth_image')) {
            $file = $request->file('fourth_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_fourth_image']){
                    removeImage($data['old_fourth_image']);
                }
                if($data['old_fourth_image_webp']){
                    removeImage($data['old_fourth_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['fourth_image'] = $filename;
                $saveData['fourth_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('about_image')) {
            $file = $request->file('about_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_about_image']){
                    removeImage($data['old_about_image']);
                }
                if($data['old_about_image_webp']){
                    removeImage($data['old_about_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['about_image'] = $filename;
                $saveData['about_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('expertise_image')) {
            $file = $request->file('expertise_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_expertise_image']){
                    removeImage($data['old_expertise_image']);
                }
                if($data['old_expertise_image_webp']){
                    removeImage($data['old_expertise_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['expertise_image'] = $filename;
                $saveData['expertise_image_webp'] = $webp_filename;
            }
        }
        if ($request->hasFile('footer_image')) {
            $file = $request->file('footer_image');
            if ($file->isValid()) { 
                $filename = $file->hashName();
                $file->move(public_path('uploads'), $filename);
                if($data['old_footer_image']){
                    removeImage($data['old_footer_image']);
                }
                if($data['old_footer_image_webp']){
                    removeImage($data['old_footer_image_webp']);
                }
                $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $webp_path = public_path('uploads/' . $webp_filename);
                $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
                $saveData['footer_image'] = $filename;
                $saveData['footer_image_webp'] = $webp_filename;
            }
        }
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('homesetting')->insert($saveData);
            $msg = 'Homesetting Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('homesetting')->where('id',$id)->update($saveData);
            $msg = 'Homesetting Updated Successfully';
        }
        return redirect(route('admin.home-setting'))->with('success',$msg);
    }
}
