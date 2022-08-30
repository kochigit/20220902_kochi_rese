<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EvaluationController;

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'auth'
], function ($router) {
    Route::post('register',[AuthController::class, 'register'])->withoutMiddleware(['auth:api']);
    Route::post('login',[AuthController::class, 'login'])->withoutMiddleware(['auth:api']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh'])->withoutMiddleware(['auth:api']);
    Route::get('user', [AuthController::class, 'me']);
    Route::get('admin', [AuthController::class, 'admin']);
});


Route::group(['prefix' => 'v1'], function() {
    Route::get('test', function () {
        return response()->json(['message' => 'GOOD!'], 200);
    });
    Route::apiResource('restaurant', RestaurantController::class);
    Route::post('search-restaurant', [RestaurantController::class, 'search']);
    Route::apiResource('user', UserController::class);
    Route::apiResource('favorite', FavoriteController::class)->only(['store','destroy']);
    Route::apiResource('reservation', ReservationController::class)->only(['store', 'update','destroy']);
    Route::apiResource('evaluation', EvaluationController::class)->only('store');
});



