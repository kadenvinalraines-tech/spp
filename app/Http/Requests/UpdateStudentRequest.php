<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('students', 'nis')->ignore($this->route('student'))
            ],
            'nisn' => [
                'nullable', 
                'string', 
                'max:20', 
                Rule::unique('students', 'nisn')->ignore($this->route('student'))
            ],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'class_id' => ['required', 'exists:classes,id'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,graduated,dropout'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'fee_exemptions' => ['nullable', 'array'],
            'fee_exemptions.*' => ['exists:finance_posts,id']
        ];
    }
}
