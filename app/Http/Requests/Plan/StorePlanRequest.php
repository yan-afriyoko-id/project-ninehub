<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'max_users' => 'required|integer|min:1',
            'max_storage' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama plan wajib diisi',
            'name.max' => 'Nama plan maksimal 255 karakter',
            'slug.required' => 'Slug plan wajib diisi',
            'slug.unique' => 'Slug plan sudah digunakan',
            'slug.max' => 'Slug plan maksimal 255 karakter',
            'price.required' => 'Harga plan wajib diisi',
            'price.numeric' => 'Harga plan harus berupa angka',
            'price.min' => 'Harga plan minimal 0',
            'currency.required' => 'Mata uang wajib diisi',
            'currency.max' => 'Mata uang maksimal 3 karakter',
            'max_users.required' => 'Maksimal user wajib diisi',
            'max_users.integer' => 'Maksimal user harus berupa angka',
            'max_users.min' => 'Maksimal user minimal 1',
            'max_storage.required' => 'Maksimal storage wajib diisi',
            'max_storage.integer' => 'Maksimal storage harus berupa angka',
            'max_storage.min' => 'Maksimal storage minimal 1',
            'features.array' => 'Fitur harus berupa array',
            'features.*.string' => 'Fitur harus berupa string',
        ];
    }
}
