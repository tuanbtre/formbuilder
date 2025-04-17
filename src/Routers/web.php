<?php

use Illuminate\Support\Facades\Route;
use Tuanbtre\FormBuilder\Http\Controllers\FormController;

Route::prefix('form-builder')->name('form-builder.')->group(function () {
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');
});

Route::get('/', [FormController::class, 'showPublic'])->name('form-builder.home');