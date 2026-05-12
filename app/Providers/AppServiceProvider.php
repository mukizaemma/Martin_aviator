<?php

namespace App\Providers;

use App\View\Composers\FrontLayoutComposer;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(Excel::class, function ($app) {
        //     return new Excel($app['view'], $app['request']);
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.frontbase', FrontLayoutComposer::class);
    }
}
