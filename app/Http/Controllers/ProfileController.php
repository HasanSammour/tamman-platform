<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // Load specialist profile if exists
        $specialistProfile = $user->specialistProfile;
        $donorProfile = $user->donorProfile;

        // Get user statistics based on role
        $stats = $this->getUserStats($user);

        // Check if specialist is verified (for field locking)
        $isVerifiedSpecialist = $specialistProfile && $specialistProfile->is_verified;

        // Get certificate and license info using model methods
        $certificateInfo = $specialistProfile ? $specialistProfile->getCertificateInfo() : null;
        $licenseInfo = $specialistProfile ? $specialistProfile->getLicenseInfo() : null;

        return view('profile.edit', compact('user', 'specialistProfile', 'donorProfile', 'stats', 'isVerifiedSpecialist', 'certificateInfo', 'licenseInfo'));
    }

    /**
     * Update the user's profile information via AJAX.
     */
    public function update(ProfileUpdateRequest $request)
    {
        try {
            $user = $request->user();

            // Update basic user info
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Update specialist profile if user is specialist
            if ($user->hasRole('specialist') && $user->specialistProfile) {
                $specialistProfile = $user->specialistProfile;
                $isVerified = $specialistProfile->is_verified;

                // Only update fields that are allowed based on verification status
                $updatableFields = [];

                // Always updatable fields
                if ($request->has('bio')) {
                    $updatableFields['bio'] = $request->bio;
                }
                if ($request->has('consultation_fee')) {
                    $updatableFields['consultation_fee'] = $request->consultation_fee;
                }
                if ($request->has('languages')) {
                    $updatableFields['languages'] = $request->languages;
                }
                if ($request->has('experience_years')) {
                    $updatableFields['experience_years'] = $request->experience_years;
                }

                // Only allow editing of locked fields if NOT verified OR user is admin
                if (!$isVerified || $user->hasRole('admin')) {
                    if ($request->has('license_number')) {
                        $updatableFields['license_number'] = $request->license_number;
                    }
                    if ($request->has('specialization')) {
                        $updatableFields['specialization'] = $request->specialization;
                    }
                    if ($request->has('qualifications')) {
                        $updatableFields['qualifications'] = $request->qualifications;
                    }
                }

                if (!empty($updatableFields)) {
                    $specialistProfile->update($updatableFields);
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('Profile updated successfully!'),
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    /**
     * Update user's profile image via AJAX.
     */
    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old profile image if exists
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new image
        $file = $request->file('profile_image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_images', $filename, 'public');

        $user->profile_image = $path;
        $user->save();

        // Get the new image URL using the model method
        $imageUrl = $user->getProfileImageUrl();

        return response()->json([
            'success' => true,
            'message' => __('Profile image updated successfully'),
            'image_url' => $imageUrl,
            'has_image' => true,
            'user_name' => $user->name,
            'user_initial' => mb_substr($user->name, 0, 1, 'UTF-8'),
        ]);
    }

    /**
     * Remove profile image via AJAX.
     */
    public function removeProfileImage()
    {
        $user = Auth::user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->profile_image = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Profile image removed successfully'),
            'initial' => mb_substr($user->name, 0, 1, 'UTF-8'),
            'user_name' => $user->name,
        ]);
    }

    /**
     * Update user's password via AJAX.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Password updated successfully!'),
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
    
        $user = $request->user();
    
        // Delete profile image
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }
    
        // Delete specialist documents
        if ($user->specialistProfile) {
            if ($user->specialistProfile->certificate_file && Storage::disk('public')->exists($user->specialistProfile->certificate_file)) {
                Storage::disk('public')->delete($user->specialistProfile->certificate_file);
            }
            if ($user->specialistProfile->license_file && Storage::disk('public')->exists($user->specialistProfile->license_file)) {
                Storage::disk('public')->delete($user->specialistProfile->license_file);
            }
            $user->specialistProfile->delete();
        }
    
        if ($user->donorProfile) {
            $user->donorProfile->delete();
        }
    
        // Check if request expects JSON (AJAX request)
        if ($request->expectsJson() || $request->ajax()) {
            // Get user info before deleting
            $userEmail = $user->email;
            
            // Logout and delete
            Auth::logout();
            $user->delete();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.'
            ]);
        }
        
        // For non-AJAX requests (fallback)
        Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Get user statistics based on role.
     */
    private function getUserStats($user)
    {
        $stats = [
            'member_since' => $user->created_at->translatedFormat('F Y'),
            'last_active' => $user->updated_at->diffForHumans(),
        ];

        if ($user->hasRole('patient')) {
            $stats['total_sessions'] = \App\Models\TherapySession::where('patient_id', $user->id)->count();
            $stats['total_points'] = $user->total_points;
            $stats['completed_tests'] = \App\Models\TestResult::where('user_id', $user->id)->distinct('test_type')->count('test_type');
        }

        if ($user->hasRole('specialist') && $user->specialistProfile) {
            $stats['total_sessions'] = $user->specialistProfile->total_sessions ?? 0;
            $stats['average_rating'] = $user->specialistProfile->rating_avg ?? 0;
            $stats['total_clients'] = \App\Models\TherapySession::where('specialist_id', $user->id)->distinct('patient_id')->count('patient_id');
            $stats['verification_status'] = $user->specialistProfile->is_verified ? 'verified' : 'pending';
            $stats['is_verified'] = $user->specialistProfile->is_verified;
        }

        if ($user->hasRole('donor') && $user->donorProfile) {
            $stats['total_donated'] = $user->donorProfile->total_donated ?? 0;
        }

        return $stats;
    }
}