<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;

class ImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $diskpath;
    protected $filename;
    protected $extension;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($diskpath, $filename,$extension)
    {
        $this->diskpath = $diskpath;
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
            $img = Image::make('storage/' . $this->diskpath)->widen($size)
                ->save('storage/uploads/images/' . now()->year . '/' . now()->month . '/' . $this->filename . '-' . $size . 'x' . "-{$this->extension}", $size != 139 ? 80 : 100, 'webp');
        }
    }


}
