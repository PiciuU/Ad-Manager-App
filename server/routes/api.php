<?php

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

Route::group(['prefix' => 'v1', 'namespace' => 'app\Http\Controllers'], function () {
    Route::apiResource('user', UserController::class);
    Route::apiResource('ad', AdController::class);
    Route::apiResource('ad.stats', AdStatsControllerController::class); //ad.stats przesłanie parametru
    Route::apiResource('notification', NotificationController::class);
    Route::apiResource('invoice', InvoiceController::class);
    Route::apiResource('log', LogController::class);
});


Route::fallback(function () {
    return response()->json([
        'message' => "Ups, it seems like you tried to access a route that doesn't exist!"
    ], 404);
});
