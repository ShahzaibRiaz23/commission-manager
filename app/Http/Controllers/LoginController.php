<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('Super Admin')) {
                return redirect()->intended(route('dashboard'));
            }

            if ($user->hasRole('VA')) {
                return redirect()->intended(route('va.dashboard'));
            }

            if ($user->hasRole('Sales Officer')) {
                return redirect()->intended(route('sales.dashboard'));
            }


            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}