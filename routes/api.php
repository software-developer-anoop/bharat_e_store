<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Countrylist;
use App\Http\Controllers\Api\Authentication;
use App\Http\Controllers\Api\Homepage;
use App\Http\Controllers\Api\Cmspage;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('country-list', [Countrylist::class, 'index'])->name('api.countrylist');
Route::post('register', [Authentication::class, 'index'])->name('api.register');
Route::post('verify-otp', [Authentication::class, 'verifyOtp'])->name('api.verify-otp');
Route::post('resend-otp', [Authentication::class, 'resendOtp'])->name('api.resend-otp');
Route::post('customer-login', [Authentication::class, 'customerLogin'])->name('api.customer-login');
Route::post('autologin', [Authentication::class, 'autoLogin'])->name('api.autologin');
Route::post('logout', [Authentication::class, 'logOut'])->name('api.logout');
Route::post('banner', [Homepage::class, 'index'])->name('api.banner');
Route::post('category-list', [Homepage::class, 'categoryList'])->name('api.category-list');
Route::post('cms-page', [Cmspage::class, 'index'])->name('api.cms-page');
Route::post('subcategory-list', [Homepage::class, 'subcategoryList'])->name('api.subcategory-list');