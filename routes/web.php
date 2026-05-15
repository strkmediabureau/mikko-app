<?php

use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('payroll', [
            'token' => config('services.payroll_api.token')
        ]);
    });
