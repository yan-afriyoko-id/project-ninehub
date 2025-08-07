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

### 3. Login SSO

**POST** `/api/login/sso`

**Request Body:**

```json
{
    "token": "1|abc123def456ghi789...",
    "email": "user@example.com"
}
```

**Parameters:**
- `token` (required): Token Sanctum yang sudah ada dari login biasa
- `email` (required): Email user yang sudah terdaftar

**Response:**

```json
{
    "success": true,
    "message": "SSO login successful.",
    "data": {
        "id": 1,
        "name": "SSO User",
        "email": "user@example.com",
        "token": "1|sso-token-abc123...",
        "roles": ["user"],
        "permissions": ["read-profile"],
        "tenant": {
            "id": "uuid",
            "company": "Default Company",
            "domains": ["default.com"]
        }
    }
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Invalid SSO token or user not found."
}
```

### 4. Get User Profile

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

## Settings API

### Get All Settings

```http
GET /api/settings
```

**Query Parameters:**

-   `search` (optional): Search in key, value, or description
-   `group` (optional): Filter by group
-   `type` (optional): Filter by type (string, boolean, integer, float, array, json)
-   `is_public` (optional): Filter by public/private (true/false)
-   `user_id` (optional): Filter by user ID
-   `per_page` (optional): Number of items per page (default: 15)

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "key": "site_name",
            "value": "NineHub",
            "typed_value": "NineHub",
            "type": "string",
            "group": "appearance",
            "description": "Website name",
            "is_public": true,
            "user_id": null,
            "user": null,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

### Create Setting

```http
POST /api/settings
```

**Request Body:**

```json
{
    "key": "maintenance_mode",
    "value": true,
    "type": "boolean",
    "group": "system",
    "description": "Enable maintenance mode",
    "is_public": false,
    "user_id": 1
}
```

**Response:**

```json
{
    "success": true,
    "message": "Setting created successfully",
    "data": {
        "id": 2,
        "key": "maintenance_mode",
        "value": true,
        "typed_value": true,
        "type": "boolean",
        "group": "system",
        "description": "Enable maintenance mode",
        "is_public": false,
        "user_id": 1,
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

### Get Setting by ID

```http
GET /api/settings/{id}
```

### Update Setting

```http
PUT /api/settings/{id}
```

### Delete Setting

```http
DELETE /api/settings/{id}
```

### Get Settings by Group

```http
GET /api/settings/group/{group}
```

**Example:**

```http
GET /api/settings/group/appearance
```

### Get Setting by Key

```http
GET /api/settings/key/{key}
```

**Example:**

```http
GET /api/settings/key/site_name
```

### Get Settings by User

```http
GET /api/settings/user/{userId}
```

**Example:**

```http
GET /api/settings/user/1
```

### Get Settings by Type

```http
GET /api/settings/type/{type}
```

**Example:**

```http
GET /api/settings/type/boolean
```

### Get Public Settings

```http
GET /api/settings/public
```

### Get Private Settings

```http
GET /api/settings/private
```

### Search Settings

```http
GET /api/settings/search?q={query}
```

**Example:**

```http
GET /api/settings/search?q=maintenance
```

### Get Setting Statistics

```http
GET /api/settings/statistics
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total": 150,
        "public": 45,
        "private": 105,
        "by_group": {
            "appearance": 30,
            "notifications": 25,
            "security": 20,
            "performance": 15,
            "other": 60
        },
        "by_type": {
            "string": 80,
            "boolean": 20,
            "integer": 15,
            "float": 10,
            "array": 15,
            "json": 10
        }
    },
    "message": "Setting statistics retrieved successfully"
}
```

### Get Setting Value by Key (Simplified)

```http
GET /api/settings/value/{key}
```

**Example:**

```http
GET /api/settings/value/site_name
```

**Response:**

```json
{
    "success": true,
    "data": {
        "key": "site_name",
        "value": "NineHub",
        "type": "string"
    },
    "message": "Setting value retrieved successfully"
}
```

### Set Setting Value by Key

```http
POST /api/settings/value/{key}
```

**Request Body:**

```json
{
    "value": "New Site Name",
    "type": "string",
    "group": "appearance",
    "description": "Updated site name",
    "is_public": true,
    "user_id": 1
}
```

## Setting Types

The system supports the following setting types:

-   **string**: Text values
-   **boolean**: True/false values
-   **integer**: Whole numbers
-   **float**: Decimal numbers
-   **array**: Array values (stored as JSON)
-   **json**: Complex JSON objects

## Setting Groups

Common setting groups:

-   **appearance**: UI/UX settings
-   **system**: System configuration
-   **notifications**: Notification settings
-   **security**: Security settings
-   **performance**: Performance settings
-   **email**: Email configuration
-   **payment**: Payment settings

## cURL Examples

### Create a Boolean Setting

```bash
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "maintenance_mode",
    "value": true,
    "type": "boolean",
    "group": "system",
    "description": "Enable maintenance mode",
    "is_public": false
  }'
```

### Get Settings by Group

```bash
curl -X GET http://localhost:8000/api/settings/group/appearance \
  -H "Authorization: Bearer {token}"
```

### Search Settings

```bash
curl -X GET "http://localhost:8000/api/settings/search?q=maintenance" \
  -H "Authorization: Bearer {token}"
```

### Set Setting Value

```bash
curl -X POST http://localhost:8000/api/settings/value/site_name \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "value": "Updated Site Name",
    "type": "string",
    "group": "appearance"
  }'
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

## Practical Examples

### 1. Application Configuration

```bash
# Set site configuration
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "site_name",
    "value": "NineHub",
    "type": "string",
    "group": "appearance",
    "description": "Website name",
    "is_public": true
  }'

# Set maintenance mode
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "maintenance_mode",
    "value": false,
    "type": "boolean",
    "group": "system",
    "description": "Enable maintenance mode",
    "is_public": true
  }'

# Set upload limits
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "max_upload_size",
    "value": 10,
    "type": "integer",
    "group": "files",
    "description": "Maximum file upload size in MB",
    "is_public": true
  }'
```

### 2. User Preferences

```bash
# Set user theme preference
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "user_theme",
    "value": "dark",
    "type": "string",
    "group": "preferences",
    "description": "User theme preference",
    "is_public": false,
    "user_id": 1
  }'

# Set notification settings
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "email_notifications",
    "value": true,
    "type": "boolean",
    "group": "notifications",
    "description": "Enable email notifications",
    "is_public": false,
    "user_id": 1
  }'
```

### 3. Complex Settings (JSON)

```bash
# Set email configuration
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "email_config",
    "value": {
      "smtp_host": "smtp.gmail.com",
      "smtp_port": 587,
      "encryption": "tls",
      "username": "noreply@ninehub.com",
      "password": "encrypted_password"
    },
    "type": "json",
    "group": "email",
    "description": "Email server configuration",
    "is_public": false
  }'

# Set payment gateway settings
curl -X POST http://localhost:8000/api/settings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "key": "payment_gateway",
    "value": {
      "provider": "stripe",
      "public_key": "pk_test_...",
      "secret_key": "sk_test_...",
      "webhook_secret": "whsec_..."
    },
    "type": "json",
    "group": "payment",
    "description": "Payment gateway configuration",
    "is_public": false
  }'
```

### 4. Quick Value Updates

```bash
# Update site name quickly
curl -X POST http://localhost:8000/api/settings/value/site_name \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "value": "Updated NineHub",
    "type": "string",
    "group": "appearance"
  }'

# Toggle maintenance mode
curl -X POST http://localhost:8000/api/settings/value/maintenance_mode \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "value": true,
    "type": "boolean",
    "group": "system"
  }'
```

### 5. Retrieving Settings

```bash
# Get all appearance settings
curl -X GET http://localhost:8000/api/settings/group/appearance \
  -H "Authorization: Bearer {token}"

# Get all boolean settings
curl -X GET http://localhost:8000/api/settings/type/boolean \
  -H "Authorization: Bearer {token}"

# Get public settings only
curl -X GET http://localhost:8000/api/settings/public \
  -H "Authorization: Bearer {token}"

# Get user-specific settings
curl -X GET http://localhost:8000/api/settings/user/1 \
  -H "Authorization: Bearer {token}"

# Get specific setting value
curl -X GET http://localhost:8000/api/settings/value/site_name \
  -H "Authorization: Bearer {token}"
```

## Key-Based vs Field-Based Comparison

### Key-Based Approach (Recommended)

```php
// ✅ Flexible - Add new settings without migration
Setting::setValue('new_feature_enabled', true, ['group' => 'features']);

// ✅ Type-safe - Automatic type casting
$maintenanceMode = Setting::getValue('maintenance_mode', false); // Returns boolean

// ✅ Organized - Group related settings
$appearanceSettings = Setting::getMultiple([
    'site_name' => 'Default Site',
    'theme_color' => '#007bff',
    'logo_url' => '/images/logo.png'
]);

// ✅ Efficient - Single table for all settings
// ✅ Scalable - Easy to add new setting types
```

### Field-Based Approach (Not Recommended)

```php
// ❌ Requires migration for each new setting
// ❌ Multiple tables needed
// ❌ No type safety
// ❌ Harder to organize and search
```

## Benefits of Key-Based Approach

1. **Flexibility**: Add new settings without database migrations
2. **Type Safety**: Automatic type casting and validation
3. **Organization**: Group settings by category
4. **Efficiency**: Single table for all settings
5. **Scalability**: Easy to extend and maintain
6. **Search**: Powerful search across all settings
7. **Statistics**: Comprehensive analytics and reporting
8. **Caching**: Easy to implement caching strategies
