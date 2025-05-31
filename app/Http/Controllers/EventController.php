<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SchoolCalendar;
use App\Traits\HasBase64Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    use HasBase64Images;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Redirect to the custom calendar view
        return redirect()->route('events.custom-calendar');
    }

    /**
     * Display a listing of the resource for admin.
     */
    public function adminIndex(Request $request)
    {
        // Check if user can manage events
        if (!Auth::user()->canManageEvents()) {
            return redirect()->route('events.custom-calendar')
                ->with('error', 'You do not have permission to manage events.');
        }

        // Start with base query
        $query = Event::with('creator');

        // Apply academic year filter if provided, otherwise use current academic year
        if ($request->filled('school_calendar_id')) {
            // If "All Academic Years" is selected (empty value), don't apply any filter
            if ($request->input('school_calendar_id') !== '') {
                $query->where('school_calendar_id', $request->input('school_calendar_id'));
            }
        } else {
            // Default to current academic year
            $query->currentAcademicYear();
        }

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Get all events and sort them by date (newest first)
        $events = $query
            ->orderBy('start_date_time', 'desc')
            ->paginate(10);

        // Get all school calendars for the filter dropdown
        $schoolCalendars = \App\Models\SchoolCalendar::orderBy('created_at', 'desc')->get();
        $currentCalendar = \App\Models\SchoolCalendar::getCurrentCalendar();

        // Pass a flag to the view to indicate this is the admin view
        return view('events.index', compact('events', 'schoolCalendars', 'currentCalendar'))
            ->with('isAdminView', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user can manage events
        if (!Auth::user()->canManageEvents()) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to create events.');
        }

        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug the incoming request
        Log::info('Event store method called');
        Log::info('Request data: ' . json_encode($request->all()));

        // Debug authentication status
        Log::info('Auth check: ' . (Auth::check() ? 'true' : 'false'));
        if (Auth::check()) {
            Log::info('User ID: ' . Auth::id());
            Log::info('Is Admin: ' . (Auth::user()->isAdmin() ? 'true' : 'false'));
        }

        // Check if user is admin
        if (!Auth::check()) {
            Log::warning('User not authenticated');
            return redirect('/login')
                ->with('error', 'You must be logged in to create events.');
        }

        if (!Auth::user()->isAdmin()) {
            Log::warning('Non-admin user attempted to create event: ' . Auth::id());
            return redirect('/omcms/events')
                ->with('error', 'You do not have permission to create events.');
        }

        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'event_type' => ['nullable', 'string', 'max:100'],
                'start_date_time' => ['required', 'date'],
                'end_date_time' => ['required', 'date', 'after_or_equal:start_date_time'],
                'location' => ['required', 'string', 'max:255'],
                'location_details' => ['nullable', 'string', 'max:255'],
                'status' => ['required', Rule::in(['upcoming', 'completed', 'cancelled'])],
                'notes' => ['nullable', 'string'],
                'event_image' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
            ]);

            // Log the validated data for debugging
            Log::info('Validated event data: ' . json_encode($validated));

            // Try a direct database insert to bypass any model issues
            try {
                Log::info('Attempting direct database insert');
                // Get the current school calendar ID
                $currentCalendarId = \App\Models\SchoolCalendar::getCurrentCalendarId();

                $eventId = DB::table('events')->insertGetId([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'event_type' => $validated['event_type'] ?? null,
                    'start_date_time' => $validated['start_date_time'],
                    'end_date_time' => $validated['end_date_time'],
                    'location' => $validated['location'],
                    'location_details' => $validated['location_details'] ?? null,
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                    'school_calendar_id' => $currentCalendarId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Direct database insert successful with ID: ' . $eventId);

                // Handle image upload
                if ($request->hasFile('event_image')) {
                    $image = $request->file('event_image');

                    // Convert image to base64 and store in a file
                    $base64FilePath = $this->convertToBase64($image, 'base64/events');

                    if ($base64FilePath) {
                        // Update the event with the path to the base64 file
                        DB::table('events')
                            ->where('id', $eventId)
                            ->update(['image_path' => $base64FilePath]);

                        Log::info('Image converted to base64 and stored in file: ' . $base64FilePath);
                    } else {
                        Log::error('Failed to convert image to base64');
                    }
                }

                // Get the event object for related operations
                $event = Event::find($eventId);

                if ($event) {
                    // Create default evaluation questions
                    $this->createDefaultEvaluationQuestions($event);

                    // Create notifications for all users
                    \App\Http\Controllers\NotificationController::createEventNotification($event);
                }

                // Stay on the create page and show success message
                return redirect()->back()
                    ->with('success', 'Event created successfully.');

            } catch (\Exception $dbException) {
                Log::error('Exception in direct database insert: ' . $dbException->getMessage());
                Log::error('DB Exception trace: ' . $dbException->getTraceAsString());

                // Fall back to Eloquent if direct DB insert fails
                Log::info('Falling back to Eloquent model creation');

                // Create event using Eloquent
                $event = new Event();
                $event->title = $validated['title'];
                $event->description = $validated['description'] ?? null;
                $event->event_type = $validated['event_type'] ?? null;
                $event->start_date_time = $validated['start_date_time'];
                $event->end_date_time = $validated['end_date_time'];
                $event->location = $validated['location'];
                $event->location_details = $validated['location_details'] ?? null;
                $event->status = $validated['status'];
                $event->notes = $validated['notes'] ?? null;
                $event->created_by = Auth::id();
                $event->school_calendar_id = \App\Models\SchoolCalendar::getCurrentCalendarId();

                // Save the event
                $saved = $event->save();
                Log::info('Eloquent save result: ' . ($saved ? 'success' : 'failure'));

                if (!$saved) {
                    throw new \Exception('Failed to save event using Eloquent');
                }

                // Create default evaluation questions
                $this->createDefaultEvaluationQuestions($event);

                // Handle image upload
                if ($request->hasFile('event_image')) {
                    $image = $request->file('event_image');

                    // Convert image to base64 and store in a file
                    $base64FilePath = $this->convertToBase64($image, 'base64/events');

                    if ($base64FilePath) {
                        // Update the event with the path to the base64 file
                        $event->image_path = $base64FilePath;
                        $event->save();

                        Log::info('Image converted to base64 and stored in file for Eloquent method: ' . $base64FilePath);
                    } else {
                        Log::error('Failed to convert image to base64 for Eloquent method');
                    }
                }

                Log::info('Event created successfully with ID: ' . $event->id);

                // Stay on the create page and show success message
                return redirect()->back()
                    ->with('success', 'Event created successfully.');
            }

        } catch (\Exception $e) {
            Log::error('Exception in event creation: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to edit events.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to update events.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'start_date_time' => ['required', 'date'],
            'end_date_time' => ['required', 'date', 'after_or_equal:start_date_time'],
            'location' => ['required', 'string', 'max:255'],
            'location_details' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['upcoming', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'event_image' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
        ]);

        // Handle image upload
        if ($request->hasFile('event_image')) {
            // Delete old image if exists and it's not a base64 file
            if ($event->image_path && !$this->isBase64File($event->image_path)) {
                $oldImagePath = public_path($event->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            } else if ($event->image_path && $this->isBase64File($event->image_path)) {
                // Delete the old base64 file
                $oldBase64Path = public_path($event->image_path);
                if (file_exists($oldBase64Path)) {
                    unlink($oldBase64Path);
                }
            }

            $image = $request->file('event_image');

            // Convert image to base64 and store in a file
            $base64FilePath = $this->convertToBase64($image, 'base64/events');

            if ($base64FilePath) {
                $validated['image_path'] = $base64FilePath;
                Log::info('Image converted to base64 and stored in file: ' . $base64FilePath);
            } else {
                Log::error('Failed to convert image to base64');
            }
        }

        if (!$event->update($validated)) {
            // Log the error if the update fails
            Log::error('Failed to update event: ' . json_encode($validated));
            return redirect()->back()
                ->with('error', 'Failed to update event. Please try again.')
                ->withInput();
        }

        // Redirect to the omcms.events route instead of events.index
        return redirect()->route('omcms.events')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to delete events.');
        }

        try {
            // Delete the image file if it exists
            if ($event->image_path) {
                $imagePath = public_path($event->image_path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted event image: ' . $imagePath);
                }
            }

            $event->delete();
            return redirect()->route('omcms.events')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete event: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete event. Please try again.');
        }
    }

    /**
     * Display a calendar view of events.
     */
    public function calendar()
    {
        $events = Event::query()
            // First order by status (upcoming first, then completed)
            ->orderByRaw("CASE WHEN status = 'upcoming' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            // For upcoming events, sort by start_date_time (soonest first)
            ->orderBy('start_date_time', 'asc')
            ->get();

        return view('events.calendar', compact('events'));
    }

    /**
     * Display a custom calendar view of events.
     */
    public function customCalendar(Request $request)
    {
        // Start with base query
        $query = Event::query();

        // Apply academic year filter if provided, otherwise use current academic year
        if ($request->filled('school_calendar_id')) {
            // If "All Academic Years" is selected (empty value), don't apply any filter
            if ($request->input('school_calendar_id') !== '') {
                $query->where('school_calendar_id', $request->input('school_calendar_id'));
            }
        } else {
            // Default to current academic year
            $query->currentAcademicYear();
        }

        // Check if we should filter for pending events only
        $showPendingOnly = $request->has('pending_only') && $request->input('pending_only') === 'true';

        // If pending_only is set, filter for events that are pending
        if ($showPendingOnly) {
            $query->where('status', 'upcoming')
                  ->where('end_date_time', '<', now());
        }

        // Get all events and sort them by date (newest first for upcoming events)
        $events = $query
            // First order by status (upcoming first, then completed)
            ->orderByRaw("CASE WHEN status = 'upcoming' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            // For upcoming events, sort by start_date_time (soonest first)
            ->orderBy('start_date_time', 'asc')
            ->get();

        // Log the events for debugging
        Log::info('Events for custom calendar: ' . $events->count());

        // Get all school calendars for the filter dropdown (admin only)
        $schoolCalendars = null;
        $currentCalendar = \App\Models\SchoolCalendar::getCurrentCalendar();

        if (Auth::check() && Auth::user()->isAdmin()) {
            $schoolCalendars = \App\Models\SchoolCalendar::orderBy('created_at', 'desc')->get();
        }

        return view('events.custom-calendar', compact('events', 'schoolCalendars', 'currentCalendar', 'showPendingOnly'));
    }

    /**
     * Display a list of pending events.
     */
    public function pendingEvents(Request $request)
    {
        // Start with base query
        $query = Event::query();

        // Apply academic year filter if provided, otherwise use current academic year
        if ($request->filled('school_calendar_id')) {
            // If "All Academic Years" is selected (empty value), don't apply any filter
            if ($request->input('school_calendar_id') !== '') {
                $query->where('school_calendar_id', $request->input('school_calendar_id'));
            }
        } else {
            // Default to current academic year
            $query->currentAcademicYear();
        }

        // Filter for events that are pending (upcoming but end date has passed)
        $query->where('status', 'upcoming')
              ->where('end_date_time', '<', now());

        // Get all pending events and sort them by end date (most recently ended first)
        $events = $query
            ->orderBy('end_date_time', 'desc')
            ->get();

        // Log the events for debugging
        Log::info('Pending events: ' . $events->count());

        // Get all school calendars for the filter dropdown (admin only)
        $schoolCalendars = null;
        $currentCalendar = \App\Models\SchoolCalendar::getCurrentCalendar();

        if (Auth::check() && Auth::user()->isAdmin()) {
            $schoolCalendars = \App\Models\SchoolCalendar::orderBy('created_at', 'desc')->get();
        }

        // Set flag to indicate we're showing pending events only
        $showPendingOnly = true;

        return view('events.pending', compact('events', 'schoolCalendars', 'currentCalendar', 'showPendingOnly'));
    }



    /**
     * Export events to iCal format.
     */
    public function exportIcal()
    {
        $events = Event::query()
            // First order by status (upcoming first, then completed)
            ->orderByRaw("CASE WHEN status = 'upcoming' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            // For upcoming events, sort by start_date_time (soonest first)
            ->orderBy('start_date_time', 'asc')
            ->get();

        $calendar = "BEGIN:VCALENDAR\r\n";
        $calendar .= "VERSION:2.0\r\n";
        $calendar .= "PRODID:-//ICSSOC//Club Management System//EN\r\n";
        $calendar .= "CALSCALE:GREGORIAN\r\n";
        $calendar .= "METHOD:PUBLISH\r\n";

        foreach ($events as $event) {
            $calendar .= "BEGIN:VEVENT\r\n";
            $calendar .= "UID:" . $event->id . "@clubmanagementsystem.com\r\n";
            $calendar .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $calendar .= "DTSTART:" . $event->start_date_time->format('Ymd\THis\Z') . "\r\n";
            $calendar .= "DTEND:" . $event->end_date_time->format('Ymd\THis\Z') . "\r\n";
            $calendar .= "SUMMARY:" . $event->title . "\r\n";

            if ($event->description) {
                $calendar .= "DESCRIPTION:" . str_replace("\n", "\\n", $event->description) . "\r\n";
            }

            if ($event->location) {
                $calendar .= "LOCATION:" . $event->location;
                if ($event->location_details) {
                    $calendar .= " - " . $event->location_details;
                }
                $calendar .= "\r\n";
            }

            $calendar .= "STATUS:" . strtoupper($event->status) . "\r\n";
            $calendar .= "END:VEVENT\r\n";
        }

        $calendar .= "END:VCALENDAR";

        return response($calendar)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="events.ics"');
    }

    /**
     * Add functionality for users to RSVP to events.
     */
    public function attend(Request $request, Event $event)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:attending,not_attending'],
            'comment' => ['nullable', 'string', 'max:255'],
        ]);

        $event->attendances()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'status' => $validated['status'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Your attendance status has been updated.');
    }

    /**
     * Show attendees for an event.
     */
    public function attendees(Event $event)
    {
        // Temporarily bypass authorization for testing
        // $this->authorize('viewAttendees', $event);

        $attendees = $event->attendances()->with('user')->get();

        return view('events.attendees', compact('event', 'attendees'));
    }

    /**
     * Toggle the evaluation status for an event.
     */
    public function toggleEvaluation(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to toggle evaluation status.');
        }

        try {
            // Get the previous state to check if it's being opened
            $wasOpen = $event->evaluation_open;

            // Toggle the evaluation status
            $event->evaluation_open = !$event->evaluation_open;
            $event->save();

            $status = $event->evaluation_open ? 'opened' : 'closed';

            // If the evaluation was closed and is now open, create notifications
            if (!$wasOpen && $event->evaluation_open) {
                \App\Http\Controllers\NotificationController::createEvaluationNotification($event);
            }

            return redirect()->route('events.show', $event)
                ->with('success', "Evaluation has been {$status} for this event.");
        } catch (\Exception $e) {
            Log::error('Failed to toggle evaluation status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update evaluation status. Please try again.');
        }
    }

    /**
     * Create default evaluation questions for an event.
     */
    private function createDefaultEvaluationQuestions($event)
    {
        try {
            // Check if the event already has questions
            if ($event->evaluationQuestions()->count() > 0) {
                return; // Skip if questions already exist
            }

            // Define default questions
            $defaultQuestions = [
                [
                    'question_text' => 'How would you rate the overall quality of this event?',
                    'question_type' => 'rating',
                    'is_required' => true,
                    'display_order' => 1,
                ],
                [
                    'question_text' => 'How relevant and engaging was the content presented during the event?',
                    'question_type' => 'rating',
                    'is_required' => true,
                    'display_order' => 2,
                ],
                [
                    'question_text' => 'How enjoyable and engaging were the activities during the event?',
                    'question_type' => 'rating',
                    'is_required' => true,
                    'display_order' => 3,
                ],
                [
                    'question_text' => 'Do you have concerns that you want to raise about this event?',
                    'question_type' => 'text',
                    'is_required' => true,
                    'display_order' => 4,
                ],
                [
                    'question_text' => 'Please provide any feedback or suggestion for the improvement of our events.',
                    'question_type' => 'text',
                    'is_required' => true,
                    'display_order' => 5,
                ],
            ];

            // Create the questions
            foreach ($defaultQuestions as $question) {
                $event->evaluationQuestions()->create($question);
            }

            Log::info("Created default evaluation questions for event ID: {$event->id}");
        } catch (\Exception $e) {
            Log::error("Failed to create default evaluation questions: {$e->getMessage()}");
        }
    }
}
