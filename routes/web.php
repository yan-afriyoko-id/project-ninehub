<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return response()->json([
        'message' => 'AI Chat API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/chat' => 'Send message to AI',
            'GET /api/chat/history' => 'Get chat history',
            'DELETE /api/chat/clear' => 'Clear chat history',
            'GET /api/chat/conversation/{id}' => 'Get specific conversation',
            'DELETE /api/chat/conversation/{id}' => 'Delete specific conversation'
        ]
    ]);
});
// API Routes
// Route::prefix('api')->group(function () {
//     Route::post('/chat', [ChatController::class, 'send'])->name('api.chat.send');
//     Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('api.chat.history');
//     Route::delete('/chat/clear', [ChatController::class, 'clearHistory'])->name('api.chat.clear');
//     Route::get('/chat/conversation/{id}', [ChatController::class, 'getConversation'])->name('api.chat.conversation');
//     Route::delete('/chat/conversation/{id}', [ChatController::class, 'deleteConversation'])->name('api.chat.delete');
// });

