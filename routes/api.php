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


Route::middleware(['auth:sanctum','query_tracing'])->group( function () {
    Route::delete('logout',[\App\Http\Controllers\Api\AuthController::class,'logout'])->name('logout');
    Route::get('users',[\App\Http\Controllers\Api\UserController::class,'index'])->name('user.index');
    Route::get('feeds',[\App\Http\Controllers\Api\FeedController::class,'index'])->name('feeds');
});

Route::middleware(['query_tracing'])->group( function () {
    Route::get('user/{token}',[\App\Http\Controllers\Api\AuthController::class,'verifyToken'])->name('verify-token');
    Route::post('register',[\App\Http\Controllers\Api\AuthController::class,'register'])->name('register');
    Route::post('login',[\App\Http\Controllers\Api\AuthController::class,'login'])->name('login');

});




