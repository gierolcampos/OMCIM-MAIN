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
        Schema::table('users', function (Blueprint $table) {
            // Add role_id and officer_position_id columns
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles');
            $table->foreignId('officer_position_id')->nullable()->after('role_id')->constrained('officer_positions');

            // We'll keep the existing user_role and is_admin columns for backward compatibility
            // but they will be deprecated in favor of the new role system
        });

        // Migrate existing users to the new role system
        // Get the role IDs
        $memberRoleId = DB::table('roles')->where('name', 'member')->value('id');
        $officerRoleId = DB::table('roles')->where('name', 'officer')->value('id');
        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->value('id');

        // Update regular members
        DB::table('users')
            ->where('user_role', 'member')
            ->update(['role_id' => $memberRoleId]);

        // Update officers
        DB::table('users')
            ->where('user_role', 'officer')
            ->update(['role_id' => $officerRoleId]);

        // Update admin users to superadmin (for now, all is_admin=true will be set as superadmins)
        // You can manually adjust this later if needed
        DB::table('users')
            ->where('is_admin', true)
            ->update(['role_id' => $superadminRoleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['officer_position_id']);
            $table->dropColumn(['role_id', 'officer_position_id']);
        });
    }
};
