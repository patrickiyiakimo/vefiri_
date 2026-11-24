<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final class CloudinaryProductImages
{
    public function isConfigured(): bool
    {
        return filled(config('services.cloudinary.cloud'))
            && filled(config('services.cloudinary.key'))
            && filled(config('services.cloudinary.secret'));
    }

    public function signUpload(int $vendorId): array
    {
        $this->ensureConfigured();

        $token = (string) Str::uuid();
        $publicId = $this->publicId($vendorId);
        $timestamp = now()->timestamp;
        $signature = $this->sign([
            'public_id' => $publicId,
            'timestamp' => (string) $timestamp,
        ]);

        Cache::put(
            $this->cacheKey($token),
            ['vendor_id' => $vendorId, 'public_id' => $publicId],
            now()->addMinutes((int) config('services.cloudinary.signature_ttl_minutes', 55)),
        );

        return [
            'token' => $token,
            'url' => $this->endpoint('image/upload'),
            'publicId' => $publicId,
            'apiKey' => (string) config('services.cloudinary.key'),
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
    }

    public function verifyUpload(string $token, int $version, string $signature, int $vendorId): string
    {
        $this->ensureConfigured();

        $intent = Cache::pull($this->cacheKey($token));

        if (! is_array($intent) || ($intent['vendor_id'] ?? null) !== $vendorId) {
            throw ValidationException::withMessages([
                'image_token' => 'This image upload has expired. Please choose the image again.',
            ]);
        }

        $publicId = (string) ($intent['public_id'] ?? '');
        $expected = $this->sign([
            'public_id' => $publicId,
            'version' => (string) $version,
        ]);

        if (! hash_equals($expected, $signature)) {
            throw ValidationException::withMessages([
                'image_signature' => 'The uploaded image could not be verified. Please choose the image again.',
            ]);
        }

        return $publicId;
    }

    public static function isManagedPath(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        return str_starts_with($path, self::prefix().'/');
    }

    public static function deliveryUrl(string $publicId): string
    {
        $cloud = rawurlencode((string) config('services.cloudinary.cloud'));
        $transformations = trim((string) config('services.cloudinary.delivery_transformations'));
        $path = collect(explode('/', $publicId))
            ->map(fn (string $segment): string => rawurlencode($segment))
            ->implode('/');

        $base = "https://res.cloudinary.com/{$cloud}/image/upload";

        return $transformations === ''
            ? "{$base}/{$path}"
            : "{$base}/{$transformations}/{$path}";
    }

    public function delete(?string $path): void
    {
        if (! $this->isManagedPath($path) || ! $this->isConfigured()) {
            return;
        }

        $timestamp = now()->timestamp;
        $parameters = [
            'invalidate' => 'true',
            'public_id' => (string) $path,
            'timestamp' => (string) $timestamp,
        ];

        try {
            $response = Http::asMultipart()
                ->connectTimeout((int) config('services.cloudinary.connect_timeout', 3))
                ->timeout((int) config('services.cloudinary.timeout', 15))
                ->retry(2, 250, fn (Throwable $exception): bool => $exception instanceof ConnectionException)
                ->post($this->endpoint('image/destroy'), [
                    ...$parameters,
                    'api_key' => (string) config('services.cloudinary.key'),
                    'signature' => $this->sign($parameters),
                ]);

            if (! $response->successful()) {
                Log::warning('Cloudinary image deletion failed.', [
                    'public_id' => $path,
                    'status' => $response->status(),
                    'error' => $response->json('error.message'),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Cloudinary image deletion failed.', [
                'public_id' => $path,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw ValidationException::withMessages([
                'image' => 'Image uploads are not configured. Please contact support.',
            ]);
        }
    }

    private function publicId(int $vendorId): string
    {
        return self::prefix().'/'.$vendorId.'/'.Str::uuid();
    }

    private static function prefix(): string
    {
        return trim((string) config('services.cloudinary.public_id_prefix', 'cloudinary/vendor-products'), '/');
    }

    private function cacheKey(string $token): string
    {
        return 'cloudinary-upload:'.hash('sha256', $token);
    }

    private function sign(array $parameters): string
    {
        ksort($parameters);

        $toSign = collect($parameters)
            ->map(fn (string $value, string $key): string => "{$key}={$value}")
            ->implode('&');

        return sha1($toSign.(string) config('services.cloudinary.secret'));
    }

    private function endpoint(string $action): string
    {
        $base = rtrim((string) config('services.cloudinary.api_base', 'https://api.cloudinary.com'), '/');
        $cloud = rawurlencode((string) config('services.cloudinary.cloud'));

        return "{$base}/v1_1/{$cloud}/{$action}";
    }
}
