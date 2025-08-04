<?php

use App\Http\Controllers\Api\TenantSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TenantController;

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PlanController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/', [TenantController::class, 'index']);
    Route::post('/', [TenantController::class, 'store']);
    Route::get('/statistics', [TenantController::class, 'statistics']);
    Route::get('/{id}', [TenantController::class, 'show']);
    Route::put('/{id}', [TenantController::class, 'update']);
    Route::delete('/{id}', [TenantController::class, 'destroy']);
    Route::patch('/{id}/activate', [TenantController::class, 'activate']);
    Route::patch('/{id}/suspend', [TenantController::class, 'suspend']);
});

// Plan Management Routes
Route::prefix('plans')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PlanController::class, 'index']);
    Route::post('/', [PlanController::class, 'store']);
    Route::get('/statistics', [PlanController::class, 'statistics']);
    Route::get('/active', [PlanController::class, 'active']);
    Route::get('/free', [PlanController::class, 'free']);
    Route::get('/paid', [PlanController::class, 'paid']);
    Route::get('/search', [PlanController::class, 'search']);
    Route::get('/{id}', [PlanController::class, 'show']);
    Route::put('/{id}', [PlanController::class, 'update']);
    Route::delete('/{id}', [PlanController::class, 'destroy']);
});



// Permission Management Routes
Route::prefix('permissions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PermissionController::class, 'index']);
    Route::post('/', [PermissionController::class, 'store']);
    Route::get('/statistics', [PermissionController::class, 'statistics']);
    Route::get('/guard/{guard}', [PermissionController::class, 'byGuard']);
    Route::get('/module/{moduleSlug}', [PermissionController::class, 'byModule']);
    Route::get('/search', [PermissionController::class, 'search']);
    Route::post('/sync', [PermissionController::class, 'sync']);
    Route::get('/{id}', [PermissionController::class, 'show']);
    Route::put('/{id}', [PermissionController::class, 'update']);
    Route::delete('/{id}', [PermissionController::class, 'destroy']);
});

// Role Management Routes
Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/statistics', [RoleController::class, 'statistics']);
    Route::get('/guard/{guard}', [RoleController::class, 'byGuard']);
    Route::get('/search', [RoleController::class, 'search']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::post('/{id}/remove-permissions', [RoleController::class, 'removePermissions']);
});
