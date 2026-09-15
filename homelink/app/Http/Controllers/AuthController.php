<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt([...$data, 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match an active account.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
            'role' => ['required', Rule::in(['customer', 'provider'])],
            'phone' => ['required', 'regex:/^[+0-9 ()-]{9,20}$/'], 'city' => ['required', 'string', 'max:80'],
        ]);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome to HomeLink. Your account is ready.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function profile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['required', 'regex:/^[+0-9 ()-]{9,20}$/'], 'city' => ['required', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:1500'],
        ]);
        $request->user()->update($data);

        return back()->with('success', 'Your profile has been updated.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()]]);
        $request->user()->update(['password' => $data['password']]);
        $request->session()->regenerate();

        return back()->with('success', 'Password updated.');
    }
}
