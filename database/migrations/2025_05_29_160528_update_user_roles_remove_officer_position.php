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
        // First, update the user_role enum to include the new values (keeping officer temporarily)
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN user_role ENUM('superadmin', 'officer', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM', 'member')
            NOT NULL DEFAULT 'member'
        ");

        // Then, migrate existing officer data to the new role system
        DB::statement("
            UPDATE users
            SET user_role = CASE
                WHEN officer_position = 'secretary' THEN 'Secretary'
                WHEN officer_position = 'treasurer' THEN 'Treasurer'
                WHEN officer_position = 'auditor' THEN 'Auditor'
                WHEN officer_position = 'pio' THEN 'PIO'
                WHEN officer_position = 'business_manager' THEN 'BM'
                WHEN officer_position = 'president' THEN 'superadmin'
                WHEN officer_position = 'vice_president' THEN 'superadmin'
                WHEN user_role = 'officer' AND officer_position IS NULL THEN 'Secretary'
                ELSE user_role
            END
            WHERE user_role = 'officer'
        ");

        // Drop the officer_position column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('officer_position');
        });

        // Finally, remove 'officer' from the enum since it's no longer needed
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN user_role ENUM('superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM', 'member')
            NOT NULL DEFAULT 'member'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the officer_position column
        Schema::table('users', function (Blueprint $table) {
            $table->enum('officer_position', [
                'president', 'vice_president', 'secretary', 'treasurer',
                'auditor', 'pio', 'business_manager'
            ])->nullable()->after('user_role');
        });

        // Migrate data back to officer role with positions
        DB::statement("
            UPDATE users
            SET
                user_role = 'officer',
                officer_position = CASE
                    WHEN user_role = 'Secretary' THEN 'secretary'
                    WHEN user_role = 'Treasurer' THEN 'treasurer'
                    WHEN user_role = 'Auditor' THEN 'auditor'
                    WHEN user_role = 'PIO' THEN 'pio'
                    WHEN user_role = 'BM' THEN 'business_manager'
                    ELSE NULL
                END
            WHERE user_role IN ('Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM')
        ");

        // Restore the original user_role enum
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN user_role ENUM('superadmin', 'officer', 'member')
            NOT NULL DEFAULT 'member'
        ");
    }
};
