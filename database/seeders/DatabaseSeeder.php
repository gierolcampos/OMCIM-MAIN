<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Legacy seeders
        $this->call(UsersTableSeeder::class);

        // Call the EventsTableSeeder
        $this->call(EventsTableSeeder::class);

        // Call the AnnouncementsTableSeeder
        $this->call(AnnouncementsTableSeeder::class);

        // Call the CompletedEventsSeeder
        $this->call(CompletedEventsSeeder::class);

        // Call the PaymentFeeSeeder
        $this->call(PaymentFeeSeeder::class);

        // New RBAC sample users
        $this->call(UserRoleSeeder::class);
    }
}
