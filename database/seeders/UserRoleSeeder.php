<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$now = now();
		$password = Hash::make('password123');

		$users = [
			[
				'firstname' => 'Super',
				'lastname' => 'Admin',
				'email' => 'superadmin@example.com',
				'user_role' => 'super_admin',
			],
			[
				'firstname' => 'Finance',
				'lastname' => 'Admin',
				'email' => 'finance@example.com',
				'user_role' => 'finance_admin',
			],
			[
				'firstname' => 'Operations',
				'lastname' => 'Admin',
				'email' => 'operations@example.com',
				'user_role' => 'operations_admin',
			],
			[
				'firstname' => 'Mod',
				'lastname' => 'Erator',
				'email' => 'moderator@example.com',
				'user_role' => 'moderator',
			],
			[
				'firstname' => 'Regular',
				'lastname' => 'Member',
				'email' => 'member@example.com',
				'user_role' => 'member',
			],
		];

		$counter = 1;
		foreach ($users as $data) {
			DB::table('users')->updateOrInsert(
				['email' => $data['email']],
				[
					'firstname' => $data['firstname'],
					'lastname' => $data['lastname'],
					'middlename' => null,
					'suffix' => null,
					'studentnumber' => str_pad((string)$counter, 6, '0', STR_PAD_LEFT),
					'course' => 'BSCS',
					'major' => null,
					'year' => 3,
					'section' => 'A',
					'mobile_no' => '0912345678' . ($counter % 10),
					'user_role' => $data['user_role'],
					'password' => $password,
					'email_verified_at' => $now,
					'created_at' => $now,
					'updated_at' => $now,
				]
			);
			$counter++;
		}
	}
}


