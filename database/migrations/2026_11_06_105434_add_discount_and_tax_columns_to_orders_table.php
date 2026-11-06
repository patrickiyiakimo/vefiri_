<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('subtotal')->default(0)->after('delivery_notes');
            $table->unsignedBigInteger('discount_total')->default(0)->after('subtotal');
            $table->unsignedBigInteger('taxable_amount')->default(0)->after('discount_total');
            $table->unsignedBigInteger('tax_total')->default(0)->after('taxable_amount');
            $table->unsignedBigInteger('shipping_total')->default(0)->after('tax_total');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('shipping_total');
            $table->boolean('tax_after_discount')->default(true)->after('tax_rate');
            $table->foreignId('coupon_id')->nullable()->after('tax_after_discount')->constrained()->nullOnDelete();
            $table->string('coupon_code', 50)->nullable()->after('coupon_id');
        });

        DB::table('orders')->update(['subtotal' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'subtotal',
                'discount_total',
                'taxable_amount',
                'tax_total',
                'shipping_total',
                'tax_rate',
                'tax_after_discount',
                'coupon_code',
            ]);
        });
    }
};
