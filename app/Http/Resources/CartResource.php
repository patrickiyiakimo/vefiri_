<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => CartItemResource::collection($this->resource['items']),
            'count' => $this->resource['count'],
            'subtotal' => $this->resource['subtotal'],
        ];
    }
}
