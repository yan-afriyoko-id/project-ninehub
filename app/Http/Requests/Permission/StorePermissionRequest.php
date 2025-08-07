<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|max:255|in:web,api',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Permission name is required.',
            'name.string' => 'Permission name must be a string.',
            'name.max' => 'Permission name must be less than 255 characters.',
            'name.unique' => 'Permission name already exists.',
            'guard_name.required' => 'Guard name is required.',
            'guard_name.string' => 'Guard name must be a string.',
            'guard_name.max' => 'Guard name must be less than 255 characters.',
            'guard_name.in' => 'Guard name must be either web or api.',
        ];
    }
}
