<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Event;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the latest 3 published announcements
        $announcements = Announcement::with('creator')
            ->published()
            ->orderBy('is_boosted', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get the nearest upcoming event as the featured event
        // Only include events that are in the near future (start date is after current date)
        $featuredEvent = Event::where('status', 'upcoming')
            ->where('start_date_time', '>', now()) // Only future events
            ->orderBy('start_date_time', 'asc') // Soonest first
            ->first();

        // Get the most recently completed events (latest first)
        // Only include events that have already passed (end date is before current date)
        $completedEvents = Event::where('status', 'completed')
            ->where('end_date_time', '<', now()) // Only events that have already ended
            ->orderBy('end_date_time', 'desc') // Most recently completed first
            ->take(5) // Show 5 completed events
            ->get();

        // If no completed events, check for events with 'upcoming' status but past end date (pending events)
        if ($completedEvents->isEmpty()) {
            $completedEvents = Event::where('status', 'upcoming')
                ->where('end_date_time', '<', now()) // Events that have already ended but status not updated
                ->orderBy('end_date_time', 'desc') // Most recently ended first
                ->take(5)
                ->get();
        }

        return view('ics_hall.index', compact('announcements', 'featuredEvent', 'completedEvents'));
    }

    public function events()
    {
        // Redirect to the custom calendar view
        return redirect()->route('events.custom-calendar');
    }

    public function announcements()
    {
        // Use the AnnouncementController to handle the request
        $announcementController = new \App\Http\Controllers\AnnouncementController();
        return $announcementController->index();
    }

    public function payments()
    {
        $user = auth()->user();
        
        // Block moderators from accessing payments
        if ($user) {
            $role = is_string($user->user_role) ? strtolower($user->user_role) : '';
            if ($role === 'moderator') {
                abort(403, 'Moderators do not have access to the payment section.');
            }
        }
        
        // Redirect only super_admin and finance_admin to admin payments page
        if ($user && $user->canManagePayments()) {
            return redirect()->route('admin.payments.index');
        }
        
        return redirect()->route('client.payments.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}