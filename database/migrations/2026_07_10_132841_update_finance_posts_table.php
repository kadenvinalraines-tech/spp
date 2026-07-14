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
        Schema::table('finance_posts', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('finance_posts', function (Blueprint $table) {
            $table->string('type')->after('name');
            $table->decimal('default_amount', 15, 2)->default(0)->after('type');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('default_amount');
        });
    }

    public function down(): void
    {
        Schema::table('finance_posts', function (Blueprint $table) {
            $table->dropColumn(['type', 'default_amount', 'status']);
        });

        Schema::table('finance_posts', function (Blueprint $table) {
            $table->enum('type', ['monthly', 'one-time'])->default('monthly')->after('name');
        });
    }
};
