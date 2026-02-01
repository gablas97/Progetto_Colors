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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable(); // Coupon code
            $table->enum('type', ['percentage', 'fixed', 'shipping']); // Tipo sconto
            $table->decimal('value', 10, 2); // Valore sconto
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Importo minimo ordine
            $table->integer('usage_limit')->nullable(); // Limite utilizzi totali
            $table->integer('usage_count')->default(0); // Contatore utilizzi
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index(['is_active', 'starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
