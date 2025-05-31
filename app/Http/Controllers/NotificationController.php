<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get the latest notifications for the dropdown.
     */
    public function getLatest()
    {
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        $unreadCount = Auth::user()->unread_notifications_count;
        
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Create a notification for a new event.
     */
    public static function createEventNotification($event)
    {
        // Create notifications for all users
        $users = \App\Models\User::where('status', 'active')->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'event',
                'reference_id' => $event->id,
                'title' => 'New Event: ' . $event->title,
                'message' => 'A new event has been created: ' . $event->title . '. Check it out!',
            ]);
        }
    }

    /**
     * Create a notification for a new announcement.
     */
    public static function createAnnouncementNotification($announcement)
    {
        // Create notifications for all users
        $users = \App\Models\User::where('status', 'active')->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'announcement',
                'reference_id' => $announcement->id,
                'title' => 'New Announcement: ' . $announcement->title,
                'message' => 'A new announcement has been posted: ' . $announcement->title,
            ]);
        }
    }

    /**
     * Create a notification when an evaluation is opened.
     */
    public static function createEvaluationNotification($event)
    {
        // Create notifications for all users
        $users = \App\Models\User::where('status', 'active')->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'evaluation',
                'reference_id' => $event->id,
                'title' => 'Evaluation Open: ' . $event->title,
                'message' => 'The evaluation form for "' . $event->title . '" is now open. Please submit your feedback!',
            ]);
        }
    }
}
