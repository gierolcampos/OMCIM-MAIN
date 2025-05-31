<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\NonIcsMemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentFeeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('home.index') : redirect()->route('login');
});




Route::middleware(['auth', 'verified'])->group(function () {

    // Client side

    // Events routes
    Route::get('/events/calendar', function() {
        return redirect()->route('events.custom-calendar');
    })->name('events.calendar');
    Route::get('/events/custom-calendar', [EventController::class, 'customCalendar'])->name('events.custom-calendar');
    Route::get('/events/export/ical', [EventController::class, 'exportIcal'])->name('events.export.ical');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/attend', [EventController::class, 'attend'])->name('events.attend');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::post('/events/{event}/evaluate', [App\Http\Controllers\EventEvaluationController::class, 'store'])->name('events.evaluate');
    Route::get('/events/{event}/evaluation', [App\Http\Controllers\EventEvaluationController::class, 'showForm'])->name('events.evaluation');
    Route::get('/events/{event}/evaluation/view', [App\Http\Controllers\EventEvaluationController::class, 'viewForm'])->name('events.evaluation.view');
    Route::post('/events/{event}/evaluation', [App\Http\Controllers\EventEvaluationController::class, 'submitEvaluation'])->name('events.evaluate.submit');
    Route::get('/events/{event}/evaluation/thankyou', [App\Http\Controllers\EventEvaluationController::class, 'thankYou'])->name('events.evaluation.thankyou');
    Route::get('/events/{event}/evaluation/respondents', [App\Http\Controllers\EventEvaluationController::class, 'respondents'])->name('events.evaluation.respondents');
    Route::post('/events/{event}/toggle-evaluation', [EventController::class, 'toggleEvaluation'])->name('events.toggle-evaluation');

    // Evaluation Questions Management
    Route::get('/events/{event}/questions', [App\Http\Controllers\EvaluationQuestionController::class, 'index'])->name('events.questions.index');
    Route::get('/events/{event}/questions/create', [App\Http\Controllers\EvaluationQuestionController::class, 'create'])->name('events.questions.create');
    Route::post('/events/{event}/questions', [App\Http\Controllers\EvaluationQuestionController::class, 'store'])->name('events.questions.store');
    Route::get('/events/{event}/questions/{question}/edit', [App\Http\Controllers\EvaluationQuestionController::class, 'edit'])->name('events.questions.edit');
    Route::put('/events/{event}/questions/{question}', [App\Http\Controllers\EvaluationQuestionController::class, 'update'])->name('events.questions.update');
    Route::delete('/events/{event}/questions/{question}', [App\Http\Controllers\EvaluationQuestionController::class, 'destroy'])->name('events.questions.destroy');
    Route::post('/events/{event}/questions/reorder', [App\Http\Controllers\EvaluationQuestionController::class, 'reorder'])->name('events.questions.reorder');

    // Redirect route for /events to /omcms/events is defined at the end of the middleware group

    // Announcements routes
    Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [App\Http\Controllers\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [App\Http\Controllers\AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{announcement}/toggle-pin', [App\Http\Controllers\AnnouncementController::class, 'togglePin'])->name('announcements.togglePin');

    Route::get('/omcms/ics_hall', [HomeController::class, 'index'])->name('home.index');

    // Client-side routes with different URLs
    Route::get('/omcms/events', function() {
        return redirect()->route('events.custom-calendar');
    })->name('omcms.events');
    Route::get('/omcms/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('omcms.announcements');

    Route::get('/omcms/payments', [HomeController::class, 'payments'])->name('payments');

    Route::get('/omcms/about_us/about_ics', [AboutUsController::class, 'about_ics'])->name('about_us.about_ics');

    Route::get('/omcms/about_us/vision_mission', [AboutUsController::class, 'vision_mission'])->name('about_us.vision_mission');

    Route::get('/omcms/about_us/history', [AboutUsController::class, 'history'])->name('about_us.history');

    Route::get('/omcms/about_us/logo_symbolism', [AboutUsController::class, 'logo_symbolism'])->name('about_us.logo_symbolism');

    Route::get('/omcms/about_us/student_leaders', [AboutUsController::class, 'student_leaders'])->name('about_us.student_leaders');

    Route::get('/omcms/about_us/developers', [AboutUsController::class, 'developers'])->name('about_us.developers');

    Route::get('/omcms/about_us/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('about_us.contact');
    Route::post('/omcms/about_us/contact', [App\Http\Controllers\ContactController::class, 'submitContactForm'])->name('contact.submit');
    Route::post('/omcms/about_us/feedback', [App\Http\Controllers\ContactController::class, 'submitFeedback'])->name('feedback.submit');

    Route::get('/aboutus', function () { return view('aboutus.index');
    })->name('aboutus');

     // Client Payment Routes
     Route::prefix('omcms/payments')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/', [PaymentController::class, 'clientIndex'])->name('client.payments.index');
        Route::get('/create', [PaymentController::class, 'memberCreate'])->name('client.payments.create');
        Route::post('/', [PaymentController::class, 'memberStore'])->name('client.payments.store');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('client.payments.show');
        Route::get('/{id}/edit', [PaymentController::class, 'memberEdit'])->name('client.payments.edit');
        Route::put('/{id}', [PaymentController::class, 'memberUpdate'])->name('client.payments.update');

        // Payment Fees API
        Route::get('/fees', [PaymentController::class, 'getAllPaymentFees'])->name('client.payments.fees.all');
        Route::get('/fees/by-purpose', [PaymentController::class, 'getPaymentFeeByPurpose'])->name('client.payments.fees.by-purpose');
    });

    // Add a redirect route for /events to the custom calendar

});


 // Admin side




 Route::prefix('admin')->middleware(['auth', 'role:superadmin'])->group(function () {
    // Route::get('/', function () {
    //     return view('admin.dashboard');
    // });

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/member-stats', [DashboardController::class, 'memberStats'])->name('dashboard.member-stats');
    Route::get('/dashboard/event-stats', [DashboardController::class, 'eventStats'])->name('dashboard.event-stats');
    Route::get('/events/calendar', function() {
        return redirect()->route('admin.events.custom-calendar');
    })->name('admin.events.calendar');
    Route::get('/events/custom-calendar', [EventController::class, 'customCalendar'])->name('admin.events.custom-calendar');
    Route::get('/events/export/ical', [EventController::class, 'exportIcal'])->name('admin.events.export.ical');
    // Event routes - only accessible to Secretary and PIO
    Route::middleware(['permission:manage-events'])->group(function () {
        Route::get('/events', [EventController::class, 'adminIndex'])->name('admin.events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('admin.events.create');
        Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');

        Route::get('/events/{event}', [EventController::class, 'show'])->name('admin.events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');
        Route::post('/events/{event}/attend', [EventController::class, 'attend'])->name('admin.events.attend');
        Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('admin.events.attendees');
        Route::post('/events/{event}/evaluate', [App\Http\Controllers\EventEvaluationController::class, 'store'])->name('admin.events.evaluate');
        Route::get('/events/{event}/evaluation', [App\Http\Controllers\EventEvaluationController::class, 'showForm'])->name('admin.events.evaluation');
        Route::get('/events/{event}/evaluation/view', [App\Http\Controllers\EventEvaluationController::class, 'viewForm'])->name('admin.events.evaluation.view');
        Route::post('/events/{event}/evaluation', [App\Http\Controllers\EventEvaluationController::class, 'submitEvaluation'])->name('admin.events.evaluate.submit');
        Route::get('/events/{event}/evaluation/thankyou', [App\Http\Controllers\EventEvaluationController::class, 'thankYou'])->name('admin.events.evaluation.thankyou');
        Route::post('/events/{event}/toggle-evaluation', [EventController::class, 'toggleEvaluation'])->name('admin.events.toggle-evaluation');

        // Evaluation Questions Management
        Route::get('/events/{event}/questions', [App\Http\Controllers\EvaluationQuestionController::class, 'index'])->name('admin.events.questions.index');
        Route::get('/events/{event}/questions/create', [App\Http\Controllers\EvaluationQuestionController::class, 'create'])->name('admin.events.questions.create');
        Route::post('/events/{event}/questions', [App\Http\Controllers\EvaluationQuestionController::class, 'store'])->name('admin.events.questions.store');
        Route::get('/events/{event}/questions/{question}/edit', [App\Http\Controllers\EvaluationQuestionController::class, 'edit'])->name('admin.events.questions.edit');
        Route::put('/events/{event}/questions/{question}', [App\Http\Controllers\EvaluationQuestionController::class, 'update'])->name('admin.events.questions.update');
        Route::delete('/events/{event}/questions/{question}', [App\Http\Controllers\EvaluationQuestionController::class, 'destroy'])->name('admin.events.questions.destroy');
        Route::post('/events/{event}/questions/reorder', [App\Http\Controllers\EvaluationQuestionController::class, 'reorder'])->name('admin.events.questions.reorder');
    });

     // Members routes - only accessible to Secretary and Superadmins
    Route::middleware(['permission:manage-members'])->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::patch('/members/{member}/status', [MemberController::class, 'updateStatus'])->name('members.updateStatus');
        Route::patch('/members/{member}/role', [MemberController::class, 'updateRole'])->name('members.updateRole');

    });

    // Admin members route - only accessible to Secretary and Superadmins
    Route::middleware(['permission:manage-members'])->group(function () {
        Route::get('/admin-members', [MemberController::class, 'index'])->name('admin.members.index');
    });

     // Reports routes - only accessible to Secretary and Superadmins
    Route::middleware(['permission:manage-reports'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('admin.reports.generate');
    });

    // Admin Announcements routes - only accessible to Secretary and PIO
    Route::middleware(['permission:manage-announcements'])->group(function () {
        Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'adminIndex'])->name('admin.announcements.index');
        Route::get('/announcements/create', [App\Http\Controllers\AnnouncementController::class, 'create'])->name('admin.announcements.create');
        Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::post('/announcements/{announcement}/toggle-pin', [App\Http\Controllers\AnnouncementController::class, 'togglePin'])->name('admin.announcements.togglePin');
        Route::get('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('admin.announcements.show');
        Route::get('/announcements/{announcement}/edit', [App\Http\Controllers\AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
        Route::put('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    });

    // School Calendar Routes - only accessible to Superadmin
    Route::prefix('school-calendars')->middleware(['role:superadmin'])->group(function () {
        Route::get('/', [App\Http\Controllers\SchoolCalendarController::class, 'index'])->name('admin.school-calendars.index');
        Route::get('/create', [App\Http\Controllers\SchoolCalendarController::class, 'create'])->name('admin.school-calendars.create');
        Route::post('/', [App\Http\Controllers\SchoolCalendarController::class, 'store'])->name('admin.school-calendars.store');
        Route::get('/{schoolCalendar}/edit', [App\Http\Controllers\SchoolCalendarController::class, 'edit'])->name('admin.school-calendars.edit');
        Route::put('/{schoolCalendar}', [App\Http\Controllers\SchoolCalendarController::class, 'update'])->name('admin.school-calendars.update');
        Route::delete('/{schoolCalendar}', [App\Http\Controllers\SchoolCalendarController::class, 'destroy'])->name('admin.school-calendars.destroy');
        Route::post('/{schoolCalendar}/set-current', [App\Http\Controllers\SchoolCalendarController::class, 'setCurrent'])->name('admin.school-calendars.set-current');
    });

    // Account Deletion Requests Routes - only accessible to Superadmin
    Route::prefix('deletion-requests')->middleware(['role:superadmin'])->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DeletionRequestController::class, 'index'])->name('admin.deletion-requests.index');
        Route::delete('/{id}', [App\Http\Controllers\Admin\DeletionRequestController::class, 'approve'])->name('admin.deletion-requests.approve');
        Route::patch('/{id}', [App\Http\Controllers\Admin\DeletionRequestController::class, 'reject'])->name('admin.deletion-requests.reject');
    });

    // Admin Payment Routes - only accessible to Treasurer, Auditor, Business Manager, and Superadmins
    Route::prefix('payments')->middleware(['permission:manage-payments'])->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('/create', [PaymentController::class, 'create'])->name('admin.payments.create');
        Route::post('/', [PaymentController::class, 'store'])->name('admin.payments.store');

        // Payment Fees API
        Route::get('/fees', [PaymentController::class, 'getAllPaymentFees'])->name('admin.payments.fees.all');
        Route::get('/fees/by-purpose', [PaymentController::class, 'getPaymentFeeByPurpose'])->name('admin.payments.fees.by-purpose');

        // Payment Fees Management
        Route::get('/fees/manage', [PaymentFeeController::class, 'index'])->name('admin.payment-fees.index');
        Route::get('/fees/create', [PaymentFeeController::class, 'create'])->name('admin.payment-fees.create');
        Route::post('/fees', [PaymentFeeController::class, 'store'])->name('admin.payment-fees.store');
        Route::get('/fees/{id}', [PaymentFeeController::class, 'show'])->name('admin.payment-fees.show');
        Route::get('/fees/{id}/edit', [PaymentFeeController::class, 'edit'])->name('admin.payment-fees.edit');
        Route::put('/fees/{id}', [PaymentFeeController::class, 'update'])->name('admin.payment-fees.update');
        Route::delete('/fees/{id}', [PaymentFeeController::class, 'destroy'])->name('admin.payment-fees.destroy');
        Route::patch('/fees/{id}/toggle-active', [PaymentFeeController::class, 'toggleActive'])->name('admin.payment-fees.toggle-active');

        // Non-ICS Member Payment Routes - must be defined before the generic routes
        // Redirect non-ics index to main payments page
        Route::get('/non-ics', function() {
            return redirect()->route('admin.payments.index');
        })->name('admin.payments.non-ics.index');

        Route::get('/non-ics/{id}', [PaymentController::class, 'showNonIcs'])->name('admin.payments.show-non-ics');
        Route::get('/non-ics/{id}/edit', [PaymentController::class, 'editNonIcs'])->name('admin.payments.non-ics.edit');
        Route::put('/non-ics/{id}', [PaymentController::class, 'updateNonIcs'])->name('admin.payments.non-ics.update');
        Route::post('/non-ics/{id}/approve', [PaymentController::class, 'approveNonIcs'])->name('admin.payments.approve-non-ics');
        // Reject route removed as requested

        // Regular payment routes
        Route::get('/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');
        Route::get('/{id}/edit', [PaymentController::class, 'edit'])->name('admin.payments.edit');
        Route::put('/{id}', [PaymentController::class, 'update'])->name('admin.payments.update');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');
        Route::post('/{id}/approve', [PaymentController::class, 'approve'])->name('admin.payments.approve');
        // Reject route removed as requested
    });


    // Admin Non-ICS Members Routes - only accessible to Treasurer, Auditor, Business Manager, and Superadmins
    Route::prefix('non-ics-members')->middleware(['permission:manage-payments'])->group(function () {
        Route::get('/', [NonIcsMemberController::class, 'index'])->name('admin.non-ics-members.index');
        Route::get('/create', [NonIcsMemberController::class, 'create'])->name('admin.non-ics-members.create');
        Route::post('/', [NonIcsMemberController::class, 'store'])->name('admin.non-ics-members.store');
        Route::get('/{id}', [NonIcsMemberController::class, 'show'])->name('admin.non-ics-members.show');
        Route::get('/{id}/edit', [NonIcsMemberController::class, 'edit'])->name('admin.non-ics-members.edit');
        Route::put('/{id}', [NonIcsMemberController::class, 'update'])->name('admin.non-ics-members.update');
        Route::delete('/{id}', [NonIcsMemberController::class, 'destroy'])->name('admin.non-ics-members.destroy');
    });
});


// Protected routes that require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{notification}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

// Test route for authentication

// Route to display completed events list
Route::get('/completed-events', function() {
    return view('completed_events_list');
})->name('completed.events.list');

// Example routes
Route::get('/examples/event-card', function() {
    return view('examples.event-card-example');
})->name('examples.event-card');

require __DIR__.'/auth.php';

// Include payment types routes
require __DIR__.'/payment-types.php';

// Include role-based routes
require __DIR__.'/role-based-routes.php';

// API routes for event status updates
Route::post('/api/events/update-status', [App\Http\Controllers\Api\EventStatusController::class, 'updateStatus'])
    ->middleware(['auth'])
    ->name('api.events.update-status');

// Image conversion routes
Route::middleware(['auth'])->prefix('api/images')->group(function () {
    Route::post('/to-base64', [App\Http\Controllers\ImageController::class, 'convertToBase64'])->name('api.images.to-base64');
    Route::post('/from-base64', [App\Http\Controllers\ImageController::class, 'revertFromBase64'])->name('api.images.from-base64');

    // Chunked file processing routes
    Route::post('/chunk/get', [App\Http\Controllers\Api\ChunkedImageController::class, 'getChunk'])->name('api.images.chunk.get');
    Route::post('/chunk/save', [App\Http\Controllers\Api\ChunkedImageController::class, 'saveChunk'])->name('api.images.chunk.save');
    Route::post('/chunk/merge', [App\Http\Controllers\Api\ChunkedImageController::class, 'mergeChunks'])->name('api.images.chunk.merge');
});
