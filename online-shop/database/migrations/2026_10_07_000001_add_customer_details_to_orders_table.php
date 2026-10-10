<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('city')->nullable()->after('shipping_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->default('Nepal')->after('postal_code');
            $table->text('notes')->nullable()->after('country');

            $table->index('customer_email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_email']);
            $table->dropIndex(['phone']);

            $table->dropColumn([
                'customer_email',
                'city',
                'state',
                'postal_code',
                'country',
                'notes',
            ]);
        });
    }
};
