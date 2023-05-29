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
    Route::apiResource('Users', UsersController::class);
    Route::apiResource('Users_roles', UsersRolesController::class);
    Route::apiResource('Ads', AdsController::class);
    Route::apiResource('Ads_stats', AdsStatsControllerController::class);
    Route::apiResource('Notifications', NotificationsController::class);
    Route::apiResource('Invoices', InvoicesController::class);
    Route::apiResource('Logs', LogsController::class);
});


Route::fallback(function () {
    return response()->json([
        'message' => "Ups, it seems like you tried to access a route that doesn't exist!"
    ], 404);
});
