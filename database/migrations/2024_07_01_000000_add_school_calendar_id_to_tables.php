<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolCalendar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the current selected school calendar
        $currentCalendar = SchoolCalendar::where('is_selected', true)->first();
        $calendarId = $currentCalendar ? $currentCalendar->id : null;

        // Add school_calendar_id to announcements table
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('school_calendar_id')->nullable()->after('created_by')->constrained('school_calendars');
        });

        // Add school_calendar_id to events table
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('school_calendar_id')->nullable()->after('created_by')->constrained('school_calendars');
        });

        // Add school_calendar_id to cash_payments table
        Schema::table('cash_payments', function (Blueprint $table) {
            $table->foreignId('school_calendar_id')->nullable()->after('user_id')->constrained('school_calendars');
        });

        // Add school_calendar_id to gcash_payments table
        Schema::table('gcash_payments', function (Blueprint $table) {
            $table->foreignId('school_calendar_id')->nullable()->after('user_id')->constrained('school_calendars');
        });

        // Add school_calendar_id to non_ics_members table
        Schema::table('non_ics_members', function (Blueprint $table) {
            $table->foreignId('school_calendar_id')->nullable()->after('id')->constrained('school_calendars');
        });

        // Update existing records to use the current school calendar
        if ($calendarId) {
            DB::table('announcements')->update(['school_calendar_id' => $calendarId]);
            DB::table('events')->update(['school_calendar_id' => $calendarId]);
            DB::table('cash_payments')->update(['school_calendar_id' => $calendarId]);
            DB::table('gcash_payments')->update(['school_calendar_id' => $calendarId]);
            DB::table('non_ics_members')->update(['school_calendar_id' => $calendarId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['school_calendar_id']);
            $table->dropColumn('school_calendar_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['school_calendar_id']);
            $table->dropColumn('school_calendar_id');
        });

        Schema::table('cash_payments', function (Blueprint $table) {
            $table->dropForeign(['school_calendar_id']);
            $table->dropColumn('school_calendar_id');
        });

        Schema::table('gcash_payments', function (Blueprint $table) {
            $table->dropForeign(['school_calendar_id']);
            $table->dropColumn('school_calendar_id');
        });

        Schema::table('non_ics_members', function (Blueprint $table) {
            $table->dropForeign(['school_calendar_id']);
            $table->dropColumn('school_calendar_id');
        });
    }
};
