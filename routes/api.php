<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/team', [TeamController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/{slug}', [DepartmentController::class, 'show']);
