<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'expired'])->default('pending');
            $table->string('qris_reference')->nullable();
            $table->string('qris_checkout_url')->nullable();
            $table->timestamp('qris_expired_at')->nullable();
            $table->timestamps();

            $table->index(['payment_status', 'qris_expired_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
