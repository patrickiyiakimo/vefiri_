<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductPhotoInquiryRequest;
use App\Models\ProductPhotoInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductPhotoInquiryController extends Controller
{
    public function store(StoreProductPhotoInquiryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $imagePath = $request->file('image')->storePublicly('product-inquiries', $this->imageDisk());

        try {
            ProductPhotoInquiry::query()->create([
                'user_id' => $request->user()?->getKey(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'order_reference' => $data['order_reference'] ?? null,
                'type' => $data['type'],
                'message' => $data['message'] ?? null,
                'image_path' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            Storage::disk($this->imageDisk())->delete($imagePath);

            throw $exception;
        }

        return response()->json([
            'message' => 'Photo sent. Our team will review it and get back to you.',
        ], JsonResponse::HTTP_CREATED);
    }

    private function imageDisk(): string
    {
        return (string) config('filesystems.product_images_disk', 'public');
    }
}
