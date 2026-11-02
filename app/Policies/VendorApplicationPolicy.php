<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorApplication;

class VendorApplicationPolicy
{
    public function review(User $user, VendorApplication $vendorApplication): bool
    {
        return $user->isAdmin();
    }
}
