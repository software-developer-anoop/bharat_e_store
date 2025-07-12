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
    public function privacy_policy(){
        $page = DB::table('cms_pages')->where('page_name','Privacy Policy')->select('page_name','description')->first();
        return view('cms_page',compact('page'));
    }
}
