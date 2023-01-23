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
/*Route::resource('/media', MediaController::class)->except('show');
Route::get('/library/all', [MediaController::class, 'getAllImagesUploadedByCurrentUser'])->name('library.all');
Route::prefix('/media')->name('media.')->controller(MediaController::class)->group(function () {
    Route::get('/all', 'getAllImagesUploadedByCurrentUser')->name('all');
    Route::get('/get-by-folder', 'getImageByGalleryFolder')->name('get-by-folder');
    Route::post('/remove', 'destroy')->name('remove');
    Route::post('/get', 'getSingleImage')->name('single');
    Route::post('/set-alt', 'setAlt')->name('setAlt');
});


Route::post('/util/folder/add-media', [MediaController::class, 'addToFolder'])->name('media.to.folder');
Route::post('/util/folder/remove', [MediaController::class, 'removeFolder'])->name('media.folder.destroy');
Route::post('/util/folder/remove-media', [MediaController::class, 'removeMediaFromFolder'])->name('media.folder.remove-media');

Route::get('/util/folder/all', [MediaController::class, 'getAllFolders'])->name('media.folder.all');
Route::get('/util/folder/{folder}/get-media', [MediaController::class, 'getMediaByFolder'])->name('media.folder.get-media');

Route::post('/image-exists', [MediaController::class, 'imageExists'])->name('image-exists');*/
