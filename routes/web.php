<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\CKEditorUploadController;

Route::post('/ckeditor/upload', [CKEditorUploadController::class, 'upload'])
    ->middleware(['web', 'auth'])
    ->name('ckeditor.upload');
Route::post('/filament/forms/rich-editor/attachments', EditorUploadController::class)
    ->name('filament.forms.rich-editor.attachments')
    ->middleware(['web', 'auth']); // keep auth for admin area
Route::get('/', function () {
    return view('welcome');
})->name('home');
