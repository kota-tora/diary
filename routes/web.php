<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiaryController::class, 'index'])->name('diary.index');
Route::get('/create', [DiaryController::class, 'create'])->name('diary.create');
Route::post('/', [DiaryController::class, 'store'])->name('diary.store');
Route::get('/{diary}/edit', [DiaryController::class, 'edit'])->name('diary.edit');
Route::put('/{diary}', [DiaryController::class, 'update'])->name('diary.update');
Route::delete('/{diary}', [DiaryController::class, 'destroy'])->name('diary.destroy');
