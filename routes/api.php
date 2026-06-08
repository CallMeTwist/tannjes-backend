<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/team', [TeamController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/{slug}', [DepartmentController::class, 'show']);

use App\Http\Controllers\Api\PatientAuthController;

Route::post('/patient/register', [PatientAuthController::class, 'register']);
Route::post('/patient/login', [PatientAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/patient/logout', [PatientAuthController::class, 'logout']);
    Route::get('/patient/me', [PatientAuthController::class, 'me']);
    Route::post('/patient/payment', [\App\Http\Controllers\Api\PatientPaymentController::class, 'store']);
});

use App\Http\Controllers\Api\ConsultationController;

Route::middleware(['auth:sanctum', 'patient.approved'])->group(function () {
    Route::get('/patient/consultations', [ConsultationController::class, 'index']);
    Route::get('/patient/consultations/{consultation}/messages', [ConsultationController::class, 'messages']);
    Route::post('/patient/consultations/{consultation}/messages', [ConsultationController::class, 'store']);
    Route::get('/patient/results', [\App\Http\Controllers\Api\TestResultController::class, 'index']);
});
