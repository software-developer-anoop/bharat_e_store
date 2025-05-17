<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Countrylist;
use App\Http\Controllers\Api\Authentication;
use App\Http\Controllers\Api\Homepage;
use App\Http\Controllers\Api\Cmspage;
use App\Http\Controllers\Api\Wishlist;
use App\Http\Controllers\Api\Couponlist;
use App\Http\Controllers\Api\Address;
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('country-list', [Countrylist::class, 'index'])->name('api.countrylist');
//Authentication
Route::post('register', [Authentication::class, 'index'])->name('api.register');
Route::post('verify-otp', [Authentication::class, 'verifyOtp'])->name('api.verify-otp');
Route::post('resend-otp', [Authentication::class, 'resendOtp'])->name('api.resend-otp');
Route::post('customer-login', [Authentication::class, 'customerLogin'])->name('api.customer-login');
Route::post('autologin', [Authentication::class, 'autoLogin'])->name('api.autologin');
Route::post('logout', [Authentication::class, 'logOut'])->name('api.logout');
//Homepage 
Route::post('banner', [Homepage::class, 'index'])->name('api.banner');
Route::post('category-list', [Homepage::class, 'categoryList'])->name('api.category-list');
Route::post('cms-page', [Cmspage::class, 'index'])->name('api.cms-page');
Route::post('subcategory-list', [Homepage::class, 'subcategoryList'])->name('api.subcategory-list');
Route::post('trending-products', [Homepage::class, 'trendingProducts'])->name('api.trending-products');
Route::post('hot-deals-products', [Homepage::class, 'hotDealsProducts'])->name('api.hot-deals-products');
//Wishlist
Route::post('add-to-wishlist', [Wishlist::class, 'index'])->name('api.add-to-wishlist');
Route::post('my-wishlist', [Wishlist::class, 'myWishlist'])->name('api.my-wishlist');
Route::post('remove-from-wishlist', [Wishlist::class, 'removeFromWishlist'])->name('api.remove-from-wishlist');
//Address
Route::post('my-address', [Address::class, 'index'])->name('api.my-address');
Route::post('add-edit-address', [Address::class, 'addEditAddress'])->name('api.add-edit-address');
Route::post('delete-address', [Address::class, 'deleteAddress'])->name('api.delete-address');
Route::post('referral-history', [Homepage::class, 'referralHistory'])->name('api.referral-history');
Route::get('coupon-list', [Couponlist::class, 'index'])->name('api.coupon-list');
Route::post('search', [Homepage::class, 'search'])->name('api.search');
Route::post('edit-profile', [Authentication::class, 'editProfile'])->name('api.edit-profile');

Route::post('product-detail', [Homepage::class, 'productDetail'])->name('api.product-detail');