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
        Schema::table('accounts', function (Blueprint $table) {
            // إضافة حقل طبيعة الحساب
            $table->enum('account_nature', [
                'general',      // حساب عام
                'cash_box',     // صندوق
                'bank',         // بنك
                'customer',     // عميل
                'supplier',     // مورد
                'employee',     // موظف
                'debtor',       // مدين
                'creditor'      // دائن
            ])->default('general')->after('account_type_id');
            
            // إضافة index للبحث السريع
            $table->index('account_nature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex(['account_nature']);
            $table->dropColumn('account_nature');
        });
    }
};
