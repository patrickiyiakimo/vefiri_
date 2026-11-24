<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryProductImages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProductImageController extends Controller
{
    public function __construct(private readonly CloudinaryProductImages $images) {}

    public function store(Request $request): JsonResponse
    {
        if (! $this->images->isConfigured()) {
            return response()->json(['message' => 'Image uploads are not configured.'], 503);
        }

        $vendor = $request->user()->vendor()->firstOrFail();

        return response()->json($this->images->signUpload((int) $vendor->getKey()), 201);
    }
}
