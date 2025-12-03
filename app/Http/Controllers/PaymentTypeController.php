<?php

namespace App\Http\Controllers;

use App\Models\CashPayment;
use App\Models\GcashPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->canManagePayments()) {
            // Admin view - fetch both cash and GCash payments
            $cashQuery = CashPayment::with('user');
            $gcashQuery = GcashPayment::with('user');

            // Apply search filter
            if (request('search')) {
                $search = request('search');
                $cashQuery->where(function($q) use ($search) {
                    $q->where('id', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                      });
                });

                $gcashQuery->where(function($q) use ($search) {
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
                $cashQuery->where('payment_status', request('status'));
                $gcashQuery->where('payment_status', request('status'));
            }

            $cashPayments = $cashQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'cash_page');
            $gcashPayments = $gcashQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'gcash_page');

            // Calculate statistics
            $totalCashPayments = CashPayment::where('payment_status', 'Paid')->sum('total_price');
            $totalGcashPayments = GcashPayment::where('payment_status', 'Paid')->sum('total_price');
            $totalPayments = $totalCashPayments + $totalGcashPayments;

            $thisMonthCashPayments = CashPayment::where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            $thisMonthGcashPayments = GcashPayment::where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');
            $thisMonthPayments = $thisMonthCashPayments + $thisMonthGcashPayments;

            $pendingCashPayments = CashPayment::where('payment_status', 'Pending')->sum('total_price');
            $pendingGcashPayments = GcashPayment::where('payment_status', 'Pending')->sum('total_price');
            $pendingPayments = $pendingCashPayments + $pendingGcashPayments;

            return view('payments.type-index', compact(
                'cashPayments',
                'gcashPayments',
                'totalPayments',
                'thisMonthPayments',
                'pendingPayments'
            ));
        } else {
            // Client view
            $cashQuery = CashPayment::where('user_id', $user->id);
            $gcashQuery = GcashPayment::where('user_id', $user->id);

            // Apply search filter
            if (request('search')) {
                $search = request('search');
                $cashQuery->where('id', 'like', '%' . $search . '%');
                $gcashQuery->where('id', 'like', '%' . $search . '%');
            }

            // Apply status filter
            if (request('payment_status') && request('payment_status') !== '') {
                $cashQuery->where('payment_status', request('payment_status'));
                $gcashQuery->where('payment_status', request('payment_status'));
            }

            $cashPayments = $cashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'cash_page');
            $gcashPayments = $gcashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'gcash_page');

            // Calculate statistics
            $totalCashPayments = CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->sum('total_price');

            $totalGcashPayments = GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->sum('total_price');
            $totalPayments = $totalCashPayments + $totalGcashPayments;

            $thisMonthCashPayments = CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            $thisMonthGcashPayments = GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');
            $thisMonthPayments = $thisMonthCashPayments + $thisMonthGcashPayments;

            $pendingCashPayments = CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Pending')
                ->sum('total_price');

            $pendingGcashPayments = GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Pending')
                ->sum('total_price');
            $pendingPayments = $pendingCashPayments + $pendingGcashPayments;

            return view('payments.type-member', compact(
                'cashPayments',
                'gcashPayments',
                'totalPayments',
                'thisMonthPayments',
                'pendingPayments'
            ));
        }
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $admin = Auth::user();

        // Restrict payment creation to admin users only
        if (!$admin->canManagePayments()) {
            return redirect()->route('payment.types.member.create')
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

        // Fetch all admin users for officer selection (excluding PIO who cannot manage payments)
        $officers = User::whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'BM'])
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

        return view('payments.type-create', compact('users', 'officers', 'adminName'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        // Restrict payment creation to admin users only
        if (!auth()->user()->canManagePayments()) {
            return redirect()->route('payment.types.index')
                ->with('error', 'You do not have permission to create payments as admin.');
        }

        try {
            // Common validation rules
            $rules = [
                'user_id' => 'required|exists:users,id',
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'payment_status' => 'required|string|in:Paid,Pending,Rejected,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
            ];

            // Add payment method specific rules
            if ($request->payment_method === 'GCASH') {
                $rules['gcash_name'] = 'required|string';
                $rules['gcash_num'] = 'required|string';
                $rules['reference_number'] = 'required|string';
                $rules['gcash_proof_of_payment'] = 'required|file|mimes:jpg,jpeg|max:2048';
            } else if ($request->payment_method === 'CASH') {
                $rules['officer_in_charge'] = 'required|string';
                $rules['receipt_control_number'] = 'required|numeric';
                $rules['cash_proof_of_payment'] = 'required|file|mimes:jpg,jpeg|max:2048';
            }

            $messages = [
                'user_id.required' => 'Please select a member.',
                'officer_in_charge.required' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.numeric' => 'The receipt control number must be a number.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required' => 'The proof of payment is required when payment method is GCASH.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.required' => 'The proof of payment is required when payment method is CASH.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ];

            $validated = $request->validate($rules, $messages);

            // Get the user
            $user = User::findOrFail($validated['user_id']);

            // Handle file uploads
            $gcashProofPath = null;
            $cashProofPath = null;

            if ($request->hasFile('gcash_proof_of_payment') && $validated['payment_method'] === 'GCASH') {
                $gcashProofFile = $request->file('gcash_proof_of_payment');
                $gcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                $gcashProofFile->move(public_path('proofs'), $gcashProofPath);
            }

            if ($request->hasFile('cash_proof_of_payment') && $validated['payment_method'] === 'CASH') {
                $cashProofFile = $request->file('cash_proof_of_payment');
                $cashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                $cashProofFile->move(public_path('proofs'), $cashProofPath);
            }

            // Create the payment record based on the payment method
            if ($validated['payment_method'] === 'CASH') {
                $payment = CashPayment::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'placed_on' => now(),
                    'payment_status' => $validated['payment_status'],
                    'officer_in_charge' => $validated['officer_in_charge'],
                    'receipt_control_number' => $validated['receipt_control_number'],
                    'cash_proof_path' => $cashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Cash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);
            } else if ($validated['payment_method'] === 'GCASH') {
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
            }

            return redirect()->route('payment.types.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Payment Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified payment.
     */
    public function showCash($id)
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
     * Display the specified payment.
     */
    public function showGcash($id)
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
     * Approve a pending cash payment.
     */
    public function approveCash($id)
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
    public function rejectCash($id)
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
     * Approve a pending GCash payment.
     */
    public function approveGcash($id)
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
    public function rejectGcash($id)
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

        return view('payments.type-member-create', compact('user', 'memberName'));
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
            // Common validation rules
            $rules = [
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
            ];

            // Add payment method specific rules
            if ($request->payment_method === 'GCASH') {
                $rules['gcash_name'] = 'required|string';
                $rules['gcash_num'] = 'required|string';
                $rules['reference_number'] = 'required|string';
                $rules['gcash_proof_of_payment'] = 'required|file|mimes:jpg,jpeg|max:2048';
            } else if ($request->payment_method === 'CASH') {
                $rules['officer_in_charge'] = 'required|string';
                $rules['receipt_control_number'] = 'required|integer';
                $rules['cash_proof_of_payment'] = 'required|file|mimes:jpg,jpeg|max:2048';
            }

            $messages = [
                'officer_in_charge.required' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.integer' => 'The receipt control number must be an integer.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required' => 'The proof of payment is required when payment method is GCASH.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.required' => 'The proof of payment is required when payment method is CASH.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ];

            $validated = $request->validate($rules, $messages);

            // Handle file uploads
            $gcashProofPath = null;
            $cashProofPath = null;

            if ($request->hasFile('gcash_proof_of_payment') && $validated['payment_method'] === 'GCASH') {
                $gcashProofFile = $request->file('gcash_proof_of_payment');
                $gcashProofPath = 'proofs/gcash_' . time() . '_' . $gcashProofFile->getClientOriginalName();
                $gcashProofFile->move(public_path('proofs'), $gcashProofPath);
            }

            if ($request->hasFile('cash_proof_of_payment') && $validated['payment_method'] === 'CASH') {
                $cashProofFile = $request->file('cash_proof_of_payment');
                $cashProofPath = 'proofs/cash_' . time() . '_' . $cashProofFile->getClientOriginalName();
                $cashProofFile->move(public_path('proofs'), $cashProofPath);
            }

            // Create the payment record based on the payment method
            if ($validated['payment_method'] === 'CASH') {
                $payment = CashPayment::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'placed_on' => now(),
                    'payment_status' => 'Pending', // Members can only submit pending payments
                    'officer_in_charge' => $validated['officer_in_charge'],
                    'receipt_control_number' => $validated['receipt_control_number'],
                    'cash_proof_path' => $cashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Member cash payment created:', ['id' => $payment->id, 'user_id' => $user->id]);
            } else if ($validated['payment_method'] === 'GCASH') {
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
            }

            return redirect()->route('client.payments.index')
                ->with('success', 'Payment submitted successfully. It is pending approval from an administrator.');
        } catch (\Exception $e) {
            Log::error('Member payment submission failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to submit payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
