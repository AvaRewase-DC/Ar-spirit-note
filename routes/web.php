<?php

use App\Http\Controllers\DailyReadController;
use App\Http\Controllers\MassController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return to_route('daily-read.index');
});

Route::group(['prefix' => 'daily-reads'], function () {
    Route::get('/', [DailyReadController::class, 'index'])->name('daily-read.index');
    Route::get('/today', [DailyReadController::class, 'show'])->name('daily-read.today');
    Route::get('/create', [DailyReadController::class, 'create'])->name('daily-read.create');
    Route::get('/edit/{id}', [DailyReadController::class, 'edit'])->name('daily-read.edit');
    Route::put('/update/{id}', [DailyReadController::class, 'update'])->name('daily-read.update');
    Route::post('/store', [DailyReadController::class, 'store'])->name('daily-read.store');
    Route::get('/{date}', [DailyReadController::class, 'show'])->name('daily-read.show');
});

Route::group(['prefix' => 'events'], function () {
    Route::get('/', [MassController::class, 'index'])->name('mass.index');
    Route::post('/search', [MassController::class, 'search'])->name('mass.search');
    Route::get('/details', [MassController::class, 'details'])->name('mass.details');
    Route::post('/cancel', [MassController::class, 'cancelRequest'])->name('mass.cancel');
    Route::get('/policy', [MassController::class, 'policy'])->name('mass.policy');
    Route::get('/request', [MassController::class, 'createRequest'])->name('mass.request');
    Route::post('/request', [MassController::class, 'storeRequest'])->name('mass.request.store');
    Route::get('/request-done', [MassController::class, 'requestDone'])->name('mass.request.done');
});
