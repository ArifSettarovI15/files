<?php

namespace App\Modules\Files\Routes;


use Illuminate\Support\Facades\Route;
use App\Modules\Files\Controllers\FilesController;


Route::prefix('files')->namespace('App\Modules\Files\Controllers')->group(function(){
    Route::post('add', [FilesController::class, 'add'])->name('files.add');
});
