<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RejectsOwnVendorProducts;
use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavouriteRequest extends FormRequest
{
    use RejectsOwnVendorProducts;

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
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->rejectOwnVendorProducts(
                    $validator,
                    'You cannot add your own product to your wishlist.',
                );
            },
        ];
    }
}
