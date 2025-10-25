<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\KosFacilityController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/kos/{kosId}/facilities', [KosFacilityController::class, 'index']);
    Route::post('/kos/{kosId}/facilities', [KosFacilityController::class, 'store']);
    Route::put('/facilities/{id}', [KosFacilityController::class, 'update']);
    Route::delete('/facilities/{id}', [KosFacilityController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/kos/{kos_id}/rooms', [RoomController::class, 'index']);
    Route::post('/kos/{kos_id}/rooms', [RoomController::class, 'store']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/kos', [KosController::class, 'listForSociety']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/kos/{kosId}/reviews', [ReviewController::class, 'index']);
    Route::post('/kos/{kosId}/reviews', [ReviewController::class, 'store']);
    Route::post('/reviews/{reviewId}/reply', [ReviewController::class, 'reply']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
});
