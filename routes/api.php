<?php

use App\Http\Controllers\Api\CopticDayController;
use App\Http\Controllers\Api\MassApiController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\DailyReadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'coptic-date'], function () {
    Route::get('/', [CopticDayController::class, 'getCopticDate'])->name('api.coptic-date');
});

Route::group(['prefix' => 'qr-code'], function () {
    Route::get('/', [QrCodeController::class, 'generateQrCode'])->name('api.qr-code');
});

Route::group(['prefix' => 'daily-reads'], function () {
    Route::get('/', [DailyReadController::class, 'index'])->name('api.daily-read.index');
    Route::get('/{date}', [DailyReadController::class, 'show'])->name('api.daily-read.show');
});

Route::group(['prefix' => 'mass'], function () {
    Route::get('/settings', [MassApiController::class, 'settings'])->name('api.mass.settings');
    Route::get('/mass-appointments/get-active-appointments', [MassApiController::class, 'activeAppointments'])->name('api.mass.active-appointments');
    Route::get('/requests/search-by-membership/{membershipID}', [MassApiController::class, 'requestsByMembership'])->name('api.mass.requests.by-membership');
    Route::post('/requests', [MassApiController::class, 'storeRequest'])->name('api.mass.requests.store');
    Route::post('/requests/cancel-request-by-user/{requestId}/{nationalId}', [MassApiController::class, 'cancelRequestByUser'])->name('api.mass.requests.cancel');
});
