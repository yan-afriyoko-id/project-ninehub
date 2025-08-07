<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class TenantSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'branding' => [
                'app_name' => $this['app_name'] ?? null,
                'company_name' => $this['company_name'] ?? null,
                'logo_url' => $this['logo_url'] ?? null,
                'favicon_url' => $this['favicon_url'] ?? null,
            ],
            'localization' => [
                'timezone' => $this['timezone'] ?? null,
                'locale' => $this['locale'] ?? null,
            ],
            'email' => [
                'sender' => $this['email_sender'] ?? null,
                'support' => $this['support_email'] ?? null,
                'signature' => $this['email_signature'] ?? null,
            ],
            'theme' => [
                'primary_color' => data_get($this, 'theme.primary_color'),
                'secondary_color' => data_get($this, 'theme.secondary_color'),
                'dark_mode' => data_get($this, 'theme.dark_mode'),
            ]
        ];
    }
}


