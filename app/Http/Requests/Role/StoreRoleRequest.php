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
            'name.required' => 'Nama role wajib diisi.',
            'name.string' => 'Nama role harus berupa teks.',
            'name.max' => 'Nama role maksimal 255 karakter.',
            'name.unique' => 'Nama role sudah digunakan.',
            'guard_name.required' => 'Guard name wajib diisi.',
            'guard_name.string' => 'Guard name harus berupa teks.',
            'guard_name.max' => 'Guard name maksimal 255 karakter.',
            'guard_name.in' => 'Guard name harus web atau api.',
        ];
    }
}
