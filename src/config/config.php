<?php

return [

    'prefix' => 'medialibrary',
    'user_model_path' => 'App\Models\User\User',
    'middleware' => ['web', 'auth'],
    'medialibrary_storage' => 'public',
    'generate_sizes' => true,

    'providers' => [
        \YektaDG\Medialibrary\Providers\ExtendedMediaServiceProvider::class,
    ],


    'aliases' => [
        'MediaUploader' => \YektaDG\Medialibrary\Facades\ExtendedMediaFacade::class,
    ]
];