<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Rotas do Admin (protegidas por JWT)
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::get('/events/dashboard', [EventController::class, 'dashboard']);
    Route::get('/events/{event}/participants', [EventController::class, 'participants']);
    Route::apiResource('events', EventController::class);
});

// Rotas Públicas (Participantes)
Route::get('/events', [PublicEventController::class, 'index']);
Route::get('/events/{event}', [PublicEventController::class, 'show']);
Route::post('/events/register', [ParticipantController::class, 'store']);
Route::post('/events/register/verify', [ParticipantController::class, 'verify']);
