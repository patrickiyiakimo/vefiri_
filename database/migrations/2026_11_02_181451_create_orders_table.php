<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 32)->nullable();

            // All monetary columns are stored in the currency subunit (kobo) to
            // match Paystack's API and avoid floating point rounding errors.
            $table->unsignedBigInteger('total');

            $table->string('payment_reference')->nullable()->unique();
            $table->string('status')->default(OrderStatus::Pending->value)->index();
            $table->string('idempotency_key')->unique();
            $table->text('checkout_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
