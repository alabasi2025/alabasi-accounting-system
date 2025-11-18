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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name_ar', 200);
            $table->string('name_en', 200)->nullable();
            $table->unsignedBigInteger('account_id'); // الحساب المرتبط في الدليل
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->decimal('salary', 15, 2)->default(0);
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict');
            
            // Indexes
            $table->index('code');
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
