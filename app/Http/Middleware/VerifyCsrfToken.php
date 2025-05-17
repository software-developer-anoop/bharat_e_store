<?php
namespace App\Http\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = 
    ['/api/countrylist', '/api/register', '/api/verify-otp', 
     '/api/resend-otp', '/api/customer-login', '/api/autologin', 
     '/api/logout', '/api/banner', '/api/category-list', 
     '/api/cms-page', '/api/subcategory-list', '/api/trending-products',
     '/api/add-to-wishlist', '/api/my-wishlist', '/api/couponlist',
     '/api/edit-profile', '/api/my-address', '/api/add-edit-address',
     '/api/delete-address', '/api/referral-history', '/api/product-detail',
     '/api/hot-deals-products','/api/category-products','/api/subcategory-products'];
}
