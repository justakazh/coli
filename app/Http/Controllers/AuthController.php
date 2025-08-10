<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // If already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.index');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate input with best practice rules
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string', 'captcha'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'captcha.required' => 'Please complete the captcha.',
            'captcha.captcha' => 'The captcha is invalid, please try again.'
        ]);

        // Attempt authentication
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Return with error message
        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }


    public function changeProfile()
    {
        return view('auth.change-profile');
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id],
        ]);

        if ($request->new_password) {
            $request->validate([
                'new_password' => ['required', 'string', 'confirmed'],
            ]);
            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return redirect()->back()->withErrors(['current_password' => 'The provided credentials do not match our records.']);
            }
            Auth::user()->update(['password' => Hash::make($request->new_password)]);
        }
        

        

        Auth::user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        return redirect()->back()->with('status', 'Profile updated successfully.');    
    }


    /**
     * Log the user out and invalidate the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Optionally, you can log logout activity here

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }


    public function refreshCaptcha()
    {
        $captcha = captcha_img('flat');
        return response()->json([
            'captcha' => $captcha
        ]);
    }
}
