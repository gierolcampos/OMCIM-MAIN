<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // superadmin, admin, member
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles - align with existing user_role enum
        DB::table('roles')->insert([
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'President and Vice President with full access to all parts of the system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'officer',
                'display_name' => 'Officer',
                'description' => 'Organization officers with limited, scoped permissions based on their position',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'member',
                'display_name' => 'Member',
                'description' => 'General users with basic access',
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
        Schema::dropIfExists('roles');
    }
};
