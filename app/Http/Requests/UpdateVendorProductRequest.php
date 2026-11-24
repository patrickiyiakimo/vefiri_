<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\VerifiesCloudinaryUploads;
use App\Services\CloudinaryProductImages;
use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorProductRequest extends FormRequest
{
    use VerifiesCloudinaryUploads;

    public function __construct(CloudinaryProductImages $cloudinaryImages)
    {
        $this->cloudinaryImages = $cloudinaryImages;
    }

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('product')) ?? false;
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
            'image_token' => ['sometimes', 'nullable', 'string', 'max:100'],
            'image_version' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'image_signature' => ['sometimes', 'nullable', 'string', 'size:40'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            fn (Validator $validator) => $this->verifyCloudinaryUpload($validator, false),
        ];
    }
}
