<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:modules,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:view,create,edit,delete,export,import',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama modul wajib diisi',
            'name.max' => 'Nama modul maksimal 255 karakter',
            'slug.required' => 'Slug modul wajib diisi',
            'slug.unique' => 'Slug modul sudah digunakan',
            'slug.max' => 'Slug modul maksimal 255 karakter',
            'icon.max' => 'Icon maksimal 255 karakter',
            'route.max' => 'Route maksimal 255 karakter',
            'order.integer' => 'Order harus berupa angka',
            'order.min' => 'Order minimal 0',
            'permissions.array' => 'Permissions harus berupa array',
            'permissions.*.string' => 'Permission harus berupa string',
            'permissions.*.in' => 'Permission tidak valid',
        ];
    }
}
