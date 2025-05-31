<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DeletionRequestController extends Controller
{
    /**
     * Display a listing of deletion requests.
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        $deletionRequests = User::whereNotNull('deletion_requested_at')
            ->orderBy('deletion_requested_at', 'desc')
            ->get();

        return view('admin.deletion-requests.index', compact('deletionRequests'));
    }

    /**
     * Approve a deletion request and delete the user.
     */
    public function approve($id)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home.index')
                ->with('error', 'You do not have permission to perform this action.');
        }

        try {
            $user = User::findOrFail($id);

            if ($user->deletion_requested_at === null) {
                return redirect()->route('admin.deletion-requests.index')
                    ->with('error', 'This user has not requested account deletion.');
            }

            // Delete the user
            $user->delete();

            return redirect()->route('admin.deletion-requests.index')
                ->with('success', 'User account has been deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Reject a deletion request.
     */
    public function reject($id)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home.index')
                ->with('error', 'You do not have permission to perform this action.');
        }

        try {
            $user = User::findOrFail($id);

            if ($user->deletion_requested_at === null) {
                return redirect()->route('admin.deletion-requests.index')
                    ->with('error', 'This user has not requested account deletion.');
            }

            // Clear the deletion request
            $user->deletion_requested_at = null;
            $user->save();

            return redirect()->route('admin.deletion-requests.index')
                ->with('success', 'Deletion request has been rejected.');
        } catch (\Exception $e) {
            Log::error('Failed to reject deletion request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject deletion request: ' . $e->getMessage());
        }
    }
}
