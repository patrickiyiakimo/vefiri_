<?php

namespace App\Mail\Concerns;

trait UsesVefiriBrand
{
    /**
     * @return array<string, string>
     */
    protected function brandData(): array
    {
        return [
            'brandName' => (string) config('app.name', 'Vefiri'),
            'brandUrl' => (string) config('app.url'),
            'brandHost' => (string) (parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'vefiri.com'),
            'supportEmail' => (string) config('vefiri.support_email', 'support@vefiri.com'),
        ];
    }
}
