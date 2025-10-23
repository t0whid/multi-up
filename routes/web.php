<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FichierController;

Route::get('/', function() { return view('upload'); });
Route::post('/api/upload', [FichierController::class, 'upload'])->name('upload');
