<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'PLAYER TAG REQUIRED',
            'name.min' => 'PLAYER TAG TOO SHORT (MIN 3)',
            'name.max' => 'PLAYER TAG TOO LONG (MAX 50)',
            'email.required' => 'PLAYER ID REQUIRED',
            'email.email' => 'INVALID PLAYER ID FORMAT',
            'email.unique' => 'PLAYER ID ALREADY REGISTERED',
            'password.required' => 'ACCESS CODE REQUIRED',
            'password.confirmed' => 'ACCESS CODE CONFIRMATION FAILED',
            'password.min' => 'ACCESS CODE TOO WEAK (MIN 8 CHARS)',
        ];
    }
}
