<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Redirect based on user role
            if ($request->user()->isAdmin()) {
                return redirect()->intended(route('dashboard', absolute: false));
            } else {
                return redirect()->intended(route('home.index', absolute: false));
            }
        }

        return view('auth.verify-email');
    }
}
