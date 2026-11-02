<?php

namespace App\Http\Requests;

use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartItemRequest extends FormRequest
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
        return [
            'product_id' => ['required', 'string', Rule::in(app(ProductCatalog::class)->keys())],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ];
    }
}
