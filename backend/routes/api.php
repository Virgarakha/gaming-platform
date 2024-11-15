<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\UserProfileController;

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



Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('/auth/signin', [AuthController::class, 'signin']);
    Route::post('/auth/signup', [AuthController::class, 'signup']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/signout', [AuthController::class, 'signout']);
        Route::get('/admins', [UserController::class, 'getAdmins']);
        Route::resource('users', UserController::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::get('/games/{slug}', [GameController::class, 'show']);
    Route::put('/games/{slug}', [GameController::class, 'update']);
    Route::delete('/games/{slug}', [GameController::class, 'destroy']);
    Route::post('/games/{slug}/upload', [GameController::class, 'upload']);
    Route::get('/games/{slug}/scores', [GameController::class, 'getScores']);
    Route::post('/games/{slug}/scores', [GameController::class, 'storeScore']);
    
    // User profile
    Route::get('/users/{username}', [UserProfileController::class, 'show']);
});