<?php

use App\Enums\VendorPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('plan')->default(VendorPlan::Free->value)->index();
            $table->timestamp('premium_started_at')->nullable();
            $table->timestamp('premium_until')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['plan']);
            $table->dropColumn(['plan', 'premium_started_at', 'premium_until']);
        });
    }
};
