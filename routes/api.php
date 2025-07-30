<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OpenAIController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/openai/chat', [OpenAIController::class, 'chat']);

Route::middleware('auth:sanctum')->group(function () {
});
