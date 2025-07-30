# AI Chat API Documentation

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Send Message to AI
**POST** `/api/chat`

**Request Body:**
```json
{
    "message": "Halo, bagaimana kabarmu?"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "response": "Halo! Kabar saya baik, terima kasih sudah bertanya. Bagaimana dengan kabar Anda?",
        "timestamp": "2024-01-15T10:30:00.000000Z",
        "message_id": "507f1f77bcf86cd799439011"
    }
}
```

### 2. Get Chat History
**GET** `/api/chat/history`

**Response:**
```json
{
    "success": true,
    "data": {
        "history": [
            {
                "id": "507f1f77bcf86cd799439011",
                "user_message": "Halo, bagaimana kabarmu?",
                "ai_response": "Halo! Kabar saya baik, terima kasih sudah bertanya.",
                "timestamp": "2024-01-15T10:30:00.000000Z",
                "created_at": "2024-01-15 10:30:00"
            }
        ],
        "total_conversations": 1
    }
}
```

### 3. Clear Chat History
**DELETE** `/api/chat/clear`

**Response:**
```json
{
    "success": true,
    "message": "Chat history cleared successfully"
}
```

### 4. Get Specific Conversation
**GET** `/api/chat/conversation/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": "507f1f77bcf86cd799439011",
        "user_message": "Halo, bagaimana kabarmu?",
        "ai_response": "Halo! Kabar saya baik, terima kasih sudah bertanya.",
        "timestamp": "2024-01-15T10:30:00.000000Z",
        "created_at": "2024-01-15 10:30:00"
    }
}
```

### 5. Delete Specific Conversation
**DELETE** `/api/chat/conversation/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Conversation deleted successfully"
}
```

## Error Responses

### Validation Error
```json
{
    "success": false,
    "error": "The message field is required."
}
```

### Server Error
```json
{
    "success": false,
    "error": "Error message here"
}
```

### Not Found Error
```json
{
    "success": false,
    "error": "Conversation not found"
}
```

## Testing with cURL

### Send Message
```bash
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "Halo, bagaimana kabarmu?"}'
```

### Get History
```bash
curl -X GET http://localhost:8000/api/chat/history
```

### Clear History
```bash
curl -X DELETE http://localhost:8000/api/chat/clear
```

## Features

- ✅ Full API endpoints
- ✅ Chat history stored in storage
- ✅ Automatic cleanup (max 100 conversations)
- ✅ Error handling
- ✅ JSON responses
- ✅ Conversation management (get, delete specific conversations)

## Storage

Chat history disimpan di file `storage/app/chat_history.json` dan akan otomatis dibersihkan jika melebihi 100 percakapan. 