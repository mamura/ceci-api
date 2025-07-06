<?php

use Illuminate\Support\Facades\Route;
use Spatie\Multitenancy\Http\Middleware\NeedsTenant;
use App\Http\Controllers\AuthController;

Route::prefix('auth')
    ->group(function () {
         Route::middleware([NeedsTenant::class])->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
            //Route::get('/user', [AuthController::class, 'user']);
        });
        
        //Route::get('/user', [AuthController::class, 'user']);

        Route::middleware(['api', NeedsTenant::class, 'auth:sanctum'])->group(function () {
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });