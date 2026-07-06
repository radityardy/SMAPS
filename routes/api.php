<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\QueueController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public queue endpoints
Route::get('/display', [QueueController::class, 'display']);
Route::get('/check-queue', [QueueController::class, 'check']);
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Queues
    Route::get('/queues', [QueueController::class, 'index']);
    Route::post('/queues', [QueueController::class, 'store']);
    Route::get('/queues/{queue}', [QueueController::class, 'show']);
    Route::post('/queues/{queue}/call', [QueueController::class, 'call']);
    Route::post('/queues/{queue}/serve', [QueueController::class, 'serve']);
    Route::post('/queues/{queue}/complete', [QueueController::class, 'complete']);
    Route::post('/queues/{queue}/skip', [QueueController::class, 'skip']);

    // Doctor queue management
    Route::post('/doctors/{doctor}/call-next', [QueueController::class, 'callNext']);
    Route::get('/doctors/{doctor}/summary', [DoctorController::class, 'summary']);

    // Admin doctor CRUD
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);
});
