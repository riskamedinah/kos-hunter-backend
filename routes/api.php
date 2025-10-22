<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KosController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/kos', [KosController::class, 'index']);
    Route::get('/kos/{id}', [KosController::class, 'show']);
    Route::post('/kos', [KosController::class, 'store']);
    Route::put('/kos/{id}', [KosController::class, 'update']);
    Route::delete('/kos/{id}', [KosController::class, 'destroy']);
});
