<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', Rule::in(app(ProductCatalog::class)->categories())],
            'description' => ['required', 'string', 'min:30', 'max:5000'],
            'price' => ['required', 'integer', 'min:100', 'max:1000000000'],
            'compare_price' => ['nullable', 'integer', 'gt:price', 'max:1000000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
