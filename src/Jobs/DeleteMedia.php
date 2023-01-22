<?php

namespace YektaDG\Medialibrary\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class DeleteMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $directory;
    protected $filename;
    protected $extension;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($directory, $filename, $extension)
    {
        $this->directory = $directory;
        $this->filename = $filename;
        $this->extension = $extension;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sizes = [
            139,
            1280,
            1500,
        ];
        foreach ($sizes as $size) {
            $path = 'storage/' . $this->directory . '/' . $this->filename . '-' . $size . "x-{$this->extension}";
            File::delete(public_path($path));
        }

    }
}