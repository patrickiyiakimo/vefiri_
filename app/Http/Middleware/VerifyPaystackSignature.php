<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaystackSignature
{
    /**
     * Verify that the webhook payload was signed with our Paystack secret key.
     *
     * Paystack signs the raw request body with HMAC-SHA256 and sends the
     * hexadecimal digest in the X-Paystack-Signature header.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = (string) config('paystack.secret_key');
        $signature = (string) $request->header('X-Paystack-Signature');

        if ($secretKey === '' || $signature === '') {
            abort(401, 'Missing Paystack signature.');
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $secretKey);

        if (! hash_equals($computedSignature, $signature)) {
            abort(401, 'Invalid Paystack signature.');
        }

        return $next($request);
    }
}
