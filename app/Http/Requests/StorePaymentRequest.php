<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.bill_detail_id' => ['required', 'exists:bill_details,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
        ];
    }
}
