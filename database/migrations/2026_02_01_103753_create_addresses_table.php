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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['shipping', 'billing', 'both'])->default('both');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('vat_number')->nullable(); // P.IVA per fattura
            $table->string('tax_code')->nullable(); // Codice fiscale
            $table->string('address');
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('province', 2); // Sigla provincia
            $table->string('postal_code');
            $table->string('country')->default('IT');
            $table->string('phone')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
