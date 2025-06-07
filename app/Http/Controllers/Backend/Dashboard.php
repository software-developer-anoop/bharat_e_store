<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Dashboard extends Controller
{
    public function index(){
        $page_name = 'Dashboard';
        $currency = getUserCurrency();
        $stats = DB::table('orders')
            ->selectRaw("
                SUM(amount) as total_amount,
                SUM(CASE WHEN order_status = 'delivered' THEN amount ELSE 0 END) as delivered_amount,
                SUM(CASE WHEN order_status = 'pending' THEN amount ELSE 0 END) as pending_amount
            ")
            ->first();

        return view('backend.dashboard',compact('page_name','currency','stats'));
    }
    public function getEnquiries(){
        $page_name = 'Enquiry List';
        $data = DB::table('enquiry_list')->get();
        return view('backend.enquiry-list',compact('page_name','data'));
    }
}
