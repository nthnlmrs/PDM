<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_name')->nullable();
            $table->text('store_description')->nullable();
        });

        Schema::table('seller_requests', function (Blueprint $table) {
            $table->string('store_name');
            $table->text('store_description');
            $table->text('store_address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_description']);
        });

        Schema::table('seller_requests', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_description', 'store_address']);
        });
    }
};
