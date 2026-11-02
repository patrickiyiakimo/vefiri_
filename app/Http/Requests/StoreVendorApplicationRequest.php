<?php

namespace App\Http\Requests;

use App\Enums\VendorApplicationStatus;
use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $catalog = app(ProductCatalog::class);

        return [
            'business_name' => ['required', 'string', 'max:150'],
            'store_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vendors', 'store_name'),
                Rule::unique('vendor_applications', 'store_name')
                    ->where('status', VendorApplicationStatus::Pending->value),
            ],
            'business_email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'business_registration_number' => ['nullable', 'string', 'max:64'],
            'description' => ['required', 'string', 'min:30', 'max:2000'],
            'product_categories' => ['required', 'array', 'min:1', 'max:5'],
            'product_categories.*' => ['string', Rule::in($catalog->categories())],
        ];
    }
}
