<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Raw gateway payload / verification details (JSON).
            $table->json('meta')->nullable()->after('paid_at');
            // Uploaded bank-transfer receipt image (storage path).
            $table->string('proof_path')->nullable()->after('meta');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['meta', 'proof_path']);
        });
    }
};
