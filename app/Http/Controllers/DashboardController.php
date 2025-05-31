<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\SchoolCalendar;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        // Get all school calendars for the filter dropdown
        $schoolCalendars = SchoolCalendar::orderBy('created_at', 'desc')->get();

        // Get current academic year
        $currentCalendar = SchoolCalendar::where('is_selected', true)->first();

        // Get the selected calendar ID from the request or use the current calendar
        $selectedCalendarId = $request->input('calendar_id', $currentCalendar ? $currentCalendar->id : null);

        // Get the selected calendar
        $selectedCalendar = $selectedCalendarId ? SchoolCalendar::find($selectedCalendarId) : $currentCalendar;

        // Get recent announcements filtered by the selected calendar
        $announcements = Announcement::with('creator')
            ->when($selectedCalendarId, function($query) use ($selectedCalendarId) {
                return $query->where('school_calendar_id', $selectedCalendarId);
            })
            ->orderBy('is_boosted', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get pending events (events that have ended but still marked as upcoming)
        $events = Event::where('status', 'upcoming')
            ->where('end_date_time', '<', now())
            ->when($selectedCalendarId, function($query) use ($selectedCalendarId) {
                return $query->where('school_calendar_id', $selectedCalendarId);
            })
            ->orderBy('end_date_time', 'desc')
            ->get();

        // Get stats counts based on the selected calendar
        $memberCount = User::count();

        $eventCount = Event::when($selectedCalendarId, function($query) use ($selectedCalendarId) {
                return $query->where('school_calendar_id', $selectedCalendarId);
            })->count();

        $announcementCount = Announcement::when($selectedCalendarId, function($query) use ($selectedCalendarId) {
                return $query->where('school_calendar_id', $selectedCalendarId);
            })->count();

        // Get count of deletion requests
        $deletionRequestsCount = User::whereNotNull('deletion_requested_at')->count();

        return view('dashboard.index', compact(
            'announcements',
            'events',
            'currentCalendar',
            'schoolCalendars',
            'selectedCalendar',
            'memberCount',
            'eventCount',
            'announcementCount',
            'deletionRequestsCount'
        ));
    }

    /**
     * Display member statistics.
     */
    public function memberStats()
    {
        // Get all users for statistics
        $users = User::all();

        return view('dashboard.member_stats', compact('users'));
    }

    /**
     * Display event statistics.
     */
    public function eventStats()
    {
        // Get all events for statistics
        $events = Event::all();

        return view('dashboard.event_stats', compact('events'));
    }
}
