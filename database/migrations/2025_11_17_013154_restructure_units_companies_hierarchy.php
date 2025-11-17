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
        // 1. إزالة company_id من جدول units
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        // 2. إضافة unit_id إلى جدول companies
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // 3. تعديل جدول branches: استبدال unit_id بـ company_id
        Schema::table('branches', function (Blueprint $table) {
            // حذف unit_id القديم
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            
            // إضافة company_id
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // 4. إضافة company_id إلى جدول accounts إذا كان موجوداً
        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('accounts', 'company_id')) {
                    $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
            });
        }

        // 5. إضافة company_id إلى جدول vouchers
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'company_id')) {
                    $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
            });
        }

        // 6. إضافة company_id إلى جدول journal_entries إذا كان موجوداً
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entries', 'company_id')) {
                    $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // عكس العمليات
        
        // إزالة company_id من journal_entries
        if (Schema::hasTable('journal_entries') && Schema::hasColumn('journal_entries', 'company_id')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        // إزالة company_id من vouchers
        if (Schema::hasTable('vouchers') && Schema::hasColumn('vouchers', 'company_id')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        // إزالة company_id من accounts
        if (Schema::hasTable('accounts') && Schema::hasColumn('accounts', 'company_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        // استعادة unit_id في branches
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->foreignId('unit_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // إزالة unit_id من companies
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });

        // استعادة company_id في units
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
