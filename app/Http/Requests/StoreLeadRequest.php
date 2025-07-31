<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:Baru,Terkualifikasi,Tidak Terkualifikasi,Konversi',
            'potential_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'contact_id' => 'required|exists:contacts,id',
        ];
    }
}
