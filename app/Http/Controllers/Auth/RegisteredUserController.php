<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if student number already exists
        $existingStudent = User::where('studentnumber', $request->studentnumber)->first();
        if ($existingStudent) {
            return back()->withInput()->withErrors([
                'studentnumber' => 'This student number is already registered. Please use a different student number or contact support if you believe this is an error.'
            ]);
        }

        // Check if email already exists
        $existingEmail = User::where('email', $request->email)->first();
        if ($existingEmail) {
            return back()->withInput()->withErrors([
                'email' => 'This email is already registered. Please use a different email or contact support if you believe this is an error.'
            ]);
        }

        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['required', 'regex:/^[0-9]{11}$/'],
            'studentnumber' => ['required', 'regex:/^[0-9]{6}$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                'regex:/^[a-zA-Z0-9._%+-]+@navotaspolytechniccollege\.edu\.ph$/',
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'course' => ['required', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'suffix'=> $request->suffix,
            'mobile_no' => $request->mobile_no,
            'studentnumber' => $request->studentnumber,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'course' => $request->course,
            'major' => $request->major,
            'year' => $request->year,
            'section' => $request->section,
            'id_school_calendar' => '1ST SEMESTER A.Y. 2024-2025',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Thank you for registering in OMCMS - ICS Portal. Your account will be under validation to ensure authenticity and eligibility. We will email you once you can login. Thank you!');
    }
}
