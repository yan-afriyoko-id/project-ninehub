<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 
