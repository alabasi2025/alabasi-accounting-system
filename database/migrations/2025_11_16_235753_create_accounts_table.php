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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('code', 50);
            $table->string('name_ar', 200);
            $table->string('name_en', 200)->nullable();
            $table->unsignedBigInteger('account_type_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('level')->default(1);
            $table->boolean('is_parent')->default(false);
            $table->boolean('allow_posting')->default(true);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->unique(['unit_id', 'code'], 'unique_unit_code');
            $table->index('code');
            $table->index('unit_id');
            $table->index('parent_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
