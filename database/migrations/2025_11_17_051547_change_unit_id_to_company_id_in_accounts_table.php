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
            // Drop the old unique index
            $table->dropUnique('unique_unit_code');
            $table->dropIndex(['unit_id']);
            
            // Rename column
            $table->renameColumn('unit_id', 'company_id');
        });
        
        Schema::table('accounts', function (Blueprint $table) {
            // Add new unique index
            $table->unique(['company_id', 'code'], 'unique_company_code');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Drop the new unique index
            $table->dropUnique('unique_company_code');
            $table->dropIndex(['company_id']);
            
            // Rename column back
            $table->renameColumn('company_id', 'unit_id');
        });
        
        Schema::table('accounts', function (Blueprint $table) {
            // Add old unique index back
            $table->unique(['unit_id', 'code'], 'unique_unit_code');
            $table->index('unit_id');
        });
    }
};
