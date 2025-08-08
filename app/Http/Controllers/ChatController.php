 <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    private $chatFile = 'chat_history.json';

    // API endpoint untuk mengirim pesan
    public function send(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000'
    ]);

    try {
        $apiKey = env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'API key not configured'
            ], 500);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->message]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'error' => 'API Error: ' . $response->json()['error']['message'] ?? 'Unknown error'
            ], 500);
        }

        $data = $response->json();

        $aiResponse = $data['choices'][0]['message']['content'] ?? 'No response';

        // Save chat to storage
        $this->saveChat($request->message, $aiResponse);

        return response()->json([
            'success' => true,
            'data' => [
                'response' => $aiResponse,
                'timestamp' => now()->toISOString(),
                'message_id' => uniqid()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Exception in send(): ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage()
        ], 500);
    }
}

    // Get chat history
    public function getHistory()
    {
        try {
            $history = $this->loadChatHistory();
            return response()->json([
                'success' => true,
                'data' => [
                    'history' => $history,
                    'total_conversations' => count($history)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Clear chat history
    public function clearHistory()
    {
        try {
            if (Storage::exists($this->chatFile)) {
                Storage::delete($this->chatFile);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get specific conversation by ID
    public function getConversation($id)
    {
        try {
            $history = $this->loadChatHistory();
            $conversation = collect($history)->firstWhere('id', $id);
            
            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Conversation not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete specific conversation
    public function deleteConversation($id)
    {
        try {
            $history = $this->loadChatHistory();
            $filteredHistory = collect($history)->filter(function($item) use ($id) {
                return $item['id'] !== $id;
            })->values()->toArray();

            Storage::put($this->chatFile, json_encode($filteredHistory, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Conversation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Test endpoint untuk cek konfigurasi
    public function test()
    {
        try {
            $apiKey = config('openai.api_key');
            return response()->json([
                'success' => true,
                'data' => [
                    'api_key_configured' => !empty($apiKey),
                    'api_key_length' => strlen($apiKey),
                    'storage_writable' => Storage::disk('local')->exists('.'),
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function saveChat($userMessage, $aiResponse)
    {
        try {
            $history = $this->loadChatHistory();
            
            $chatEntry = [
                'id' => uniqid(),
                'user_message' => $userMessage,
                'ai_response' => $aiResponse,
                'timestamp' => now()->toISOString(),
                'created_at' => now()->format('Y-m-d H:i:s')
            ];

            $history[] = $chatEntry;

            // Keep only last 100 conversations to prevent file from getting too large
            if (count($history) > 100) {
                $history = array_slice($history, -100);
            }

            Storage::put($this->chatFile, json_encode($history, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            Log::error('Error saving chat: ' . $e->getMessage());
        }
    }

    private function loadChatHistory()
    {
        try {
            if (Storage::exists($this->chatFile)) {
                $content = Storage::get($this->chatFile);
                return json_decode($content, true) ?: [];
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Error loading chat history: ' . $e->getMessage());
            return [];
        }
    }
}
