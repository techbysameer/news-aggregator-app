<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\UserPreferenceController;

// Public routes
Route::group([], function () { // Add an empty array here
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    //Article Routes
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);

    //User Preferences
    Route::prefix('user')->group(function () {
        Route::post('preferences', [UserPreferenceController::class, 'setPreferences']);
        Route::get('preferences', [UserPreferenceController::class, 'getPreferences']);
        Route::get('personalized-feed', [UserPreferenceController::class, 'getPersonalizedFeed']);
    });
});