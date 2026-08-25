<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id' => ['required', 'array'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'finance_post_id' => ['required', 'exists:finance_posts,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
        ];
    }
}
