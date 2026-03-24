<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix for MySQL old versions
        Schema::defaultStringLength(191);
        
        // Share user data with all views
        View::composer('*', function ($view) {
            $view->with('currentUser', Auth::user());
        });
        
        // Custom validation for IC Number
        \Validator::extend('ic_number', function ($attribute, $value, $parameters) {
            return preg_match('/^\d{6}-\d{2}-\d{4}$/', $value);
        });
        
        \Validator::replacer('ic_number', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute must be in format 123456-78-9012');
        });
    }
}