<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'title' => $this->resource['title'],
            'image' => $this->resource['image'],
            'store' => $this->resource['store'],
            'price' => $this->resource['price'],
            'quantity' => $this->resource['quantity'],
            'line_total' => $this->resource['line_total'],
        ];
    }
}
