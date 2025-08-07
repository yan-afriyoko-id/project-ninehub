# NineHub - Multi-Tenant SaaS Platform

NineHub adalah platform SaaS multi-tenant yang dibangun dengan Laravel. Sistem ini mendukung isolasi data per tenant dengan berbagai fitur manajemen tenant yang komprehensif.

## Fitur Utama

### 🏢 Manajemen Tenant

-   **Multi-tenant Architecture**: Setiap tenant memiliki data terisolasi
-   **Domain Management**: Support untuk custom domain dan subdomain
-   **Plan Management**: Sistem paket Free, Basic, Premium, dan Enterprise
-   **Trial System**: Sistem trial period untuk tenant baru
-   **Subscription Management**: Manajemen subscription dan expiry date
-   **Feature Management**: Kontrol fitur berdasarkan plan tenant

### 📊 Tenant Features

-   **Status Management**: Active, Inactive, Suspended, Pending
-   **User Limits**: Kontrol maksimal user per tenant
-   **Storage Limits**: Kontrol storage per tenant
-   **Settings Management**: Pengaturan tenant dalam format JSON
-   **Metadata Support**: Data tambahan untuk tenant
-   **Timezone & Locale**: Support multi-timezone dan multi-language

### 🔧 Technical Features

-   **Soft Deletes**: Data history dengan soft delete
-   **UUID Support**: Identifikasi unik dengan UUID
-   **JSON Fields**: Settings, features, dan metadata dalam JSON
-   **Indexing**: Optimized database indexing
-   **Factory & Seeder**: Testing data dengan factory dan seeder

## Database Structure

### Tabel `tenants`

```sql
- id (Primary Key)
- name (Nama tenant)
- domain (Custom domain/subdomain)
- database (Nama database untuk tenant)
- uuid (UUID unik)
- company_name (Nama perusahaan)
- email (Email kontak)
- phone (Nomor telepon)
- address, city, state, country, postal_code (Alamat)
- website (Website perusahaan)
- logo, favicon (Branding assets)
- settings (JSON - pengaturan tenant)
- features (JSON - fitur yang diaktifkan)
- status (enum: active, inactive, suspended, pending)
- plan (enum: free, basic, premium, enterprise)
- max_users, max_storage (Limits)
- trial_ends_at, subscription_ends_at (Timestamps)
- last_login_at (Last activity)
- timezone, locale, currency (Regional settings)
- description (Deskripsi tenant)
- metadata (JSON - data tambahan)
- timestamps, soft deletes
```

### Relasi dengan Users

-   Tabel `users` memiliki foreign key `tenant_id`
-   Setiap user terkait dengan satu tenant
-   Cascade delete saat tenant dihapus

## API Documentation

### Authentication

Semua endpoint memerlukan autentikasi menggunakan Laravel Sanctum. Tambahkan header:

```
Authorization: Bearer {token}
```

### Base URL

```
http://localhost:8000/api
```

### Route Groups & Middleware

**Public Routes (No Authentication):**

-   `/chat/*` - AI Chat endpoints
-   `/register` - User registration
-   `/login` - User authentication

**Protected Routes (Require Authentication):**

-   `/profile/*` - Profile management
-   `/contact/*` - Contact management
-   `/company/*` - Company management
-   `/lead/*` - Lead management
-   `/tenants/*` - Tenant management (with module permissions)
-   `/plans/*` - Plan management (with module permissions)
-   `/modules/*` - Module management (with module permissions)
-   `/permissions/*` - Permission management (with module permissions)
-   `/roles/*` - Role management (with module permissions)

### 1. Authentication Endpoints

#### Register User

```
POST /api/register
```

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Registration successful.",
    "data": {
        "token": "1|abc123...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Login User

```
POST /api/login
```

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
        "token": "1|abc123...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Get User Profile

```
GET /api/auth/profile
```

**Controller Method:** `AuthController@profile`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Request Body:** Tidak diperlukan

**Response (AuthResponse Resource):**

```json
{
    "success": true,
    "message": "User profile retrieved successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

**Controller Implementation:**

```php
// app/Http/Controllers/API/AuthController.php
public function profile(Request $request): AuthResponse
{
    $user = $request->user();
    $data = [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ];
    return AuthResponse::success($data, 'User profile retrieved successfully.');
}
```

#### Logout

```
POST /api/auth/logout
```

**Controller Method:** `AuthController@logout`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Request Body:** Tidak diperlukan (empty body)

**Response (AuthResponse Resource):**

```json
{
    "success": true,
    "message": "Logout successful."
}
```

**Controller Implementation:**

```php
// app/Http/Controllers/API/AuthController.php
public function logout(Request $request): AuthResponse
{
    $request->user()->tokens()->delete();
    return AuthResponse::success([], 'Logout successful.');
}
```

**cURL Example:**

```bash
# Get Profile
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN"

# Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Tenant Management

#### List All Tenants

```
GET /api/tenants
```

**Query Parameters:**

-   `is_active` - Filter by active status
-   `plan_id` - Filter by plan ID
-   `search` - Search by name or email
-   `per_page` - Number of items per page

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Acme Corp",
            "email": "admin@acme.com",
            "phone": "+1234567890",
            "logo": "https://example.com/logo.png",
            "is_active": true,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "owner": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "plan": {
                "id": 1,
                "name": "Premium Plan",
                "slug": "premium",
                "price": 100000
            },
            "users_count": 5,
            "modules_count": 8
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

#### Create Tenant

```
POST /api/tenants
```

**Request Body:**

```json
{
    "name": "Acme Corp",
    "email": "admin@acme.com",
    "phone": "+1234567890",
    "logo": "https://example.com/logo.png",
    "user_id": 1,
    "plan_id": 1,
    "is_active": true
}
```

**Response:**

```json
{
    "success": true,
    "message": "Tenant created successfully",
    "data": {
        "id": 1,
        "name": "Acme Corp",
        "email": "admin@acme.com",
        "phone": "+1234567890",
        "logo": "https://example.com/logo.png",
        "is_active": true,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "owner": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "plan": {
            "id": 1,
            "name": "Premium Plan",
            "slug": "premium",
            "price": 100000
        },
        "users_count": 0,
        "modules_count": 0
    }
}
```

#### Get Tenant Details

```
GET /api/tenants/{id}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Acme Corp",
        "email": "admin@acme.com",
        "phone": "+1234567890",
        "logo": "https://example.com/logo.png",
        "is_active": true,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "owner": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "plan": {
            "id": 1,
            "name": "Premium Plan",
            "slug": "premium",
            "price": 100000
        },
        "users_count": 5,
        "modules_count": 8
    },
    "message": "Tenant retrieved successfully"
}
```

#### Update Tenant

```
PUT /api/tenants/{id}
```

**Request Body:**

```json
{
    "name": "Acme Corporation Updated",
    "email": "admin@acme.com",
    "phone": "+1234567890",
    "logo": "https://example.com/new-logo.png",
    "user_id": 1,
    "plan_id": 2,
    "is_active": true
}
```

**Response:**

```json
{
    "success": true,
    "message": "Tenant updated successfully",
    "data": {
        "id": 1,
        "name": "Acme Corporation Updated",
        "email": "admin@acme.com",
        "phone": "+1234567890",
        "logo": "https://example.com/new-logo.png",
        "is_active": true,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:35:00.000000Z",
        "owner": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "plan": {
            "id": 2,
            "name": "Enterprise Plan",
            "slug": "enterprise",
            "price": 200000
        },
        "users_count": 5,
        "modules_count": 8
    }
}
```

#### Delete Tenant

```
DELETE /api/tenants/{id}
```

**Response:**

```json
{
    "success": true,
    "message": "Tenant deleted successfully"
}
```

#### Get Tenant Statistics

```
GET /api/tenants/statistics
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total_tenants": 10,
        "active_tenants": 8,
        "inactive_tenants": 2,
        "tenants_by_plan": {
            "free": 2,
            "basic": 3,
            "premium": 4,
            "enterprise": 1
        }
    },
    "message": "Tenant statistics retrieved successfully"
}
```

#### Activate Tenant

```
PATCH /api/tenants/{id}/activate
```

**Response:**

```json
{
    "success": true,
    "message": "Tenant activated successfully"
}
```

#### Suspend Tenant

```
PATCH /api/tenants/{id}/suspend
```

**Response:**

```json
{
    "success": true,
    "message": "Tenant suspended successfully"
}
```

### 3. Module Management

#### List All Modules

```
GET /api/modules
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Dashboard",
            "slug": "dashboard",
            "description": "Main dashboard module",
            "icon": "dashboard",
            "route": "/dashboard",
            "order": 1,
            "is_active": true,
            "is_public": true,
            "permissions": ["view", "create", "edit", "delete"],
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "tenants_count": 5,
            "permissions_to_create": [
                "dashboard.view",
                "dashboard.create",
                "dashboard.edit",
                "dashboard.delete"
            ]
        }
    ],
    "message": "Modules retrieved successfully"
}
```

#### Create Module

```
POST /api/modules
```

**Request Body:**

```json
{
    "name": "User Management",
    "slug": "user-management",
    "description": "Manage users and permissions",
    "icon": "users",
    "route": "/users",
    "order": 2,
    "is_active": true,
    "is_public": false,
    "permissions": ["view", "create", "edit", "delete"]
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "User Management",
        "slug": "user-management",
        "description": "Manage users and permissions",
        "icon": "users",
        "route": "/users",
        "order": 2,
        "is_active": true,
        "is_public": false,
        "permissions": ["view", "create", "edit", "delete"],
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "tenants_count": 0,
        "permissions_to_create": [
            "user-management.view",
            "user-management.create",
            "user-management.edit",
            "user-management.delete"
        ]
    },
    "message": "Module created successfully"
}
```

#### Get Module Details

```
GET /api/modules/{id}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Dashboard",
        "slug": "dashboard",
        "description": "Main dashboard module",
        "icon": "dashboard",
        "route": "/dashboard",
        "order": 1,
        "is_active": true,
        "is_public": true,
        "permissions": ["view", "create", "edit", "delete"],
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "tenants_count": 5,
        "permissions_to_create": [
            "dashboard.view",
            "dashboard.create",
            "dashboard.edit",
            "dashboard.delete"
        ]
    },
    "message": "Module retrieved successfully"
}
```

#### Update Module

```
PUT /api/modules/{id}
```

**Request Body:**

```json
{
    "name": "Dashboard Updated",
    "description": "Updated dashboard description",
    "is_active": true,
    "is_public": true
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Dashboard Updated",
        "slug": "dashboard",
        "description": "Updated dashboard description",
        "icon": "dashboard",
        "route": "/dashboard",
        "order": 1,
        "is_active": true,
        "is_public": true,
        "permissions": ["view", "create", "edit", "delete"],
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:35:00.000000Z",
        "tenants_count": 5,
        "permissions_to_create": [
            "dashboard.view",
            "dashboard.create",
            "dashboard.edit",
            "dashboard.delete"
        ]
    },
    "message": "Module updated successfully"
}
```

#### Delete Module

```
DELETE /api/modules/{id}
```

**Response:**

```json
{
    "success": true,
    "message": "Module deleted successfully"
}
```

### 4. Permission Management

#### List All Permissions

```
GET /api/permissions
```

**Query Parameters:**

-   `guard_name` - Filter by guard name
-   `search` - Search by permission name
-   `per_page` - Number of items per page

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "dashboard.view",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "module": "dashboard",
            "action": "view",
            "roles_count": 2
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

#### Create Permission

```
POST /api/permissions
```

**Request Body:**

```json
{
    "name": "user-management.create",
    "guard_name": "api"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Permission created successfully",
    "data": {
        "id": 2,
        "name": "user-management.create",
        "guard_name": "api",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "module": "user-management",
        "action": "create",
        "roles_count": 0
    }
}
```

#### Get Permissions by Guard

```
GET /api/permissions/guard/{guard}
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "dashboard.view",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "module": "dashboard",
            "action": "view",
            "roles_count": 2
        }
    ],
    "message": "Permissions retrieved successfully"
}
```

#### Get Permissions by Module

```
GET /api/permissions/module/{moduleSlug}
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "dashboard.view",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "module": "dashboard",
            "action": "view",
            "roles_count": 2
        }
    ],
    "message": "Permissions retrieved successfully"
}
```

#### Search Permissions

```
GET /api/permissions/search?q=dashboard
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "dashboard.view",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "module": "dashboard",
            "action": "view",
            "roles_count": 2
        }
    ],
    "message": "Permissions retrieved successfully"
}
```

#### Sync Permissions from Modules

```
POST /api/permissions/sync
```

**Response:**

```json
{
    "success": true,
    "message": "Permissions synced successfully"
}
```

#### Get Permission Statistics

```
GET /api/permissions/statistics
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total_permissions": 20,
        "permissions_by_guard": {
            "api": 15,
            "web": 5
        },
        "permissions_by_module": {
            "dashboard": 4,
            "user-management": 4,
            "settings": 4
        }
    },
    "message": "Permission statistics retrieved successfully"
}
```

### 5. Role Management

#### List All Roles

```
GET /api/roles
```

**Query Parameters:**

-   `guard` - Filter by guard name
-   `search` - Search by role name
-   `per_page` - Number of items per page

**Response:**

```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "admin",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "permissions": [
                {
                    "id": 1,
                    "name": "dashboard.view",
                    "guard_name": "api"
                }
            ],
            "permissions_count": 1
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

#### Create Role

```
POST /api/roles
```

**Request Body:**

```json
{
    "name": "manager",
    "guard_name": "api"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Role created successfully",
    "data": {
        "id": 2,
        "name": "manager",
        "guard_name": "api",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "permissions": [],
        "permissions_count": 0
    }
}
```

#### Get Roles by Guard

```
GET /api/roles/guard/{guard}
```

**Response:**

```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "admin",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "permissions": [
                {
                    "id": 1,
                    "name": "dashboard.view",
                    "guard_name": "api"
                }
            ],
            "permissions_count": 1
        }
    ]
}
```

#### Search Roles

```
GET /api/roles/search?q=admin
```

**Response:**

```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "admin",
            "guard_name": "api",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "permissions": [
                {
                    "id": 1,
                    "name": "dashboard.view",
                    "guard_name": "api"
                }
            ],
            "permissions_count": 1
        }
    ]
}
```

#### Assign Permissions to Role

```
POST /api/roles/{id}/permissions
```

**Request Body:**

```json
{
    "permission_ids": [1, 2, 3]
}
```

**Response:**

```json
{
    "success": true,
    "message": "Permissions assigned to role successfully"
}
```

#### Remove Permissions from Role

```
DELETE /api/roles/{id}/permissions
```

**Request Body:**

```json
{
    "permission_ids": [1, 2]
}
```

**Response:**

```json
{
    "success": true,
    "message": "Permissions removed from role successfully"
}
```

#### Get Role Statistics

```
GET /api/roles/statistics
```

**Response:**

```json
{
    "success": true,
    "message": "Role statistics retrieved successfully",
    "data": {
        "total_roles": 5,
        "roles_by_guard": {
            "api": 3,
            "web": 2
        },
        "roles_with_permissions": 3,
        "average_permissions_per_role": 2.5
    }
}
```

### 6. Plan Management

#### List All Plans

```
GET /api/plans
```

**Query Parameters:**

-   `is_active` - Filter by active status
-   `search` - Search by name or description
-   `price_min` - Minimum price filter
-   `price_max` - Maximum price filter
-   `per_page` - Number of items per page

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Free Plan",
            "slug": "free",
            "description": "Basic features for free",
            "price": 0,
            "currency": "IDR",
            "max_users": 5,
            "max_storage": 100,
            "features": ["dashboard", "basic-reports"],
            "is_active": true,
            "formatted_price": "IDR 0",
            "is_free": true,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
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

#### Create Plan

```
POST /api/plans
```

**Request Body:**

```json
{
    "name": "Premium Plan",
    "slug": "premium",
    "description": "Advanced features for businesses",
    "price": 100000,
    "currency": "IDR",
    "max_users": 25,
    "max_storage": 1000,
    "features": ["dashboard", "advanced-reports", "custom-branding"],
    "is_active": true
}
```

**Response:**

```json
{
    "success": true,
    "message": "Plan created successfully",
    "data": {
        "id": 2,
        "name": "Premium Plan",
        "slug": "premium",
        "description": "Advanced features for businesses",
        "price": 100000,
        "currency": "IDR",
        "max_users": 25,
        "max_storage": 1000,
        "features": ["dashboard", "advanced-reports", "custom-branding"],
        "is_active": true,
        "formatted_price": "IDR 100,000",
        "is_free": false,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

#### Get Active Plans

```
GET /api/plans/active
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Free Plan",
            "slug": "free",
            "description": "Basic features for free",
            "price": 0,
            "currency": "IDR",
            "max_users": 5,
            "max_storage": 100,
            "features": ["dashboard", "basic-reports"],
            "is_active": true,
            "formatted_price": "IDR 0",
            "is_free": true,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "message": "Active plans retrieved successfully"
}
```

#### Get Free Plans

```
GET /api/plans/free
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Free Plan",
            "slug": "free",
            "description": "Basic features for free",
            "price": 0,
            "currency": "IDR",
            "max_users": 5,
            "max_storage": 100,
            "features": ["dashboard", "basic-reports"],
            "is_active": true,
            "formatted_price": "IDR 0",
            "is_free": true,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "message": "Free plans retrieved successfully"
}
```

#### Get Paid Plans

```
GET /api/plans/paid
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "name": "Premium Plan",
            "slug": "premium",
            "description": "Advanced features for businesses",
            "price": 100000,
            "currency": "IDR",
            "max_users": 25,
            "max_storage": 1000,
            "features": ["dashboard", "advanced-reports", "custom-branding"],
            "is_active": true,
            "formatted_price": "IDR 100,000",
            "is_free": false,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "message": "Paid plans retrieved successfully"
}
```

#### Search Plans

```
GET /api/plans/search?q=premium
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "name": "Premium Plan",
            "slug": "premium",
            "description": "Advanced features for businesses",
            "price": 100000,
            "currency": "IDR",
            "max_users": 25,
            "max_storage": 1000,
            "features": ["dashboard", "advanced-reports", "custom-branding"],
            "is_active": true,
            "formatted_price": "IDR 100,000",
            "is_free": false,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "message": "Plans retrieved successfully"
}
```

#### Get Plan Statistics

```
GET /api/plans/statistics
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total_plans": 5,
        "active_plans": 4,
        "free_plans": 1,
        "paid_plans": 4,
        "price_ranges": {
            "0-50000": 2,
            "50001-100000": 2,
            "100001+": 1
        }
    },
    "message": "Plan statistics retrieved successfully"
}
```

### 7. Chat API

#### Send Message

```
POST /api/chat
```

**Request Body:**

```json
{
    "message": "Hello, how are you?"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "response": "Hello! I'm doing well, thank you for asking. How are you?",
        "timestamp": "2024-01-15T10:30:00.000000Z",
        "message_id": "507f1f77bcf86cd799439011"
    }
}
```

#### Get Chat History

```
GET /api/chat/history
```

**Response:**

```json
{
    "success": true,
    "data": {
        "history": [
            {
                "id": "507f1f77bcf86cd799439011",
                "user_message": "Hello, how are you?",
                "ai_response": "Hello! I'm doing well, thank you for asking.",
                "timestamp": "2024-01-15T10:30:00.000000Z",
                "created_at": "2024-01-15 10:30:00"
            }
        ],
        "total_conversations": 1
    }
}
```

#### Clear Chat History

```
DELETE /api/chat/clear
```

**Response:**

```json
{
    "success": true,
    "message": "Chat history cleared successfully"
}
```

### 8. Company Management

#### List All Companies

```
GET /api/companies
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Acme Corp",
            "industry": "Technology",
            "phone": "+1234567890",
            "email": "contact@acme.com",
            "address": "123 Main St",
            "website": "https://acme.com",
            "description": "Leading technology company",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ]
}
```

#### Create Company

```
POST /api/companies
```

**Request Body:**

```json
{
    "name": "Acme Corp",
    "industry": "Technology",
    "phone": "+1234567890",
    "email": "contact@acme.com",
    "address": "123 Main St",
    "website": "https://acme.com",
    "description": "Leading technology company"
}
```

### 9. Contact Management

#### List All Contacts

```
GET /api/contacts
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "full_name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "job_title": "Manager",
            "company": {
                "id": 1,
                "name": "Acme Corp",
                "industry": "Technology",
                "phone": "+1234567890",
                "email": "contact@acme.com",
                "address": "123 Main St",
                "website": "https://acme.com",
                "description": "Leading technology company"
            }
        }
    ]
}
```

#### Create Contact

```
POST /api/contacts
```

**Request Body:**

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "job_title": "Manager",
    "company_id": 1
}
```

### 10. Lead Management

#### List All Leads

```
GET /api/leads
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Sales Lead",
            "email": "lead@example.com",
            "phone": "+1234567890",
            "source": "Website",
            "status": "Baru",
            "potential_value": 50000,
            "notes": "Interested in premium plan",
            "contact": {
                "id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "full_name": "John Doe",
                "email": "john@example.com",
                "phone": "+1234567890",
                "job_title": "Manager"
            }
        }
    ]
}
```

#### Create Lead

```
POST /api/leads
```

**Request Body:**

```json
{
    "name": "Sales Lead",
    "email": "lead@example.com",
    "phone": "+1234567890",
    "source": "Website",
    "status": "Baru",
    "potential_value": 50000,
    "notes": "Interested in premium plan",
    "contact_id": 1
}
```

### 11. Profile Management

#### List All Profiles

```
GET /api/profile
```

**Controller Method:** `ProfileController@index`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Response (ProfileResource Collection):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "age": 30,
            "gender": "male",
            "phone_number": "+1234567890",
            "address": "123 Main St",
            "birth_date": "1994-01-15",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ]
}
```

#### Get Profile Details

```
GET /api/profile/{id}
```

**Controller Method:** `ProfileController@show`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Response (ProfileResource):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "age": 30,
        "gender": "male",
        "phone_number": "+1234567890",
        "address": "123 Main St",
        "birth_date": "1994-01-15",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Create Profile

```
POST /api/profile
```

**Controller Method:** `ProfileController@store`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "name": "John Doe",
    "age": 30,
    "gender": "male",
    "phone_number": "+1234567890",
    "address": "123 Main St",
    "birth_date": "1994-01-15"
}
```

**Validation Rules:**

```php
'name' => 'required|string|max:255',
'age' => 'required|integer|min:0',
'gender' => 'nullable|in:male,female',
'phone_number' => 'nullable|string|max:20',
'address' => 'nullable|string',
'birth_date' => 'nullable|date',
```

**Response (ProfileResource):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "age": 30,
        "gender": "male",
        "phone_number": "+1234567890",
        "address": "123 Main St",
        "birth_date": "1994-01-15",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Update Profile

```
PUT /api/profile/{id}
```

**Controller Method:** `ProfileController@update`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "name": "John Doe Updated",
    "age": 31,
    "gender": "male",
    "phone_number": "+1234567890",
    "address": "456 New St",
    "birth_date": "1994-01-15"
}
```

**Response (ProfileResource):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        "age": 31,
        "gender": "male",
        "phone_number": "+1234567890",
        "address": "456 New St",
        "birth_date": "1994-01-15",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Delete Profile

```
DELETE /api/profile/{id}
```

**Controller Method:** `ProfileController@destroy`

**Headers Required:**

```
Authorization: Bearer {token}
```

**Response:**

```
HTTP/1.1 204 No Content
```

**Controller Implementation:**

```php
// app/Http/Controllers/Api/ProfileController.php
public function store(StoreProfileRequest $request)
{
    $userId = Auth::id();
    $Profile = $this->service->create($request->validated(), $userId);
    return new ProfileResource($Profile);
}

public function update(StoreProfileRequest $request, $id)
{
    $Profile = $this->service->getProfileById($id);
    $updated = $this->service->update($Profile, $request->validated());
    return new ProfileResource($updated);
}

public function destroy($id)
{
    $this->service->delete($id);
    return response()->noContent();
}
```

**cURL Examples:**

```bash
# List Profiles
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get Profile Details
curl -X GET http://localhost:8000/api/profile/1 \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create Profile
curl -X POST http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "age": 30,
    "gender": "male",
    "phone_number": "+1234567890",
    "address": "123 Main St",
    "birth_date": "1994-01-15"
  }'

# Update Profile
curl -X PUT http://localhost:8000/api/profile/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe Updated",
    "age": 31,
    "gender": "male",
    "phone_number": "+1234567890",
    "address": "456 New St",
    "birth_date": "1994-01-15"
  }'

# Delete Profile
curl -X DELETE http://localhost:8000/api/profile/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email field is required."]
    }
}
```

### Not Found Error (404)

```json
{
    "success": false,
    "message": "Tenant not found"
}
```

### Unauthorized Error (401)

```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

### Forbidden Error (403)

```json
{
    "success": false,
    "message": "Access denied. You do not have permission to access this resource."
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Failed to retrieve tenants",
    "error": "Database connection failed"
}
```

## Testing with cURL

### Authentication

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Tenant Management

```bash
# List tenants
curl -X GET http://localhost:8000/api/tenants \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create tenant
curl -X POST http://localhost:8000/api/tenants \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Acme Corp",
    "email": "admin@acme.com",
    "user_id": 1,
    "plan_id": 1
  }'
```

### Module Management

```bash
# List modules
curl -X GET http://localhost:8000/api/modules \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create module
curl -X POST http://localhost:8000/api/modules \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "User Management",
    "slug": "user-management",
    "description": "Manage users and permissions",
    "is_active": true
  }'
```

## Features

-   ✅ Full CRUD operations for all entities
-   ✅ Authentication with Laravel Sanctum
-   ✅ Role-based access control with Spatie Laravel Permission
-   ✅ Multi-tenant architecture
-   ✅ Comprehensive validation
-   ✅ Error handling
-   ✅ JSON API responses
-   ✅ Pagination support
-   ✅ Search and filtering
-   ✅ Statistics endpoints
-   ✅ Chat integration with AI

## Model Methods

### Tenant Model

```php
// Status checks
$tenant->isActive()
$tenant->isOnTrial()
$tenant->hasExpired()
$tenant->canAddUser()

// Date calculations
$tenant->getTrialDaysRemaining()
$tenant->getSubscriptionDaysRemaining()

// Settings management
$tenant->getSetting('key', 'default')
$tenant->setSetting('key', 'value')

// Feature management
$tenant->hasFeature('feature_name')
$tenant->addFeature('feature_name')
$tenant->removeFeature('feature_name')

// Metadata management
$tenant->getMetadata('key', 'default')
$tenant->setMetadata('key', 'value')

// Scopes
Tenant::active()
Tenant::byPlan('premium')
Tenant::expired()
Tenant::onTrial()
```

## Installation & Setup

1. **Clone repository**

```bash
git clone <repository-url>
cd project-ninehub
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**

```bash
php artisan migrate
php artisan db:seed
```

5. **Run development server**

```bash
php artisan serve
```

## Testing

### Factory Usage

```php
// Create tenant dengan factory
Tenant::factory()->create();

// Create dengan state tertentu
Tenant::factory()->active()->premium()->create();
Tenant::factory()->onTrial()->create();
Tenant::factory()->expired()->create();
```

### Seeder

```bash
php artisan db:seed --class=TenantSeeder
```

## Contoh Penggunaan

### Membuat Tenant Baru

```php
$tenant = Tenant::create([
    'name' => 'Acme Corp',
    'company_name' => 'Acme Corporation',
    'email' => 'admin@acme.com',
    'domain' => 'acme.ninehub.local',
    'plan' => 'premium',
    'max_users' => 25,
    'max_storage' => 1000,
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'currency' => 'IDR',
]);
```

### Mengelola Settings

```php
// Set setting
$tenant->setSetting('theme', 'dark');
$tenant->setSetting('notifications', true);

// Get setting
$theme = $tenant->getSetting('theme', 'light');
```

### Mengelola Features

```php
// Add feature
$tenant->addFeature('advanced_analytics');
$tenant->addFeature('custom_branding');

// Check feature
if ($tenant->hasFeature('advanced_analytics')) {
    // Show analytics
}
```

### Filtering & Searching

```php
// Active premium tenants
$tenants = Tenant::active()->byPlan('premium')->get();

// Search tenants
$tenants = Tenant::where('name', 'like', '%Acme%')
    ->orWhere('company_name', 'like', '%Acme%')
    ->get();
```

## Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License.

## Detailed API Implementation Examples

### Controller Implementation Examples

#### Tenant Controller

```php
// app/Http/Controllers/TenantController.php
public function store(StoreTenantRequest $request): JsonResponse
{
    try {
        $tenant = $this->tenantService->createTenant($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'data' => new TenantResource($tenant),
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create tenant',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

#### Plan Controller

```php
// app/Http/Controllers/PlanController.php
public function index(Request $request): JsonResponse
{
    try {
        $filters = $request->only(['is_active', 'search', 'price_min', 'price_max', 'per_page']);
        $plans = $this->planService->getAllPlans($filters);

        return response()->json([
            'success' => true,
            'data' => PlanResource::collection($plans),
            'pagination' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve plans',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

### Service Interface Examples

#### Tenant Service Interface

```php
// app/Services/Interfaces/TenantServiceInterface.php
interface TenantServiceInterface
{
    public function getAllTenants(array $filters = []): LengthAwarePaginator;
    public function getTenantById(int $id): ?Tenant;
    public function createTenant(array $data): Tenant;
    public function updateTenant(int $id, array $data): Tenant;
    public function deleteTenant(int $id): bool;
    public function activateTenant(int $id): bool;
    public function suspendTenant(int $id): bool;
    public function getTenantStatistics(): array;
}
```

#### Plan Service Interface

```php
// app/Services/Interfaces/PlanServiceInterface.php
interface PlanServiceInterface
{
    public function getAllPlans(array $filters = []): LengthAwarePaginator;
    public function getPlanById(int $id): ?Plan;
    public function createPlan(array $data): Plan;
    public function updatePlan(int $id, array $data): Plan;
    public function deletePlan(int $id): bool;
    public function getActivePlans(): Collection;
    public function getFreePlans(): Collection;
    public function getPaidPlans(): Collection;
    public function searchPlans(string $search): Collection;
    public function getPlanStatistics(): array;
}
```

### Repository Pattern Examples

#### Tenant Repository Interface

```php
// app/Repositories/Interfaces/TenantRepositoryInterface.php
interface TenantRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Tenant;
    public function findOrFail(int $id): Tenant;
    public function create(array $data): Tenant;
    public function update(int $id, array $data): Tenant;
    public function delete(int $id): bool;
    public function paginate(array $filters = []): LengthAwarePaginator;
    public function getActiveTenants(): Collection;
    public function getInactiveTenants(): Collection;
    public function getTenantsByPlan(int $planId): Collection;
    public function searchTenants(string $search): Collection;
    public function getTenantStatistics(): array;
}
```

### Form Request Validation Examples

#### Store Tenant Request

```php
// app/Http/Requests/Tenant/StoreTenantRequest.php
class StoreTenantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tenant wajib diisi',
            'name.max' => 'Nama tenant maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'phone.max' => 'Nomor telepon maksimal 50 karakter',
            'user_id.required' => 'User ID wajib diisi',
            'user_id.exists' => 'User tidak ditemukan',
            'plan_id.required' => 'Plan ID wajib diisi',
            'plan_id.exists' => 'Plan tidak ditemukan',
        ];
    }
}
```

### API Resource Examples

#### Tenant Resource

```php
// app/Http/Resources/TenantResource.php
class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'owner' => [
                'id' => $this->owner->id ?? null,
                'name' => $this->owner->name ?? null,
                'email' => $this->owner->email ?? null,
            ],
            'plan' => [
                'id' => $this->plan->id ?? null,
                'name' => $this->plan->name ?? null,
                'slug' => $this->plan->slug ?? null,
                'price' => $this->plan->price ?? null,
            ],
            'users_count' => $this->users_count ?? $this->users->count(),
            'modules_count' => $this->modules_count ?? $this->modules->count(),
        ];
    }
}
```

#### Plan Resource

```php
// app/Http/Resources/PlanResource.php
class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'max_users' => $this->max_users,
            'max_storage' => $this->max_storage,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'formatted_price' => $this->formatted_price,
            'is_free' => $this->isFree(),
            'tenants_count' => $this->whenLoaded('tenants', function () {
                return $this->tenants->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

### Middleware Examples

#### Module Permission Middleware

```php
// app/Http/Middleware/CheckModulePermission.php
class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$permission) {
            return $next($request);
        }

        if (!$user->hasPermissionTo($permission)) {
            return response()->json([
                'message' => 'Access denied. You do not have permission to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
```

### Route Configuration Examples

#### Routes with Middleware

```php
// routes/api.php
Route::prefix('tenants')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TenantController::class, 'index'])
        ->middleware('module.permission:tenant-management.view');
    Route::post('/', [TenantController::class, 'store'])
        ->middleware('module.permission:tenant-management.create');
    Route::get('/statistics', [TenantController::class, 'statistics'])
        ->middleware('module.permission:tenant-management.view');
    Route::get('/{id}', [TenantController::class, 'show'])
        ->middleware('module.permission:tenant-management.view');
    Route::put('/{id}', [TenantController::class, 'update'])
        ->middleware('module.permission:tenant-management.edit');
    Route::delete('/{id}', [TenantController::class, 'destroy'])
        ->middleware('module.permission:tenant-management.delete');
    Route::patch('/{id}/activate', [TenantController::class, 'activate'])
        ->middleware('module.permission:tenant-management.edit');
    Route::patch('/{id}/suspend', [TenantController::class, 'suspend'])
        ->middleware('module.permission:tenant-management.edit');
});
```

### Service Provider Binding Examples

#### App Service Provider

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
    $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
    $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
    $this->app->bind(TenantServiceInterface::class, TenantService::class);
    $this->app->bind(ModuleServiceInterface::class, ModuleService::class);
    $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
    $this->app->bind(RoleServiceInterface::class, RoleService::class);
    $this->app->bind(PlanServiceInterface::class, PlanService::class);
}
```
