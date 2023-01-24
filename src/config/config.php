<?php

return [

    'prefix' => 'medialibrary',
    'middleware' => ['web', 'auth'],

    'providers' => [
        \YektaDG\Medialibrary\Providers\ExtendedMediaServiceProvider::class,
    ],


    'aliases' => [
        'MediaUploader' => \YektaDG\Medialibrary\Facades\ExtendedMediaFacade::class,
    ]
];