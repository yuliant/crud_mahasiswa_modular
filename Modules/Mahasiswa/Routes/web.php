<?php

use Illuminate\Support\Facades\Route;
use Modules\Mahasiswa\Http\Controllers\MahasiswaController;

Route::middleware('web')->group(function () {
    Route::get('/mahasiswa/{mahasiswa}/hapus', [MahasiswaController::class, 'confirmDelete'])->name('mahasiswa.delete');
    Route::resource('mahasiswa', MahasiswaController::class)
        ->except('show')
        ->parameters(['mahasiswa' => 'mahasiswa']);
});
