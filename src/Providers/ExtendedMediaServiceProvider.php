<?php


namespace YektaDG\Medialibrary\Providers;


use Illuminate\Contracts\Container\Container;
use Plank\Mediable\MediableServiceProvider;
use Plank\Mediable\MediaUploader;
use YektaDG\Medialibrary\Http\Services\ExtendedMediaUploader;

class ExtendedMediaServiceProvider extends MediableServiceProvider
{

    public function registerUploader(): void
    {
        $this->app->bind('extendedmediable.uploader', function (Container $app) {
            return new ExtendedMediaUploader(
                $app['filesystem'],
                $app['mediable.source.factory'],
                $app['config']->get('mediable')
            );
        });
        $this->app->alias('extendedmediable.uploader', ExtendedMediaUploader::class);
    }
}
