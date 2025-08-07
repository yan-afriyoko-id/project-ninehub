<?php

namespace App\Http\Requests\TenantSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'sometimes|required|string|max:255',
            'company_email' => 'sometimes|required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|string|max:255',
            'timezone' => 'sometimes|required|string|max:50',
            'date_format' => 'sometimes|required|string|max:20',
            'time_format' => 'sometimes|required|string|max:10',
            'currency' => 'sometimes|required|string|max:10',
            'language' => 'sometimes|required|string|max:10',
            'theme' => 'nullable|string|max:50',
            'notifications' => 'nullable|array',
            'notifications.email' => 'nullable|boolean',
            'notifications.sms' => 'nullable|boolean',
            'notifications.push' => 'nullable|boolean',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required',
            'company_name.string' => 'Company name must be a string',
            'company_name.max' => 'Company name must be less than 255 characters',
            'company_email.required' => 'Company email is required',
            'company_email.email' => 'Company email must be a valid email',
            'company_email.max' => 'Company email must be less than 255 characters',
            'company_phone.string' => 'Company phone must be a string',
            'company_phone.max' => 'Company phone must be less than 20 characters',
            'company_address.string' => 'Company address must be a string',
            'company_address.max' => 'Company address must be less than 500 characters',
            'company_website.url' => 'Company website must be a valid URL',
            'company_website.max' => 'Company website must be less than 255 characters',
            'company_logo.string' => 'Company logo must be a string',
            'company_logo.max' => 'Company logo must be less than 255 characters',
            'timezone.required' => 'Timezone is required',
            'timezone.string' => 'Timezone must be a string',
            'timezone.max' => 'Timezone must be less than 50 characters',
            'date_format.required' => 'Date format is required',
            'date_format.string' => 'Date format must be a string',
            'date_format.max' => 'Date format must be less than 20 characters',
            'time_format.required' => 'Time format is required',
            'time_format.string' => 'Time format must be a string',
            'time_format.max' => 'Time format must be less than 10 characters',
            'currency.required' => 'Currency is required',
            'currency.string' => 'Currency must be a string',
            'currency.max' => 'Currency must be less than 10 characters',
            'language.required' => 'Language is required',
            'language.string' => 'Language must be a string',
            'language.max' => 'Language must be less than 10 characters',
            'theme.string' => 'Theme must be a string',
            'theme.max' => 'Theme must be less than 50 characters',
            'notifications.array' => 'Notifications must be an array',
            'notifications.email.boolean' => 'Email notifications must be a boolean',
            'notifications.sms.boolean' => 'SMS notifications must be a boolean',
            'notifications.push.boolean' => 'Push notifications must be a boolean',
        ];
    }
}
