<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_address')->after('customer_phone');
            $table->string('delivery_address_line_2')->nullable()->after('delivery_address');
            $table->string('delivery_city')->after('delivery_address_line_2');
            $table->string('delivery_state')->after('delivery_city');
            $table->string('delivery_postal_code', 20)->nullable()->after('delivery_state');
            $table->text('delivery_notes')->nullable()->after('delivery_postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_address',
                'delivery_address_line_2',
                'delivery_city',
                'delivery_state',
                'delivery_postal_code',
                'delivery_notes',
            ]);
        });
    }
};
