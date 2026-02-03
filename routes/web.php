<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EditorUploadController;

Route::post('/filament/forms/rich-editor/attachments', EditorUploadController::class)
    ->name('filament.forms.rich-editor.attachments')
    ->middleware(['web', 'auth']); // keep auth for admin area
Route::get('/', function () {
    return view('welcome');
})->name('home');
