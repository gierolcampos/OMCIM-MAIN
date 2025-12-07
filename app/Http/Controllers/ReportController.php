<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\CashPayment;
use App\Models\GcashPayment;
use App\Models\PaymentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the report generation form.
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->canManageReports()) {
            return redirect()->route('home.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Get events for the attendance report dropdown
        $events = Event::orderBy('start_date_time', 'desc')->get();

        // Get payment fees for the payment report dropdown
        $paymentFees = PaymentFee::where('is_active', true)->orderBy('purpose')->get();

        return view('reports.generate', compact('events', 'paymentFees'));
    }

    /**
     * Generate the requested report.
     */
    public function generate(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        try {
            $reportType = $request->input('report_type');
            $exportFormat = $request->input('export_format', 'pdf');

            switch ($reportType) {
                case 'attendance':
                    return $this->generateAttendanceReport($request, $exportFormat);
                case 'payment':
                    return $this->generatePaymentReport($request, $exportFormat);
                case 'membership':
                    return $this->generateMembershipReport($request, $exportFormat);
                case 'evaluation':
                    return $this->generateEvaluationReport($request, $exportFormat);
                default:
                    return redirect()->back()
                        ->with('error', 'Invalid report type selected.');
            }
        } catch (\Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Generate attendance report.
     */
    private function generateAttendanceReport(Request $request, $exportFormat)
    {
        // Validate request
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'attendance_status' => 'nullable|in:all,present,absent,excused',
        ]);

        // Get the event
        $event = Event::findOrFail($request->input('event_id'));

        // Get attendance data from the event attendances
        $attendanceStatus = $request->input('attendance_status', 'all');

        // Get the actual attendances for this event with their users
        $attendances = $event->attendances()->with('user')->get();

        // Map the attendances to the report format
        $attendanceData = [];
        foreach ($attendances as $attendance) {
            // Map event attendance status to report status
            $reportStatus = 'absent'; // Default status

            if ($attendance->status === 'attending') {
                $reportStatus = 'present';
            } else if ($attendance->status === 'not_attending') {
                $reportStatus = 'absent';
            } else if ($attendance->status === 'maybe') {
                $reportStatus = 'excused';
            }

            $attendanceData[] = [
                'id' => $attendance->user->id,
                'name' => $attendance->user->firstname . ' ' . $attendance->user->lastname,
                'email' => $attendance->user->email,
                'status' => $reportStatus,
                'comment' => $attendance->comment,
            ];
        }

        // Filter by status if needed
        if ($attendanceStatus !== 'all') {
            $attendanceData = array_filter($attendanceData, function($item) use ($attendanceStatus) {
                return $item['status'] === $attendanceStatus;
            });
        }

        // Prepare data for the report
        $reportData = [
            'title' => 'Attendance Report',
            'event' => $event,
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'generated_by' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'attendance' => $attendanceData,
        ];

        // Generate the report in the requested format
        if ($exportFormat === 'csv') {
            return $this->generateCsvReport($reportData, 'attendance_report_' . $event->id);
        } else {
            return $this->generatePdfReport($reportData, 'attendance_report', 'reports.attendance_pdf');
        }
    }

    /**
     * Generate payment report.
     */
    private function generatePaymentReport(Request $request, $exportFormat)
    {
        // Validate request
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'payment_method' => 'nullable|in:all,cash,gcash',
            'payment_status' => 'nullable|in:all,Paid,Pending',
            'purpose' => 'nullable|string',
        ]);

        // Build query based on filters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $paymentMethod = $request->input('payment_method', 'all');
        $paymentStatus = $request->input('payment_status', 'all');
        $purpose = $request->input('purpose', 'all');

        // Get cash payments
        $cashPayments = CashPayment::query();
        if ($dateFrom) {
            $cashPayments->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $cashPayments->whereDate('created_at', '<=', $dateTo);
        }
        if ($paymentStatus !== 'all') {
            $cashPayments->where('payment_status', $paymentStatus);
        }
        if ($purpose !== 'all') {
            $cashPayments->where('purpose', $purpose);
        }

        // Get GCash payments
        $gcashPayments = GcashPayment::query();
        if ($dateFrom) {
            $gcashPayments->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $gcashPayments->whereDate('created_at', '<=', $dateTo);
        }
        if ($paymentStatus !== 'all') {
            $gcashPayments->where('payment_status', $paymentStatus);
        }
        if ($purpose !== 'all') {
            $gcashPayments->where('purpose', $purpose);
        }

        // Combine results based on payment method filter
        $payments = [];
        if ($paymentMethod === 'all' || $paymentMethod === 'cash') {
            $cashResults = $cashPayments->get()->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'email' => $payment->email,
                    'method' => 'Cash',
                    'amount' => $payment->total_price,
                    'purpose' => $payment->purpose,
                    'status' => $payment->payment_status,
                    'date' => $payment->created_at->format('Y-m-d'),
                ];
            })->toArray();
            $payments = array_merge($payments, $cashResults);
        }

        if ($paymentMethod === 'all' || $paymentMethod === 'gcash') {
            $gcashResults = $gcashPayments->get()->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'email' => $payment->email,
                    'method' => 'GCash',
                    'amount' => $payment->total_price,
                    'purpose' => $payment->purpose,
                    'status' => $payment->payment_status,
                    'date' => $payment->created_at->format('Y-m-d'),
                ];
            })->toArray();
            $payments = array_merge($payments, $gcashResults);
        }

        // Calculate totals
        $totalAmount = array_sum(array_column($payments, 'amount'));
        $totalPaid = array_sum(array_column(array_filter($payments, function($p) {
            return $p['status'] === 'Paid';
        }), 'amount'));
        $totalPending = array_sum(array_column(array_filter($payments, function($p) {
            return $p['status'] === 'Pending';
        }), 'amount'));

        // Prepare data for the report
        $reportData = [
            'title' => 'Payment Report',
            'date_from' => $dateFrom ? Carbon::parse($dateFrom)->format('F d, Y') : 'All time',
            'date_to' => $dateTo ? Carbon::parse($dateTo)->format('F d, Y') : 'Present',
            'payment_method' => ucfirst($paymentMethod),
            'payment_status' => $paymentStatus === 'all' ? 'All' : $paymentStatus,
            'purpose' => $purpose === 'all' ? 'All Purposes' : $purpose,
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'generated_by' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'payments' => $payments,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
        ];

        // Generate the report in the requested format
        if ($exportFormat === 'csv') {
            return $this->generateCsvReport($reportData, 'payment_report');
        } else {
            return $this->generatePdfReport($reportData, 'payment_report', 'reports.payment_pdf');
        }
    }

    /**
     * Generate membership report.
     */
    private function generateMembershipReport(Request $request, $exportFormat)
    {
        // Validate request
        $request->validate([
            'status' => 'nullable|in:all,active,pending,inactive',
            'role' => 'nullable|in:all,member,admin',
            'join_date_from' => 'nullable|date',
            'join_date_to' => 'nullable|date|after_or_equal:join_date_from',
        ]);

        // Build query based on filters
        $status = $request->input('status', 'all');
        $role = $request->input('role', 'all');
        $joinDateFrom = $request->input('join_date_from');
        $joinDateTo = $request->input('join_date_to');

        $members = User::query();

        if ($status !== 'all') {
            $members->where('status', $status);
        }

        if ($role !== 'all') {
            if ($role === 'admin') {
                $members->whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']);
            } else {
                $members->where('user_role', 'member');
            }
        }

        if ($joinDateFrom) {
            $members->whereDate('created_at', '>=', $joinDateFrom);
        }

        if ($joinDateTo) {
            $members->whereDate('created_at', '<=', $joinDateTo);
        }

        $members = $members->get();

        // Calculate statistics
        $totalMembers = $members->count();
        $activeMembers = $members->where('status', 'active')->count();
        $pendingMembers = $members->where('status', 'pending')->count();
        $inactiveMembers = $members->where('status', 'inactive')->count();
        $adminCount = $members->whereIn('user_role', ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM'])->count();
        $regularMemberCount = $members->where('user_role', 'member')->count();

        // Prepare data for the report
        $reportData = [
            'title' => 'Membership Report',
            'status' => ucfirst($status),
            'role' => ucfirst($role),
            'join_date_from' => $joinDateFrom ? Carbon::parse($joinDateFrom)->format('F d, Y') : 'All time',
            'join_date_to' => $joinDateTo ? Carbon::parse($joinDateTo)->format('F d, Y') : 'Present',
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'generated_by' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'members' => $members,
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'pending_members' => $pendingMembers,
            'inactive_members' => $inactiveMembers,
            'admin_count' => $adminCount,
            'regular_member_count' => $regularMemberCount,
        ];

        // Generate the report in the requested format
        if ($exportFormat === 'csv') {
            return $this->generateCsvReport($reportData, 'membership_report');
        } else {
            return $this->generatePdfReport($reportData, 'membership_report', 'reports.membership_pdf');
        }
    }

    /**
     * Generate a PDF report.
     */
    private function generatePdfReport($data, $filename, $view)
    {
        $pdf = PDF::loadView($view, $data);

        // Set PDF options to properly handle images
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
            'isPhpEnabled' => true,
        ]);

        return $pdf->download($filename . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Generate evaluation report.
     */
    private function generateEvaluationReport(Request $request, $exportFormat)
    {
        // Validate request
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rating_type' => 'nullable|in:all,positive,negative,neutral',
        ]);

        // Get the event with evaluations and users
        $event = Event::with(['evaluations.user', 'evaluations.responses.question'])->findOrFail($request->input('event_id'));

        // Get rating type filter
        $ratingType = $request->input('rating_type', 'all');

        // Get actual evaluations from the database
        $eventEvaluations = $event->evaluations;

        // Format evaluations for the report
        $evaluations = [];
        foreach ($eventEvaluations as $evaluation) {
            // Skip if user is null (deleted user)
            if (!$evaluation->user) {
                continue;
            }

            // Create evaluation data
            $evaluationData = [
                'id' => $evaluation->id,
                'user_name' => $evaluation->user->firstname . ' ' . $evaluation->user->lastname,
                'email' => $evaluation->user->email,
                'rating' => $evaluation->rating,
                'feedback' => $evaluation->feedback,
                'submitted_at' => $evaluation->created_at->format('Y-m-d H:i:s'),
            ];

            // Add responses if available
            if ($evaluation->responses->isNotEmpty()) {
                $responseData = [];
                foreach ($evaluation->responses as $response) {
                    if ($response->question) {
                        $responseData[] = [
                            'question' => $response->question->question_text,
                            'type' => $response->question->question_type,
                            'rating' => $response->rating_value,
                            'text' => $response->response_text,
                        ];
                    }
                }
                $evaluationData['responses'] = $responseData;
            }

            $evaluations[] = $evaluationData;
        }

        // Filter by rating type if needed
        if ($ratingType !== 'all') {
            $evaluations = array_filter($evaluations, function($item) use ($ratingType) {
                if ($ratingType === 'positive') {
                    return $item['rating'] >= 4;
                } elseif ($ratingType === 'negative') {
                    return $item['rating'] <= 2;
                } else { // neutral
                    return $item['rating'] == 3;
                }
            });
        }

        // Calculate statistics
        $totalEvaluations = count($evaluations);
        $ratingSum = array_sum(array_column($evaluations, 'rating'));
        $averageRating = $totalEvaluations > 0 ? $ratingSum / $totalEvaluations : 0;

        // Count ratings by score
        $ratingCounts = [
            '5' => 0,
            '4' => 0,
            '3' => 0,
            '2' => 0,
            '1' => 0,
        ];

        foreach ($evaluations as $evaluation) {
            $ratingCounts[$evaluation['rating']]++;
        }

        // Calculate percentages
        $ratingPercentages = [];
        foreach ($ratingCounts as $rating => $count) {
            $ratingPercentages[$rating] = $totalEvaluations > 0 ? ($count / $totalEvaluations) * 100 : 0;
        }

        // Get questions summary if available
        $questionSummary = [];
        if (!empty($evaluations) && isset($evaluations[0]['responses'])) {
            // Get all questions from the first evaluation
            $questions = [];
            foreach ($eventEvaluations as $evaluation) {
                foreach ($evaluation->responses as $response) {
                    if ($response->question && !isset($questions[$response->question->id])) {
                        $questions[$response->question->id] = [
                            'id' => $response->question->id,
                            'text' => $response->question->question_text,
                            'type' => $response->question->question_type,
                            'ratings' => [],
                            'responses' => [],
                        ];
                    }
                }
            }

            // Collect all ratings and responses for each question
            foreach ($eventEvaluations as $evaluation) {
                foreach ($evaluation->responses as $response) {
                    if ($response->question) {
                        $questionId = $response->question->id;

                        if ($response->rating_value) {
                            $questions[$questionId]['ratings'][] = $response->rating_value;
                        }

                        if ($response->response_text) {
                            $questions[$questionId]['responses'][] = [
                                'text' => $response->response_text,
                                'user' => $evaluation->user ? $evaluation->user->firstname . ' ' . $evaluation->user->lastname : 'Unknown',
                            ];
                        }
                    }
                }
            }

            // Calculate averages for rating questions
            foreach ($questions as &$question) {
                if (!empty($question['ratings'])) {
                    $question['average_rating'] = array_sum($question['ratings']) / count($question['ratings']);

                    // Count ratings by score
                    $question['rating_counts'] = [
                        '5' => 0,
                        '4' => 0,
                        '3' => 0,
                        '2' => 0,
                        '1' => 0,
                    ];

                    foreach ($question['ratings'] as $rating) {
                        $question['rating_counts'][$rating]++;
                    }
                }
            }

            $questionSummary = array_values($questions);
        }

        // Prepare data for the report
        $reportData = [
            'title' => 'Event Evaluation Report',
            'event' => $event,
            'rating_type' => ucfirst($ratingType),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'generated_by' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'evaluations' => $evaluations,
            'total_evaluations' => $totalEvaluations,
            'average_rating' => $averageRating,
            'rating_counts' => $ratingCounts,
            'rating_percentages' => $ratingPercentages,
            'question_summary' => $questionSummary,
        ];

        // Generate the report in the requested format
        if ($exportFormat === 'csv') {
            return $this->generateCsvReport($reportData, 'evaluation_report_' . $event->id);
        } else {
            return $this->generatePdfReport($reportData, 'evaluation_report', 'reports.evaluation_pdf');
        }
    }

    /**
     * Generate a CSV report.
     */
    private function generateCsvReport($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add report title and metadata
            fputcsv($file, [$data['title']]);
            fputcsv($file, ['Generated at:', $data['generated_at']]);
            fputcsv($file, ['Generated by:', $data['generated_by']]);
            fputcsv($file, []); // Empty line

            // Add headers and data based on report type
            if (isset($data['attendance'])) {
                // Attendance report
                fputcsv($file, ['Event:', $data['event']->title]);
                fputcsv($file, ['Date:', Carbon::parse($data['event']->start_date_time)->format('F d, Y')]);
                fputcsv($file, []); // Empty line

                fputcsv($file, ['ID', 'Name', 'Email', 'Status', 'Comment']);
                foreach ($data['attendance'] as $record) {
                    fputcsv($file, [
                        $record['id'],
                        $record['name'],
                        $record['email'],
                        ucfirst($record['status']),
                        $record['comment'] ?? '',
                    ]);
                }
            } elseif (isset($data['payments'])) {
                // Payment report
                fputcsv($file, ['Date Range:', $data['date_from'] . ' to ' . $data['date_to']]);
                fputcsv($file, ['Payment Method:', $data['payment_method']]);
                fputcsv($file, ['Payment Status:', $data['payment_status']]);
                fputcsv($file, ['Purpose:', $data['purpose']]);
                fputcsv($file, []); // Empty line

                fputcsv($file, ['ID', 'Email', 'Method', 'Amount', 'Purpose', 'Status', 'Date']);
                foreach ($data['payments'] as $payment) {
                    fputcsv($file, [
                        $payment['id'],
                        $payment['email'],
                        $payment['method'],
                        $payment['amount'],
                        $payment['purpose'],
                        $payment['status'],
                        $payment['date'],
                    ]);
                }

                fputcsv($file, []); // Empty line
                fputcsv($file, ['Total Amount:', $data['total_amount']]);
                fputcsv($file, ['Total Paid:', $data['total_paid']]);
                fputcsv($file, ['Total Pending:', $data['total_pending']]);
            } elseif (isset($data['members'])) {
                // Membership report
                fputcsv($file, ['Status:', $data['status']]);
                fputcsv($file, ['Role:', $data['role']]);
                fputcsv($file, ['Join Date Range:', $data['join_date_from'] . ' to ' . $data['join_date_to']]);
                fputcsv($file, []); // Empty line

                fputcsv($file, ['ID', 'Name', 'Email', 'Status', 'Role', 'Join Date']);
                foreach ($data['members'] as $member) {
                    fputcsv($file, [
                        $member->id,
                        $member->firstname . ' ' . $member->lastname,
                        $member->email,
                        ucfirst($member->status),
                        in_array($member->user_role, ['superadmin', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']) ? 'Admin' : 'Member',
                        $member->created_at->format('Y-m-d'),
                    ]);
                }

                fputcsv($file, []); // Empty line
                fputcsv($file, ['Total Members:', $data['total_members']]);
                fputcsv($file, ['Active Members:', $data['active_members']]);
                fputcsv($file, ['Pending Members:', $data['pending_members']]);
                fputcsv($file, ['Inactive Members:', $data['inactive_members']]);
                fputcsv($file, ['Admin Count:', $data['admin_count']]);
                fputcsv($file, ['Regular Member Count:', $data['regular_member_count']]);
            } elseif (isset($data['evaluations'])) {
                // Evaluation report
                fputcsv($file, ['Event:', $data['event']->title]);
                fputcsv($file, ['Date:', Carbon::parse($data['event']->start_date_time)->format('F d, Y')]);
                fputcsv($file, ['Rating Type:', $data['rating_type']]);
                fputcsv($file, []); // Empty line

                // Summary statistics
                fputcsv($file, ['Total Evaluations:', $data['total_evaluations']]);
                fputcsv($file, ['Average Rating:', number_format($data['average_rating'], 2) . ' / 5.00']);
                fputcsv($file, []); // Empty line

                // Rating distribution
                fputcsv($file, ['Rating Distribution:']);
                fputcsv($file, ['Rating', 'Count', 'Percentage']);
                foreach ($data['rating_counts'] as $rating => $count) {
                    fputcsv($file, [
                        $rating . ' stars',
                        $count,
                        number_format($data['rating_percentages'][$rating], 2) . '%'
                    ]);
                }
                fputcsv($file, []); // Empty line

                // Individual evaluations
                fputcsv($file, ['Evaluation Details:']);
                fputcsv($file, ['ID', 'Name', 'Email', 'Rating', 'Feedback', 'Submitted At']);
                foreach ($data['evaluations'] as $evaluation) {
                    fputcsv($file, [
                        $evaluation['id'],
                        $evaluation['user_name'],
                        $evaluation['email'],
                        $evaluation['rating'] . ' / 5',
                        $evaluation['feedback'],
                        $evaluation['submitted_at'],
                    ]);
                }

                // Add question summary if available
                if (isset($data['question_summary']) && !empty($data['question_summary'])) {
                    fputcsv($file, []); // Empty line
                    fputcsv($file, ['Question Summary:']);

                    foreach ($data['question_summary'] as $question) {
                        fputcsv($file, []); // Empty line
                        fputcsv($file, ['Question:', $question['text']]);
                        fputcsv($file, ['Type:', $question['type']]);

                        if (!empty($question['ratings'])) {
                            fputcsv($file, ['Average Rating:', number_format($question['average_rating'], 2) . ' / 5.00']);

                            fputcsv($file, ['Rating Distribution:']);
                            fputcsv($file, ['Rating', 'Count']);
                            foreach ($question['rating_counts'] as $rating => $count) {
                                fputcsv($file, [$rating . ' stars', $count]);
                            }
                        }

                        if (!empty($question['responses'])) {
                            fputcsv($file, ['Text Responses:']);
                            foreach ($question['responses'] as $response) {
                                fputcsv($file, [$response['user'], $response['text']]);
                            }
                        }
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
