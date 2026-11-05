<?php

namespace App\Enums;

enum VendorPlan: string
{
    case Free = 'free';
    case Premium = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Premium => 'Premium',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Free => 'gray',
            self::Premium => 'green',
        };
    }
}
