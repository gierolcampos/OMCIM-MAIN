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
        Schema::table('non_ics_members', function (Blueprint $table) {
            // Add a field for student ID or registration number (optional)
            if (!Schema::hasColumn('non_ics_members', 'student_id')) {
                $table->string('student_id')->nullable()->after('fullname');
            }

            // Add a field for department or college
            if (!Schema::hasColumn('non_ics_members', 'department')) {
                $table->string('department')->nullable()->after('course_year_section');
            }

            // Add a field for additional contact information
            if (!Schema::hasColumn('non_ics_members', 'alternative_email')) {
                $table->string('alternative_email')->nullable()->after('email');
            }

            // Add a field for address
            if (!Schema::hasColumn('non_ics_members', 'address')) {
                $table->text('address')->nullable()->after('mobile_no');
            }

            // Add a field for payment status tracking
            if (!Schema::hasColumn('non_ics_members', 'payment_status')) {
                $table->enum('payment_status', ['Paid', 'Pending', 'None'])->default('None');
            }

            // Add a field for membership type
            if (!Schema::hasColumn('non_ics_members', 'membership_type')) {
                $table->string('membership_type')->nullable()->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_ics_members', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'department',
                'alternative_email',
                'address',
                'payment_status',
                'membership_type',
                'membership_expiry'
            ]);
        });
    }
};
