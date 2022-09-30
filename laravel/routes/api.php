<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\StripeController;
use App\Models\Management;

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
    Route::get('email-verify', [AuthController::class, 'sendEmailVerification']);
    Route::post('email-verify', [AuthController::class, 'verifyEmail']);
    Route::post('payment', [StripeController::class, 'pay']);
});


Route::group(['prefix' => 'v1'], function() {
    Route::apiResource('restaurant', RestaurantController::class);
    Route::post('search-restaurant', [RestaurantController::class, 'search']);
    Route::post('restaurant-img', [RestaurantController::class, 'updateImage']);
    Route::apiResource('user', UserController::class);
    Route::apiResource('favorite', FavoriteController::class)->only(['store','destroy']);
    Route::apiResource('reservation', ReservationController::class)->only(['store', 'update','destroy']);
    Route::apiResource('evaluation', EvaluationController::class)->only('store');
    
    Route::group([
        'prefix' => 'management',
        'middleware' => 'auth:api'
    ], function() {
        Route::get('manager', [ManagementController::class, 'getManager']);
        Route::get('restaurant', [ManagementController::class, 'getRestaurants']);
        Route::post('/', [ManagementController::class, 'store']);
        Route::post('check', [ManagementController::class, 'checkManagement']);
        Route::get('managedRestaurant/{restaurant}', [ManagementController::class, 'getManagedRestaurant']);
        Route::post('email', [ManagementController::class, 'sendEmails']);
        Route::get('/', [ManagementController::class, 'index']);
        Route::post('approval', [ManagementController::class, 'approve']);
        Route::post('unapproval', [ManagementController::class, 'unapprove']);
        Route::delete('/{management}', [ManagementController::class, 'destroy']);
    });
});



