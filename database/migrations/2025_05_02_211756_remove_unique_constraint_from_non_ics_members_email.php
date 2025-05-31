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
        try {
            Schema::table('non_ics_members', function (Blueprint $table) {
                $table->dropUnique('non_ics_members_email_unique');
            });
        } catch (\Exception $e) {
            // If the index doesn't exist, just continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_ics_members', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
