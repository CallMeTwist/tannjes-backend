<?php

use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/team', [TeamController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
