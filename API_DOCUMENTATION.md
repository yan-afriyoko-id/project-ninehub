# API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication Endpoints

### 1. Register User

**POST** `/api/register`

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "company": "My Company",
    "domain": "mycompany.com"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Registration successful.",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "token": "1|abc123...",
        "roles": ["user"],
        "permissions": ["read-profile"],
        "tenant": {
            "id": "uuid",
            "company": "My Company",
            "domains": ["mycompany.com"]
        }
    }
}
```

### 2. Login User

**POST** `/api/login`

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "token": "1|abc123...",
        "roles": ["user"],
        "permissions": ["read-profile"],
        "tenant": {
            "id": "uuid",
            "company": "My Company",
            "domains": ["mycompany.com"]
        }
    }
}
```

### 3. Get User Profile

**GET** `/api/profile`

**Headers:**

```
Authorization: Bearer 1|abc123...
```

**Response:**

```json
{
    "success": true,
    "message": "User profile retrieved successfully.",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["user"],
        "permissions": ["read-profile"],
        "profile": {
            "id": 1,
            "name": "John Doe",
            "age": 30,
            "gender": "male",
            "phone_number": "+1234567890",
            "address": "123 Main St",
            "birth_date": "1993-01-01",
            "user_id": 1
        },
        "tenant": {
            "id": "uuid",
            "company": "My Company",
            "domains": ["mycompany.com"]
        }
    }
}
```

### 4. Logout User

**POST** `/api/logout`

**Headers:**

```
Authorization: Bearer 1|abc123...
```

**Response:**

```json
{
    "success": true,
    "message": "Logout successful."
}
```

## AI Chat API Documentation

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

### Register User

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "company": "My Company",
    "domain": "mycompany.com"
  }'
```

### Login User

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get User Profile

```bash
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer 1|abc123..."
```

### Logout User

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer 1|abc123..."
```

## AI Chat Testing with cURL

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

-   ✅ Full API endpoints
-   ✅ Chat history stored in storage
-   ✅ Automatic cleanup (max 100 conversations)
-   ✅ Error handling
-   ✅ JSON responses
-   ✅ Conversation management (get, delete specific conversations)

## Storage

Chat history disimpan di file `storage/app/chat_history.json` dan akan otomatis dibersihkan jika melebihi 100 percakapan.
