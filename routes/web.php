<?php

use App\Http\Controllers\DailyReadController;
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
    Route::get('/show', [DailyReadController::class, 'show'])->name('daily-read.show');
});
