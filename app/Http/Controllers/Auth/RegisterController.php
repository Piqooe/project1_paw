<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => (string) $request->string('name'),
            'email' => (string) $request->string('email'),
            'password' => Hash::make((string) $request->string('password')),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        Log::info('New user registered', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('dashboard');
    }
}
