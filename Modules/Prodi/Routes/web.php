<?php

use Illuminate\Support\Facades\Route;
use Modules\Prodi\Http\Controllers\ProdiController;

Route::middleware('web')->group(function () {
    Route::get('/prodi/{prodi}/hapus', [ProdiController::class, 'confirmDelete'])->name('prodi.delete');
    Route::resource('prodi', ProdiController::class)
        ->except('show')
        ->parameters(['prodi' => 'prodi']);
});
