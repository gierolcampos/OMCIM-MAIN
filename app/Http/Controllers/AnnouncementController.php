<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\SchoolCalendar;
use App\Traits\HasBase64Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    use HasBase64Images;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Regular users only see published announcements from the current academic year
        $announcements = Announcement::with('creator')
            ->published()
            ->currentAcademicYear() // Only show announcements from current academic year
            ->orderBy('is_boosted', 'desc') // Show pinned announcements first
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get the current school calendar for display
        $currentCalendar = SchoolCalendar::getCurrentCalendar();

        return view('announcements.index', compact('announcements', 'currentCalendar'));
    }

    /**
     * Display a listing of the resource for admin.
     */
    public function adminIndex(Request $request)
    {
        // Ensure user can manage announcements
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to access the admin area.');
        }

        // Start with base query - for admin, show ALL announcements including drafts
        $query = Announcement::with('creator');

        // Apply academic year filter if provided, otherwise use current academic year
        if ($request->filled('school_calendar_id')) {
            $query->where('school_calendar_id', $request->school_calendar_id);
        } else {
            $query->currentAcademicYear();
        }

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Get announcements with ordering
        $announcements = $query->orderBy('is_boosted', 'desc') // Show pinned announcements first
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all()); // Maintain query parameters in pagination links

        // Get all school calendars for the filter dropdown
        $schoolCalendars = SchoolCalendar::orderBy('created_at', 'desc')->get();
        $currentCalendar = SchoolCalendar::getCurrentCalendar();

        // Pass a flag to the view to indicate this is the admin view
        return view('announcements.index', compact('announcements', 'schoolCalendars', 'currentCalendar'))
            ->with('isAdminView', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('admin.announcements.index')
                ->with('error', 'You do not have permission to create announcements.');
        }

        return view('announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to create announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => ['required', Rule::in(['published', 'draft'])],
            'priority' => ['required', Rule::in(['normal', 'high'])],
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,wmv|max:20480',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'text_color' => 'nullable|string',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            // Convert media to base64 if it's an image
            $mediaFile = $request->file('media');
            $mimeType = $mediaFile->getMimeType();

            if (strpos($mimeType, 'image/') === 0) {
                // It's an image, convert to base64 and store in a file
                $mediaPath = $this->convertToBase64($mediaFile, 'base64/announcements/media');
                Log::info('Media converted to base64 and stored in file: ' . $mediaPath);
            } else {
                // Not an image, store as file
                $mediaPath = $mediaFile->store('media/announcements', 'public');
                Log::info('Media stored as file: ' . $mediaPath);
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Convert image to base64 and store in a file
            $imageFile = $request->file('image');
            $imagePath = $this->convertToBase64($imageFile, 'base64/announcements/images');

            if ($imagePath) {
                Log::info('Image converted to base64 and stored in file: ' . $imagePath);
            } else {
                // Fallback to regular file storage if conversion fails
                $imagePath = $imageFile->store('announcements', 'public');
                Log::info('Image stored as file: ' . $imagePath);
            }
        }

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'created_by' => Auth::id(),
            'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
            'media_path' => $mediaPath,
            'image_path' => $imagePath,
            'text_color' => $request->input('text_color'),
        ]);

        // Create notifications for all users if the announcement is published
        if ($validated['status'] === 'published') {
            \App\Http\Controllers\NotificationController::createAnnouncementNotification($announcement);
        }

        // Redirect based on where the request came from
        if (str_contains(url()->previous(), 'admin')) {
            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement created successfully.');
        } else {
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement created successfully.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        // If announcement is not published, only users who can manage announcements can view it
        if (!$announcement->isActive() && (!Auth::check() || !Auth::user()->canManageAnnouncements())) {
            return redirect()->route('announcements.index')
                ->with('error', 'This announcement is not available.');
        }

        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to edit announcements.');
        }

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to update announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => ['required', Rule::in(['published', 'draft', 'archived'])],
            'priority' => ['required', Rule::in(['normal', 'high', 'medium', 'low'])],
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,wmv|max:20480',
            'text_color' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'publish_date' => $validated['publish_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'text_color' => $request->input('text_color'),
        ];

        if ($request->hasFile('media')) {
            // Delete old media file if exists
            if ($announcement->media_path) {
                if ($this->isBase64File($announcement->media_path)) {
                    // It's a base64 file, delete it
                    $oldMediaPath = public_path($announcement->media_path);
                    if (file_exists($oldMediaPath)) {
                        unlink($oldMediaPath);
                    }
                } else if (!$this->isBase64Image($announcement->media_path)) {
                    // It's a regular file, delete it
                    Storage::disk('public')->delete($announcement->media_path);
                }
            }

            // Convert media to base64 if it's an image
            $mediaFile = $request->file('media');
            $mimeType = $mediaFile->getMimeType();

            if (strpos($mimeType, 'image/') === 0) {
                // It's an image, convert to base64 and store in a file
                $data['media_path'] = $this->convertToBase64($mediaFile, 'base64/announcements/media');
                Log::info('Media updated and converted to base64 and stored in file: ' . $data['media_path']);
            } else {
                // Not an image, store as file
                $data['media_path'] = $mediaFile->store('media/announcements', 'public');
                Log::info('Media updated and stored as file: ' . $data['media_path']);
            }
        }

        // Handle image upload too for backward compatibility
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($announcement->image_path) {
                if ($this->isBase64File($announcement->image_path)) {
                    // It's a base64 file, delete it
                    $oldImagePath = public_path($announcement->image_path);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                } else if (!$this->isBase64Image($announcement->image_path)) {
                    // It's a regular file, delete it
                    Storage::disk('public')->delete($announcement->image_path);
                }
            }

            // Convert image to base64 and store in a file
            $imageFile = $request->file('image');
            $data['image_path'] = $this->convertToBase64($imageFile, 'base64/announcements/images');

            if ($data['image_path']) {
                Log::info('Image updated and converted to base64 and stored in file: ' . $data['image_path']);
            } else {
                // Fallback to regular file storage if conversion fails
                $data['image_path'] = $imageFile->store('announcements', 'public');
                Log::info('Image updated and stored as file: ' . $data['image_path']);
            }
        }

        $announcement->update($data);

        // Redirect based on where the request came from
        if (str_contains(url()->previous(), 'admin')) {
            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement updated successfully.');
        } else {
            return redirect()->route('announcements.show', $announcement)
                ->with('success', 'Announcement updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to delete announcements.');
        }

        // Delete media files if they exist
        if ($announcement->image_path) {
            if ($this->isBase64File($announcement->image_path)) {
                // It's a base64 file, delete it
                $imagePath = public_path($announcement->image_path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted announcement base64 image file: ' . $announcement->image_path);
                }
            } else if (!$this->isBase64Image($announcement->image_path)) {
                // It's a regular file, delete it
                Storage::disk('public')->delete($announcement->image_path);
                Log::info('Deleted announcement image file: ' . $announcement->image_path);
            }
        }

        if ($announcement->media_path) {
            if ($this->isBase64File($announcement->media_path)) {
                // It's a base64 file, delete it
                $mediaPath = public_path($announcement->media_path);
                if (file_exists($mediaPath)) {
                    unlink($mediaPath);
                    Log::info('Deleted announcement base64 media file: ' . $announcement->media_path);
                }
            } else if (!$this->isBase64Image($announcement->media_path)) {
                // It's a regular file, delete it
                Storage::disk('public')->delete($announcement->media_path);
                Log::info('Deleted announcement media file: ' . $announcement->media_path);
            }
        }

        $announcement->delete();

        // Redirect based on where the request came from
        if (str_contains(url()->previous(), 'admin')) {
            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement deleted successfully.');
        } else {
            return redirect()->route('announcements.index')
                ->with('success', 'Announcement deleted successfully.');
        }
    }

    /**
     * Display announcements for homepage/dashboard.
     */
    public function latest()
    {
        if (Auth::check() && Auth::user()->canManageAnnouncements()) {
            // Admin can see all latest announcements from current academic year
            $announcements = Announcement::with('creator')
                ->currentAcademicYear()
                ->orderBy('is_boosted', 'desc') // Show pinned announcements first
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            // Regular users only see published announcements from current academic year
            $announcements = Announcement::with('creator')
                ->published()
                ->currentAcademicYear()
                ->orderBy('is_boosted', 'desc') // Show pinned announcements first
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // Get the current school calendar for display
        $currentCalendar = SchoolCalendar::getCurrentCalendar();

        return view('announcements', compact('announcements', 'currentCalendar'));
    }

    /**
     * Toggle the pinned status of an announcement.
     */
    public function togglePin(Announcement $announcement)
    {
        if (!Auth::user()->canManageAnnouncements()) {
            return redirect()->route('announcements.index')
                ->with('error', 'You do not have permission to pin/unpin announcements.');
        }

        // Toggle the is_boosted status
        $announcement->update([
            'is_boosted' => !$announcement->is_boosted
        ]);

        $status = $announcement->is_boosted ? 'pinned' : 'unpinned';

        // Redirect based on where the request came from
        if (str_contains(url()->previous(), 'admin')) {
            return redirect()->route('admin.announcements.index')
                ->with('success', "Announcement has been {$status} successfully.");
        } else {
            return redirect()->back()
                ->with('success', "Announcement has been {$status} successfully.");
        }
    }
}
