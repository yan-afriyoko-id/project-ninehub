<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ModuleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Tenant Management Routes
Route::prefix('tenants')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TenantController::class, 'index'])->middleware('module.permission:tenant-management.view');
    Route::post('/', [TenantController::class, 'store'])->middleware('module.permission:tenant-management.create');
    Route::get('/statistics', [TenantController::class, 'statistics'])->middleware('module.permission:tenant-management.view');
    Route::get('/{id}', [TenantController::class, 'show'])->middleware('module.permission:tenant-management.view');
    Route::put('/{id}', [TenantController::class, 'update'])->middleware('module.permission:tenant-management.edit');
    Route::delete('/{id}', [TenantController::class, 'destroy'])->middleware('module.permission:tenant-management.delete');
    Route::patch('/{id}/activate', [TenantController::class, 'activate'])->middleware('module.permission:tenant-management.edit');
    Route::patch('/{id}/suspend', [TenantController::class, 'suspend'])->middleware('module.permission:tenant-management.edit');
});

// Module Management Routes
Route::prefix('modules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ModuleController::class, 'index']);
    Route::post('/', [ModuleController::class, 'store'])->middleware('module.permission:plan-management.create');
    Route::get('/{module}', [ModuleController::class, 'show'])->middleware('module.permission:plan-management.view');
    Route::put('/{module}', [ModuleController::class, 'update'])->middleware('module.permission:plan-management.edit');
    Route::delete('/{module}', [ModuleController::class, 'destroy'])->middleware('module.permission:plan-management.delete');
});
