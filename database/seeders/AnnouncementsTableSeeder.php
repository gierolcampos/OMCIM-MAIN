<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AnnouncementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user or create one if none exists
        $admin = User::whereIn('user_role', ['superadmin', 'officer'])->first();

        if (!$admin) {
            // Create a default admin user if none exists
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

        // Create sample announcements
        $announcements = [
            [
                'title' => 'Welcome to the ICS Organization Management System',
                'content' => "Welcome to the Integrated Computer Society Organization Management System!\n\nThis platform is designed to help manage all aspects of our organization, including events, announcements, payments, and more.\n\nStay tuned for more updates and announcements.",
                'status' => 'published',
                'priority' => 'high',
                'publish_date' => Carbon::now()->subDays(7),
                'expiry_date' => Carbon::now()->addMonths(3),
                'created_by' => $admin->id,
                'is_boosted' => true,
                'text_color' => '#c21313', // Using the main color mentioned in memories
            ],
            [
                'title' => 'Getting Started with the ICS-OMCMS',
                'content' => "Here are some tips to get started with the ICS Organization Management System:\n\n1. Update your profile information\n2. Check the events calendar for upcoming activities\n3. Stay updated with announcements\n4. Make payments for membership and events\n\nIf you have any questions, please contact the ICS officers.",
                'status' => 'published',
                'priority' => 'normal',
                'publish_date' => Carbon::now()->subDays(5),
                'expiry_date' => Carbon::now()->addMonths(2),
                'created_by' => $admin->id,
                'is_boosted' => false,
                'text_color' => null,
            ],
            [
                'title' => 'Upcoming Membership Drive',
                'content' => "We are excited to announce our upcoming membership drive for the new academic year!\n\nJoin the Integrated Computer Society and enjoy benefits such as:\n\n- Access to exclusive workshops and seminars\n- Networking opportunities with industry professionals\n- Participation in coding competitions\n- Discounts on ICS merchandise\n\nMembership fees can be paid through GCash or Cash. Visit the payments section for more details.",
                'status' => 'published',
                'priority' => 'high',
                'publish_date' => Carbon::now()->subDays(2),
                'expiry_date' => Carbon::now()->addMonths(1),
                'created_by' => $admin->id,
                'is_boosted' => true,
                'text_color' => '#c21313',
            ],
            [
                'title' => 'ICS Technical Workshop Series',
                'content' => "The ICS Technical Workshop Series is starting next week!\n\nTopics include:\n\n1. Web Development Fundamentals\n2. Mobile App Development\n3. Database Management\n4. Cloud Computing\n5. Cybersecurity Basics\n\nWorkshops will be held every Saturday from 9:00 AM to 12:00 PM at the Computer Laboratory. Registration is required and limited slots are available.",
                'status' => 'published',
                'priority' => 'medium',
                'publish_date' => Carbon::now(),
                'expiry_date' => Carbon::now()->addWeeks(3),
                'created_by' => $admin->id,
                'is_boosted' => false,
                'text_color' => null,
            ],
            [
                'title' => 'ICS Merchandise Now Available',
                'content' => "ICS merchandise is now available for purchase!\n\nItems include:\n\n- ICS T-shirts (S, M, L, XL)\n- ICS Hoodies\n- ICS Tumblers\n- ICS Lanyards\n- ICS Stickers\n\nVisit the ICS office to place your orders or contact the merchandise committee through our official Facebook page.",
                'status' => 'draft',
                'priority' => 'normal',
                'publish_date' => Carbon::now()->addDays(5),
                'expiry_date' => Carbon::now()->addMonths(2),
                'created_by' => $admin->id,
                'is_boosted' => false,
                'text_color' => null,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
