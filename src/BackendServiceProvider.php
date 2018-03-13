<?php

namespace Helori\LaravelBackend;

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
            __DIR__.'/Assets/Components' => base_path('resources/assets/js/backend'),
            __DIR__.'/Assets/backend.scss' => base_path('resources/assets/sass/backend.scss'),
        ], 'laravel-backend-assets');

        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/laravel-backend'),
        ], 'laravel-backend-views');

        $this->publishes([
            __DIR__.'/config.php' => config_path('laravel-backend.php'),
        ], 'laravel-backend-config');
	}
}
