<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\User;
use App\Models\SchoolCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommitteeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the committees.
     */
    public function index()
    {
        // Get all active committees from the current academic year
        $committees = Committee::with('head', 'members')
            ->active()
            ->currentAcademicYear()
            ->orderBy('name')
            ->get();

        return view('committees.index', compact('committees'));
    }

    /**
     * Display a listing of the committees for admin.
     */
    public function adminIndex()
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to manage committees.');
        }

        // Get all committees, including inactive ones
        $committees = Committee::with('head', 'members')
            ->currentAcademicYear()
            ->orderBy('name')
            ->get();

        // Get all users who can be committee heads (admins)
        $potentialHeads = User::whereHas('role', function ($query) {
            $query->where('name', 'admin')->orWhere('name', 'superadmin');
        })->orderBy('lastname')->get();

        return view('committees.admin.index', compact('committees', 'potentialHeads'));
    }

    /**
     * Show the form for creating a new committee.
     */
    public function create()
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to create committees.');
        }

        // Get all users who can be committee heads (admins)
        $potentialHeads = User::whereHas('role', function ($query) {
            $query->where('name', 'admin')->orWhere('name', 'superadmin');
        })->orderBy('lastname')->get();

        return view('committees.admin.create', compact('potentialHeads'));
    }

    /**
     * Store a newly created committee in storage.
     */
    public function store(Request $request)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to create committees.');
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Create the committee
        $committee = Committee::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['head_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
        ]);

        return redirect()->route('admin.committees.index')
            ->with('success', 'Committee created successfully.');
    }

    /**
     * Display the specified committee.
     */
    public function show(Committee $committee)
    {
        // Load the committee with its head and members
        $committee->load('head', 'members');

        return view('committees.show', compact('committee'));
    }

    /**
     * Show the form for editing the specified committee.
     */
    public function edit(Committee $committee)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to edit committees.');
        }

        // Load the committee with its head and members
        $committee->load('head', 'members');

        // Get all users who can be committee heads (admins)
        $potentialHeads = User::whereHas('role', function ($query) {
            $query->where('name', 'admin')->orWhere('name', 'superadmin');
        })->orderBy('lastname')->get();

        // Get all users who can be committee members (all active users)
        $potentialMembers = User::where('status', 'active')
            ->orderBy('lastname')
            ->get();

        return view('committees.admin.edit', compact('committee', 'potentialHeads', 'potentialMembers'));
    }

    /**
     * Update the specified committee in storage.
     */
    public function update(Request $request, Committee $committee)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to update committees.');
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Update the committee
        $committee->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['head_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.committees.index')
            ->with('success', 'Committee updated successfully.');
    }

    /**
     * Remove the specified committee from storage.
     */
    public function destroy(Committee $committee)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to delete committees.');
        }

        // Delete the committee
        $committee->delete();

        return redirect()->route('admin.committees.index')
            ->with('success', 'Committee deleted successfully.');
    }

    /**
     * Add a member to the committee.
     */
    public function addMember(Request $request, Committee $committee)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to manage committee members.');
        }

        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'nullable|string|max:255',
        ]);

        // Check if the user is already a member of the committee
        if ($committee->members()->where('user_id', $validated['user_id'])->exists()) {
            return redirect()->route('admin.committees.edit', $committee)
                ->with('error', 'User is already a member of this committee.');
        }

        // Add the user to the committee
        $committee->members()->attach($validated['user_id'], [
            'position' => $validated['position'] ?? null,
        ]);

        return redirect()->route('admin.committees.edit', $committee)
            ->with('success', 'Member added to committee successfully.');
    }

    /**
     * Remove a member from the committee.
     */
    public function removeMember(Committee $committee, User $user)
    {
        // Check if user can manage committees
        if (!Gate::allows('manage-committees')) {
            return redirect()->route('committees.index')
                ->with('error', 'You do not have permission to manage committee members.');
        }

        // Remove the user from the committee
        $committee->members()->detach($user->id);

        return redirect()->route('admin.committees.edit', $committee)
            ->with('success', 'Member removed from committee successfully.');
    }
}
