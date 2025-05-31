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
        Schema::table('announcements', function (Blueprint $table) {
            // Add is_boosted column to track pinned/boosted announcements
            if (!Schema::hasColumn('announcements', 'is_boosted')) {
                $table->boolean('is_boosted')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove the is_boosted column if it exists
            if (Schema::hasColumn('announcements', 'is_boosted')) {
                $table->dropColumn('is_boosted');
            }
        });
    }
};
