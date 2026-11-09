<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorPremiumPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isVendor() === true
            && $this->user()->vendor()->exists();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'uuid'],
            'quantity' => ['required', 'integer', Rule::in(config('vendor.product_slots.bundles', []))],
        ];
    }
}
