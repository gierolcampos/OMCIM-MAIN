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
            // Add media_path column for storing uploaded images and videos
            $table->string('media_path')->nullable()->after('image_path');
            
            // Add is_boosted column to track boosted announcements
            $table->boolean('is_boosted')->default(false)->after('status');
            
            // Update priority column to allow 'normal' value (instead of just low, medium, high)
            $table->dropColumn('priority');
            $table->enum('priority', ['low', 'medium', 'high', 'normal'])->default('normal')->after('is_boosted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove the added columns
            $table->dropColumn('media_path');
            $table->dropColumn('is_boosted');
            
            // Restore original priority column
            $table->dropColumn('priority');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
        });
    }
};
