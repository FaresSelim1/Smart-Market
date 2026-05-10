<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'original_price' => (float) $this->price,
            'current_price' => (float) $this->current_price,
            'is_on_flash_sale' => $this->on_flash_sale,
            'category' => $this->category?->name,
            'images' => $this->images->map(fn($img) => [
                'url' => Storage::disk('public')->url($img->path),
            ]),
            'stock' => $this->whenPivotLoaded('branch_product', function () {
                return (int) $this->pivot->stock_level;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}