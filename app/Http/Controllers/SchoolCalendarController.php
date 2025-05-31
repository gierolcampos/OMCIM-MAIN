<?php

namespace App\Http\Controllers;

use App\Models\SchoolCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schoolCalendars = SchoolCalendar::orderBy('created_at', 'desc')->get();
        $currentCalendar = SchoolCalendar::where('is_selected', true)->first();

        return view('admin.school-calendars.index', compact('schoolCalendars', 'currentCalendar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.school-calendars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_calendar_desc' => 'required|string|max:255',
            'school_calendar_short_desc' => 'required|string|max:100',
            'is_selected' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this calendar is selected, unselect all others
            if ($request->has('is_selected') && $request->is_selected) {
                SchoolCalendar::where('is_selected', true)->update(['is_selected' => false]);
            }

            SchoolCalendar::create($validated);

            DB::commit();

            return redirect()->route('admin.school-calendars.index')
                ->with('success', 'School calendar created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create school calendar: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create school calendar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolCalendar $schoolCalendar)
    {
        return view('admin.school-calendars.edit', compact('schoolCalendar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolCalendar $schoolCalendar)
    {
        $validated = $request->validate([
            'school_calendar_desc' => 'required|string|max:255',
            'school_calendar_short_desc' => 'required|string|max:100',
            'is_selected' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this calendar is selected, unselect all others
            if ($request->has('is_selected') && $request->is_selected) {
                SchoolCalendar::where('id', '!=', $schoolCalendar->id)
                    ->where('is_selected', true)
                    ->update(['is_selected' => false]);
            }

            $schoolCalendar->update($validated);

            DB::commit();

            return redirect()->route('admin.school-calendars.index')
                ->with('success', 'School calendar updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update school calendar: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update school calendar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolCalendar $schoolCalendar)
    {
        // Don't allow deleting the selected calendar
        if ($schoolCalendar->is_selected) {
            return redirect()->route('admin.school-calendars.index')
                ->with('error', 'Cannot delete the currently selected school calendar.');
        }

        try {
            $schoolCalendar->delete();
            
            return redirect()->route('admin.school-calendars.index')
                ->with('success', 'School calendar deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete school calendar: ' . $e->getMessage());
            
            return redirect()->route('admin.school-calendars.index')
                ->with('error', 'Failed to delete school calendar: ' . $e->getMessage());
        }
    }

    /**
     * Set the specified school calendar as the current one.
     */
    public function setCurrent(SchoolCalendar $schoolCalendar)
    {
        try {
            DB::beginTransaction();

            // Unselect all calendars
            SchoolCalendar::where('is_selected', true)->update(['is_selected' => false]);

            // Select the specified calendar
            $schoolCalendar->update(['is_selected' => true]);

            DB::commit();

            return redirect()->route('admin.school-calendars.index')
                ->with('success', 'Current school calendar updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update current school calendar: ' . $e->getMessage());
            
            return redirect()->route('admin.school-calendars.index')
                ->with('error', 'Failed to update current school calendar: ' . $e->getMessage());
        }
    }
}
