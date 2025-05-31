<?php

namespace App\Http\Controllers;

use App\Models\NonIcsMember;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NonIcsMemberController extends Controller
{
    /**
     * Display a listing of the non-ICS members.
     */
    public function index(Request $request)
    {
        // Base query
        $query = NonIcsMember::query();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('course_year_section', 'like', "%{$search}%");
            });
        }

        // Apply payment status filter
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Get paginated results
        $nonIcsMembers = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $totalMembers = NonIcsMember::count();
        $paidMembers = NonIcsMember::where('payment_status', 'Paid')->count();
        $pendingMembers = NonIcsMember::where('payment_status', 'Pending')->count();

        return view('non-ics-members.index', compact('nonIcsMembers', 'totalMembers', 'paidMembers', 'pendingMembers'));
    }

    /**
     * Show the form for creating a new non-ICS member.
     */
    public function create()
    {
        return view('payments.non-ics-member-form');
    }

    /**
     * Store a newly created non-ICS member in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'alternative_email' => 'nullable|email',
                'fullname' => 'required|string|max:255',
                'student_id' => 'nullable|string|max:50',
                'course_year_section' => 'required|string|max:50',
                'department' => 'nullable|string|max:100',
                'mobile_no' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
                'payment_status' => 'nullable|string|in:None,Pending,Paid',
                'membership_type' => 'nullable|string|max:50',
                'membership_expiry' => 'nullable|date',
            ]);

            // Check if the non-ICS member already exists
            $existingMember = NonIcsMember::where('email', $validated['email'])->first();

            if ($existingMember) {
                // Update the existing member
                $existingMember->update($validated);
                $message = 'Non-ICS member updated successfully.';
                $nonIcsMember = $existingMember;
            } else {
                // Create a new non-ICS member
                $nonIcsMember = NonIcsMember::create($validated);
                $message = 'Non-ICS member created successfully.';
            }

            Log::info('Non-ICS member saved: ' . json_encode([
                'id' => $nonIcsMember->id,
                'email' => $nonIcsMember->email,
                'fullname' => $nonIcsMember->fullname
            ]));

            return redirect()->route('admin.non-ics-members.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to save Non-ICS member: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to save Non-ICS member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified non-ICS member.
     */
    public function show($id)
    {
        $nonIcsMember = NonIcsMember::findOrFail($id);
        $payments = Order::where('non_ics_member_id', $id)->orderBy('created_at', 'desc')->get();

        return view('non-ics-members.show', compact('nonIcsMember', 'payments'));
    }

    /**
     * Show the form for editing the specified non-ICS member.
     */
    public function edit($id)
    {
        $nonIcsMember = NonIcsMember::findOrFail($id);
        return view('non-ics-members.edit', compact('nonIcsMember'));
    }

    /**
     * Update the specified non-ICS member in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $nonIcsMember = NonIcsMember::findOrFail($id);

            $validated = $request->validate([
                'email' => 'required|email',
                'alternative_email' => 'nullable|email',
                'fullname' => 'required|string|max:255',
                'student_id' => 'nullable|string|max:50',
                'course_year_section' => 'required|string|max:50',
                'department' => 'nullable|string|max:100',
                'mobile_no' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
                'payment_status' => 'nullable|string|in:None,Pending,Paid',
                'membership_type' => 'nullable|string|max:50',
                'membership_expiry' => 'nullable|date',
            ]);

            // Check if email is being changed and if it already exists
            if ($validated['email'] !== $nonIcsMember->email) {
                $existingMember = NonIcsMember::where('email', $validated['email'])->first();
                if ($existingMember) {
                    return redirect()->back()
                        ->with('error', 'A non-ICS member with this email already exists.')
                        ->withInput();
                }
            }

            $nonIcsMember->update($validated);

            return redirect()->route('admin.payments.index')
                ->with('success', 'Non-ICS member updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update Non-ICS member: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update Non-ICS member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified non-ICS member from storage.
     */
    public function destroy($id)
    {
        try {
            $nonIcsMember = NonIcsMember::findOrFail($id);

            // Check if there are any payments associated with this non-ICS member
            $paymentsCount = Order::where('non_ics_member_id', $id)->count();
            if ($paymentsCount > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete this non-ICS member because there are payments associated with them.');
            }

            $nonIcsMember->delete();

            return redirect()->route('admin.non-ics-members.index')
                ->with('success', 'Non-ICS member deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete Non-ICS member: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete Non-ICS member: ' . $e->getMessage());
        }
    }
}
