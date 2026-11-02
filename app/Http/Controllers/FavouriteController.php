<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Requests\StoreFavouriteRequest;
use App\Services\FavouriteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly FavouriteService $favourites) {}

    public function index(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ids' => $this->favourites->ids($request->user()),
            ]);
        }

        return view('favourites.index', [
            ...$this->sharedData(),
            'products' => $this->favourites->products($request->user()),
        ]);
    }

    public function store(StoreFavouriteRequest $request): JsonResponse
    {
        $this->favourites->add($request->user(), $request->validated('product_id'));

        return response()->json([
            'active' => true,
            'ids' => $this->favourites->ids($request->user()),
        ]);
    }

    public function destroy(Request $request, string $product): JsonResponse
    {
        $this->favourites->remove($request->user(), $product);

        return response()->json([
            'active' => false,
            'ids' => $this->favourites->ids($request->user()),
        ]);
    }
}
