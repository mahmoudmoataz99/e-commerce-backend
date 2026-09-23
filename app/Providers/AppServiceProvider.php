<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (PHP_VERSION_ID >= 80500) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                if ($errno === E_DEPRECATED && str_contains($errfile, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
                    return true; 
                }
                return false; 
            });
        }
    }
}