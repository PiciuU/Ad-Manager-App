<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdStatsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:sanctum', 'check.ban']], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('user', [UserController::class, 'userData']);
        Route::put('user', [UserController::class, 'updateData']);
        Route::put('user/mail', [UserController::class, 'updateMail']);
        Route::put('user/password', [UserController::class, 'updatePassword']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::get('logout/all', [UserController::class, 'forceLogout']);
    });

    /* Only admin has access to these endpoints */
    Route::group(['prefix' => 'admin'], function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);

        Route::get('user/activationKey', [UserController::class, 'generateActivationKey']);
        Route::put('user/activationKey', [UserController::class, 'assignActivationKey']);
        Route::put('user/ban', [UserController::class, 'toggleBan']);
        Route::put('user/password', [UserController::class, 'changePassword']);

        Route::get('logs', [LogController::class, 'index']);
        Route::get('logs/{id}', [LogController::class, 'show']);
        Route::put('logs/{id}', [LogController::class, 'update']);
    });

    Route::get('ads', [AdController::class, 'index']);
    Route::post('ads', [AdController::class, 'store']);
    Route::get('ads/{id}', [AdController::class, 'show']);
    Route::put('ads/{id}', [AdController::class, 'update']);
    Route::put('ads/{id}/renew', [AdController::class, 'renew']);
    Route::get('ads/{id}/deactivate', [AdController::class, 'deactivate']);

    Route::get('ads/{id}/files', [FileController::class, 'fetch']);
    Route::post('ads/{id}/files', [FileController::class, 'upload']);
    Route::get('ads/{id}/files/{fileName}', [FileController::class, 'highlight']);
    Route::delete('ads/{id}/files/{fileName}', [FileController::class, 'delete']);

    Route::get('ad/{id}/invoices', [InvoiceController::class, 'index']);
    Route::get('ad/{id}/invoices/{invoiceId}/payment', [InvoiceController::class, 'payment']);



    // Route::put('ads/{id}', [AdController::class, 'update']);
    // Route::delete('ads/{id}', [AdController::class, 'destroy']);

    Route::get('stats', [AdStatsController::class, 'index']);
    Route::get('stats/{ad_id}/{stat_id?}', [AdStatsController::class, 'show']);
    Route::post('stats/{stat_id}', [AdStatsController::class, 'update']); //admin only
    Route::get('stats/{stat_id}/delete', [AdStatsController::class, 'delete']); //admin only

    Route::post('invoice', [InvoiceController::class, 'store']);
    Route::get('invoice/{id}', [InvoiceController::class, 'show']);
    Route::put('invoice/{id}', [InvoiceController::class, 'update']); //admin only
    Route::delete('invoice/{id}', [InvoiceController::class, 'destroy']); //admin only

    Route::get('notification', [NotificationController::class, 'index']);
    Route::post('notification', [NotificationController::class, 'store']);
    Route::get('notification/{id}', [NotificationController::class, 'show']);
    Route::get('notification/{id}/seen', [NotificationController::class, 'isSeen']);
    Route::post('notification/{id}', [NotificationController::class, 'update']);
    Route::get('notification/{id}/delete', [NotificationController::class, 'delete']);
});


Route::group(['prefix' => 'auth'], function () {
    Route::group(['prefix' => 'validate'], function () {
        Route::put('key', [UserController::class, 'validateAuthenticationKey']);
        Route::put('login', [UserController::class, 'validateLogin']);
        Route::put('email', [UserController::class, 'validateEmail']);
    });

    Route::put('activate', [UserController::class, 'activateAccount']);
    Route::post('login', [UserController::class, 'login']);
    Route::get('recover/{hash}', [UserController::class, 'recoverToken']);
    Route::post('recover', [UserController::class, 'recover']);
    Route::post('reset', [UserController::class, 'resetPassword']);
});

Route::fallback(function () {
    return response()->json([
        'message' => "Ups, it seems like you tried to access a route that doesn't exist!"
    ], 404);
});
