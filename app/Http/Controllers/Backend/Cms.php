<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Cms extends Controller
{
    public function index(){
        $page_name = 'CMS Page List';
        $data = DB::table('cms_pages')->get();
        return view('backend.cms-list',compact('page_name','data'));
    }
    public function addCmsPage($id=null){
        $data = $id?DB::table('cms_pages')->where('id',$id)->first():'';
        $page_name = $id?'Edit Cms Page':'Add Cms Page';
        return view('backend.add-cms-page',compact('data','page_name'));
    }
    public function saveCmsPage(Request $request){
        $data = $request->all();
        $saveData = [];
        $id = $data['id']?trim($data['id']):'';
        $checkData['page_name'] = ucwords(trim($data['page_name']));
        $duplicate = DB::table('cms_pages')->where($checkData)->first();

        if (!empty($duplicate)) {
            if ($id === '' || $duplicate->id != $id) {
                return redirect()->back()->with('error', 'Duplicate Entry');
            }
        }

        // $filename = $data['old_bg_image_jpg'];
        // if ($request->hasFile('bg_image_jpg')) {
        //     $file = $request->file('bg_image_jpg');
        //     if ($file->isValid()) { 
        //         $filename = $file->hashName();
        //         $file->move(public_path('uploads'), $filename);
        //         if($data['old_bg_image_jpg']){
        //             removeImage($data['old_bg_image_jpg']);
        //         }
        //         if($data['old_bg_image_webp']){
        //             removeImage($data['old_bg_image_webp']);
        //         }
        //         $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        //         $webp_path = public_path('uploads/' . $webp_filename);
        //         $webp_image = convertImageToWebp(public_path('uploads/'), $filename, $webp_filename);
        //         $saveData['bg_image_jpg'] = $filename;
        //         $saveData['bg_image_webp'] = $webp_filename;
        //     }
        // }
        $saveData = $checkData;
        // $saveData['page_name'] = $data['page_name']?trim($data['page_name']):'';
        // $saveData['page_slug'] = $data['page_slug']?trim($data['page_slug']):'';
        // $saveData['h1_heading'] = $data['h1_heading']?trim($data['h1_heading']):'';
        // $saveData['short_description'] = $data['short_description']?trim($data['short_description']):'';
        $saveData['description'] = $data['description']?trim($data['description']):'';
        // $saveData['second_description'] = $data['second_description']?trim($data['second_description']):'';
        // $saveData['meta_title'] = $data['meta_title']?trim($data['meta_title']):'';
        // $saveData['meta_description'] = $data['meta_description']?trim($data['meta_description']):'';
        // $saveData['meta_keyword'] = $data['meta_keyword']?trim($data['meta_keyword']):'';
        // $saveData['bg_image_alt'] = $data['bg_image_alt']?trim($data['bg_image_alt']):'';
        // $saveData['meta_schema'] = empty(($data['meta_schema'])) ? generateProductSchema(trim($data['page_name']), $filename, trim($data['meta_description'])) : trim($data['meta_schema']);
        
        if(empty($id)){
            $saveData['created_at'] = Carbon::now();
            DB::table('cms_pages')->insert($saveData);
            $msg = 'Cms Page Added successfully';
        }else{
            $saveData['updated_at'] = Carbon::now();
            DB::table('cms_pages')->where('id',$id)->update($saveData);
            $msg = 'Cms Page Updated Successfully';
        }
        return redirect(route('admin.cms-list'))->with('success',$msg);
    }
}
