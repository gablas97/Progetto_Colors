<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Mittente (sempre Colors S.r.l.)
            $table->string('sender_name')->default('Colors S.r.l.');
            $table->string('sender_address')->nullable();
            $table->string('sender_city')->nullable();
            $table->string('sender_province', 2)->nullable();
            $table->string('sender_postal_code')->nullable();

            // Destinatario
            $table->string('recipient_name');
            $table->string('recipient_address');
            $table->string('recipient_city');
            $table->string('recipient_province', 2);
            $table->string('recipient_postal_code');
            $table->string('recipient_country')->default('IT');

            // Trasporto
            $table->enum('shipping_method', ['corriere', 'ritiro', 'proprio_mezzo'])->default('corriere');
            $table->string('carrier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->integer('packages_count')->default(1);
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->string('appearance')->nullable()->comment('Aspetto esteriore beni');
            $table->enum('reason', ['vendita', 'reso', 'conto_lavorazione', 'omaggio', 'riparazione', 'altro'])->default('vendita');

            $table->dateTime('shipping_date');
            $table->dateTime('delivery_date')->nullable();
            $table->enum('status', ['draft', 'ready', 'shipped', 'delivered'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transport_document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_document_id')->constrained('transport_documents')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('description');
            $table->integer('quantity');
            $table->string('unit')->default('pz');
            $table->decimal('weight', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_document_items');
        Schema::dropIfExists('transport_documents');
    }
};
