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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->enum('entry_type', ['normal', 'inter_company_transfer', 'inter_unit_transfer'])
                  ->default('normal')
                  ->after('description')
                  ->comment('نوع القيد: عادي، تحويل بين مؤسسات، تحويل بين وحدات');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn('entry_type');
        });
    }
};
