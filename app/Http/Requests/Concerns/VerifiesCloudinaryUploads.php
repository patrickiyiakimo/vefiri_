<?php

namespace App\Http\Requests\Concerns;

use App\Services\CloudinaryProductImages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait VerifiesCloudinaryUploads
{
    protected CloudinaryProductImages $cloudinaryImages;

    protected function verifyCloudinaryUpload(Validator $validator, bool $required): void
    {
        $fields = ['image_token', 'image_version', 'image_signature'];

        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $hasAny = false;

        foreach ($fields as $field) {
            if ($this->filled($field)) {
                $hasAny = true;
            }
        }

        if (! $required && ! $hasAny) {
            return;
        }

        if (! $this->filled('image_token') || ! $this->filled('image_version') || ! $this->filled('image_signature')) {
            $validator->errors()->add('image_token', 'The image upload is incomplete. Please choose the image again.');

            return;
        }

        $user = $this->user();

        if ($user === null) {
            return;
        }

        try {
            $publicId = $this->cloudinaryImages->verifyUpload(
                (string) $this->input('image_token'),
                (int) $this->input('image_version'),
                (string) $this->input('image_signature'),
                (int) $user->vendor()->firstOrFail()->getKey(),
            );
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $validator->errors()->add($field, $message);
                }
            }

            return;
        }

        $this->attributes->set('product_image_public_id', $publicId);
    }
}
