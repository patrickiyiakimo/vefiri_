<?php

namespace App\Services;

use App\Models\Favourite;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FavouriteService
{
    public function __construct(private readonly ProductCatalog $catalog) {}

    /**
     * @return array<int, string>
     */
    public function ids(User $user): array
    {
        return $user->favourites()->pluck('product_key')->all();
    }

    public function add(User $user, string $productKey): Favourite
    {
        $this->productOrFail($productKey);

        return $user->favourites()->firstOrCreate([
            'product_key' => $productKey,
        ]);
    }

    public function remove(User $user, string $productKey): void
    {
        $user->favourites()->where('product_key', $productKey)->delete();
    }

    public function toggle(User $user, string $productKey): bool
    {
        $favourite = $user->favourites()->where('product_key', $productKey)->first();

        if ($favourite !== null) {
            $favourite->delete();

            return false;
        }

        $this->add($user, $productKey);

        return true;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function products(User $user): Collection
    {
        return $user->favourites()
            ->latest()
            ->pluck('product_key')
            ->map(fn (string $key): ?array => $this->catalog->find($key))
            ->filter()
            ->values();
    }

    private function productOrFail(string $productKey): void
    {
        if (! $this->catalog->has($productKey)) {
            throw ValidationException::withMessages([
                'product_id' => 'The selected product is no longer available.',
            ]);
        }
    }
}
