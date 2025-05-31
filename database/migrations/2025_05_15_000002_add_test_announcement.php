<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Announcement;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if there are any announcements
        if (Announcement::count() === 0) {
            // Get an admin user
            $admin = User::where('is_admin', true)->first();
            
            if ($admin) {
                // Create a test announcement
                Announcement::create([
                    'title' => 'Welcome to the ICS Organization Management System',
                    'content' => "Welcome to the Integrated Computer Society Organization Management System!\n\nThis platform is designed to help manage all aspects of our organization, including events, announcements, payments, and more.\n\nStay tuned for more updates and announcements.",
                    'status' => 'published',
                    'priority' => 'high',
                    'created_by' => $admin->id,
                    'is_boosted' => true,
                ]);
                
                // Create a second announcement
                Announcement::create([
                    'title' => 'Getting Started with the ICS-OMCMS',
                    'content' => "Here are some tips to get started with the ICS Organization Management System:\n\n1. Update your profile information\n2. Check the events calendar for upcoming activities\n3. Stay updated with announcements\n4. Make payments for membership and events\n\nIf you have any questions, please contact the ICS officers.",
                    'status' => 'published',
                    'priority' => 'normal',
                    'created_by' => $admin->id,
                    'is_boosted' => false,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to remove the test announcements
    }
};
