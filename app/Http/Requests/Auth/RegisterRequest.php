<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1920-01-01'],
            'referral_code' => ['nullable', 'string', 'max:20', 'exists:users,referral_code'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'This phone number is already registered.',
            'referral_code.exists' => 'The referral code you entered is invalid.',
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
            'date_of_birth.before' => 'You must be at least 18 years old.',
        ];
    }
}