<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;

        // Handle optional password change
        if ($request->filled('current_password') || $request->filled('new_password') || $request->filled('new_password_confirmation')) {
            // If any password field is filled, all three must be provided
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required',
                'new_password_confirmation' => 'required|same:new_password',
            ]);

            // Validate current password
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                        
            } else {
                return redirect()->route('profile')->with('error', 'Current password is incorrect');
            }
        }

        $user->save();



        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
}
