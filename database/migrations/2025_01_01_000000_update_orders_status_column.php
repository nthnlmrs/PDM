<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Change enum to string to allow more flexible statuses like 'shipped'
            $table->string('status')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to enum if needed (be careful with data loss if invalid statuses exist)
            // We'll stick to string for safety in down() or just map known ones
            // For now, let's just reverse it potentially
            // $table->enum('status', ['pending', 'completed', 'refunded', 'disputed'])->change();
        });
    }
};
