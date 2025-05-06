<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class GlobalViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $web = webSetting('*');
        //$home = homeSetting('*');
        $userData = userData('*');
        View::share(['web'=>$web,
            //'home'=>$home,
            'userData'=>$userData]);
    }
}
