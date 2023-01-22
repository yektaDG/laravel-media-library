<?php

return [

    'prefix' => 'medialibrary',
    'middleware' => ['web'],

    'providers' => [
        \YektaDG\Medialibrary\Providers\ExtendedMediaServiceProvider::class,
    ],


    'aliases' => [
        'MediaUploader' => \YektaDG\Medialibrary\Facades\ExtendedMediaFacade::class,
    ]
];