<?php

namespace App\Models;

use App\Enums\ProductPhotoInquiryType;
use Database\Factories\ProductPhotoInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'customer_name',
    'customer_email',
    'order_reference',
    'type',
    'message',
    'image_path',
])]
class ProductPhotoInquiry extends Model
{
    /** @use HasFactory<ProductPhotoInquiryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ProductPhotoInquiryType::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
