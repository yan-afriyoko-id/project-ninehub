<?php

use App\Http\Controllers\Api\TenantSettingController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\OpenAIController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// AI Chat API Routes
Route::post('/chat', [ChatController::class, 'send'])->name('api.chat.send');
Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('api.chat.history');
Route::delete('/chat/clear', [ChatController::class, 'clearHistory'])->name('api.chat.clear');
Route::get('/chat/conversation/{id}', [ChatController::class, 'getConversation'])->name('api.chat.conversation');
Route::delete('/chat/conversation/{id}', [ChatController::class, 'deleteConversation'])->name('api.chat.delete');
Route::get('/chat/test', [ChatController::class, 'test'])->name('api.chat.test');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('profile', ProfileController::class);
    Route::apiResource('contact', ContactController::class);
    Route::apiResource('company', CompanyController::class);
    Route::apiResource('lead', LeadController::class);
    Route::get('/settings', [TenantSettingController::class, 'show']);
    Route::put('/settings', [TenantSettingController::class, 'update']);

    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
 });

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

// Plan Management Routes
Route::prefix('plans')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PlanController::class, 'index'])->middleware('module.permission:plan-management.view');
    Route::post('/', [PlanController::class, 'store'])->middleware('module.permission:plan-management.create');
    Route::get('/statistics', [PlanController::class, 'statistics'])->middleware('module.permission:plan-management.view');
    Route::get('/active', [PlanController::class, 'active'])->middleware('module.permission:plan-management.view');
    Route::get('/free', [PlanController::class, 'free'])->middleware('module.permission:plan-management.view');
    Route::get('/paid', [PlanController::class, 'paid'])->middleware('module.permission:plan-management.view');
    Route::get('/search', [PlanController::class, 'search'])->middleware('module.permission:plan-management.view');
    Route::get('/{id}', [PlanController::class, 'show'])->middleware('module.permission:plan-management.view');
    Route::put('/{id}', [PlanController::class, 'update'])->middleware('module.permission:plan-management.edit');
    Route::delete('/{id}', [PlanController::class, 'destroy'])->middleware('module.permission:plan-management.delete');
});

// Module Management Routes
Route::prefix('modules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ModuleController::class, 'index']);
    Route::post('/', [ModuleController::class, 'store'])->middleware('module.permission:plan-management.create');
    Route::get('/{module}', [ModuleController::class, 'show'])->middleware('module.permission:plan-management.view');
    Route::put('/{module}', [ModuleController::class, 'update'])->middleware('module.permission:plan-management.edit');
    Route::delete('/{module}', [ModuleController::class, 'destroy'])->middleware('module.permission:plan-management.delete');
});

// Permission Management Routes
Route::prefix('permissions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->middleware('module.permission:permission-management.view');
    Route::post('/', [PermissionController::class, 'store'])->middleware('module.permission:permission-management.create');
    Route::get('/statistics', [PermissionController::class, 'statistics'])->middleware('module.permission:permission-management.view');
    Route::get('/guard/{guard}', [PermissionController::class, 'byGuard'])->middleware('module.permission:permission-management.view');
    Route::get('/module/{moduleSlug}', [PermissionController::class, 'byModule'])->middleware('module.permission:permission-management.view');
    Route::get('/search', [PermissionController::class, 'search'])->middleware('module.permission:permission-management.view');
    Route::post('/sync', [PermissionController::class, 'sync'])->middleware('module.permission:permission-management.create');
    Route::get('/{id}', [PermissionController::class, 'show'])->middleware('module.permission:permission-management.view');
    Route::put('/{id}', [PermissionController::class, 'update'])->middleware('module.permission:permission-management.edit');
    Route::delete('/{id}', [PermissionController::class, 'destroy'])->middleware('module.permission:permission-management.delete');
});

// Role Management Routes
Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->middleware('module.permission:role-management.view');
    Route::post('/', [RoleController::class, 'store'])->middleware('module.permission:role-management.create');
    Route::get('/statistics', [RoleController::class, 'statistics'])->middleware('module.permission:role-management.view');
    Route::get('/guard/{guard}', [RoleController::class, 'byGuard'])->middleware('module.permission:role-management.view');
    Route::get('/search', [RoleController::class, 'search'])->middleware('module.permission:role-management.view');
    Route::get('/{id}', [RoleController::class, 'show'])->middleware('module.permission:role-management.view');
    Route::put('/{id}', [RoleController::class, 'update'])->middleware('module.permission:role-management.edit');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('module.permission:role-management.delete');
    Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->middleware('module.permission:role-management.edit');
    Route::post('/{id}/remove-permissions', [RoleController::class, 'removePermissions'])->middleware('module.permission:role-management.edit');
});
