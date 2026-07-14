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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        // Set default academic year to active one
        $activeYear = \Illuminate\Support\Facades\DB::table('academic_years')->where('is_active', true)->first();
        if ($activeYear) {
            \Illuminate\Support\Facades\DB::table('payments')->update(['academic_year_id' => $activeYear->id]);
            \Illuminate\Support\Facades\DB::table('expenses')->update(['academic_year_id' => $activeYear->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
