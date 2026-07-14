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
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id']);
        });

        // Migrate data
        $activeYear = \Illuminate\Support\Facades\DB::table('academic_years')->where('is_active', true)->first();
        if ($activeYear) {
            $students = \Illuminate\Support\Facades\DB::table('students')->whereNotNull('class_id')->get();
            foreach ($students as $student) {
                \Illuminate\Support\Facades\DB::table('student_classes')->insert([
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'academic_year_id' => $activeYear->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete();
        });

        $activeYear = \Illuminate\Support\Facades\DB::table('academic_years')->where('is_active', true)->first();
        if ($activeYear) {
            $studentClasses = \Illuminate\Support\Facades\DB::table('student_classes')->where('academic_year_id', $activeYear->id)->get();
            foreach ($studentClasses as $sc) {
                \Illuminate\Support\Facades\DB::table('students')
                    ->where('id', $sc->student_id)
                    ->update(['class_id' => $sc->class_id]);
            }
        }

        Schema::dropIfExists('student_classes');
    }
};
