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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            
            // Snapshot dati prodotto al momento ordine
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('variant_name')->nullable();
            $table->string('product_image')->nullable();
            
            $table->decimal('price', 10, 2); // Prezzo unitario al momento ordine
            $table->integer('quantity');
            $table->decimal('vat_rate', 5, 2); // IVA applicata
            $table->decimal('subtotal', 10, 2); // price * quantity
            $table->decimal('tax_amount', 10, 2); // IVA su questo item
            $table->decimal('total', 10, 2); // Totale con IVA
            
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
