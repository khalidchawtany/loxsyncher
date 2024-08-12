<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductCheckTypeController;
use App\Http\Controllers\CheckTypeController;

// check routes

// Product check types
Route::get('/products/check-type', [ProductCheckTypeController::class, 'index']);
Route::get('/products/check-type/list', [ProductCheckTypeController::class, 'list']);
Route::get('/products/check-type/list/check-types', [ProductCheckTypeController::class, 'listCheckTypes']);
Route::post('/products/check-type/create', [ProductCheckTypeController::class, 'create']);
Route::post('/products/check-type/update', [ProductCheckTypeController::class, 'update']);
Route::post('/products/check-type/destroy', [ProductCheckTypeController::class, 'destroy']);

// CheckTypes
Route::get('/check_types', [CheckTypeController::class, 'index']);
Route::get('/check_types/list', [CheckTypeController::class, 'list']);
Route::get('/check-types/category/list', [CheckTypeController::class, 'listCategories']);
Route::post('/check_types/create', [CheckTypeController::class, 'create']);
Route::post('/check_types/update', [CheckTypeController::class, 'update']);
Route::post('/check_types/destroy', [CheckTypeController::class, 'destroy']);
