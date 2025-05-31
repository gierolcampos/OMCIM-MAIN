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
        Schema::create('school_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('school_calendar_desc');
            $table->string('school_calendar_short_desc');
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
        });

        // Insert default school calendar
        DB::table('school_calendars')->insert([
            'school_calendar_desc' => '1ST SEMESTER ACADEMIC YEAR 2024-2025',
            'school_calendar_short_desc' => '1ST SEM A.Y. 2024-2025',
            'is_selected' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};
