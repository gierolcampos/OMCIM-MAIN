<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set president and vice president positions for superadmin users
        // For now, we'll just set the first superadmin as president and the rest as vice presidents
        $superadmins = DB::table('users')
            ->where('user_role', 'superadmin')
            ->orWhere('is_admin', 1)
            ->get();

        if ($superadmins->count() > 0) {
            // Set the first superadmin as president
            DB::table('users')
                ->where('id', $superadmins->first()->id)
                ->update(['officer_position' => 'president']);

            // Set the rest as vice presidents
            if ($superadmins->count() > 1) {
                $vicePresidentIds = $superadmins->slice(1)->pluck('id')->toArray();
                DB::table('users')
                    ->whereIn('id', $vicePresidentIds)
                    ->update(['officer_position' => 'vice_president']);
            }
        }

        // For now, we'll just distribute officer positions randomly among officers
        // In a real application, you would want to assign these based on actual roles
        $officers = DB::table('users')
            ->where('user_role', 'officer')
            ->where('is_admin', 0)
            ->get();

        $positions = ['secretary', 'pio', 'treasurer', 'auditor', 'business_manager'];
        
        foreach ($officers as $index => $officer) {
            $position = $positions[$index % count($positions)];
            DB::table('users')
                ->where('id', $officer->id)
                ->update(['officer_position' => $position]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all officer positions to 'none'
        DB::table('users')
            ->update(['officer_position' => 'none']);
    }
};
