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
        Schema::table('users', function (Blueprint $table) {
            // Add officer_position column as an enum with the different officer positions
            $table->enum('officer_position', [
                'president',
                'vice_president',
                'secretary',
                'pio',
                'treasurer',
                'auditor',
                'business_manager',
                'none'
            ])->default('none')->after('user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('officer_position');
        });
    }
};
