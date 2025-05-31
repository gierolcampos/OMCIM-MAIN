<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Order model removed as it's no longer needed
// use App\Models\Order;
use App\Models\NonIcsMember;
use App\Models\PaymentFee;
use App\Models\SchoolCalendar;
use App\Models\CashPayment;
use App\Models\GcashPayment;
use App\Traits\HasBase64Images;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class PaymentController extends Controller
{
    use HasBase64Images;
    /**
     * Get payment fee by purpose
     *
     * @param string $purpose
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentFeeByPurpose(Request $request)
    {
        try {
            $purpose = $request->input('purpose');

            if (!$purpose) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purpose is required',
                    'data' => null
                ], 400);
            }

            $paymentFee = PaymentFee::where('purpose', $purpose)
                ->where('is_active', true)
                ->first();

            if (!$paymentFee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment fee not found for the specified purpose',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment fee retrieved successfully',
                'data' => [
                    'fee_id' => $paymentFee->fee_id,
                    'purpose' => $paymentFee->purpose,
                    'description' => $paymentFee->description,
                    'total_price' => $paymentFee->total_price
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving payment fee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the payment fee',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get all active payment fees
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPaymentFees()
    {
        try {
            $paymentFees = PaymentFee::where('is_active', true)
                ->orderBy('purpose')
                ->get(['fee_id', 'purpose', 'description', 'total_price']);

            return response()->json([
                'success' => true,
                'message' => 'Payment fees retrieved successfully',
                'data' => $paymentFees
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving payment fees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving payment fees',
                'data' => null
            ], 500);
        }
    }
    /**
     * Display a listing of the payments.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin view
            // Initialize empty array for payments
            $paymentsArray = [];

            // We no longer use the Order table, so we'll just use an empty collection
            // This will be replaced by the cash and gcash payments

            // Get cash payments
            $cashQuery = \App\Models\CashPayment::with('user');

            // Apply academic year filter if provided, otherwise use current academic year
            if (request('school_calendar_id')) {
                $cashQuery->where('school_calendar_id', request('school_calendar_id'));
            } else {
                $cashQuery->currentAcademicYear();
            }

            // Apply search filter for cash payments
            if (request('search')) {
                $cashQuery->where(function($q) {
                    $q->where('id', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%')
                      ->orWhereHas('user', function($q) {
                          $q->where('firstname', 'like', '%' . request('search') . '%')
                            ->orWhere('lastname', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%');
                      });
                });
            }

            // Apply payment method filter for cash payments
            if (request('payment_method')) {
                // Only show cash payments if CASH is selected
                if (request('payment_method') !== 'CASH') {
                    $cashQuery->where('id', 0); // This will return no results
                }
            }

            // Apply status filter for cash payments
            if (request('status')) {
                $cashQuery->where('payment_status', request('status'));
            }

            $cashPayments = $cashQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'cash_page');

            // Get GCash payments
            $gcashQuery = \App\Models\GcashPayment::with('user');

            // Apply academic year filter if provided, otherwise use current academic year
            if (request('school_calendar_id')) {
                $gcashQuery->where('school_calendar_id', request('school_calendar_id'));
            } else {
                $gcashQuery->currentAcademicYear();
            }

            // Apply search filter for GCash payments
            if (request('search')) {
                $gcashQuery->where(function($q) {
                    $q->where('id', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%')
                      ->orWhereHas('user', function($q) {
                          $q->where('firstname', 'like', '%' . request('search') . '%')
                            ->orWhere('lastname', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%');
                      });
                });
            }

            // Apply payment method filter for GCash payments
            if (request('payment_method')) {
                // Only show GCash payments if GCASH is selected
                if (request('payment_method') !== 'GCASH') {
                    $gcashQuery->where('id', 0); // This will return no results
                }
            }

            // Apply status filter for GCash payments
            if (request('status')) {
                $gcashQuery->where('payment_status', request('status'));
            }

            $gcashPayments = $gcashQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'gcash_page');

            // Get non-ICS members data directly from the non_ics_members table
            $nonIcsQuery = NonIcsMember::query();

            // Apply academic year filter if provided, otherwise use current academic year
            if (request('school_calendar_id')) {
                $nonIcsQuery->where('school_calendar_id', request('school_calendar_id'));
            } else {
                $nonIcsQuery->currentAcademicYear();
            }

            // Apply search filter for non-ICS members
            if (request('search')) {
                $nonIcsQuery->where(function($q) {
                    $q->where('id', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%')
                      ->orWhere('fullname', 'like', '%' . request('search') . '%');
                });
            }

            // Apply payment method filter for non-ICS members
            if (request('payment_method')) {
                $nonIcsQuery->where('method', request('payment_method'));
            }

            // Apply status filter for non-ICS members
            if (request('status')) {
                $nonIcsQuery->where('payment_status', request('status'));
            }

            $nonIcsMembers = $nonIcsQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'non_ics_page');

            // Calculate statistics
            // Reset all statistics to 0
            $totalPayments = 0;
            $thisMonthPayments = 0;
            $pendingPayments = 0;
            $rejectedPayments = 0;

            // We no longer use the Order table for statistics

            // Add cash payments statistics
            $totalPayments += \App\Models\CashPayment::where('payment_status', 'Paid')->sum('total_price');
            $thisMonthPayments += \App\Models\CashPayment::where('payment_status', 'Paid')
                ->whereMonth('placed_on', now()->month)
                ->whereYear('placed_on', now()->year)
                ->sum('total_price');
            $pendingPayments += \App\Models\CashPayment::where('payment_status', 'Pending')->sum('total_price');
            $rejectedPayments += \App\Models\CashPayment::where('payment_status', 'Rejected')->sum('total_price');

            // Add GCash payments statistics
            $totalPayments += \App\Models\GcashPayment::where('payment_status', 'Paid')->sum('total_price');
            $thisMonthPayments += \App\Models\GcashPayment::where('payment_status', 'Paid')
                ->whereMonth('placed_on', now()->month)
                ->whereYear('placed_on', now()->year)
                ->sum('total_price');
            $pendingPayments += \App\Models\GcashPayment::where('payment_status', 'Pending')->sum('total_price');
            $rejectedPayments += \App\Models\GcashPayment::where('payment_status', 'Rejected')->sum('total_price');

            // Add non-ICS members statistics
            $totalPayments += NonIcsMember::where('payment_status', 'Paid')->sum('total_price');
            $thisMonthPayments += NonIcsMember::where('payment_status', 'Paid')
                ->whereMonth('placed_on', now()->month)
                ->whereYear('placed_on', now()->year)
                ->sum('total_price');
            $pendingPayments += NonIcsMember::where('payment_status', 'Pending')->sum('total_price');
            $rejectedPayments += NonIcsMember::where(function($query) {
                $query->where('payment_status', 'Failed')
                      ->orWhere('payment_status', 'Rejected');
            })->sum('total_price');

            // We need to create a paginator for the combined payments
            $currentPage = request()->input('page', 1);
            $perPage = 10;

            // We'll use an empty array for the combined payments
            // since we're displaying cash_payments and gcash_payments separately
            $allPayments = [];

            // Sort all payments by created_at in descending order
            usort($allPayments, function($a, $b) {
                return $b->created_at <=> $a->created_at;
            });

            // Create a custom paginator manually
            $currentPage = request()->input('page', 1);
            $paymentsCollection = collect($allPayments);
            $paymentsForPage = $paymentsCollection->forPage($currentPage, $perPage);

            // Create a LengthAwarePaginator instance
            try {
                $payments = new \Illuminate\Pagination\LengthAwarePaginator(
                    $paymentsForPage,
                    $paymentsCollection->count(),
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } catch (\Exception $e) {
                // If there's an error creating the paginator, just use the collection
                Log::error('Error creating paginator: ' . $e->getMessage());
                $payments = $paymentsCollection;
            }

            // Get all school calendars for the filter dropdown
            $schoolCalendars = SchoolCalendar::orderBy('created_at', 'desc')->get();
            $currentCalendar = SchoolCalendar::getCurrentCalendar();

            return view('payments.index', compact(
                'payments',
                'cashPayments',
                'gcashPayments',
                'nonIcsMembers',
                'totalPayments',
                'thisMonthPayments',
                'pendingPayments',
                'rejectedPayments',
                'schoolCalendars',
                'currentCalendar'
            ));
        } else {
            // Client view
            // Initialize empty payments collection since we no longer use the Order table
            $payments = collect([]);

            // Get cash payments
            $cashQuery = \App\Models\CashPayment::where('user_id', $user->id);

            // Apply current academic year filter
            $cashQuery->currentAcademicYear();

            // Apply search filter for cash payments
            if (request('search')) {
                $cashQuery->where('id', 'like', '%' . request('search') . '%');
            }

            // Apply payment method filter for cash payments
            if (request('payment_method')) {
                // Only show cash payments if CASH is selected
                if (request('payment_method') !== 'CASH') {
                    $cashQuery->where('id', 0); // This will return no results
                }
            }

            // Apply status filter for cash payments
            if (request('payment_status')) {
                $cashQuery->where('payment_status', request('payment_status'));
            }

            $cashPayments = $cashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'cash_page');

            // Get GCash payments
            $gcashQuery = \App\Models\GcashPayment::where('user_id', $user->id);

            // Apply current academic year filter
            $gcashQuery->currentAcademicYear();

            // Apply search filter for GCash payments
            if (request('search')) {
                $gcashQuery->where('id', 'like', '%' . request('search') . '%');
            }

            // Apply payment method filter for GCash payments
            if (request('payment_method')) {
                // Only show GCash payments if GCASH is selected
                if (request('payment_method') !== 'GCASH') {
                    $gcashQuery->where('id', 0); // This will return no results
                }
            }

            // Apply status filter for GCash payments
            if (request('payment_status')) {
                $gcashQuery->where('payment_status', request('payment_status'));
            }

            $gcashPayments = $gcashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'gcash_page');

            // Calculate statistics
            // Initialize statistics to 0
            $totalPayments = 0;
            $thisMonthPayments = 0;
            $pendingPayments = 0;

            // Add cash payments statistics
            $totalPayments += \App\Models\CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->sum('total_price');

            $thisMonthPayments += \App\Models\CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            $pendingPayments += \App\Models\CashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Pending')
                ->sum('total_price');

            // Add GCash payments statistics
            $totalPayments += \App\Models\GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->sum('total_price');

            $thisMonthPayments += \App\Models\GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            $pendingPayments += \App\Models\GcashPayment::where('user_id', $user->id)
                ->where('payment_status', 'Pending')
                ->sum('total_price');

            // We need to create a paginator for the combined payments
            $currentPage = request()->input('page', 1);
            $perPage = 10;

            // We'll use an empty array for the combined payments
            // since we're displaying cash_payments and gcash_payments separately
            $allPayments = [];

            // Sort all payments by created_at in descending order
            usort($allPayments, function($a, $b) {
                return $b->created_at <=> $a->created_at;
            });

            // Create a custom paginator manually
            $currentPage = request()->input('page', 1);
            $paymentsCollection = collect($allPayments);
            $paymentsForPage = $paymentsCollection->forPage($currentPage, $perPage);

            // Create a LengthAwarePaginator instance
            try {
                $payments = new \Illuminate\Pagination\LengthAwarePaginator(
                    $paymentsForPage,
                    $paymentsCollection->count(),
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } catch (\Exception $e) {
                // If there's an error creating the paginator, just use the collection
                Log::error('Error creating paginator: ' . $e->getMessage());
                $payments = $paymentsCollection;
            }

            // Get the current school calendar for display
            $currentCalendar = SchoolCalendar::getCurrentCalendar();

            return view('payments.member', compact(
                'payments',
                'cashPayments',
                'gcashPayments',
                'totalPayments',
                'thisMonthPayments',
                'pendingPayments',
                'currentCalendar'
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
            return redirect()->route('client.payments.index')
                ->with('error', 'You do not have permission to create payments.');
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
        $paymentFees = PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.create', compact('users', 'officers', 'adminName', 'paymentFees'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        // Restrict payment creation to admin users only
        if (!auth()->user()->canManagePayments()) {
            return redirect()->route('client.payments.index')
                ->with('error', 'You do not have permission to create payments.');
        }

        try {
            // Ensure base64 directories exist
            $this->ensureDirectoryExists('base64/payments/cash');
            $this->ensureDirectoryExists('base64/payments/gcash');

            // Check if this is a non-ICS payment submission
            $isNonIcsPayment = $request->has('non_ics_payment') && $request->non_ics_payment == 1;
            Log::info('Payment submission type:', ['is_non_ics_payment' => $isNonIcsPayment]);

            // Process payment data

            // Simplify validation rules
            $rules = [
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'payment_status' => 'required|string|in:Paid,Pending,Failed,Refunded',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'payer_type' => 'required|string|in:ics_member,non_ics_member',
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

            // Add payer type specific rules
            if ($request->payer_type === 'ics_member') {
                $rules['user_email'] = 'required|email|exists:users,email';
            } else if ($request->payer_type === 'non_ics_member') {
                // For non-ICS members, user_email should not be required
                $rules['user_email'] = 'nullable'; // Make it nullable instead of required

                // Basic Non-ICS member fields
                $rules['non_ics_email'] = 'required|email';
                $rules['non_ics_fullname'] = 'required|string|max:100';
                $rules['course_year_section'] = 'required|string|max:50';
                $rules['non_ics_mobile'] = 'nullable|string|max:20';

                Log::info('Using non-ICS member validation rules', [
                    'has_user_email' => $request->has('user_email'),
                    'user_email_value' => $request->input('user_email')
                ]);

                // Additional Non-ICS member fields
                $rules['payment_status'] = 'nullable|string|in:None,Pending,Paid';
            }



            $messages = [
                'officer_in_charge.required' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.numeric' => 'The receipt control number must be a number.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required' => 'The proof of payment is required when payment method is GCASH.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.required' => 'The proof of payment is required when payment method is CASH.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'payer_type.required' => 'The payer type field is required.',
                'non_ics_email.required' => 'The NPC email field is required for non-ICS members.',
                'non_ics_email.email' => 'The NPC email must be a valid email address.',
                'non_ics_fullname.required' => 'The full name field is required for non-ICS members.',
                'course_year_section.required' => 'The course, year & section field is required for non-ICS members.',
            ];

            $validated = $request->validate($rules, $messages);

            // Get the user or non-ICS member based on the payer type
            $user = null;
            $nonIcsMember = null;

            if ($validated['payer_type'] === 'ics_member') {
                // For ICS members, get the user from the database
                $user = User::where('email', $validated['user_email'])->firstOrFail();
                Log::info('ICS Member selected:', ['user_id' => $user->id, 'email' => $user->email]);
            } else if ($validated['payer_type'] === 'non_ics_member') {
                // For non-ICS members, find or create a record in the non_ics_members table
                try {
                    Log::info('Non-ICS Member selected, checking database');

                    // Always create a new non-ICS member payment record for each submission
                    // This allows multiple payments with different purposes, descriptions, etc.
                    Log::info('Creating new Non-ICS Member record');
                    // Prepare data for NonIcsMember creation
                    $nonIcsMemberData = [
                        'email' => $validated['non_ics_email'],
                        'fullname' => $validated['non_ics_fullname'],
                        'course_year_section' => $validated['course_year_section'],
                        'mobile_no' => $validated['non_ics_mobile'] ?? null,
                        'payment_status' => $validated['payment_status'] ?? 'None',
                        'purpose' => $validated['purpose'] ?? null,
                        'total_price' => $validated['total_price'] ?? null,
                        'method' => $validated['payment_method'],
                        'description' => $validated['description'] ?? null,
                        'placed_on' => now(),
                        'school_calendar_id' => SchoolCalendar::getCurrentCalendarId()
                    ];

                    // Add payment method specific fields
                    if ($validated['payment_method'] === 'CASH') {
                        $nonIcsMemberData['receipt_control_number'] = $validated['receipt_control_number'] ?? null;
                        // Cash proof path will be set later after file upload
                    } else if ($validated['payment_method'] === 'GCASH') {
                        $nonIcsMemberData['gcash_name'] = $validated['gcash_name'] ?? null;
                        $nonIcsMemberData['gcash_num'] = $validated['gcash_num'] ?? null;
                        $nonIcsMemberData['reference_number'] = $validated['reference_number'] ?? null;
                        // GCash proof path will be set later after file upload
                    }

                    // Use the create method to ensure proper model creation
                    $nonIcsMember = NonIcsMember::create($nonIcsMemberData);
                    Log::info('New Non-ICS Member created:', ['id' => $nonIcsMember->id, 'email' => $nonIcsMember->email]);
                } catch (\Exception $e) {
                    Log::error('Error processing Non-ICS Member:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'data' => $validated
                    ]);
                    throw $e;
                }
            } else {
                throw new \Exception('Invalid payer type: ' . $validated['payer_type']);
            }

            // GCash amount validation removed

            // Handle file uploads
            $gcashProofPath = null;
            $cashProofPath = null;

            if ($request->hasFile('gcash_proof_of_payment') && $validated['payment_method'] === 'GCASH') {
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

                // Update the NonIcsMember record with the proof path if it's a non-ICS member
                if ($validated['payer_type'] === 'non_ics_member' && $nonIcsMember) {
                    $nonIcsMember->gcash_proof_path = $gcashProofPath;
                    $nonIcsMember->save();
                    Log::info('Updated NonIcsMember with GCash proof path', [
                        'id' => $nonIcsMember->id,
                        'gcash_proof_path' => $gcashProofPath
                    ]);
                }
            }

            if ($request->hasFile('cash_proof_of_payment') && $validated['payment_method'] === 'CASH') {
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

                // Update the NonIcsMember record with the proof path if it's a non-ICS member
                if ($validated['payer_type'] === 'non_ics_member' && $nonIcsMember) {
                    $nonIcsMember->cash_proof_path = $cashProofPath;
                    $nonIcsMember->save();
                    Log::info('Updated NonIcsMember with Cash proof path', [
                        'id' => $nonIcsMember->id,
                        'cash_proof_path' => $cashProofPath
                    ]);
                }
            }

            // Create the payment record
            try {
                $orderData = [
                    'method' => $validated['payment_method'],
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'description' => $validated['description'] ?? null,
                    // GCash details
                    'gcash_name' => $validated['gcash_name'] ?? null,
                    'gcash_num' => $validated['gcash_num'] ?? null,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'gcash_proof_path' => $gcashProofPath,
                    'gcash_amount' => null, // Set to null explicitly
                    // Cash details
                    'officer_in_charge' => $validated['officer_in_charge'] ?? null,
                    'receipt_control_number' => $validated['receipt_control_number'] ?? null,
                    'cash_proof_path' => $cashProofPath,
                    'placed_on' => now()->format('Y-m-d H:i:s'),
                    'payment_status' => $validated['payment_status']
                ];

                // Add user or non-ICS member details
                if ($validated['payer_type'] === 'ics_member') {
                    $orderData['user_id'] = $user->id;
                    $orderData['is_non_ics_member'] = false;
                    $orderData['non_ics_member_id'] = null; // Explicitly set to null
                    $orderData['email'] = $user->email; // Set email from user

                    Log::info('Creating payment for ICS Member:', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->firstname . ' ' . $user->lastname
                    ]);
                } else if ($validated['payer_type'] === 'non_ics_member') {
                    // For non-ICS members, ensure we have a valid non_ics_member_id
                    if (!$nonIcsMember || !$nonIcsMember->id) {
                        throw new \Exception('Failed to create or find Non-ICS member record');
                    }

                    $orderData['user_id'] = null; // Explicitly set to null
                    $orderData['non_ics_member_id'] = $nonIcsMember->id;
                    $orderData['email'] = $nonIcsMember->email;
                    $orderData['course_year_section'] = $nonIcsMember->course_year_section;
                    $orderData['is_non_ics_member'] = true;

                    Log::info('Creating payment for Non-ICS Member:', [
                        'non_ics_member_id' => $nonIcsMember->id,
                        'email' => $nonIcsMember->email,
                        'fullname' => $nonIcsMember->fullname,
                        'course_year_section' => $nonIcsMember->course_year_section,
                        'mobile_no' => $nonIcsMember->mobile_no,
                        'payment_status' => $nonIcsMember->payment_status
                    ]);
                } else {
                    throw new \Exception('Invalid payer type: ' . $validated['payer_type']);
                }
            } catch (\Exception $e) {
                throw $e;
            }

            try {
                // Check if this is a non-ICS payment submission
                if ($isNonIcsPayment && $validated['payer_type'] === 'non_ics_member' && $nonIcsMember) {
                    Log::info('Processing as Non-ICS payment - updating NonIcsMember record directly', [
                        'non_ics_member_id' => $nonIcsMember->id,
                        'email' => $nonIcsMember->email
                    ]);

                    // Update the NonIcsMember record with payment details
                    $nonIcsMember->payment_status = $validated['payment_status'];
                    $nonIcsMember->purpose = $validated['purpose'];
                    $nonIcsMember->total_price = $validated['total_price'];

                    // Payment method specific fields
                    if ($validated['payment_method'] === 'CASH') {
                        $nonIcsMember->receipt_control_number = $validated['receipt_control_number'] ?? null;
                        $nonIcsMember->cash_proof_path = $cashProofPath;
                    } else if ($validated['payment_method'] === 'GCASH') {
                        $nonIcsMember->gcash_name = $validated['gcash_name'] ?? null;
                        $nonIcsMember->gcash_num = $validated['gcash_num'] ?? null;
                        $nonIcsMember->reference_number = $validated['reference_number'] ?? null;
                        $nonIcsMember->gcash_proof_path = $gcashProofPath;
                    }

                    $nonIcsMember->save();

                    Log::info('NonIcsMember record updated successfully', [
                        'id' => $nonIcsMember->id,
                        'payment_status' => $nonIcsMember->payment_status,
                        'total_price' => $nonIcsMember->total_price
                    ]);
                }

                // Only create a payment if this is NOT a non-ICS payment
                if (!($isNonIcsPayment && $validated['payer_type'] === 'non_ics_member')) {
                    // Log the order data before creation
                    Log::info('Payment Data Before Creation:', $orderData);

                    // For ICS members, save to either cash_payments or gcash_payments table based on payment method
                    if ($validated['payer_type'] === 'ics_member') {
                        if ($validated['payment_method'] === 'CASH') {
                            // Create a new cash payment record for each submission
                            $cashPayment = \App\Models\CashPayment::create([
                                'user_id' => $user->id,
                                'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
                                'email' => $user->email,
                                'total_price' => $validated['total_price'],
                                'purpose' => $validated['purpose'],
                                'placed_on' => now(),
                                'payment_status' => $validated['payment_status'],
                                'officer_in_charge' => $validated['officer_in_charge'] ?? null,
                                'receipt_control_number' => $validated['receipt_control_number'] ?? null,
                                'cash_proof_path' => $cashProofPath,
                                'description' => $validated['description'] ?? null,
                            ]);
                            Log::info('Cash Payment Created:', ['id' => $cashPayment->id, 'data' => $cashPayment->toArray()]);

                            $order = null; // No Order record needed
                        } else if ($validated['payment_method'] === 'GCASH') {
                            // Create a new GCash payment record for each submission
                            $gcashPayment = \App\Models\GcashPayment::create([
                                'user_id' => $user->id,
                                'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
                                'email' => $user->email,
                                'total_price' => $validated['total_price'],
                                'purpose' => $validated['purpose'],
                                'placed_on' => now(),
                                'payment_status' => $validated['payment_status'],
                                'gcash_name' => $validated['gcash_name'] ?? null,
                                'gcash_num' => $validated['gcash_num'] ?? null,
                                'reference_number' => $validated['reference_number'] ?? null,
                                'gcash_proof_path' => $gcashProofPath,
                                'description' => $validated['description'] ?? null,
                            ]);
                            Log::info('GCash Payment Created:', ['id' => $gcashPayment->id, 'data' => $gcashPayment->toArray()]);

                            $order = null; // No Order record needed
                        } else {
                            // We no longer use the Order table
                            Log::warning('Unrecognized payment method: ' . $validated['payment_method']);
                            throw new \Exception('Unrecognized payment method: ' . $validated['payment_method']);
                        }
                    } else {
                        // We no longer use the Order table
                        Log::warning('Unrecognized payer type: ' . $validated['payer_type']);
                        throw new \Exception('Unrecognized payer type: ' . $validated['payer_type']);
                    }
                } else {
                    Log::info('Skipping payment creation for Non-ICS member payment');
                    $order = null; // Set to null since we're not creating an order
                }

                // Double-check that the relationship is properly established for non-ICS members
                // Only if an order was created and it's for a non-ICS member
                if ($order && $validated['payer_type'] === 'non_ics_member' && $nonIcsMember) {
                    // Ensure the non_ics_member_id is set correctly
                    if ($order->non_ics_member_id != $nonIcsMember->id) {
                        Log::warning('Order created with incorrect non_ics_member_id, fixing...', [
                            'order_id' => $order->id,
                            'current_non_ics_member_id' => $order->non_ics_member_id,
                            'expected_non_ics_member_id' => $nonIcsMember->id
                        ]);

                        $order->non_ics_member_id = $nonIcsMember->id;
                        $order->is_non_ics_member = true;
                        $order->save();

                        Log::info('Order Updated After Creation:', [
                            'id' => $order->id,
                            'non_ics_member_id' => $order->non_ics_member_id,
                            'is_non_ics_member' => $order->is_non_ics_member
                        ]);
                    } else {
                        Log::info('Order correctly linked to Non-ICS Member:', [
                            'order_id' => $order->id,
                            'non_ics_member_id' => $order->non_ics_member_id,
                            'non_ics_member_email' => $nonIcsMember->email
                        ]);
                    }

                    // Verify the relationship works
                    $relatedNonIcsMember = $order->nonIcsMember;
                    if ($relatedNonIcsMember) {
                        Log::info('Relationship verification successful:', [
                            'order_id' => $order->id,
                            'related_non_ics_member_id' => $relatedNonIcsMember->id,
                            'related_non_ics_member_email' => $relatedNonIcsMember->email
                        ]);
                    } else {
                        Log::warning('Relationship verification failed - nonIcsMember relationship returned null', [
                            'order_id' => $order->id,
                            'non_ics_member_id' => $order->non_ics_member_id
                        ]);
                    }
                }

                // Prepare success message based on payment type
                $successMessage = $isNonIcsPayment && $validated['payer_type'] === 'non_ics_member'
                    ? 'Non-ICS member payment recorded successfully.'
                    : 'Payment recorded successfully.';

                // Redirect to payments index
                return redirect()->route('admin.payments.index')
                    ->with('success', $successMessage);
            } catch (\Exception $e) {
                Log::error('Payment Creation Failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $orderData ?? []
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to record payment: ' . $e->getMessage())
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        // Check if it's a cash payment
        try {
            $payment = \App\Models\CashPayment::findOrFail($id);
            $user = Auth::user();

            // Allow admins to view any payment
            // For regular members, only allow them to view their own payments
            if (!$user->canManagePayments() && $payment->user_id !== $user->id) {
                abort(403, 'Unauthorized.');
            }

            return view('payments.show', compact('payment'));
        } catch (\Exception $e) {
            // Not a cash payment, try GCash payment
            try {
                $payment = \App\Models\GcashPayment::findOrFail($id);
                $user = Auth::user();

                // Allow admins to view any payment
                // For regular members, only allow them to view their own payments
                if (!$user->canManagePayments() && $payment->user_id !== $user->id) {
                    abort(403, 'Unauthorized.');
                }

                return view('payments.show', compact('payment'));
            } catch (\Exception $e) {
                // Not a GCash payment either, return 404
                abort(404, 'Payment not found.');
            }
        }
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit($id)
    {
        $user = Auth::user();

        // Only admins can edit any payment
        if (!$user->canManagePayments()) {
            abort(403, 'Unauthorized.');
        }

        // Check if it's a cash payment
        try {
            $payment = \App\Models\CashPayment::findOrFail($id);
            return view('payments.edit', compact('payment'));
        } catch (\Exception $e) {
            // Not a cash payment, try GCash payment
            try {
                $payment = \App\Models\GcashPayment::findOrFail($id);
                return view('payments.edit', compact('payment'));
            } catch (\Exception $e) {
                // Not a GCash payment either, return 404
                abort(404, 'Payment not found.');
            }
        }
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Only admins can update any payment
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Ensure base64 directories exist
            $this->ensureDirectoryExists('base64/payments/cash');
            $this->ensureDirectoryExists('base64/payments/gcash');

            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'payment_status' => 'required|string|in:Paid,Pending,Failed,Refunded',
                'description' => 'nullable|string',
                // GCASH specific fields
                'gcash_name' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_num' => 'required_if:payment_method,GCASH|string|nullable',
                'reference_number' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
                // CASH specific fields
                'officer_in_charge' => 'required_if:payment_method,CASH|string|nullable',
                'receipt_control_number' => 'required_if:payment_method,CASH|integer|nullable',
                'cash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required_if' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required_if' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.integer' => 'The receipt control number must be an integer.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Try to find the payment in either cash or gcash tables
            $payment = null;
            $paymentType = '';

            // Check if it's a cash payment
            try {
                $payment = \App\Models\CashPayment::findOrFail($id);
                $paymentType = 'cash';
            } catch (\Exception $e) {
                // Not a cash payment, try GCash payment
                try {
                    $payment = \App\Models\GcashPayment::findOrFail($id);
                    $paymentType = 'gcash';
                } catch (\Exception $e) {
                    // Not found in either table
                    abort(404, 'Payment not found.');
                }
            }

            // Handle file uploads
            if ($paymentType === 'cash') {
                $cashProofPath = $payment->cash_proof_path;

                if ($request->hasFile('cash_proof_of_payment')) {
                    $cashProofFile = $request->file('cash_proof_of_payment');

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

                    // Delete old file if it exists
                    if ($payment->cash_proof_path && file_exists(public_path($payment->cash_proof_path))) {
                        unlink(public_path($payment->cash_proof_path));
                    }

                    $cashProofPath = $newCashProofPath;
                }

                // Update the cash payment record
                $payment->update([
                    'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
                    'total_price' => $validated['total_price'],
                    'description' => $validated['description'] ?? null,
                    'officer_in_charge' => $validated['officer_in_charge'] ?? null,
                    'receipt_control_number' => $validated['receipt_control_number'] ?? null,
                    'cash_proof_path' => $cashProofPath,
                    'payment_status' => $validated['payment_status']
                ]);

                Log::info('Admin cash payment updated:', [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'school_calendar_id' => SchoolCalendar::getCurrentCalendarId()
                ]);
            } else if ($paymentType === 'gcash') {
                $gcashProofPath = $payment->gcash_proof_path;

                if ($request->hasFile('gcash_proof_of_payment')) {
                    $gcashProofFile = $request->file('gcash_proof_of_payment');

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

                    // Delete old file if it exists
                    if ($payment->gcash_proof_path && file_exists(public_path($payment->gcash_proof_path))) {
                        unlink(public_path($payment->gcash_proof_path));
                    }

                    $gcashProofPath = $newGcashProofPath;
                }

                // Update the GCash payment record
                $payment->update([
                    'school_calendar_id' => SchoolCalendar::getCurrentCalendarId(),
                    'total_price' => $validated['total_price'],
                    'description' => $validated['description'] ?? null,
                    'gcash_name' => $validated['gcash_name'] ?? null,
                    'gcash_num' => $validated['gcash_num'] ?? null,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'gcash_proof_path' => $gcashProofPath,
                    'payment_status' => $validated['payment_status']
                ]);

                Log::info('Admin GCash payment updated:', [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'school_calendar_id' => SchoolCalendar::getCurrentCalendarId()
                ]);
            }

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Payment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            // Only admins can delete payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Try to find the payment in either cash or gcash tables
            $payment = null;
            $paymentType = '';

            // Check if it's a cash payment
            try {
                $payment = \App\Models\CashPayment::findOrFail($id);
                $paymentType = 'cash';
            } catch (\Exception $e) {
                // Not a cash payment, try GCash payment
                try {
                    $payment = \App\Models\GcashPayment::findOrFail($id);
                    $paymentType = 'gcash';
                } catch (\Exception $e) {
                    // Not found in either table
                    abort(404, 'Payment not found.');
                }
            }

            // Delete the payment
            $payment->delete();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Payment deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete payment. Please try again.');
        }
    }

    /**
     * Approve a pending payment.
     */
    public function approve($id)
    {
        try {
            $user = Auth::user();

            // Only admins can approve payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Try to find the payment in either cash or gcash tables
            $payment = null;
            $paymentType = '';

            // Check if it's a cash payment
            try {
                $payment = \App\Models\CashPayment::findOrFail($id);
                $paymentType = 'cash';
            } catch (\Exception $e) {
                // Not a cash payment, try GCash payment
                try {
                    $payment = \App\Models\GcashPayment::findOrFail($id);
                    $paymentType = 'gcash';
                } catch (\Exception $e) {
                    // Not found in either table
                    abort(404, 'Payment not found.');
                }
            }

            if ($payment->payment_status !== 'Pending') {
                return redirect()->back()
                    ->with('error', 'Only pending payments can be approved.');
            }

            $officerName = isset($user->firstname) && isset($user->lastname)
                ? "{$user->firstname} {$user->lastname}"
                : ($user->name ?? 'Admin');

            $payment->update([
                'payment_status' => 'Paid',
                'officer_in_charge' => $officerName
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            Log::error('Payment approval failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Reject a pending payment.
     */
    public function reject($id)
    {
        try {
            $user = Auth::user();

            // Only admins can reject payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Try to find the payment in either cash or gcash tables
            $payment = null;
            $paymentType = '';

            // Check if it's a cash payment
            try {
                $payment = \App\Models\CashPayment::findOrFail($id);
                $paymentType = 'cash';
            } catch (\Exception $e) {
                // Not a cash payment, try GCash payment
                try {
                    $payment = \App\Models\GcashPayment::findOrFail($id);
                    $paymentType = 'gcash';
                } catch (\Exception $e) {
                    // Not found in either table
                    abort(404, 'Payment not found.');
                }
            }

            if ($payment->payment_status !== 'Pending') {
                return redirect()->back()
                    ->with('error', 'Only pending payments can be rejected.');
            }

            $officerName = isset($user->firstname) && isset($user->lastname)
                ? "{$user->firstname} {$user->lastname}"
                : ($user->name ?? 'Admin');

            $payment->update([
                'payment_status' => 'Rejected',
                'officer_in_charge' => $officerName
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Payment rejection failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject payment. Please try again.');
        }
    }

    /**
     * Approve a pending non-ICS member payment.
     */
    public function approveNonIcs($id)
    {
        try {
            $nonIcsMember = NonIcsMember::findOrFail($id);
            $user = Auth::user();

            // Only admins can approve payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            if ($nonIcsMember->payment_status !== 'Pending') {
                return redirect()->back()
                    ->with('error', 'Only pending payments can be approved.');
            }

            $officerName = isset($user->firstname) && isset($user->lastname)
                ? "{$user->firstname} {$user->lastname}"
                : ($user->name ?? 'Admin');

            $nonIcsMember->update([
                'payment_status' => 'Paid',
                'officer_in_charge' => $officerName
            ]);

            return redirect()->route('admin.non-ics-members.index')
                ->with('success', 'Non-ICS member payment approved successfully.');
        } catch (\Exception $e) {
            Log::error('Non-ICS member payment approval failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Reject a pending non-ICS member payment.
     */
    public function rejectNonIcs($id)
    {
        try {
            Log::info('Rejecting non-ICS member payment', ['id' => $id, 'request_data' => request()->all()]);

            $nonIcsMember = NonIcsMember::findOrFail($id);
            Log::info('Found non-ICS member', ['id' => $nonIcsMember->id, 'email' => $nonIcsMember->email, 'status' => $nonIcsMember->payment_status]);

            $user = Auth::user();
            Log::info('User info', ['id' => $user->id, 'can_manage_payments' => $user->canManagePayments()]);

            // Only admins can reject payments
            if (!$user->canManagePayments()) {
                Log::warning('Unauthorized attempt to reject payment', ['user_id' => $user->id]);
                abort(403, 'Unauthorized.');
            }

            if ($nonIcsMember->payment_status !== 'Pending') {
                Log::warning('Attempt to reject non-pending payment', ['payment_status' => $nonIcsMember->payment_status]);
                return redirect()->back()
                    ->with('error', 'Only pending payments can be rejected.');
            }

            // Check if the user has firstname and lastname fields
            $officerName = isset($user->firstname) && isset($user->lastname)
                ? "{$user->firstname} {$user->lastname}"
                : ($user->name ?? 'Admin');

            // Use the update method with quoted values
            DB::table('non_ics_members')
                ->where('id', $nonIcsMember->id)
                ->update([
                    'payment_status' => 'Rejected',
                    'officer_in_charge' => $officerName,
                    'updated_at' => now()
                ]);

            Log::info('Non-ICS member payment rejected successfully', ['id' => $nonIcsMember->id, 'new_status' => 'Rejected']);

            return redirect()->route('admin.non-ics-members.index')
                ->with('success', 'Non-ICS member payment rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Non-ICS member payment rejection failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }



    /**
     * Show non-ICS member payment details.
     */
    public function showNonIcs($id)
    {
        try {
            $payment = NonIcsMember::findOrFail($id);
            $user = Auth::user();

            // Only admins can view non-ICS member payment details
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            return view('payments.show', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Failed to show non-ICS member payment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to show payment details. Please try again.');
        }
    }

    /**
     * Show the form for editing a non-ICS member payment.
     */
    public function editNonIcs($id)
    {
        try {
            $nonIcsMember = NonIcsMember::findOrFail($id);
            $user = Auth::user();

            // Only admins can edit non-ICS member payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Get all admin users for officer selection
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

            return view('payments.edit-non-ics', compact('nonIcsMember', 'officers'));
        } catch (\Exception $e) {
            Log::error('Failed to edit non-ICS member payment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to edit payment details. Please try again.');
        }
    }

    /**
     * Update a non-ICS member payment.
     */
    public function updateNonIcs(Request $request, $id)
    {
        try {
            $nonIcsMember = NonIcsMember::findOrFail($id);
            $user = Auth::user();

            // Only admins can update non-ICS member payments
            if (!$user->canManagePayments()) {
                abort(403, 'Unauthorized.');
            }

            // Ensure base64 directories exist
            $this->ensureDirectoryExists('base64/payments/cash');
            $this->ensureDirectoryExists('base64/payments/gcash');

            // Validate the request
            $validated = $request->validate([
                'email' => 'required|email',
                'fullname' => 'required|string|max:100',
                'course_year_section' => 'required|string|max:50',
                'mobile_no' => 'nullable|string|max:20',
                'total_price' => 'required|numeric|min:0',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                'payment_status' => 'required|string|in:Paid,Pending',
                'method' => 'required|string|in:CASH,GCASH',
                // GCASH specific fields
                'gcash_name' => 'required_if:method,GCASH|nullable|string',
                'gcash_num' => 'required_if:method,GCASH|nullable|string',
                'reference_number' => 'required_if:method,GCASH|nullable|string',
                'gcash_proof' => 'nullable|file|mimes:jpg,jpeg|max:2048',
                // CASH specific fields
                'officer_in_charge' => 'required_if:method,CASH|nullable|string',
                'receipt_control_number' => 'required_if:method,CASH|nullable|integer',
                'cash_proof' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'gcash_proof.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Update the non-ICS member payment
            $nonIcsMember->update($validated);

            // Handle file uploads if provided
            if ($request->hasFile('gcash_proof')) {
                $gcashProofFile = $request->file('gcash_proof');

                // Convert image to base64 and store in a file
                $gcashProofPath = $this->convertToBase64($gcashProofFile, 'base64/payments/gcash');

                if (!$gcashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $path = $gcashProofFile->store('payments/gcash', 'public');
                    $gcashProofPath = 'storage/' . $path;
                    Log::info('Non-ICS GCash proof stored as file: ' . $gcashProofPath);
                } else {
                    Log::info('Non-ICS GCash proof converted to base64 and stored in file: ' . $gcashProofPath);
                }

                // Delete old file if it exists
                if ($nonIcsMember->gcash_proof_path && file_exists(public_path($nonIcsMember->gcash_proof_path))) {
                    unlink(public_path($nonIcsMember->gcash_proof_path));
                }

                $nonIcsMember->update(['gcash_proof_path' => $gcashProofPath]);
            }

            if ($request->hasFile('cash_proof')) {
                $cashProofFile = $request->file('cash_proof');

                // Convert image to base64 and store in a file
                $cashProofPath = $this->convertToBase64($cashProofFile, 'base64/payments/cash');

                if (!$cashProofPath) {
                    // Fallback to regular file storage if conversion fails
                    $path = $cashProofFile->store('payments/cash', 'public');
                    $cashProofPath = 'storage/' . $path;
                    Log::info('Non-ICS Cash proof stored as file: ' . $cashProofPath);
                } else {
                    Log::info('Non-ICS Cash proof converted to base64 and stored in file: ' . $cashProofPath);
                }

                // Delete old file if it exists
                if ($nonIcsMember->cash_proof_path && file_exists(public_path($nonIcsMember->cash_proof_path))) {
                    unlink(public_path($nonIcsMember->cash_proof_path));
                }

                $nonIcsMember->update(['cash_proof_path' => $cashProofPath]);
            }

            // Redirect to the main payments page instead of the details page
            return redirect()->route('admin.payments.index')
                ->with('success', 'Non-ICS member payment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update non-ICS member payment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment details: ' . $e->getMessage())
                ->withInput();
        }
    }



    public function clientIndex(Request $request)
    {
        $user = auth()->user();

        // Initialize empty payments collection since we no longer use the Order table
        $payments = collect([]);

        // Get cash payments
        $cashQuery = \App\Models\CashPayment::where('user_id', $user->id);

        // Apply search filter for cash payments
        if ($request->has('search')) {
            $search = $request->search;
            $cashQuery->where('id', 'like', "%{$search}%");
        }

        // Apply payment method filter for cash payments
        if ($request->has('payment_method') && $request->payment_method !== '') {
            // Only show cash payments if CASH is selected
            if ($request->payment_method !== 'CASH') {
                $cashQuery->where('id', 0); // This will return no results
            }
        }

        // Apply status filter for cash payments
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $cashQuery->where('payment_status', $request->payment_status);
        }

        $cashPayments = $cashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'cash_page');

        // Get GCash payments
        $gcashQuery = \App\Models\GcashPayment::where('user_id', $user->id);

        // Apply search filter for GCash payments
        if ($request->has('search')) {
            $search = $request->search;
            $gcashQuery->where('id', 'like', "%{$search}%");
        }

        // Apply payment method filter for GCash payments
        if ($request->has('payment_method') && $request->payment_method !== '') {
            // Only show GCash payments if GCASH is selected
            if ($request->payment_method !== 'GCASH') {
                $gcashQuery->where('id', 0); // This will return no results
            }
        }

        // Apply status filter for GCash payments
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $gcashQuery->where('payment_status', $request->payment_status);
        }

        $gcashPayments = $gcashQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'gcash_page');

        // Calculate statistics - initialize to 0
        $totalPayments = 0;
        $thisMonthPayments = 0;
        $pendingPayments = 0;

        // Add cash payments statistics
        $totalPayments += \App\Models\CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->sum('total_price');

        $thisMonthPayments += \App\Models\CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $pendingPayments += \App\Models\CashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Pending')
            ->sum('total_price');

        // Add GCash payments statistics
        $totalPayments += \App\Models\GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->sum('total_price');

        $thisMonthPayments += \App\Models\GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $pendingPayments += \App\Models\GcashPayment::where('user_id', $user->id)
            ->where('payment_status', 'Pending')
            ->sum('total_price');

        return view('payments.member', compact('payments', 'cashPayments', 'gcashPayments', 'totalPayments', 'thisMonthPayments', 'pendingPayments'));
    }

    /**
     * Show the form for creating a new payment for members.
     */
    public function memberCreate()
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to access this page
        if ($user->canManagePayments()) {
            return redirect()->route('admin.payments.create')
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
        $paymentFees = PaymentFee::where('is_active', true)
                        ->orderBy('purpose')
                        ->get();

        return view('payments.member-create', compact('user', 'memberName', 'paymentFees'));
    }

    /**
     * Store a newly created payment from a member.
     */
    public function memberStore(Request $request)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Please use the admin payment creation form.');
        }

        // Ensure base64 directories exist
        $this->ensureDirectoryExists('base64/payments/cash');
        $this->ensureDirectoryExists('base64/payments/gcash');

        // Check if there is a current school calendar
        $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();
        if (!$schoolCalendarId) {
            Log::error('No current school calendar found when creating payment');
            return redirect()->back()
                ->with('error', 'No active academic year found. Please contact an administrator.')
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                // GCASH specific fields
                'gcash_name' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_num' => 'required_if:payment_method,GCASH|string|nullable',

                'reference_number' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_proof_of_payment' => 'required_if:payment_method,GCASH|file|mimes:jpg,jpeg|max:2048|nullable',
                // CASH specific fields
                'officer_in_charge' => 'required_if:payment_method,CASH|string|nullable',
                'receipt_control_number' => 'required_if:payment_method,CASH|integer|nullable',
                'cash_proof_of_payment' => 'required_if:payment_method,CASH|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required_if' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required_if' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.integer' => 'The receipt control number must be an integer.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.required_if' => 'The proof of payment is required when payment method is GCASH.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.required_if' => 'The proof of payment is required when payment method is CASH.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);



            // Handle file uploads
            $gcashProofPath = null;
            $cashProofPath = null;

            if ($request->hasFile('gcash_proof_of_payment') && $validated['payment_method'] === 'GCASH') {
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

            if ($request->hasFile('cash_proof_of_payment') && $validated['payment_method'] === 'CASH') {
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

            // Create the payment record based on payment method
            if ($validated['payment_method'] === 'CASH') {
                // Get the current school calendar ID
                $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();

                // Create a cash payment record
                $payment = CashPayment::create([
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId, // Add school calendar ID
                    'email' => $user->email,
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'placed_on' => now(),
                    'payment_status' => 'Pending', // Members can only submit pending payments
                    'officer_in_charge' => $validated['officer_in_charge'] ?? null,
                    'receipt_control_number' => $validated['receipt_control_number'] ?? null,
                    'cash_proof_path' => $cashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Member cash payment created:', [
                    'id' => $payment->id,
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId
                ]);

                return redirect()->route('client.payments.index')
                    ->with('success', "Cash payment #{$payment->id} submitted successfully. It is pending approval from an administrator.");
            }
            else if ($validated['payment_method'] === 'GCASH') {
                // Get the current school calendar ID (reuse if already fetched)
                if (!isset($schoolCalendarId)) {
                    $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();
                }

                // Create a GCash payment record
                $payment = GcashPayment::create([
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId, // Add school calendar ID
                    'email' => $user->email,
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'placed_on' => now(),
                    'payment_status' => 'Pending', // Members can only submit pending payments
                    'gcash_name' => $validated['gcash_name'] ?? null,
                    'gcash_num' => $validated['gcash_num'] ?? null,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'gcash_proof_path' => $gcashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Member GCash payment created:', [
                    'id' => $payment->id,
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId
                ]);

                return redirect()->route('client.payments.index')
                    ->with('success', "GCash payment #{$payment->id} submitted successfully. It is pending approval from an administrator.");
            }
            else {
                // We no longer use the Order table, so we'll just log a warning and return an error
                Log::warning('Payment method not recognized: ' . $validated['payment_method']);
                return redirect()->back()
                    ->with('error', 'Invalid payment method. Please select either CASH or GCASH.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Member payment submission failed: ' . $e->getMessage());
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
            return redirect()->route('admin.payments.edit', $id)
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Try to find the payment in different tables
        $payment = null;
        $paymentType = null;

        // Check in CashPayment table
        $cashPayment = \App\Models\CashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($cashPayment) {
            $payment = $cashPayment;
            $paymentType = 'cash';
        }

        // Check in GcashPayment table if not found
        if (!$payment) {
            $gcashPayment = \App\Models\GcashPayment::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if ($gcashPayment) {
                $payment = $gcashPayment;
                $paymentType = 'gcash';
            }
        }

        // We no longer use the Order table

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

        return view('payments.member-edit', compact('payment', 'user', 'memberName', 'paymentType'));
    }

    /**
     * Update a member payment.
     */
    public function memberUpdate(Request $request, $id)
    {
        $user = Auth::user();

        // Only allow non-admin users (members) to use this method
        if ($user->canManagePayments()) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Please use the admin payment edit form.');
        }

        // Ensure base64 directories exist
        $this->ensureDirectoryExists('base64/payments/cash');
        $this->ensureDirectoryExists('base64/payments/gcash');

        // Check if there is a current school calendar
        $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();
        if (!$schoolCalendarId) {
            Log::error('No current school calendar found when updating payment');
            return redirect()->back()
                ->with('error', 'No active academic year found. Please contact an administrator.')
                ->withInput();
        }

        // Try to find the payment in different tables
        $payment = null;
        $paymentType = null;

        // Check in CashPayment table
        $cashPayment = \App\Models\CashPayment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($cashPayment) {
            $payment = $cashPayment;
            $paymentType = 'cash';
        }

        // Check in GcashPayment table if not found
        if (!$payment) {
            $gcashPayment = \App\Models\GcashPayment::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if ($gcashPayment) {
                $payment = $gcashPayment;
                $paymentType = 'gcash';
            }
        }

        // We no longer use the Order table

        if (!$payment) {
            return redirect()->route('client.payments.index')
                ->with('error', 'Payment not found or you do not have permission to edit it.');
        }

        // Only allow editing of pending payments
        if ($payment->payment_status !== 'Pending') {
            return redirect()->route('client.payments.index')
                ->with('error', 'Only pending payments can be edited.');
        }

        try {
            $validated = $request->validate([
                'total_price' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:CASH,GCASH',
                'purpose' => 'required|string',
                'description' => 'nullable|string',
                // GCASH specific fields
                'gcash_name' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_num' => 'required_if:payment_method,GCASH|string|nullable',
                'reference_number' => 'required_if:payment_method,GCASH|string|nullable',
                'gcash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
                // CASH specific fields
                'officer_in_charge' => 'required_if:payment_method,CASH|string|nullable',
                'receipt_control_number' => 'required_if:payment_method,CASH|integer|nullable',
                'cash_proof_of_payment' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            ], [
                'officer_in_charge.required_if' => 'The officer in charge field is required when payment method is CASH.',
                'receipt_control_number.required_if' => 'The receipt control number field is required when payment method is CASH.',
                'receipt_control_number.integer' => 'The receipt control number must be an integer.',
                'purpose.required' => 'The purpose field is required.',
                'gcash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
                'cash_proof_of_payment.mimes' => 'The proof of payment must be a JPG file.',
            ]);

            // Handle file uploads based on payment type
            if ($paymentType === 'cash') {
                $cashProofPath = $payment->cash_proof_path;

                if ($request->hasFile('cash_proof_of_payment')) {
                    $cashProofFile = $request->file('cash_proof_of_payment');

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

                    // Delete old file if it exists
                    if ($payment->cash_proof_path && file_exists(public_path($payment->cash_proof_path))) {
                        unlink(public_path($payment->cash_proof_path));
                    }

                    $cashProofPath = $newCashProofPath;
                }

                // Get the current school calendar ID
                $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();

                // Update the cash payment record
                $payment->update([
                    'school_calendar_id' => $schoolCalendarId, // Add school calendar ID
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'officer_in_charge' => $validated['officer_in_charge'] ?? null,
                    'receipt_control_number' => $validated['receipt_control_number'] ?? null,
                    'cash_proof_path' => $cashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Member cash payment updated:', [
                    'id' => $payment->id,
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId
                ]);
            }
            else if ($paymentType === 'gcash') {
                $gcashProofPath = $payment->gcash_proof_path;

                if ($request->hasFile('gcash_proof_of_payment')) {
                    $gcashProofFile = $request->file('gcash_proof_of_payment');

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

                    // Delete old file if it exists
                    if ($payment->gcash_proof_path && file_exists(public_path($payment->gcash_proof_path))) {
                        unlink(public_path($payment->gcash_proof_path));
                    }

                    $gcashProofPath = $newGcashProofPath;
                }

                // Get the current school calendar ID (reuse if already fetched)
                if (!isset($schoolCalendarId)) {
                    $schoolCalendarId = SchoolCalendar::getCurrentCalendarId();
                }

                // Update the GCash payment record
                $payment->update([
                    'school_calendar_id' => $schoolCalendarId, // Add school calendar ID
                    'total_price' => $validated['total_price'],
                    'purpose' => $validated['purpose'],
                    'gcash_name' => $validated['gcash_name'] ?? null,
                    'gcash_num' => $validated['gcash_num'] ?? null,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'gcash_proof_path' => $gcashProofPath,
                    'description' => $validated['description'] ?? null,
                ]);

                Log::info('Member GCash payment updated:', [
                    'id' => $payment->id,
                    'user_id' => $user->id,
                    'school_calendar_id' => $schoolCalendarId
                ]);
            }
            else {
                // We no longer use the Order table
                Log::warning('Payment type not recognized: ' . $paymentType);
                return redirect()->route('client.payments.index')
                    ->with('error', 'Invalid payment type. Please contact an administrator.');
            }

            return redirect()->route('client.payments.index')
                ->with('success', 'Payment updated successfully. It is still pending approval from an administrator.');

        } catch (\Exception $e) {
            Log::error('Member payment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Ensure a directory exists, creating it if necessary
     *
     * @param string $path
     * @return bool
     */
    private function ensureDirectoryExists(string $path): bool
    {
        $fullPath = public_path($path);
        if (!file_exists($fullPath)) {
            return mkdir($fullPath, 0755, true);
        }
        return true;
    }
}
