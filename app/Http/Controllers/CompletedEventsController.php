<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class CompletedEventsController extends Controller
{
    /**
     * Display a listing of completed events.
     */
    public function index()
    {
        // Get all completed events, ordered by end date (most recent first)
        $completedEvents = Event::where('status', 'completed')
            ->where('end_date_time', '<', now()) // Only events that have already ended
            ->orderBy('end_date_time', 'desc') // Most recently completed first
            ->get();

        return view('completed_events.index', compact('completedEvents'));
    }
}
