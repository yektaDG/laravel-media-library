<?php

namespace YektaDG\Medialibrary\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MediaLibraryServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '\..\routes\web.php');
        $this->loadViewsFrom(__DIR__ . '\..\resources\views', 'mediaLibrary');
        $this->publishes([
            __DIR__ . '\..\resources\js' => public_path('vendor/yektadg/medialibrary'),
            __DIR__ . '\..\resources\views\publish' => resource_path('views/vendor/yektadg/medialibrary'),

        ], 'public');

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }


    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('medialibrary.prefix'),
            'middleware' => config('medialibrary.middleware'),
        ];
    }

}