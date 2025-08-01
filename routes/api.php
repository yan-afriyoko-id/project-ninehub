<?php

use App\Http\Controllers\Api\TenantSettingController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OpenAIController;

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
