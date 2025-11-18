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
        Schema::table('branches', function (Blueprint $table) {
            // إضافة عمود unit_id بعد company_id
            $table->unsignedBigInteger('unit_id')->after('company_id')->nullable();
            
            // إضافة foreign key constraint
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // حذف foreign key أولاً
            $table->dropForeign(['unit_id']);
            
            // ثم حذف العمود
            $table->dropColumn('unit_id');
        });
    }
};
