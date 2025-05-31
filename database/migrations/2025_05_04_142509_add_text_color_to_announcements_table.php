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
            // Add text_color column for storing custom text colors
            $table->string('text_color')->nullable()->after('media_path');
            
            // Remove is_boosted column as this feature is being removed
            $table->dropColumn('is_boosted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove the added column
            $table->dropColumn('text_color');
            
            // Add back is_boosted column for rollback
            $table->boolean('is_boosted')->default(false)->after('status');
        });
    }
};
