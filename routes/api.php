<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('test', function () {
    return response()->json(['message' => 'Hello World!']);
})->withoutMiddleware([JwtMiddleware::class]);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::apiResource('user', UserController::class);

});
