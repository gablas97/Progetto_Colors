<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('source', ['online', 'negozio'])->default('online')->after('status');
            $table->enum('return_status', ['none', 'requested', 'approved', 'received', 'refunded', 'rejected'])->default('none')->after('source');
            $table->text('return_reason')->nullable()->after('return_status');
            $table->dateTime('return_requested_at')->nullable()->after('return_reason');
            $table->dateTime('return_completed_at')->nullable()->after('return_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'return_status', 'return_reason', 'return_requested_at', 'return_completed_at']);
        });
    }
};
