<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        // see: https://github.com/laravel/framework/issues/2554
        if (url('/')=='http://localhost') {
            /** @var \Illuminate\Routing\UrlGenerator $url */
            $url = $this->app['url'];
            // Force the application URL
            $url->forceRootUrl(config('app.url'));
        }
    }


    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map()
    {
        Route::group(
            ['namespace' => $this->namespace],
            function ($router) {
                require app_path('Http/routes.php');
            }
        );
    }
}
