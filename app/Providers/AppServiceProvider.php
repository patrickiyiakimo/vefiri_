<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        View::composer(['layouts.storefront', 'components.navigation.header', 'components.cart.drawer'], function ($view): void {
            $cartCount = once(function (): int {
                $user = auth()->user();

                return $user === null ? 0 : app(CartService::class)->count($user);
            });

            $view->with('cartCount', $cartCount);
        });
    }
}
