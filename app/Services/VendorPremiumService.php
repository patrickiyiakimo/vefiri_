<?php

namespace App\Services;

use App\Enums\VendorPlan;
use App\Enums\VendorPremiumPaymentStatus;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VendorPremiumService
{
    public function __construct(private readonly PaystackPaymentService $payments) {}

    public function initialize(Vendor $vendor, string $idempotencyKey, int $quantity): VendorPremiumPayment
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Choose how many extra products you want to buy.',
            ]);
        }

        $amount = $quantity * (int) config('vendor.product_slots.price');

        try {
            return DB::transaction(function () use ($vendor, $idempotencyKey, $quantity, $amount): VendorPremiumPayment {
                $existing = VendorPremiumPayment::query()
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();

                if ($existing !== null) {
                    $this->ensurePaymentBelongsToVendor($existing, $vendor);

                    return $existing;
                }

                $payment = $vendor->premiumPayments()->create([
                    'reference' => 'VEF_SLOT_'.Str::ulid()->toBase32(),
                    'amount' => $amount,
                    'quantity' => $quantity,
                    'currency' => (string) config('vendor.product_slots.currency', 'NGN'),
                    'status' => VendorPremiumPaymentStatus::Pending,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $payment->update([
                    'authorization_url' => $this->payments->initializePremiumPayment($vendor, $payment),
                ]);

                return $payment;
            });
        } catch (UniqueConstraintViolationException $exception) {
            $payment = VendorPremiumPayment::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($payment === null) {
                throw $exception;
            }

            $this->ensurePaymentBelongsToVendor($payment, $vendor);

            return $payment;
        }
    }

    public function confirm(VendorPremiumPayment $payment): bool
    {
        if ($payment->isPaid()) {
            return true;
        }

        if (! $this->payments->verifyTransaction($payment->reference, $payment->amount, $payment->currency)) {
            return false;
        }

        return $this->activate($payment);
    }

    public function markFailed(VendorPremiumPayment $payment): void
    {
        DB::transaction(function () use ($payment): void {
            $lockedPayment = VendorPremiumPayment::query()
                ->whereKey($payment->getKey())
                ->lockForUpdate()
                ->first();

            if ($lockedPayment === null || $lockedPayment->isPaid()) {
                return;
            }

            $lockedPayment->update([
                'status' => VendorPremiumPaymentStatus::Failed,
            ]);
        });
    }

    private function activate(VendorPremiumPayment $payment): bool
    {
        return DB::transaction(function () use ($payment): bool {
            $lockedPayment = VendorPremiumPayment::query()
                ->whereKey($payment->getKey())
                ->lockForUpdate()
                ->first();

            if ($lockedPayment === null) {
                return false;
            }

            if ($lockedPayment->isPaid()) {
                return true;
            }

            $vendor = Vendor::query()
                ->whereKey($lockedPayment->vendor_id)
                ->lockForUpdate()
                ->first();

            if ($vendor === null) {
                return false;
            }

            $activatedAt = now();

            $lockedPayment->update([
                'status' => VendorPremiumPaymentStatus::Paid,
                'paid_at' => $activatedAt,
            ]);

            $vendor->update([
                'plan' => VendorPlan::Premium,
                'premium_started_at' => $vendor->premium_started_at ?? $activatedAt,
                'premium_until' => null,
                'product_slots' => $vendor->product_slots + (int) $lockedPayment->quantity,
            ]);

            return true;
        });
    }

    private function ensurePaymentBelongsToVendor(VendorPremiumPayment $payment, Vendor $vendor): void
    {
        if ((string) $payment->vendor_id !== (string) $vendor->getKey()) {
            throw ValidationException::withMessages([
                'idempotency_key' => 'This idempotency key has already been used.',
            ]);
        }
    }
}
