<?php

namespace App\Enums;

enum ProductPhotoInquiryType: string
{
    case DamagedProduct = 'damaged_product';
    case ProductRequest = 'product_request';

    public function label(): string
    {
        return match ($this) {
            self::DamagedProduct => 'Damaged product / delivery issue',
            self::ProductRequest => 'Product availability request',
        };
    }
}
