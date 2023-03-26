<?php

namespace YektaDG\Medialibrary\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use YektaDG\Medialibrary\Console\Commands\UpdateCommand;

class MediaLibraryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'medialibrary');

    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mediaLibrary');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('vendor/yektadg/medialibrary'),
            __DIR__ . '/../resources/views/publish' => resource_path('views/vendor/yektadg/medialibrary'),
            __DIR__ . '/../public/media' => public_path('/vendor/yektadg/medialibrary'),
            __DIR__ . '/../config/config.php' => config_path('medialibrary.php'),
            __DIR__ . '/../resources/css' => public_path('vendor/yektadg/medialibrary'),
            __DIR__ . '/../lang' => public_path('vendor/yektadg/medialibrary'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('vendor/yektadg/medialibrary'),
            __DIR__ . '/../public/media' => public_path('/vendor/yektadg/medialibrary'),
            __DIR__ . '/../resources/css' => public_path('vendor/yektadg/medialibrary'),
        ], 'update');

        $this->publishes([
            __DIR__ . '/../lang' => public_path('vendor/yektadg/medialibrary'),
        ], 'update-lang');


        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('medialibrary.php'),
        ], 'config');


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