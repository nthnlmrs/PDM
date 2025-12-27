<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('status');
            $table->string('shipping_phone')->nullable()->after('shipping_name');
            $table->text('shipping_address')->nullable()->after('shipping_phone');
            $table->string('tracking_number')->nullable()->after('shipping_address');
            $table->timestamp('buyer_confirmed_at')->nullable()->after('tracking_number');
            $table->foreignId('voucher_id')->nullable()->after('buyer_confirmed_at')->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0)->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['shipping_name', 'shipping_phone', 'shipping_address', 'tracking_number', 'buyer_confirmed_at', 'voucher_id', 'discount_amount']);
        });
    }
};
