<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 300;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'PLAYER ID REQUIRED',
            'email.email' => 'INVALID PLAYER ID FORMAT',
            'password.required' => 'ACCESS CODE REQUIRED',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::DECAY_SECONDS);

            Log::warning('Failed login attempt', [
                'email' => (string) $this->string('email'),
                'ip' => $this->ip(),
                'user_agent' => (string) $this->userAgent(),
                'attempts' => RateLimiter::attempts($this->throttleKey()),
            ]);

            throw ValidationException::withMessages([
                'email' => 'ACCESS DENIED — CREDENTIALS DO NOT MATCH ANY RECORD',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        Log::warning('Login rate limit hit', [
            'email' => (string) $this->string('email'),
            'ip' => $this->ip(),
            'retry_in_seconds' => $seconds,
        ]);

        throw ValidationException::withMessages([
            'email' => "SYSTEM LOCKED — RETRY IN {$seconds}s",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->string('email')) . '|' . $this->ip());
    }
}
