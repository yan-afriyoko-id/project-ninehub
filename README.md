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

## API Endpoints

### Tenant Management

```
GET    /api/tenants              - List semua tenant
POST   /api/tenants              - Buat tenant baru
GET    /api/tenants/{id}         - Detail tenant
PUT    /api/tenants/{id}         - Update tenant
DELETE /api/tenants/{id}         - Hapus tenant
```

### Tenant Operations

```
GET    /api/tenants/statistics   - Statistik tenant
PATCH  /api/tenants/{id}/activate   - Aktivasi tenant
PATCH  /api/tenants/{id}/suspend    - Suspend tenant
PUT    /api/tenants/{id}/settings   - Update settings
POST   /api/tenants/{id}/features   - Tambah fitur
DELETE /api/tenants/{id}/features   - Hapus fitur
```

### Query Parameters

```
?status=active          - Filter by status
?plan=premium          - Filter by plan
?search=company        - Search by name/company/email
```

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
