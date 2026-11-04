<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'vendor_id',
    'name',
    'slug',
    'description',
    'category',
    'price',
    'compare_price',
    'stock',
    'image_path',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function catalogKey(): string
    {
        return 'vendor-'.$this->slug;
    }

    public function imageUrl(): string
    {
        $diskName = (string) config('filesystems.product_images_disk', 'public');
        $disk = Storage::disk($diskName);

        $isMissingLocalFile = config("filesystems.disks.{$diskName}.driver") === 'local'
            && ! $disk->exists((string) $this->image_path);

        if (blank($this->image_path) || $isMissingLocalFile) {
            return asset('images/product-placeholder.svg');
        }

        return $disk->url($this->image_path);
    }

    public function discountPercentage(): int
    {
        if ($this->compare_price === null || $this->compare_price <= $this->price) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
