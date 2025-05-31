<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the user_role enum to include 'superadmin'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('officer', 'member', 'superadmin') NOT NULL DEFAULT 'member'");

        // Update users who are is_admin=1 to have user_role='superadmin'
        DB::table('users')
            ->where('is_admin', 1)
            ->update(['user_role' => 'superadmin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert superadmin users back to officer role
        DB::table('users')
            ->where('user_role', 'superadmin')
            ->update(['user_role' => 'officer']);

        // Revert the enum back to original values
        DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('officer', 'member') NOT NULL DEFAULT 'member'");
    }
};
