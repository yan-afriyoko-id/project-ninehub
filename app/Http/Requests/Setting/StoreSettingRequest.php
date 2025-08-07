<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'required',
            'type' => 'required|string|in:string,boolean,integer,float,array,json',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'key.required' => 'Setting key is required',
            'key.string' => 'Setting key must be a text',
            'key.max' => 'Setting key must be less than 255 characters',
            'key.unique' => 'Setting key already exists',
            'value.required' => 'Setting value is required',
            'type.required' => 'Setting type is required',
            'type.string' => 'Setting type must be a text',
            'type.in' => 'Setting type must be string, boolean, integer, float, array, or json',
            'group.string' => 'Setting group must be a text',
            'group.max' => 'Setting group must be less than 100 characters',
            'description.string' => 'Setting description must be a text',
            'description.max' => 'Setting description must be less than 500 characters',
            'is_public.boolean' => 'Is public must be a boolean',
            'user_id.integer' => 'User ID must be an integer',
            'user_id.exists' => 'User not found',
        ];
    }
}
