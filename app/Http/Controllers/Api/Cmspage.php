<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class Cmspage extends Controller
{
    public function index(Request $request){
        $post = checkPayload();
        $page = trim($post['page']??'');
        if (empty($page)) {
            return response()->json(['status' => false, 'message' => 'Page Name Is Blank']);
        }
        $where = [];
        $where['status']='Active';
        $where['page_name']=$page;
        $content = DB::table('cms_pages')->where($where)->select('description')->first();
        return response()->json([
            'status'=>true,
            'content'=>$content->description,
            'message'=>'Api Accessed Successfully']);
    }
}
