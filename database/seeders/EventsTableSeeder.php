<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user or create one if none exists
        $admin = User::whereIn('user_role', ['superadmin', 'officer'])->first();

        if (!$admin) {
            $admin = User::create([
                'studentnumber' => '000001',
                'firstname' => 'Admin',
                'lastname' => 'User',
                'course' => 'BSIT',
                'year' => 4,
                'section' => 'A',
                'mobile_no' => '09123456789',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'user_role' => 'superadmin',
                'status' => 'active',
            ]);
        }

        // Create sample events
        $events = [
            [
                'title' => 'General Assembly',
                'description' => 'Annual general assembly for all club members',
                'event_type' => 'Club meeting',
                'start_date_time' => now()->addDays(7)->setHour(10)->setMinute(0),
                'end_date_time' => now()->addDays(7)->setHour(12)->setMinute(0),
                'location' => 'Main Auditorium',
                'location_details' => 'Building A, 2nd Floor',
                'status' => 'upcoming',
                'notes' => 'Please bring your ID cards',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Web Development Workshop',
                'description' => 'Learn the basics of web development with HTML, CSS, and JavaScript',
                'event_type' => 'Technical workshop',
                'start_date_time' => now()->addDays(14)->setHour(13)->setMinute(0),
                'end_date_time' => now()->addDays(14)->setHour(16)->setMinute(0),
                'location' => 'Computer Lab',
                'location_details' => 'Building B, Room 101',
                'status' => 'upcoming',
                'notes' => 'Bring your laptops',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Programming Contest',
                'description' => 'Annual programming contest for all club members',
                'event_type' => 'Competition',
                'start_date_time' => now()->addDays(21)->setHour(9)->setMinute(0),
                'end_date_time' => now()->addDays(21)->setHour(17)->setMinute(0),
                'location' => 'Computer Lab',
                'location_details' => 'Building B, Room 102',
                'status' => 'upcoming',
                'notes' => 'Teams of 3 members',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Alumni Networking Night',
                'description' => 'Network with alumni and industry professionals',
                'event_type' => 'Networking',
                'start_date_time' => now()->addDays(28)->setHour(18)->setMinute(0),
                'end_date_time' => now()->addDays(28)->setHour(21)->setMinute(0),
                'location' => 'Function Hall',
                'location_details' => 'Building C, 3rd Floor',
                'status' => 'upcoming',
                'notes' => 'Business casual attire',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Club Orientation',
                'description' => 'Orientation for new club members',
                'event_type' => 'Club meeting',
                'start_date_time' => now()->subDays(14)->setHour(14)->setMinute(0),
                'end_date_time' => now()->subDays(14)->setHour(16)->setMinute(0),
                'location' => 'Main Auditorium',
                'location_details' => 'Building A, 2nd Floor',
                'status' => 'completed',
                'notes' => 'Attendance is mandatory for new members',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
