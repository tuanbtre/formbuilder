<?php

use Illuminate\Support\Facades\Route;
use Tuanbtre\FormBuilder\Http\Controllers\FormController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::any('/forms', [FormController::class, 'index'])->name('form.index')->middleware('checkright');
    //Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    //Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    //Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');
});

Route::get('/', [FormController::class, 'showPublic'])->name('form-builder.home');