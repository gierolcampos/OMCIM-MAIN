<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\OfficerPosition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignOfficerPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omcms:assign-officer-positions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign officer positions to existing admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to assign officer positions to admin users...');

        // Get all admin users
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $superadminRoleId = Role::where('name', 'superadmin')->value('id');

        $adminUsers = User::where(function ($query) use ($adminRoleId, $superadminRoleId) {
            $query->where('role_id', $adminRoleId)
                  ->orWhere('role_id', $superadminRoleId)
                  ->orWhereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']);
        })->get();

        if ($adminUsers->isEmpty()) {
            $this->info('No admin users found.');
            return;
        }

        $this->info('Found ' . $adminUsers->count() . ' admin users.');

        // Get all officer positions
        $officerPositions = OfficerPosition::all();
        $positionChoices = $officerPositions->pluck('display_name', 'id')->toArray();

        // Add option for superadmin
        $positionChoices['superadmin'] = 'Superadmin (President/Vice President)';

        foreach ($adminUsers as $user) {
            $this->info('');
            $this->info('User: ' . $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')');

            // Ask for the officer position
            $positionId = $this->choice(
                'Select officer position for this user:',
                $positionChoices,
                null
            );

            // Handle superadmin selection
            if ($positionId === 'superadmin') {
                // Set user as superadmin
                $user->role_id = $superadminRoleId;
                $user->save();

                $this->info('User set as Superadmin.');
                continue;
            }

            // Set the officer position (using new role system)
            $user->role_id = $adminRoleId;
            // Note: officer_position_id is no longer used in the new role system
            $user->save();

            $position = $officerPositions->find($positionId);
            $this->info('User assigned as ' . $position->display_name . '.');
        }

        $this->info('');
        $this->info('Officer positions assignment completed!');
    }
}
