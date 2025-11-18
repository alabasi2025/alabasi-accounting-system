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
        Schema::table('clearing_transactions', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('status')->comment('تاريخ الترحيل');
            $table->unsignedBigInteger('synced_by')->nullable()->after('synced_at')->comment('المستخدم الذي قام بالترحيل');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clearing_transactions', function (Blueprint $table) {
            $table->dropColumn(['synced_at', 'synced_by']);
        });
    }
};
