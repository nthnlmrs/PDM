<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->text('reason');
            $table->string('image_proof')->nullable();
            // pending: waiting seller, rejected: seller rejected, approved: seller approved, escalated: buyer appealed to admin, resolved: admin decided
            $table->enum('status', ['pending', 'rejected', 'approved', 'escalated', 'resolved'])->default('pending');
            $table->text('seller_response')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
