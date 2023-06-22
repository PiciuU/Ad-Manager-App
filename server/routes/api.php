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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:sanctum', 'check.ban']], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('user', [UserController::class, 'userData']);
        Route::put('user', [UserController::class, 'updateData']);
        Route::put('user/mail', [UserController::class, 'updateMail']);
        Route::put('user/password', [UserController::class, 'updatePassword']);
        Route::get('logout', [UserController::class, 'logout']);
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


    Route::get('ads/{id}', [AdController::class, 'show']);
    Route::get('ads', [AdController::class, 'index']);
    Route::post('ads', [AdController::class, 'store']);
    Route::put('ads/{id}', [AdController::class, 'update']); //lkjlkjlkjlkjlkjlkj
    Route::delete('ads/{id}', [AdController::class, 'destroy']);

    Route::get('stats', [AdStatsController::class, 'index']);
    Route::get('stats/{ad_id}/{stat_id?}', [AdStatsController::class, 'show']);
    Route::post('stats/{stat_id}', [AdStatsController::class, 'update']); //admin only
    Route::get('stats/{stat_id}/delete', [AdStatsController::class, 'delete']); //admin only

    Route::get('invoice', [InvoiceController::class, 'index']);
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

    // Route::apiResource('ads', AdsController::class);
    // Route::apiResource('adStats', AdStatsController::class);
    // Route::apiResource('invoice', InvoiceController::class);
    // Route::apiResource('log', LogController::class);
    // Route::apiResource('notification', NotificationController::class);
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

Route::get('/setup', function () {
    $credentials = [
        'user_role_id' => 2,
        'login' => 'admin',
        'name' => 'Admin',
        'email' => 'admin@test.pl',
        'password' => 'Piciu103'
    ];

    if (!Auth::attempt($credentials)) {
        $user = new User();

        $user->user_role_id = $credentials['user_role_id'];
        $user->login = $credentials['login'];
        $user->name = $credentials['name'];
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);
        $user->save();

        $cred = [
            'email' => 'admin@test.pl',
            'password' => 'Piciu103'
        ];

        if (Auth::attempt($cred)) {
            $user = Auth::user();

            $adminToken = $user->createToken('admin-token', ['all']);
            $verifiedToken = $user->createToken('verified-token', ['advanced']);
            $basicToken = $user->createToken('basic-token', ['basic']);

            return [
                'admin' => $adminToken->plainTextToken,
                'verified' => $verifiedToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ];
        }
    }
});

Route::fallback(function () {
    return response()->json([
        'message' => "Ups, it seems like you tried to access a route that doesn't exist!"
    ], 404);
});
