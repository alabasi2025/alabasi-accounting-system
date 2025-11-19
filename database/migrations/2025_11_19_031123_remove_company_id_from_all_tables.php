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
        // حذف عمود company_id من جدول accounts
        if (Schema::hasColumn('accounts', 'company_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود company_id من جدول account_types
        if (Schema::hasColumn('account_types', 'company_id')) {
            Schema::table('account_types', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود company_id من جدول analytical_accounts
        if (Schema::hasColumn('analytical_accounts', 'company_id')) {
            Schema::table('analytical_accounts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود company_id من جدول analytical_account_types
        if (Schema::hasColumn('analytical_account_types', 'company_id')) {
            Schema::table('analytical_account_types', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود company_id من جدول branches
        if (Schema::hasColumn('branches', 'company_id')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود company_id من جدول vouchers
        if (Schema::hasColumn('vouchers', 'company_id')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // حذف عمود deleted_at من جدول accounts
        if (Schema::hasColumn('accounts', 'deleted_at')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة عمود company_id لجدول accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('unit_id');
            $table->timestamp('deleted_at')->nullable();
        });

        // إعادة عمود company_id لجدول account_types
        Schema::table('account_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // إعادة عمود company_id لجدول analytical_accounts
        Schema::table('analytical_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // إعادة عمود company_id لجدول analytical_account_types
        Schema::table('analytical_account_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // إعادة عمود company_id لجدول branches
        Schema::table('branches', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        // إعادة عمود company_id لجدول vouchers
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('unit_id');
        });
    }
};
