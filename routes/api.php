<?php

use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/team', [TeamController::class, 'index']);
