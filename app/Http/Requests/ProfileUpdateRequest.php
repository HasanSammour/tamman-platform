<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ];
        
        // Specialist validation rules
        if ($user->hasRole('specialist')) {
            $rules['license_number'] = ['nullable', 'string', 'max:100'];
            $rules['specialization'] = ['nullable', 'string', 'max:255'];
            $rules['qualifications'] = ['nullable', 'string'];
            $rules['bio'] = ['nullable', 'string', 'max:1000'];
            $rules['consultation_fee'] = ['nullable', 'numeric', 'min:0', 'max:1000'];
            $rules['languages'] = ['nullable', 'string', 'max:255'];
            $rules['experience_years'] = ['nullable', 'integer', 'min:0', 'max:50'];
        }
        
        // Donor validation rules
        if ($user->hasRole('donor')) {
            $rules['organization_name'] = ['nullable', 'string', 'max:255'];
            $rules['tax_id'] = ['nullable', 'string', 'max:100'];
        }
        
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'phone' => __('Phone number'),
            'gender' => __('Gender'),
            'date_of_birth' => __('Date of birth'),
            'license_number' => __('License number'),
            'specialization' => __('Specialization'),
            'qualifications' => __('Qualifications'),
            'bio' => __('Biography'),
            'consultation_fee' => __('Consultation fee'),
            'languages' => __('Languages'),
            'experience_years' => __('Years of experience'),
            'organization_name' => __('Organization name'),
            'tax_id' => __('Tax ID'),
        ];
    }
}
