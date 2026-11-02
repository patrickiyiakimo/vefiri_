<?php

namespace Database\Factories;

use App\Models\Favourite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favourite>
 */
class FavouriteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_key' => 'headphones',
        ];
    }
}
