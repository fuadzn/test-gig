<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTController;
use App\Http\Middleware\JwtMiddleware;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [JWTController::class, 'register'])->name('register');
    Route::post('/login', [JWTController::class, 'login'])->name('login');
    Route::post('/logout', [JWTController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [JWTController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [JWTController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'companies'
], function ($router) {
    Route::post('/create', [CompanyController::class, 'create'])->middleware('auth:api')->name('create');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'employee'
], function ($router) {
    Route::post('/create', [EmployeeController::class, 'create'])->middleware('auth:api')->name('create');
    Route::post('/index', [EmployeeController::class, 'index'])->middleware('auth:api')->name('index');
    Route::put('/{id}', [EmployeeController::class, 'update'])->middleware('auth:api')->name('update');
    Route::delete('/{id}', [EmployeeController::class, 'delete'])->middleware('auth:api')->name('delete');
    Route::get('/{id}', [EmployeeController::class, 'show'])->middleware('auth:api')->name('show');
});