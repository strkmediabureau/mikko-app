<?php

use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

// login endpoint
Route::post('/login', [TokenController::class, 'store']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/payroll', [PayrollController::class, 'index']);

});