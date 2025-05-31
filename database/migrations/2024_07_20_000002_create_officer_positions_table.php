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
        Schema::create('officer_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('can_manage_events')->default(false);
            $table->boolean('can_manage_announcements')->default(false);
            $table->boolean('can_manage_payments')->default(false);
            $table->timestamps();
        });

        // Insert default officer positions with their permissions
        DB::table('officer_positions')->insert([
            [
                'name' => 'president',
                'display_name' => 'President',
                'description' => 'Organization President',
                'can_manage_events' => true,
                'can_manage_announcements' => true,
                'can_manage_payments' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'vice_president',
                'display_name' => 'Vice President',
                'description' => 'Organization Vice President',
                'can_manage_events' => true,
                'can_manage_announcements' => true,
                'can_manage_payments' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'secretary',
                'display_name' => 'Secretary',
                'description' => 'Organization Secretary',
                'can_manage_events' => true,
                'can_manage_announcements' => true,
                'can_manage_payments' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pio',
                'display_name' => 'Public Information Officer',
                'description' => 'Organization Public Information Officer',
                'can_manage_events' => true,
                'can_manage_announcements' => true,
                'can_manage_payments' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'treasurer',
                'display_name' => 'Treasurer',
                'description' => 'Organization Treasurer',
                'can_manage_events' => false,
                'can_manage_announcements' => false,
                'can_manage_payments' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'auditor',
                'display_name' => 'Auditor',
                'description' => 'Organization Auditor',
                'can_manage_events' => false,
                'can_manage_announcements' => false,
                'can_manage_payments' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'business_manager',
                'display_name' => 'Business Manager',
                'description' => 'Organization Business Manager',
                'can_manage_events' => false,
                'can_manage_announcements' => false,
                'can_manage_payments' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officer_positions');
    }
};
