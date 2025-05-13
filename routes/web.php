<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Authentication;
use App\Http\Controllers\Backend\Dashboard;
use App\Http\Controllers\Backend\Homesetting;
use App\Http\Controllers\Backend\Websetting;
use App\Http\Controllers\Backend\Cms;
use App\Http\Controllers\Backend\Ajax;
use App\Http\Controllers\Backend\Customer;
use App\Http\Controllers\Backend\Country;
use App\Http\Controllers\Backend\State;
use App\Http\Controllers\Backend\City;
use App\Http\Controllers\Backend\User;
use App\Http\Controllers\Backend\Product;
use App\Http\Controllers\Backend\Profile;
use App\Http\Controllers\Backend\Category;
use App\Http\Controllers\Backend\Subcategory;
use App\Http\Controllers\Backend\Notification;
use App\Http\Controllers\Backend\Coupon;
use App\Http\Controllers\Backend\Menu;
// use App\Http\Controllers\Frontend\Home;
// use App\Http\Controllers\Frontend\Common;


Route::get('/login', [Authentication::class, 'index'])->name('login');
Route::post('/authenticate', [Authentication::class, 'authenticate'])->name('authenticate');
Route::prefix('admin')->middleware(['auth'])->group(function () {
Route::get('/dashboard', [Dashboard::class, 'index'])->name('admin.dashboard');
Route::get('/getEnquiries', [Dashboard::class, 'getEnquiries'])->name('admin.getEnquiries');
Route::get('/home-setting', [Homesetting::class, 'index'])->name('admin.home-setting');
Route::get('/web-setting', [Websetting::class, 'index'])->name('admin.web-setting');
Route::post('/save-web-setting', [Websetting::class, 'saveWebSetting'])->name('admin.save-web-setting');
Route::post('/save-home-setting', [Homesetting::class, 'saveHomeSetting'])->name('admin.save-home-setting');
//CMS Master
Route::get('/cms-list', [Cms::class, 'index'])->name('admin.cms-list');
Route::get('/add-cms-page', [Cms::class, 'addCmsPage'])->name('admin.add-cms-page');
Route::get('/edit-cms-page/{id}', [Cms::class, 'addCmsPage'])->name('admin.edit-cms-page');
Route::post('/save-cms-page', [Cms::class, 'saveCmsPage'])->name('admin.save-cms-page');
//Customer Master
Route::get('/customer-list', [Customer::class, 'index'])->name('admin.customer-list');
Route::get('/add-customer', [Customer::class, 'addCustomer'])->name('admin.add-customer');
Route::get('/edit-customer/{id}', [Customer::class, 'addCustomer'])->name('admin.edit-customer');
Route::post('/save-customer', [Customer::class, 'saveCustomer'])->name('admin.save-customer');
//Country Master
Route::get('/country-list', [Country::class, 'index'])->name('admin.country-list');
Route::get('/add-country', [Country::class, 'addCountry'])->name('admin.add-country');
Route::get('/edit-country/{id}', [Country::class, 'addCountry'])->name('admin.edit-country');
Route::post('/save-country', [Country::class, 'saveCountry'])->name('admin.save-country');
//State Master
Route::get('/state-list', [State::class, 'index'])->name('admin.state-list');
Route::get('/add-state', [State::class, 'addState'])->name('admin.add-state');
Route::get('/edit-state/{id}', [State::class, 'addState'])->name('admin.edit-state');
Route::post('/save-state', [State::class, 'saveState'])->name('admin.save-state');
//City Master
Route::get('/city-list', [City::class, 'index'])->name('admin.city-list');
Route::get('/add-city', [City::class, 'addCity'])->name('admin.add-city');
Route::get('/edit-city/{id}', [City::class, 'addCity'])->name('admin.edit-city');
Route::post('/save-city', [City::class, 'saveCity'])->name('admin.save-city');
//User Master
Route::get('/user-list', [User::class, 'index'])->name('admin.user-list');
Route::get('/add-user', [User::class, 'addUser'])->name('admin.add-user');
Route::get('/edit-user/{id}', [User::class, 'addUser'])->name('admin.edit-user');
Route::post('/save-user', [User::class, 'saveUser'])->name('admin.save-user');
//Category Master
Route::get('/category-list', [Category::class, 'index'])->name('admin.category-list');
Route::get('/add-category', [Category::class, 'addCategory'])->name('admin.add-category');
Route::get('/edit-category/{id}', [Category::class, 'addCategory'])->name('admin.edit-category');
Route::post('/save-category', [Category::class, 'saveCategory'])->name('admin.save-category');
//Subcategory Master
Route::get('/subcategory-list', [Subcategory::class, 'index'])->name('admin.subcategory-list');
Route::get('/add-subcategory', [Subcategory::class, 'addSubcategory'])->name('admin.add-subcategory');
Route::get('/edit-subcategory/{id}', [Subcategory::class, 'addSubcategory'])->name('admin.edit-subcategory');
Route::post('/save-subcategory', [Subcategory::class, 'saveSubcategory'])->name('admin.save-subcategory');
//Product Master
Route::get('/product-list', [Product::class, 'index'])->name('admin.product-list');
Route::get('/add-product', [Product::class, 'addProduct'])->name('admin.add-product');
Route::get('/edit-product/{id}', [Product::class, 'addProduct'])->name('admin.edit-product');
Route::post('/save-product', [Product::class, 'saveProduct'])->name('admin.save-product');
//Coupon Master
Route::get('/coupon-list', [Coupon::class, 'index'])->name('admin.coupon-list');
Route::get('/add-coupon', [Coupon::class, 'addCoupon'])->name('admin.add-coupon');
Route::get('/edit-coupon/{id}', [Coupon::class, 'addCoupon'])->name('admin.edit-coupon');
Route::post('/save-coupon', [Coupon::class, 'saveCoupon'])->name('admin.save-coupon');
//Notification Master
Route::get('/notification-list', [Notification::class, 'index'])->name('admin.notification-list');
Route::get('/add-notification', [Notification::class, 'addNotification'])->name('admin.add-notification');
Route::get('/edit-notification/{id}', [Notification::class, 'addNotification'])->name('admin.edit-notification');
Route::post('/save-notification', [Notification::class, 'saveNotification'])->name('admin.save-notification');
Route::post('/push-notification', [Notification::class, 'pushNotification'])->name('admin.push-notification');
//Profile
Route::get('/my-profile', [Profile::class, 'index'])->name('admin.my-profile');
Route::post('/save-profile', [Profile::class, 'saveProfile'])->name('admin.save-profile');
//Menu
Route::get('/assign-menu', [Menu::class, 'index'])->name('admin.assign-menu');
//Ajax
Route::post('/getSlug', [Ajax::class, 'index'])->name('admin.getSlug');
Route::post('/deleteItem', [Ajax::class, 'deleteItem'])->name('admin.deleteItem');
Route::post('/changeStatus', [Ajax::class, 'changeStatus'])->name('admin.changeStatus');
Route::post('/getStates', [Ajax::class, 'getStates'])->name('admin.getStates');
Route::post('/getSubcategory', [Ajax::class, 'getSubcategory'])->name('admin.getSubcategory');
Route::post('/manageInventory', [Ajax::class, 'manageInventory'])->name('admin.manageInventory');
Route::post('/setIsTrending', [Ajax::class, 'setIsTrending'])->name('admin.setIsTrending');
Route::get('/logout', [Authentication::class, 'logout'])->name('admin.logout');
});

//Frontend Routes
// Route::post('getRandomCaptcha', [Home::class, 'getRandomCaptcha']);
// Route::post('save-query', [Home::class, 'saveQuery']);
// Route::get('/', [Home::class, 'index']);
// Route::get('/{any}', [Common::class, 'index'])->where('any', '.*');