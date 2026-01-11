<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('total');
            $table->text('refund_reason')->nullable()->after('refunded_amount');
            $table->timestamp('cancelled_at')->nullable()->after('refund_reason');
            $table->timestamp('refunded_at')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refunded_amount', 'refund_reason', 'cancelled_at', 'refunded_at']);
        });
    }
};
