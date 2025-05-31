<?php

namespace App\Http\Controllers;

use App\Models\CashPayment;
use App\Models\User;
use App\Traits\HasBase64Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CashPaymentController extends Controller
{
    use HasBase64Images;
    /**
     * Display a listing of the cash payments.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->canManagePayments()) {
            abort(403, 'Unauthorized.');
        }

        $query = CashPayment::with('user');

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
        $totalPayments = CashPayment::where('payment_status', 'Paid')->sum('total_price');
        $thisMonthPayments = CashPayment::where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        $pendingPayments = CashPayment::where('payment_status', 'Pending')->sum('total_price');

        // Redirect to the main payments page instead of trying to render a non-existent view
        return redirect()->route('admin.payments.index')
            ->with('info', 'Viewing all payments. Cash payments are included in this list.');
    }

    /**
     * Display a listing of the cash payments for the client.
     */
    public function clientIndex()
    {
        $user = Auth::user();

        $query = CashPayment::where('user_id', $user->id);

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
        $totalPayments = CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->sum('total_price');

        $thisMonthPayments = CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $pendingPayments = CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Pending')
            ->sum('total_price');

        return view('payments.cash.client', compact(
            'payments',
            'totalPayments',
            'thisMonthPayments',
            'pendingPayments'
        ));
    }

    /**
     * Show the form for creating a new cash payment.
     */
    public function create()
    {
        $admin = Auth::user();

        // Restrict payment creation to admin users only
        if (!$admin->canManagePayments()) {
            return redirect()->route('client.cash-payments.index')
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

        // Fetch all admin users for officer selection
        $officers = User::whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM'])
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

        // Get the current admin's full name
        $adminName = trim(implode(' ', array_filter([
            $admin->firstname,
            $admin->middlename,
            $admin->lastname,
            $admin->suffix
        ])));

        // Fetch active payment fees
        $paymentFees = \App\Models\PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.cash.create', compact('users', 'officers', 'adminName', 'paymentFees'));
    }

    /**
     * Store a newly created cash payment in storage.
     */
    public function store(Request $request)
    {
        // Restrict payment creation to admin users only
        if (!auth()->user()->canManagePayments()) {
            return redirect()->route('client.cash-payments.index')
                ->with('error', 'You do not have permission to create payments as admin.');
        }

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'total_price' => 'required|numeric|min:0',
                'payment_status' => 'required|string|in:Paid,Pending,Rejected,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'officer_in_charge' => 'required|string',
                'receipt_control_number' => 'required|string|regex:/^\d{4}$/',
                'cash_proof_of_payment' => 'required|file|mimes:jpg,jpeg|max:2048',
            ], [
                'user_id.required' => 'Please select a member.',
                'officer_in_charge.required' => 'The officer in charge field is required.',
                'receipt_control_number.required' => 'The receipt control number field is required.',
                'receipt_control_number.regex' => 'The receipt control number must be exactly 4 digits (e.g., 0001).',
                'purpose.required' => 'The purpose field is required.',
                'cash_proof_of_payment.required' => 'The proof of payment is required.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Get the user
            $user = User::findOrFail($validated['user_id']);

            // Handle file upload and convert to base64
            $cashProofPath = null;
            if ($request->hasFile('cash_proof_of_payment')) {
                $cashProofFile = $request->file('cash_proof_of_payment');

                // Convert image to base64 and store in a file
                $cashProofPath = $this->convertToBase64($cashProofFile, 'base64/payments/cash');

                if (!$cashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $cashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                    $cashProofFile->move(public_path('proofs'), $cashProofPath);
                    Log::info('Cash proof stored as file: ' . $cashProofPath);
                } else {
                    Log::info('Cash proof converted to base64 and stored in file: ' . $cashProofPath);
                }
            }

            // Create the payment record
            $payment = CashPayment::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'placed_on' => now(),
                'payment_status' => $validated['payment_status'],
                'officer_in_charge' => $validated['officer_in_charge'],
                'receipt_control_number' => '2-' . $validated['receipt_control_number'],
                'cash_proof_path' => $cashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('Cash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);

            return redirect()->route('admin.payments.index')
                ->with('success', "Cash payment #{$payment->id} recorded successfully.");
        } catch (\Exception $e) {
            Log::error('Cash Payment Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified cash payment.
     */
    public function show($id)
    {
        $payment = CashPayment::findOrFail($id);
        $user = Auth::user();

        // Allow admins to view any payment
        // For regular members, only allow them to view their own payments
        if (!$user->canManagePayments() && $payment->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified cash payment.
     */
    public function edit($id)
    {
        $payment = CashPayment::findOrFail($id);
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

        // Fetch all admin users for officer selection
        $officers = User::whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM'])
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

        // Use the correct view path
        return view('payments.cash.edit', compact('payment', 'users', 'officers'));
    }

    /**
     * Update the specified cash payment in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $payment = CashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can update any payment
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'payment_status' => 'required|string|in:Paid,Pending,Rejected,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'officer_in_charge' => 'required|string',
                'receipt_control_number' => 'required|string|regex:/^\d{4}$/',
                'cash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required' => 'The officer in charge field is required.',
                'receipt_control_number.required' => 'The receipt control number field is required.',
                'receipt_control_number.regex' => 'The receipt control number must be exactly 4 digits (e.g., 0001).',
                'purpose.required' => 'The purpose field is required.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Get the existing user (don't change the user)
            $memberUser = User::findOrFail($payment->user_id);

            // Handle file upload and convert to base64
            $cashProofPath = $payment->cash_proof_path;
            if ($request->hasFile('cash_proof_of_payment')) {
                $cashProofFile = $request->file('cash_proof_of_payment');

                // Delete old file if exists
                if ($payment->cash_proof_path) {
                    if ($this->isBase64File($payment->cash_proof_path)) {
                        // It's a base64 file, delete it
                        $oldProofPath = public_path($payment->cash_proof_path);
                        if (file_exists($oldProofPath)) {
                            unlink($oldProofPath);
                        }
                    } else if (!$this->isBase64Image($payment->cash_proof_path) && file_exists(public_path($payment->cash_proof_path))) {
                        // It's a regular file, delete it
                        unlink(public_path($payment->cash_proof_path));
                    }
                }

                // Convert image to base64 and store in a file
                $newCashProofPath = $this->convertToBase64($cashProofFile, 'base64/payments/cash');

                if (!$newCashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $newCashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                    $cashProofFile->move(public_path('proofs'), $newCashProofPath);
                    Log::info('Cash proof updated and stored as file: ' . $newCashProofPath);
                } else {
                    Log::info('Cash proof updated and converted to base64 and stored in file: ' . $newCashProofPath);
                }

                $cashProofPath = $newCashProofPath;
            }

            // Get the existing user (don't change the user)
            $memberUser = User::findOrFail($payment->user_id);

            // Update the payment record (keeping the same user)
            $payment->update([
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'payment_status' => $validated['payment_status'],
                'officer_in_charge' => $validated['officer_in_charge'],
                'receipt_control_number' => '2-' . $validated['receipt_control_number'],
                'cash_proof_path' => $cashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('admin.cash-payments.show', $payment->id)
                ->with('success', 'Cash payment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Cash payment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified cash payment from storage.
     */
    public function destroy($id)
    {
        try {
            $payment = CashPayment::findOrFail($id);
            $user = Auth::user();

            // Only admins can delete payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            $payment->delete();

            return redirect()->route('admin.payments.index')
                ->with('success', "Cash payment #{$id} deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Cash payment deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete payment. Please try again.');
        }
    }

    /**
     * Approve a pending cash payment.
     */
    public function approve($id)
    {
        try {
            $payment = CashPayment::findOrFail($id);
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
                'payment_status' => 'Paid',
                'officer_in_charge' => $user->firstname . ' ' . $user->lastname
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', "Cash payment #{$payment->id} approved successfully.");
        } catch (\Exception $e) {
            Log::error('Cash payment approval failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Reject a pending cash payment.
     */
    public function reject($id)
    {
        try {
            $payment = CashPayment::findOrFail($id);
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
                'payment_status' => 'Rejected',
                'officer_in_charge' => $user->firstname . ' ' . $user->lastname
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', "Cash payment #{$payment->id} rejected successfully.");
        } catch (\Exception $e) {
            Log::error('Cash payment rejection failed: ' . $e->getMessage());
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
            return redirect()->route('admin.cash-payments.create')
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

        return view('payments.cash.member-create', compact('user', 'memberName', 'paymentFees'));
    }

    /**
     * Store a newly created payment from a member.
     */
    public function memberStore(Request $request)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.cash-payments.index')
                ->with('error', 'Please use the admin payment creation form.');
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'officer_in_charge' => 'required|string',
                'receipt_control_number' => 'required|string|regex:/^\d{4}$/',
                'cash_proof_of_payment' => 'required|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required' => 'The officer in charge field is required.',
                'receipt_control_number.required' => 'The receipt control number field is required.',
                'receipt_control_number.regex' => 'The receipt control number must be exactly 4 digits (e.g., 0001).',
                'purpose.required' => 'The purpose field is required.',
                'cash_proof_of_payment.required' => 'The proof of payment is required.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Handle file upload and convert to base64
            $cashProofPath = null;
            if ($request->hasFile('cash_proof_of_payment')) {
                $cashProofFile = $request->file('cash_proof_of_payment');

                // Convert image to base64 and store in a file
                $cashProofPath = $this->convertToBase64($cashProofFile, 'base64/payments/cash');

                if (!$cashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $cashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                    $cashProofFile->move(public_path('proofs'), $cashProofPath);
                    Log::info('Member cash proof stored as file: ' . $cashProofPath);
                } else {
                    Log::info('Member cash proof converted to base64 and stored in file: ' . $cashProofPath);
                }
            }

            // Create the payment record
            $payment = CashPayment::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'placed_on' => now(),
                'payment_status' => 'Pending', // Members can only submit pending payments
                'officer_in_charge' => $validated['officer_in_charge'],
                'receipt_control_number' => '2-' . $validated['receipt_control_number'],
                'cash_proof_path' => $cashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('Member cash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);

            return redirect()->route('client.cash-payments.index')
                ->with('success', 'Payment submitted successfully. It is pending approval from an administrator.');
        } catch (\Exception $e) {
            Log::error('Member cash payment submission failed: ' . $e->getMessage());
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
            return redirect()->route('admin.cash-payments.edit', $id)
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Find the payment and ensure it belongs to the current user
        $payment = CashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return redirect()->route('client.payments.index')
                ->with('error', 'Payment not found or you do not have permission to edit it.');
        }

        // Only allow editing of pending payments
        if ($payment->payment_status !== 'Pending') {
            return redirect()->route('client.cash-payments.index')
                ->with('error', 'Only pending payments can be edited.');
        }

        // Get the current user's full name
        $memberName = trim(implode(' ', array_filter([
            $user->firstname,
            $user->middlename,
            $user->lastname,
            $user->suffix
        ])));

        return view('payments.cash.member-edit', compact('payment', 'user', 'memberName'));
    }

    /**
     * Update a member payment.
     */
    public function memberUpdate(Request $request, $id)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.cash-payments.index')
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Find the payment and ensure it belongs to the current user
        $payment = CashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return redirect()->route('client.cash-payments.index')
                ->with('error', 'Payment not found or you do not have permission to edit it.');
        }

        // Only allow editing of pending payments
        if ($payment->payment_status !== 'Pending') {
            return redirect()->route('client.cash-payments.index')
                ->with('error', 'Only pending payments can be edited.');
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'officer_in_charge' => 'required|string',
                'receipt_control_number' => 'required|string|regex:/^\d{4}$/',
                'cash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required' => 'The officer in charge field is required.',
                'receipt_control_number.required' => 'The receipt control number field is required.',
                'receipt_control_number.regex' => 'The receipt control number must be exactly 4 digits (e.g., 0001).',
                'purpose.required' => 'The purpose field is required.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Handle file upload
            $cashProofPath = $payment->cash_proof_path;
            if ($request->hasFile('cash_proof_of_payment')) {
                $cashProofFile = $request->file('cash_proof_of_payment');
                $cashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                $cashProofFile->move(public_path('proofs'), $cashProofPath);

                // Delete old file if it exists
                if ($payment->cash_proof_path && file_exists(public_path($payment->cash_proof_path))) {
                    unlink(public_path($payment->cash_proof_path));
                }
            }

            // Update the payment record
            $payment->update([
                'total_price' => $validated['total_price'],
                'purpose' => $validated['purpose'],
                'officer_in_charge' => $validated['officer_in_charge'],
                'receipt_control_number' => '2-' . $validated['receipt_control_number'],
                'cash_proof_path' => $cashProofPath,
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('client.payments.index')
                ->with('success', 'Payment updated successfully. It is still pending approval from an administrator.');
        } catch (\Exception $e) {
            Log::error('Member cash payment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
