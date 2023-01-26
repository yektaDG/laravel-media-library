<?php

use Illuminate\Support\Facades\Route;
use YektaDG\Medialibrary\Http\Controllers\MediaController;


Route::prefix('/media-library')->name('medialibrary.')->controller(MediaController::class)->group(function () {
    Route::post('/store', 'store')->name('store');
    Route::get('/all', 'getAllImagesUploadedByCurrentUser')->name('all');
    Route::get('/get-by-folder', 'getImageByGalleryFolder')->name('get-by-folder');
    Route::post('/remove', 'remove')->name('remove');
    Route::post('/get', 'getSingleImage')->name('single');
    Route::post('/set-alt', 'setAlt')->name('set-alt');
    Route::post('/image-exists', [MediaController::class, 'imageExists'])->name('image-exists');

    Route::prefix('/folder')->name('folder.')->group(function () {
        Route::post('/add-media', [MediaController::class, 'addToFolder'])->name('add-media');
        Route::post('/remove-media', [MediaController::class, 'removeMediaFromFolder'])->name('remove-media');
        Route::post('/remove', [MediaController::class, 'removeFolder'])->name('remove');
        Route::get('/all', [MediaController::class, 'getAllFolders'])->name('all');
        Route::get('/{folder}/get-media', [MediaController::class, 'getMediaByFolder'])->name('folder-media');
    });
});
