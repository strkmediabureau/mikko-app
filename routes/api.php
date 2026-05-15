<?php

use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

// login endpoint
Route::post('/login', [TokenController::class, 'store']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/payroll/exporter', [PayrollController::class, 'exporter']);
    Route::get('/payroll/exporter/{year}', [PayrollController::class, 'exporter']);
    Route::get('/payroll', [PayrollController::class, 'index']);
    Route::get('/payroll/{year}', [PayrollController::class, 'index']);
});
