<?php

namespace App\Console\Commands;

use App\Models\Committee;
use App\Models\User;
use App\Models\SchoolCalendar;
use Illuminate\Console\Command;

class CreateCommittee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omcms:create-committee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new committee and assign members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating a new committee...');

        // Get committee details
        $name = $this->ask('Enter committee name:');
        $description = $this->ask('Enter committee description (optional):');
        
        // Get current school calendar
        $currentCalendarId = SchoolCalendar::getCurrentCalendarId();
        if (!$currentCalendarId) {
            $this->error('No active school calendar found. Please create one first.');
            return;
        }

        // Get all admin users as potential heads
        $potentialHeads = User::whereHas('role', function ($query) {
            $query->where('name', 'admin')->orWhere('name', 'superadmin');
        })->orderBy('lastname')->get();

        if ($potentialHeads->isEmpty()) {
            $this->error('No admin users found to assign as committee head.');
            return;
        }

        // Format the choices for committee head
        $headChoices = [];
        foreach ($potentialHeads as $user) {
            $headChoices[$user->id] = $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')';
        }
        $headChoices['none'] = 'No head at this time';

        // Ask for committee head
        $headId = $this->choice(
            'Select committee head:',
            $headChoices,
            'none'
        );

        // Create the committee
        $committee = Committee::create([
            'name' => $name,
            'description' => $description,
            'head_id' => ($headId !== 'none') ? $headId : null,
            'school_calendar_id' => $currentCalendarId,
            'is_active' => true,
        ]);

        $this->info('Committee created successfully!');

        // Ask if user wants to add members now
        if ($this->confirm('Do you want to add members to this committee now?', true)) {
            $this->addMembers($committee);
        }
    }

    /**
     * Add members to the committee.
     */
    protected function addMembers(Committee $committee)
    {
        // Get all active users as potential members
        $potentialMembers = User::where('status', 'active')
            ->orderBy('lastname')
            ->get();

        if ($potentialMembers->isEmpty()) {
            $this->error('No active users found to add as committee members.');
            return;
        }

        $this->info('Adding members to committee: ' . $committee->name);
        $this->info('Enter "done" when finished adding members.');

        $addedMembers = [];

        while (true) {
            // Format the choices for committee members
            $memberChoices = [];
            foreach ($potentialMembers as $user) {
                // Skip users who are already added
                if (in_array($user->id, $addedMembers)) {
                    continue;
                }
                $memberChoices[$user->id] = $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')';
            }
            $memberChoices['done'] = 'Done adding members';

            // If all members have been added, break the loop
            if (count($memberChoices) <= 1) {
                $this->info('All available users have been added to the committee.');
                break;
            }

            // Ask for committee member
            $memberId = $this->choice(
                'Select committee member to add:',
                $memberChoices,
                'done'
            );

            if ($memberId === 'done') {
                break;
            }

            // Ask for position within the committee
            $position = $this->ask('Enter position within the committee (optional):');

            // Add the member to the committee
            $committee->members()->attach($memberId, [
                'position' => $position,
            ]);

            $addedMembers[] = $memberId;
            $user = $potentialMembers->find($memberId);
            $this->info($user->firstname . ' ' . $user->lastname . ' added to the committee.');
        }

        $this->info('Committee members added successfully!');
    }
}
