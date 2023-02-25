<?php

namespace YektaDG\Medialibrary\Console\Commands;


use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    protected $signature = 'medialibrary:update';
    protected $description = 'Updates the published vendor of the medialibrary.';

    public function handle()
    {
        $this->publishes([
            __DIR__ . '/../resources/js/medialibrary.min.js' => public_path('vendor/yektadg/medialibrary/medialibrary.min.js'),
            __DIR__ . '/../public/media' => public_path('/vendor/yektadg/medialibrary'),
            __DIR__ . '/../resources/css' => public_path('vendor/yektadg/medialibrary'),
        ], 'public');
    }
}
