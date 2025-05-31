<?php

namespace App\Http\Controllers;

use App\Models\GcashPayment;
use App\Models\User;
use App\Traits\HasBase64Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GcashPaymentController extends Controller
{
    use HasBase64Images;
    /**
     * Display a listing of the GCash payments.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->canManagePayments()) {
            abort(403, 'Unauthorized.');
        }

        $query = GcashPayment::with('user');

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('firstname', 'like', '%' . $search . '%')
                        ->orWhere('lastname', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply status filter
        if (request('status')) {
            $query->where('payment_status', request('status'));
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $totalPayments = GcashPayment::where('payment_status', 'Paid')->sum('total_price');
        $thisMonthPayments = GcashPayment::where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        $pendingPayments = GcashPayment::where('payment_status', 'Pending')->sum('total_price');

        // Redirect to the main payments page instead of trying to render a non-existent view
        return redirect()->route('admin.payments.index')
            ->with('info', 'Viewing all payments. GCash payments are included in this list.');
    }

    /**
     * Display a listing of the GCash payments for the client.
     */
    public function clientIndex()
    {
        $user = Auth::user();

        $query = GcashPayment::where('user_id', $user->id);

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where('id', 'like', '%' . $search . '%');
        }

        // Apply status filter
        if (request('payment_status') && request('payment_status') !== '') {
            $query->where('payment_status', request('payment_status'));
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $totalPayments = GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->sum('total_price');

        $thisMonthPayments = GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $pendingPayments = GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Pending')
            ->sum('total_price');

        return view('payments.gcash.client', compact(
            'payments',
            'totalPayments',
            'thisMonthPayments',
            'pendingPayments'
        ));
    }

    /**
     * Show the form for creating a new GCash payment.
     */
    public function create()
    {
        $admin = Auth::user();

        // Restrict payment creation to admin users only
        if (!$admin->canManagePayments()) {
            return redirect()->route('client.gcash-payments.index')
                ->with('error', 'You do not have permission to create payments as admin.');
        }

        // For admins, fetch all active members
        $users = User::where('status', 'active')
                    ->where('user_role', 'member')
                    ->select('id', 'firstname', 'lastname', 'middlename', 'suffix', 'email')
                    ->orderBy('lastname')
                    ->orderBy('firstname')
                    ->get()
                    ->map(function ($user) {
                        $user->fullname = trim(implode(' ', array_filter([
                            $user->firstname,
                            $user->middlename,
                            $user->lastname,
                            $user->suffix
                        ])));
                        return $user;
                    });

        // Fetch active payment fees
        $paymentFees = \App\Models\PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.gcash.create', compact('users', 'paymentFees'));
    }

    /**
     * Store a newly created GCash payment in storage.
     */
    public function store(Request $request)
    {
        // Restrict payment creation to admin users only
        if (!auth()->user()->canManagePayments()) {
            return redirect()->route('client.gcash-payments.index')
                ->with('error', 'You do not have permission to create payments as admin.');
        }

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'total_price' => 'required|numeric|min:0',
                'payment_status' => 'required|string|in:Paid,Pending,Rejected,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'gcash_name' => 'required|string',
                'gcash_num' => 'required|string',
                'reference_number' => 'required|string',
                'gcash_proof_of_payment' => 'required|file|mimes:jpg,jpeg|max:2048',
            ], [
                'user_id.required' => 'Please select a member.',
                'gcash_name.required' => 'The GCash name field is required.',
                'gcash_num.required' => 'The GCash number field is required.',
                'reference_number.required' => 'The reference number field is required.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required' => 'The proof of payment is required.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Get the user
            $user = User::findOrFail($validated['user_id']);

            // Handle file upload and convert to base64
            $gcashProofPath = null;
            if ($request->hasFile('gcash_proof_of_payment')) {
                $gcashProofFile = $request->file('gcash_proof_of_payment');

                // Convert image to base64 and store in a file
                $gcashProofPath = $this->convertToBase64($gcashProofFile, 'base64/payments/gcash');

                if (!$gcashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $gcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                    $gcashProofFile->move(public_path('proofs'), $gcashProofPath);
                    Log::info('GCash proof stored as file: ' . $gcashProofPath);
                } else {
                    Log::info('GCash proof converted to base64 and stored in file: ' . $gcashProofPath);
                }
            }

            // Create the payment record
            $payment = GcashPayment::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'placed_on' => now(),
                'payment_status' => $validated['payment_status'],
                'gcash_name' => $validated['gcash_name'],
                'gcash_num' => $validated['gcash_num'],
                'reference_number' => $validated['reference_number'],
                'gcash_proof_path' => $gcashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('GCash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);

            return redirect()->route('admin.gcash-payments.index')
                ->with('success', 'GCash payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('GCash Payment Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified GCash payment.
     */
    public function show($id)
    {
        $payment = GcashPayment::findOrFail($id);
        $user = Auth::user();

        // Allow admins to view any payment
        // For regular members, only allow them to view their own payments
        if (!$user->canManagePayments() && $payment->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified GCash payment.
     */
    public function edit($id)
    {
        $payment = GcashPayment::findOrFail($id);
        $user = Auth::user();

        // Only admins can edit any payment
        if (!$user->canManagePayments()) {
            abort(403, 'Unauthorized.');
        }

        // Fetch all active members
        $users = User::where('status', 'active')
                    ->where('user_role', 'member')
                    ->select('id', 'firstname', 'lastname', 'middlename', 'suffix', 'email')
                    ->orderBy('lastname')
                    ->orderBy('firstname')
                    ->get()
                    ->map(function ($user) {
                        $user->fullname = trim(implode(' ', array_filter([
                            $user->firstname,
                            $user->middlename,
                            $user->lastname,
                            $user->suffix
                        ])));
                        return $user;
                    });

        return view('payments.gcash.edit', compact('payment', 'users'));
    }

    /**
     * Update the specified GCash payment in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $payment = GcashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can update any payment
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Log the payment status change for debugging
            Log::info('GCash payment status change attempt', [
                'payment_id' => $payment->id,
                'current_status' => $payment->payment_status,
                'new_status' => $request->input('payment_status'),
                'user_id' => $user->id
            ]);

            // Log all request data for debugging
            Log::info('GCash payment update request data', [
                'all_data' => $request->all(),
                'files' => $request->allFiles()
            ]);

            $validationRules = [
                'user_id' => 'required|exists:users,id',
                'total_price' => 'required|numeric|min:0',
                'payment_status' => 'required|string|in:Paid,Pending,Rejected,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'gcash_name' => 'required|string',
                'gcash_num' => 'required|string',
                'reference_number' => 'required|string',
            ];

            // Only require proof of payment file if it's provided
            if ($request->hasFile('gcash_proof_of_payment')) {
                $validationRules['gcash_proof_of_payment'] = 'file|mimes:jpg,jpeg|max:2048';
            }

            $validationMessages = [
                'user_id.required' => 'Please select a member.',
                'gcash_name.required' => 'The GCash name field is required.',
                'gcash_num.required' => 'The GCash number field is required.',
                'reference_number.required' => 'The reference number field is required.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ];

            $validated = $request->validate($validationRules, $validationMessages);

            // Get the user
            $memberUser = User::findOrFail($validated['user_id']);

            // Handle file upload and convert to base64
            $gcashProofPath = $payment->gcash_proof_path;
            if ($request->hasFile('gcash_proof_of_payment')) {
                $gcashProofFile = $request->file('gcash_proof_of_payment');

                // Delete old file if exists
                if ($payment->gcash_proof_path) {
                    if ($this->isBase64File($payment->gcash_proof_path)) {
                        // It's a base64 file, delete it
                        $oldProofPath = public_path($payment->gcash_proof_path);
                        if (file_exists($oldProofPath)) {
                            unlink($oldProofPath);
                        }
                    } else if (!$this->isBase64Image($payment->gcash_proof_path) && file_exists(public_path($payment->gcash_proof_path))) {
                        // It's a regular file, delete it
                        unlink(public_path($payment->gcash_proof_path));
                    }
                }

                // Convert image to base64 and store in a file
                $newGcashProofPath = $this->convertToBase64($gcashProofFile, 'base64/payments/gcash');

                if (!$newGcashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $newGcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                    $gcashProofFile->move(public_path('proofs'), $newGcashProofPath);
                    Log::info('GCash proof updated and stored as file: ' . $newGcashProofPath);
                } else {
                    Log::info('GCash proof updated and converted to base64 and stored in file: ' . $newGcashProofPath);
                }

                $gcashProofPath = $newGcashProofPath;
            }

            // Update the payment record
            $payment->update([
                'user_id' => $memberUser->id,
                'email' => $memberUser->email,
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'payment_status' => $validated['payment_status'],
                'gcash_name' => $validated['gcash_name'],
                'gcash_num' => $validated['gcash_num'],
                'reference_number' => $validated['reference_number'],
                'gcash_proof_path' => $gcashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            // Redirect to the payments index page instead of the show page
            return redirect()->route('admin.payments.index')
                ->with('success', 'GCash payment updated successfully.');
        } catch (\Exception $e) {
            Log::error('GCash payment update failed: ' . $e->getMessage(), [
                'payment_id' => $id,
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified GCash payment from storage.
     */
    public function destroy($id)
    {
        try {
            $payment = GcashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can delete payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            $payment->delete();

            return redirect()->route('admin.gcash-payments.index')
                ->with('success', 'GCash payment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('GCash payment deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete payment. Please try again.');
        }
    }

    /**
     * Approve a pending GCash payment.
     */
    public function approve($id)
    {
        try {
            $payment = GcashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can approve payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            if ($payment->payment_status !== 'Pending') {
                return redirect()->back()
                    ->with('error', 'Only pending payments can be approved.');
            }

            $payment->update([
                'payment_status' => 'Paid'
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', "GCash payment #{$payment->id} approved successfully.");
        } catch (\Exception $e) {
            Log::error('GCash payment approval failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Reject a pending GCash payment.
     */
    public function reject($id)
    {
        try {
            $payment = GcashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can reject payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            if ($payment->payment_status !== 'Pending') {
                return redirect()->back()
                    ->with('error', 'Only pending payments can be rejected.');
            }

            $payment->update([
                'payment_status' => 'Rejected'
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', "GCash payment #{$payment->id} rejected successfully.");
        } catch (\Exception $e) {
            Log::error('GCash payment rejection failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject payment. Please try again.');
        }
    }

    /**
     * Show the form for creating a new payment for members.
     */
    public function memberCreate()
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to access this page
        if ($user->canManagePayments()) {
            return redirect()->route('admin.gcash-payments.create')
                ->with('error', 'Please use the admin payment creation form.');
        }

        // Get the current user's full name
        $memberName = trim(implode(' ', array_filter([
            $user->firstname,
            $user->middlename,
            $user->lastname,
            $user->suffix
        ])));

        // Fetch active payment fees
        $paymentFees = \App\Models\PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.gcash.member-create', compact('user', 'memberName', 'paymentFees'));
    }

    /**
     * Store a newly created payment from a member.
     */
    public function memberStore(Request $request)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.gcash-payments.index')
                ->with('error', 'Please use the admin payment creation form.');
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'gcash_name' => 'required|string',
                'gcash_num' => 'required|string',
                'reference_number' => 'required|string',
                'gcash_proof_of_payment' => 'required|file|mimes:jpg,jpeg|max:2048',
            ], [
                'gcash_name.required' => 'The GCash name field is required.',
                'gcash_num.required' => 'The GCash number field is required.',
                'reference_number.required' => 'The reference number field is required.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required' => 'The proof of payment is required.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Handle file upload and convert to base64
            $gcashProofPath = null;
            if ($request->hasFile('gcash_proof_of_payment')) {
                $gcashProofFile = $request->file('gcash_proof_of_payment');

                // Convert image to base64 and store in a file
                $gcashProofPath = $this->convertToBase64($gcashProofFile, 'base64/payments/gcash');

                if (!$gcashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $gcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                    $gcashProofFile->move(public_path('proofs'), $gcashProofPath);
                    Log::info('Member GCash proof stored as file: ' . $gcashProofPath);
                } else {
                    Log::info('Member GCash proof converted to base64 and stored in file: ' . $gcashProofPath);
                }
            }

            // Create the payment record
            $payment = GcashPayment::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'placed_on' => now(),
                'payment_status' => 'Pending', // Members can only submit pending payments
                'gcash_name' => $validated['gcash_name'],
                'gcash_num' => $validated['gcash_num'],
                'reference_number' => $validated['reference_number'],
                'gcash_proof_path' => $gcashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('Member GCash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);

            return redirect()->route('client.gcash-payments.index')
                ->with('success', 'Payment submitted successfully. It is pending approval from an administrator.');
        } catch (\Exception $e) {
            Log::error('Member GCash payment submission failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to submit payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a member payment.
     */
    public function memberEdit($id)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to access this page
        if ($user->canManagePayments()) {
            return redirect()->route('admin.gcash-payments.edit', $id)
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Find the payment and ensure it belongs to the current user
        $payment = GcashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return redirect()->route('client.payments.index')
                ->with('error', 'Payment not found or you do not have permission to edit it.');
        }

        // Only allow editing of pending payments
        if ($payment->payment_status !== 'Pending') {
            return redirect()->route('client.payments.index')
                ->with('error', 'Only pending payments can be edited.');
        }

        // Get the current user's full name
        $memberName = trim(implode(' ', array_filter([
            $user->firstname,
            $user->middlename,
            $user->lastname,
            $user->suffix
        ])));

        // Fetch active payment fees
        $paymentFees = \App\Models\PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.gcash.member-edit', compact('payment', 'user', 'memberName', 'paymentFees'));
    }

    /**
     * Update a member payment.
     */
    public function memberUpdate(Request $request, $id)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.gcash-payments.index')
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Find the payment and ensure it belongs to the current user
        $payment = GcashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return redirect()->route('client.gcash-payments.index')
                ->with('error', 'Payment not found or you do not have permission to edit it.');
        }

        // Only allow editing of pending payments
        if ($payment->payment_status !== 'Pending') {
            return redirect()->route('client.gcash-payments.index')
                ->with('error', 'Only pending payments can be edited.');
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'gcash_name' => 'required|string',
                'gcash_num' => 'required|string',
                'reference_number' => 'required|string',
                'gcash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'gcash_name.required' => 'The GCash name field is required.',
                'gcash_num.required' => 'The GCash number field is required.',
                'reference_number.required' => 'The reference number field is required.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Handle file upload and convert to base64
            $gcashProofPath = $payment->gcash_proof_path;
            if ($request->hasFile('gcash_proof_of_payment')) {
                $gcashProofFile = $request->file('gcash_proof_of_payment');

                // Delete old file if exists
                if ($payment->gcash_proof_path) {
                    if ($this->isBase64File($payment->gcash_proof_path)) {
                        // It's a base64 file, delete it
                        $oldProofPath = public_path($payment->gcash_proof_path);
                        if (file_exists($oldProofPath)) {
                            unlink($oldProofPath);
                        }
                    } else if (!$this->isBase64Image($payment->gcash_proof_path) && file_exists(public_path($payment->gcash_proof_path))) {
                        // It's a regular file, delete it
                        unlink(public_path($payment->gcash_proof_path));
                    }
                }

                // Convert image to base64 and store in a file
                $newGcashProofPath = $this->convertToBase64($gcashProofFile, 'base64/payments/gcash');

                if (!$newGcashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $newGcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                    $gcashProofFile->move(public_path('proofs'), $newGcashProofPath);
                    Log::info('Member GCash proof updated and stored as file: ' . $newGcashProofPath);
                } else {
                    Log::info('Member GCash proof updated and converted to base64 and stored in file: ' . $newGcashProofPath);
                }

                $gcashProofPath = $newGcashProofPath;
            }

            // Update the payment record
            $payment->update([
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'gcash_name' => $validated['gcash_name'],
                'gcash_num' => $validated['gcash_num'],
                'reference_number' => $validated['reference_number'],
                'gcash_proof_path' => $gcashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('client.payments.index')
                ->with('success', 'Payment updated successfully. It is still pending approval from an administrator.');
        } catch (\Exception $e) {
            Log::error('Member GCash payment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
