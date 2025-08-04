<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Branding
            'app_name' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url',
            'favicon_url' => 'nullable|url',

            // Localization
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string|in:id,en',

            // Email config
            'email_sender' => 'nullable|email',
            'support_email' => 'nullable|email',
            'email_signature' => 'nullable|string',


            // UI Theme
            'theme.primary_color' => 'nullable|string',
            'theme.secondary_color' => 'nullable|string',
            'theme.dark_mode' => 'nullable|boolean'
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}