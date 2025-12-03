<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// Temporarily expand enum to include both old and new values
		DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('superadmin','Secretary','Treasurer','Auditor','PIO','BM','officer','super_admin','finance_admin','operations_admin','moderator','member') NOT NULL DEFAULT 'member'");

		// Map existing roles to new RBAC roles
		// - superadmin -> super_admin
		// - Treasurer/Auditor/BM -> finance_admin
		// - Secretary/PIO -> operations_admin
		// - moderator remains moderator if it exists (future-proof), otherwise unchanged until enum change
		// - any other value not in the new set will be set to member
		DB::statement("
			UPDATE users
			SET user_role = CASE
				WHEN user_role = 'superadmin' THEN 'super_admin'
				WHEN user_role IN ('Treasurer','Auditor','BM') THEN 'finance_admin'
				WHEN user_role IN ('Secretary','PIO') THEN 'operations_admin'
				WHEN user_role IN ('super_admin','finance_admin','operations_admin','moderator','member') THEN user_role
				ELSE 'member'
			END
		");

		// Now restrict to the new ENUM set with default 'member'
		DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('super_admin','finance_admin','operations_admin','moderator','member') NOT NULL DEFAULT 'member'");
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// Expand to include both old and new values so mapping back is allowed
		DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('superadmin','Secretary','Treasurer','Auditor','PIO','BM','officer','super_admin','finance_admin','operations_admin','moderator','member') NOT NULL DEFAULT 'member'");

		// Map back to previous values best-effort
		DB::statement("
			UPDATE users
			SET user_role = CASE
				WHEN user_role = 'super_admin' THEN 'superadmin'
				WHEN user_role = 'finance_admin' THEN 'Treasurer'
				WHEN user_role = 'operations_admin' THEN 'Secretary'
				WHEN user_role IN ('moderator','member') THEN user_role
				ELSE 'member'
			END
		");

		// Restrict back to the old known set
		DB::statement("ALTER TABLE users MODIFY COLUMN user_role ENUM('superadmin','Secretary','Treasurer','Auditor','PIO','BM','member') NOT NULL DEFAULT 'member'");
	}
};


