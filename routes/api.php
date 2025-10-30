<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\KosFacilityController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookingController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']);
    Route::get('/kos/{kosId}/reviews', [ReviewController::class, 'index']);

    // ================= OWNER =================
    Route::middleware('role:owner')->group(function () {
        // Kos
        Route::get('/kos', [KosController::class, 'index']);
        Route::get('/kos/{id}', [KosController::class, 'show']);
        Route::post('/kos', [KosController::class, 'store']);
        Route::put('/kos/{id}', [KosController::class, 'update']);
        Route::delete('/kos/{id}', [KosController::class, 'destroy']);

        // Facilities
        Route::get('/kos/{kosId}/facilities', [KosFacilityController::class, 'index']);
        Route::post('/kos/{kosId}/facilities', [KosFacilityController::class, 'store']);
        Route::put('/facilities/{id}', [KosFacilityController::class, 'update']);
        Route::delete('/facilities/{id}', [KosFacilityController::class, 'destroy']);

        // Reviews (balasan)
        Route::post('/reviews/{reviewId}/reply', [ReviewController::class, 'reply']);

        // Bookings (kelola)
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
        Route::get('/bookings/history', [BookingController::class, 'history']);
    });

    // ================= SOCIETY =================
    Route::middleware('role:society')->group(function () {
        Route::get('/society/kos', [KosController::class, 'listForSociety']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{id}/print', [BookingController::class, 'print']);
        Route::post('/kos/{kosId}/reviews', [ReviewController::class, 'store']);
    });
});
