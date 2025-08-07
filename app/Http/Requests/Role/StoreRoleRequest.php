<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255|in:web,api',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.string' => 'Role name must be a text.',
            'name.max' => 'Role name must be less than 255 characters.',
            'name.unique' => 'Role name already exists.',
            'guard_name.required' => 'Guard name is required.',
            'guard_name.string' => 'Guard name must be a text.',
            'guard_name.max' => 'Guard name must be less than 255 characters.',
            'guard_name.in' => 'Guard name must be web or api.',
        ];
    }
}
