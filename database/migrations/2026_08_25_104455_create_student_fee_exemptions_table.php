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
        Schema::create('student_fee_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('finance_post_id')->constrained('finance_posts')->cascadeOnDelete();
            $table->timestamps();
            
            // Mencegah duplikasi pengecualian untuk pos yang sama pada siswa yang sama
            $table->unique(['student_id', 'finance_post_id'], 'student_fee_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_exemptions');
    }
};
