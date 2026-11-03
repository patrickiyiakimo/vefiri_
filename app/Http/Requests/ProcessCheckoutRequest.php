<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $idempotencyKey = $this->header('X-Idempotency-Key');

        if (is_string($idempotencyKey) && trim($idempotencyKey) !== '') {
            $this->merge(['idempotency_key' => trim($idempotencyKey)]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_email' => ['nullable', 'string', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:32'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_address_line_2' => ['nullable', 'string', 'max:255'],
            'delivery_city' => ['required', 'string', 'max:120'],
            'delivery_state' => ['required', 'string', 'max:120'],
            'delivery_postal_code' => ['nullable', 'string', 'max:20'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
