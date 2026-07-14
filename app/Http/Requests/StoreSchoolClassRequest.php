<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:classes,name',
            'level' => 'required|in:10,11,12',
            'major' => 'nullable|string|max:100',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Nama Kelas wajib diisi.',
            'name.unique' => 'Nama Kelas sudah terdaftar.',
            'level.required' => 'Tingkat/Level Kelas wajib diisi.',
        ];
    }
}
