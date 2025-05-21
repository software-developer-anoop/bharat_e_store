<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Menu extends Controller
{
    public function index(Request $request)
    {
        $page_name = 'Assign Menu';
        $id = $request->input('id');

        $data = $id ? DB::table('users')->where('id', $id)->first() : null;

        $users = DB::table('users')
            ->where([
                'status' => 'Active',
                'role' => 'Admin'
            ])
            ->select('id', 'name')
            ->get();

        $assigned_menus = !empty($data?->assigned_menus)
            ? explode(',', $data->assigned_menus)
            : [];

        $menus = [
            '1' => 'Country Master',
            '2' => 'State Master',
            '3' => 'City Master',
            '4' => 'Address Master',
            '5' => 'CMS Pages Master',
            '6' => 'Customer Master',
            '7' => 'User Master',
            '8' => 'Category Master',
            '9' => 'Subcategory Master',
            '10' => 'Product Master',
            '11' => 'Coupon Master',
            '12' => 'Notification Master',
            '13' => 'Orders',
            '14' => 'Settings',
            '15' => 'Assign Menu'
        ];

        return view('backend.assign-menu', compact('page_name', 'users', 'assigned_menus', 'menus', 'id'));
    }

}
