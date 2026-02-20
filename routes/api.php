<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoursController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IAController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProgressionController;

Route::get('/niveaux', [CoursController::class, 'getNiveaux']);
Route::get('/matieres', [CoursController::class, 'getMatieres']);
Route::get('/chapitres', [CoursController::class, 'getChapitres']);
Route::get('/lecons/{id}', [CoursController::class, 'getLecon']);
Route::get('/lecons/{id}/exercices', [CoursController::class, 'getExercicesLecon']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/ia/question', [IAController::class, 'poserQuestion']);
    Route::post('/ia/feedback', [IAController::class, 'feedbackExercice']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/progression', [ProgressionController::class, 'index']);
});
