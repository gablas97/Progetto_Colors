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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating'); // 1-5 stelle
            $table->string('title', 100)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false); // Acquisto verificato
            $table->boolean('is_approved')->default(false); // Moderazione admin
            $table->integer('helpful_count')->default(0); // Contatore "Utile"
            $table->timestamps();
            
            // Un utente può recensire un prodotto solo una volta
            $table->unique(['product_id', 'user_id']);
            $table->index(['product_id', 'is_approved']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
