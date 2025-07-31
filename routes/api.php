<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OpenAIController;
use App\Http\Controllers\ChatController;

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




    Route::get('/user/profile', [ProfileController::class, 'getProfile']);

});
