<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiaryController::class, 'index'])->name('diary.index');
Route::get('/create', [DiaryController::class, 'create'])->name('diary.create');
Route::post('/store', [DiaryController::class, 'store'])->name('diary.store');
