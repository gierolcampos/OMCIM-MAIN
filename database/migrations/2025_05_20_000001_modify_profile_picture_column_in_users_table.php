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
        Schema::table('users', function (Blueprint $table) {
            // First drop the existing column
            $table->dropColumn('profile_picture');
        });

        Schema::table('users', function (Blueprint $table) {
            // Then add it back as a text column to store base64 data
            $table->text('profile_picture')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // First drop the text column
            $table->dropColumn('profile_picture');
        });

        Schema::table('users', function (Blueprint $table) {
            // Then add it back as a string column
            $table->string('profile_picture')->nullable()->after('email');
        });
    }
};
