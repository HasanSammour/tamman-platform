<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SpecialistProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialistApplicationController extends Controller
{
    public function create()
    {
        return view('specialist.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:specialist_profiles,license_number',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'consultation_fee' => 'required|numeric|min:0|max:1000',
            'languages' => 'required|string',
            'qualifications' => 'required|string|min:10',
            'bio' => 'required|string|min:20',
            'license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'terms' => 'accepted',
        ]);

        try {
            // Generate unique file names using user name + timestamp + random
            $userName = Str::slug($request->name, '_');
            $timestamp = now()->format('Ymd_His');
            $random = Str::random(8);

            // Upload License File
            $licenseFile = $request->file('license_file');
            $licenseExtension = $licenseFile->getClientOriginalExtension();
            $licenseName = "license_{$userName}_{$timestamp}_{$random}.{$licenseExtension}";
            $licensePath = $licenseFile->storeAs('specialist/licenses', $licenseName, 'public');

            // Upload Certificate File
            $certificateFile = $request->file('certificate_file');
            $certificateExtension = $certificateFile->getClientOriginalExtension();
            $certificateName = "certificate_{$userName}_{$timestamp}_{$random}.{$certificateExtension}";
            $certificatePath = $certificateFile->storeAs('specialist/certificates', $certificateName, 'public');

            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => false,
            ]);

            // Assign specialist role
            $user->assignRole('specialist');

            // Format languages
            $languages = $request->languages;
            if (is_array($languages)) {
                $languages = implode(', ', $languages);
            }

            // Create specialist profile
            SpecialistProfile::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'qualifications' => $request->qualifications,
                'bio' => $request->bio,
                'consultation_fee' => $request->consultation_fee,
                'languages' => $languages,
                'experience_years' => $request->experience_years,
                'license_file' => $licensePath,
                'certificate_file' => $certificatePath,
                'is_verified' => false,
                'application_status' => 'pending',
                'applied_at' => now(),
            ]);

            return redirect()->route('specialist.application.success')
                ->with('success', 'تم تقديم طلبك بنجاح! سيتم مراجعته من قبل الإدارة وسيتم إشعارك عبر البريد الإلكتروني عند الموافقة.');

        } catch (\Exception $e) {
            // Delete uploaded files if error
            if (isset($licensePath) && Storage::disk('public')->exists($licensePath)) {
                Storage::disk('public')->delete($licensePath);
            }
            if (isset($certificatePath) && Storage::disk('public')->exists($certificatePath)) {
                Storage::disk('public')->delete($certificatePath);
            }
            
            return back()->with('error', 'حدث خطأ أثناء تقديم الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $exists = User::where('email', $request->email)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    public function success()
    {
        return view('specialist.application-success');
    }
}