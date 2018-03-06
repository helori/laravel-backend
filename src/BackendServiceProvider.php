<?php

namespace Helori\LaravelAdmin;

use Illuminate\Support\ServiceProvider;


class BackendServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }
    
    public function boot()
	{
        $this->loadRoutesFrom(__DIR__.'/Routes/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'laravel-backend');

        $this->publishes([
            __DIR__.'/Assets/backend.js' => base_path('resources/assets/js/backend.js'),
            __DIR__.'/Assets/backend.scss' => base_path('resources/assets/sass/backend.scss'),
        ], 'laravel-backend-assets');
	}
}
