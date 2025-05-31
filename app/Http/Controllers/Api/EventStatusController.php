<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventStatusController extends Controller
{
    /**
     * Update the status of events that have passed their end date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'events' => 'required|array',
                'events.*.id' => 'required|integer|exists:events,id',
                'events.*.title' => 'required|string',
                'events.*.endDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $events = $request->input('events');
            $updatedEvents = [];

            // Process each event
            foreach ($events as $eventData) {
                $event = Event::find($eventData['id']);

                // Skip if event not found or not in upcoming status
                if (!$event || $event->status !== 'upcoming') {
                    continue;
                }

                // Check if the event has actually ended
                $endDate = new \DateTime($event->end_date_time);
                $now = new \DateTime();

                if ($endDate < $now) {
                    // We'll keep the status as 'upcoming' in the database
                    // but we'll notify admins that it needs to be updated

                    // Add to updated events list
                    $updatedEvents[] = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'end_date' => $event->end_date_time,
                    ];

                    // Create notifications for admins
                    $this->notifyAdmins($event);
                }
            }

            return response()->json([
                'success' => true,
                'updated' => $updatedEvents,
                'isAdmin' => Auth::check() && Auth::user()->isAdmin(),
                'message' => count($updatedEvents) . ' events marked as pending'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update event status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update event status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create notifications for admin users about events that need status updates.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    private function notifyAdmins(Event $event)
    {
        // Get all admin users
        $admins = User::whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM'])
                      ->where('status', 'active')
                      ->get();

        foreach ($admins as $admin) {
            // Check if a notification for this event already exists
            $existingNotification = Notification::where('user_id', $admin->id)
                ->where('type', 'event_status')
                ->where('reference_id', $event->id)
                ->first();

            // Skip if notification already exists
            if ($existingNotification) {
                continue;
            }

            // Create a new notification
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'event_status',
                'reference_id' => $event->id,
                'title' => 'Event Status Update Required',
                'message' => 'The event "' . $event->title . '" has ended but is still marked as "Upcoming". Please update its status to "Completed" or "Cancelled".',
            ]);
        }
    }
}
