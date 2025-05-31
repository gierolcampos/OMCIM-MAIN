<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmitted;
use App\Mail\FeedbackReceived;

class ContactController extends Controller
{
    /**
     * Display the contact page
     */
    public function index()
    {
        return view('aboutus.contact');
    }

    /**
     * Handle contact form submission
     */
    public function submitContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        // Send email to admin
        try {
            Mail::to(config('mail.admin_address', 'admin@navotaspolytechniccollege.edu.ph'))
                ->send(new ContactFormSubmitted($validated));

            return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again later.');
        }
    }

    /**
     * Handle feedback form submission
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'feedback_type' => 'required|string|in:bug,feature,improvement,other',
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        // Save feedback to database
        $feedback = Feedback::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'feedback_type' => $validated['feedback_type'],
            'feedback' => $validated['feedback'],
            'rating' => $validated['rating'],
            'user_id' => auth()->id(),
        ]);

        // Send confirmation email
        try {
            Mail::to($validated['email'])
                ->send(new FeedbackReceived($feedback));

            return back()->with('success', 'Thank you for your feedback! It helps us improve OMCMS.');
        } catch (\Exception $e) {
            // Even if email fails, feedback is saved
            return back()->with('success', 'Thank you for your feedback! It helps us improve OMCMS.');
        }
    }
}
