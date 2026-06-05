{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Profile') . ' - ' . __('Tamman'))

@section('page-title', __('My Profile'))

@section('content')
    <div class="profile-container">
        <div class="profile-grid">
            <!-- Sidebar / Profile Card -->
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar" id="profileAvatar">
                            @php
                                $profileImage = $user->getProfileImageUrl();
                                $userInitial = mb_substr($user->name, 0, 1, 'UTF-8');
                            @endphp
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $user->name }}" id="profileAvatarImage">
                            @else
                                <div class="avatar-placeholder-large" id="profileAvatarPlaceholder">{{ $userInitial }}</div>
                            @endif
                            <button class="change-avatar-btn" id="changeAvatarBtn" title="{{ __('Change Avatar') }}">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3>{{ $user->name }}</h3>
                        <p class="user-role">
                            @if($user->hasRole('admin'))
                                <i class="fas fa-shield-alt"></i> {{ __('Administrator') }}
                            @elseif($user->hasRole('specialist'))
                                <i class="fas fa-user-md"></i> {{ __('Specialist') }}
                                @if($user->isVerifiedSpecialist())
                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> {{ __('Verified') }}</span>
                                @else
                                    <span class="pending-badge"><i class="fas fa-clock"></i> {{ __('Pending Verification') }}</span>
                                @endif
                            @elseif($user->hasRole('donor'))
                                <i class="fas fa-hand-holding-heart"></i> {{ __('Donor') }}
                            @else
                                <i class="fas fa-user"></i> {{ __('Patient') }}
                            @endif
                        </p>
                        <p class="user-email"><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                        @if($user->phone)
                            <p class="user-phone"><i class="fas fa-phone"></i> {{ $user->phone }}</p>
                        @endif
                    </div>

                    <div class="profile-menu">
                        <button class="profile-menu-item active" data-tab="personal">
                            <i class="fas fa-user"></i> {{ __('Personal Information') }}
                        </button>
                        @if($user->hasRole('specialist') && $specialistProfile)
                            <button class="profile-menu-item" data-tab="professional">
                                <i class="fas fa-briefcase"></i> {{ __('Professional Information') }}
                            </button>
                            <button class="profile-menu-item" data-tab="documents">
                                <i class="fas fa-file-alt"></i> {{ __('Documents') }}
                            </button>
                        @endif
                        @if($user->hasRole('donor') && $donorProfile)
                            <button class="profile-menu-item" data-tab="donor">
                                <i class="fas fa-hand-holding-heart"></i> {{ __('Donor Information') }}
                            </button>
                        @endif
                        <button class="profile-menu-item" data-tab="security">
                            <i class="fas fa-lock"></i> {{ __('Security') }}
                        </button>
                        <button class="profile-menu-item" data-tab="danger">
                            <i class="fas fa-trash-alt"></i> {{ __('Danger Zone') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-main">
                <!-- Personal Information Tab -->
                <div class="profile-tab-content active" id="tab-personal">
                    <div class="profile-section">
                        <div class="section-header">
                            <h3><i class="fas fa-user"></i> {{ __('Personal Information') }}</h3>
                            <button class="btn-edit-section" data-section="personal" data-form="personalInfoForm">
                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                            </button>
                        </div>
                        <form id="personalInfoForm" class="profile-form" method="POST"
                            action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">{{ __('Full Name') }} <span class="required">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required disabled>
                                    <div class="error-message" id="name-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required disabled>
                                    <div class="error-message" id="email-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">{{ __('Phone Number') }}</label>
                                    <input type="tel" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}" disabled>
                                    <div class="error-message" id="phone-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="gender">{{ __('Gender') }}</label>
                                    <select name="gender" id="gender" class="form-control" disabled>
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                            {{ __('Male') }}
                                        </option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                            {{ __('Other') }}
                                        </option>
                                    </select>
                                    <div class="error-message" id="gender-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_of_birth">{{ __('Date of Birth') }}</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" disabled>
                                    <div class="error-message" id="date_of_birth-error"></div>
                                </div>
                            </div>
                            <div class="form-actions" style="display: none;">
                                <button type="submit" class="btn-save">
                                    <span class="btn-text">{{ __('Save Changes') }}</span>
                                    <span class="btn-spinner" style="display: none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>
                                <button type="button" class="btn-cancel-edit">{{ __('Cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Professional Information Tab (Specialist Only) -->
                @if($user->hasRole('specialist') && $specialistProfile)
                    <div class="profile-tab-content" id="tab-professional">
                        <div class="profile-section">
                            <div class="section-header">
                                <h3><i class="fas fa-briefcase"></i> {{ __('Professional Information') }}</h3>
                                @if(!$specialistProfile->is_verified)
                                    <button class="btn-edit-section" data-section="professional" data-form="professionalInfoForm">
                                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                                    </button>
                                @else
                                    <span class="info-badge">
                                        <i class="fas fa-info-circle"></i> {{ __('Verified by Admin') }}
                                    </span>
                                @endif
                            </div>
                            <form id="professionalInfoForm" class="profile-form" method="POST"
                                action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="specialization">{{ __('Specialization') }} <span
                                                class="required">*</span></label>
                                        <input type="text" name="specialization" id="specialization" class="form-control"
                                            value="{{ old('specialization', $specialistProfile->specialization) }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>
                                        <div class="error-message" id="specialization-error"></div>
                                        @if($specialistProfile->is_verified)
                                            <small
                                                class="form-text text-muted">{{ __('Contact admin to change this information') }}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="license_number">{{ __('License Number') }} <span
                                                class="required">*</span></label>
                                        <input type="text" name="license_number" id="license_number" class="form-control"
                                            value="{{ old('license_number', $specialistProfile->license_number) }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>
                                        <div class="error-message" id="license_number-error"></div>
                                        @if($specialistProfile->is_verified)
                                            <small
                                                class="form-text text-muted">{{ __('Contact admin to change this information') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="qualifications">{{ __('Qualifications & Education') }} <span
                                                class="required">*</span></label>
                                        <textarea name="qualifications" id="qualifications" class="form-control" rows="3" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>{{ old('qualifications', $specialistProfile->qualifications) }}</textarea>
                                        <div class="error-message" id="qualifications-error"></div>
                                        @if($specialistProfile->is_verified)
                                            <small
                                                class="form-text text-muted">{{ __('Contact admin to change this information') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="consultation_fee">{{ __('Consultation Fee (USD)') }} <span
                                                class="required">*</span></label>
                                        <input type="number" name="consultation_fee" id="consultation_fee" class="form-control"
                                            step="0.01"
                                            value="{{ old('consultation_fee', $specialistProfile->consultation_fee) }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>
                                        <div class="error-message" id="consultation_fee-error"></div>
                                        @if($specialistProfile->is_verified)
                                            <small
                                                class="form-text text-muted">{{ __('Contact admin to change this information') }}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="experience_years">{{ __('Years of Experience') }}</label>
                                        <input type="number" name="experience_years" id="experience_years" class="form-control"
                                            value="{{ old('experience_years', $specialistProfile->experience_years) }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>
                                        <div class="error-message" id="experience_years-error"></div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="languages">{{ __('Languages Spoken') }}</label>
                                        <input type="text" name="languages" id="languages" class="form-control"
                                            value="{{ old('languages', $specialistProfile->languages) }}"
                                            placeholder="{{ __('e.g., Arabic, English') }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>
                                        <div class="error-message" id="languages-error"></div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="bio">{{ __('Professional Bio') }}</label>
                                        <textarea name="bio" id="bio" class="form-control" rows="4"
                                            placeholder="{{ __('Tell patients about your approach to therapy...') }}" {{ $specialistProfile->is_verified ? 'disabled' : '' }}>{{ old('bio', $specialistProfile->bio) }}</textarea>
                                        <div class="error-message" id="bio-error"></div>
                                    </div>
                                </div>
                                @if(!$specialistProfile->is_verified)
                                    <div class="form-actions" style="display: none;">
                                        <button type="submit" class="btn-save">
                                            <span class="btn-text">{{ __('Save Changes') }}</span>
                                            <span class="btn-spinner" style="display: none;"><i
                                                    class="fas fa-spinner fa-spin"></i></span>
                                        </button>
                                        <button type="button" class="btn-cancel-edit">{{ __('Cancel') }}</button>
                                    </div>
                                @else
                                    <div class="contact-admin-notice">
                                        <i class="fas fa-envelope"></i>
                                        <span>{{ __('To update your professional information, please contact the administrator at
                                            support@tamman.ps') }}</span>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Documents Tab (Specialist Only) -->
                    <div class="profile-tab-content" id="tab-documents">
                        <div class="profile-section">
                            <div class="section-header">
                                <h3><i class="fas fa-file-alt"></i> {{ __('Professional Documents') }}</h3>
                            </div>

                            <!-- Certificate Section -->
                            <div class="document-section">
                                <h4><i class="fas fa-certificate"></i> {{ __('Professional Certificate') }}</h4>
                                @php
                                    $certificateInfo = $specialistProfile->getCertificateInfo();
                                @endphp
                                <div class="document-preview" id="certificatePreview">
                                    @if($certificateInfo['has_file'])
                                        <div class="document-wrapper">
                                            @if($certificateInfo['is_image'])
                                                <img src="{{ $certificateInfo['url'] }}" alt="{{ __('Certificate') }}"
                                                    class="document-image" id="certificateImage"
                                                    style="max-width: 100%; max-height: 300px; border-radius: 12px; margin-bottom: 15px;">
                                            @else
                                                <div class="document-file">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span>{{ $certificateInfo['filename'] }}</span>
                                                    <a href="{{ $certificateInfo['url'] }}" target="_blank"
                                                        class="btn-view-file">{{ __('View File') }}</a>
                                                </div>
                                            @endif
                                            <button class="btn-toggle-document" data-doc="certificate"
                                                style="background: none; border: none; color: #7c3aed; cursor: pointer;">
                                                🔍 {{ __('Hide') }}
                                            </button>
                                        </div>
                                    @else
                                        <div class="document-placeholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>{{ __('No certificate uploaded') }}</p>
                                        </div>
                                    @endif
                                    @if(!$specialistProfile->is_verified)
                                        <form action="{{ route('profile.certificate.upload') }}" method="POST"
                                            enctype="multipart/form-data" class="upload-form document-upload-form"
                                            data-type="certificate">
                                            @csrf
                                            <div class="file-upload">
                                                <input type="file" name="certificate" id="certificate" accept=".pdf,.jpg,.jpeg,.png"
                                                    style="display: none;">
                                                <button type="button" class="btn-upload"
                                                    onclick="document.getElementById('certificate').click()">
                                                    <i class="fas fa-upload"></i>
                                                    {{ $certificateInfo['has_file'] ? __('Replace') : __('Upload Certificate') }}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- License Section -->
                            <div class="document-section">
                                <h4><i class="fas fa-id-card"></i> {{ __('Professional License') }}</h4>
                                @php
                                    $licenseInfo = $specialistProfile->getLicenseInfo();
                                @endphp
                                <div class="document-preview" id="licensePreview">
                                    @if($licenseInfo['has_file'])
                                        <div class="document-wrapper">
                                            @if($licenseInfo['is_image'])
                                                <img src="{{ $licenseInfo['url'] }}" alt="{{ __('License') }}" class="document-image"
                                                    id="licenseImage"
                                                    style="max-width: 100%; max-height: 300px; border-radius: 12px; margin-bottom: 15px;">
                                            @else
                                                <div class="document-file">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span>{{ $licenseInfo['filename'] }}</span>
                                                    <a href="{{ $licenseInfo['url'] }}" target="_blank"
                                                        class="btn-view-file">{{ __('View File') }}</a>
                                                </div>
                                            @endif
                                            <button class="btn-toggle-document" data-doc="license"
                                                style="background: none; border: none; color: #7c3aed; cursor: pointer;">
                                                🔍 {{ __('Hide') }}
                                            </button>
                                        </div>
                                    @else
                                        <div class="document-placeholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>{{ __('No license uploaded') }}</p>
                                        </div>
                                    @endif
                                    @if(!$specialistProfile->is_verified)
                                        <form action="{{ route('profile.license.upload') }}" method="POST"
                                            enctype="multipart/form-data" class="upload-form document-upload-form"
                                            data-type="license">
                                            @csrf
                                            <div class="file-upload">
                                                <input type="file" name="license" id="license" accept=".pdf,.jpg,.jpeg,.png"
                                                    style="display: none;">
                                                <button type="button" class="btn-upload"
                                                    onclick="document.getElementById('license').click()">
                                                    <i class="fas fa-upload"></i>
                                                    {{ $licenseInfo['has_file'] ? __('Replace') : __('Upload License') }}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @if($specialistProfile->is_verified)
                                <div class="contact-admin-notice">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ __('Documents are verified. To update your documents, please contact the administrator.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Donor Information Tab (Donor Only) -->
                @if($user->hasRole('donor') && $donorProfile)
                    <div class="profile-tab-content" id="tab-donor">
                        <div class="profile-section">
                            <div class="section-header">
                                <h3><i class="fas fa-hand-holding-heart"></i> {{ __('Donor Information') }}</h3>
                            </div>
                            <div class="donor-info-card">
                                <div class="donor-stats">
                                    <div class="donor-stat">
                                        <span class="stat-label">{{ __('Total Donated') }}</span>
                                        <span class="stat-value">${{ number_format($donorProfile->total_donated, 2) }}</span>
                                    </div>
                                    <div class="donor-stat">
                                        <span class="stat-label">{{ __('Donor Since') }}</span>
                                        <span
                                            class="stat-value">{{ $donorProfile->created_at->translatedFormat('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="organization_name">{{ __('Organization Name') }}</label>
                                        <input type="text" id="organization_name" class="form-control"
                                            value="{{ $donorProfile->organization_name }}" disabled>
                                        <small
                                            class="form-text text-muted">{{ __('Contact admin to change this information') }}</small>
                                    </div>
                                </div>
                                <div class="contact-admin-notice">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ __('To update your donor information, please contact the administrator at
                                        support@tamman.ps') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Security Tab -->
                <div class="profile-tab-content" id="tab-security">
                    <div class="profile-section">
                        <div class="section-header">
                            <h3><i class="fas fa-lock"></i> {{ __('Change Password') }}</h3>
                        </div>
                        <form id="passwordForm" class="profile-form" method="POST"
                            action="{{ route('user-password.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="current_password">{{ __('Current Password') }} <span
                                            class="required">*</span></label>
                                    <div class="password-wrapper">
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control" required>
                                        <button type="button" class="password-toggle" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="error-message" id="current_password-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">{{ __('New Password') }} <span class="required">*</span></label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="password" class="form-control" required>
                                        <button type="button" class="password-toggle" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">{{ __('At least 8 characters') }}</small>
                                    <div class="error-message" id="password-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirm New Password') }} <span
                                            class="required">*</span></label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" required>
                                        <button type="button" class="password-toggle" data-target="password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save-password">
                                    <span class="btn-text">{{ __('Update Password') }}</span>
                                    <span class="btn-spinner" style="display: none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone Tab -->
                <div class="profile-tab-content" id="tab-danger">
                    <div class="profile-section danger-zone">
                        <div class="section-header">
                            <h3><i class="fas fa-exclamation-triangle"></i> {{ __('Delete Account') }}</h3>
                            <span class="danger-badge">{{ __('Irreversible Action') }}</span>
                        </div>
                        <p class="danger-message">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                        </p>
                        <button class="btn-delete-account" id="deleteAccountBtn">
                            <i class="fas fa-trash-alt"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="modal-overlay">
        <div class="modal-container small">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> {{ __('Delete Account') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="deleteAccountForm" method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete your account?') }}</p>
                    <p class="text-warning">
                        {{ __('This action cannot be undone. All your data will be permanently deleted.') }}
                    </p>
                    <div class="form-group">
                        <label for="delete_password">{{ __('Enter your password to confirm') }} <span
                                class="required">*</span></label>
                        <input type="password" name="password" id="delete_password" class="form-control" required>
                        <div class="error-message" id="delete_password-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-danger">
                        <span class="btn-text">{{ __('Delete Permanently') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div id="imageUploadModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-camera"></i> {{ __('Update Profile Picture') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="imageUploadForm" method="POST" action="{{ route('profile.image.update') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="image-preview-area">
                        <div class="current-image" id="currentImagePreview">
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $user->name }}" id="currentPreviewImage">
                            @else
                                <div class="placeholder-preview" id="currentPreviewPlaceholder">{{ $userInitial }}</div>
                            @endif
                        </div>
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>{{ __('Click or drag image here') }}</p>
                            <input type="file" name="profile_image" id="profile_image_input" accept="image/*"
                                style="display: none;">
                            <button type="button" class="btn-select-image"
                                id="selectImageBtn">{{ __('Select Image') }}</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <small
                            class="form-text text-muted">{{ __('Supported formats: JPG, PNG, GIF. Max size: 2MB') }}</small>
                    </div>
                    <div class="modal-footer">
                        @if($profileImage)
                            <button type="button" class="btn-remove-image" id="removeImageBtn">{{ __('Remove Image') }}</button>
                        @endif
                        <button type="submit" class="btn-save">
                            <span class="btn-text">{{ __('Save Changes') }}</span>
                            <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    @push('styles')
        <style>
            .profile-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .profile-grid {
                display: grid;
                grid-template-columns: 320px 1fr;
                gap: 30px;
                align-items: start;
            }

            /* Profile Sidebar */
            .profile-sidebar {
                position: sticky;
                top: 100px;
            }

            .profile-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .profile-avatar-container {
                text-align: center;
                padding: 30px 20px 20px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                position: relative;
            }

            .profile-avatar {
                position: relative;
                display: inline-block;
            }

            .profile-avatar img,
            .avatar-placeholder-large {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid white;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .avatar-placeholder-large {
                background: linear-gradient(135deg, #a78bfa, #7c3aed);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                font-weight: 600;
                color: white;
            }

            .change-avatar-btn {
                position: absolute;
                bottom: 5px;
                right: 5px;
                background: white;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }

            .change-avatar-btn:hover {
                transform: scale(1.1);
                background: #7c3aed;
                color: white;
            }

            .profile-avatar-container h3 {
                color: white;
                margin-top: 15px;
                margin-bottom: 5px;
            }

            .user-role {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.85rem;
                margin-bottom: 8px;
            }

            .verified-badge,
            .pending-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.7rem;
                margin-left: 8px;
            }

            .verified-badge {
                background: #10b981;
                color: white;
            }

            .pending-badge {
                background: #f59e0b;
                color: white;
            }

            .user-email,
            .user-phone {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.8rem;
                margin: 5px 0;
            }

            .profile-menu {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .profile-menu-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                background: none;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.9rem;
                color: #374151;
                width: 100%;
                text-align: left;
            }

            .profile-menu-item:hover {
                background: #f3f4f6;
            }

            .profile-menu-item.active {
                background: #ede9fe;
                color: #7c3aed;
            }

            .profile-menu-item i {
                width: 22px;
            }

            /* Profile Main - Dynamic Height */
            .profile-main {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                height: auto;
            }

            .profile-tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
                height: auto;
            }

            .profile-tab-content.active {
                display: block;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .profile-section {
                padding: 30px;
                border-bottom: 1px solid #e5e7eb;
            }

            .profile-section:last-child {
                border-bottom: none;
            }

            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .section-header h3 {
                margin: 0;
                font-size: 1.2rem;
                color: #1f2937;
            }

            .btn-edit-section {
                background: #f3f4f6;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.8rem;
            }

            .btn-edit-section:hover {
                background: #e5e7eb;
            }

            .info-badge {
                background: #d1fae5;
                color: #065f46;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
            }

            /* Form Styles */
            .form-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 0;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                font-size: 0.85rem;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .form-control {
                width: 100%;
                padding: 10px 14px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control:disabled {
                background: #f9fafb;
                cursor: not-allowed;
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            .password-wrapper {
                position: relative;
            }

            .password-wrapper .form-control {
                padding-right: 45px;
            }

            .password-toggle {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #9ca3af;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.7rem;
                margin-top: 5px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            .form-text {
                font-size: 0.7rem;
                color: #6b7280;
                margin-top: 5px;
                display: block;
            }

            .form-actions {
                display: flex;
                gap: 15px;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-save,
            .btn-save-password,
            .btn-danger {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 24px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-save:hover,
            .btn-save-password:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-danger {
                background: #ef4444;
            }

            .btn-danger:hover {
                background: #dc2626;
            }

            .btn-cancel-edit {
                background: #f3f4f6;
                border: none;
                padding: 10px 24px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-save:disabled,
            .btn-save-password:disabled,
            .btn-danger:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            /* Document Section */
            .document-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #e5e7eb;
            }

            .document-section h4 {
                margin-bottom: 15px;
                color: #1f2937;
            }

            .document-preview {
                text-align: center;
            }

            .document-wrapper {
                display: inline-block;
            }

            .document-image {
                max-width: 100%;
                max-height: 300px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                margin-bottom: 15px;
            }

            .document-file {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                padding: 20px;
                background: #f9fafb;
                border-radius: 12px;
                margin-bottom: 15px;
            }

            .document-file i {
                font-size: 2rem;
                color: #ef4444;
            }

            .btn-view-file {
                background: #7c3aed;
                color: white;
                padding: 6px 12px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 0.75rem;
            }

            .btn-upload {
                background: #f3f4f6;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                cursor: pointer;
                margin-top: 15px;
            }

            .document-placeholder {
                text-align: center;
                padding: 40px;
                background: #f9fafb;
                border-radius: 12px;
            }

            .document-placeholder i {
                font-size: 2.5rem;
                color: #c4b5fd;
                margin-bottom: 10px;
            }

            /* Donor Section */
            .donor-info-card {
                padding: 20px;
                background: #f9fafb;
                border-radius: 16px;
            }

            .donor-stats {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }

            .donor-stat {
                text-align: center;
                padding: 15px;
                background: white;
                border-radius: 12px;
            }

            .stat-label {
                display: block;
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .stat-value {
                display: block;
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .contact-admin-notice {
                display: flex;
                align-items: center;
                gap: 12px;
                background: #fef3c7;
                color: #92400e;
                padding: 15px 20px;
                border-radius: 12px;
                margin-top: 20px;
            }

            /* Danger Zone */
            .danger-zone {
                background: #fef2f2;
            }

            .danger-badge {
                background: #ef4444;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .danger-message {
                color: #991b1b;
                margin-bottom: 20px;
            }

            .btn-delete-account {
                background: #ef4444;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-delete-account:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* Modal */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 2000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .modal-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .modal-container {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-container.small {
                max-width: 400px;
            }

            .modal-header {
                padding: 20px 25px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h3 {
                margin: 0;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
            }

            .modal-body {
                padding: 25px;
            }

            .modal-footer {
                padding: 20px 25px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .btn-cancel {
                background: #f3f4f6;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            /* Image Upload Area */
            .image-preview-area {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }

            .current-image {
                text-align: center;
            }

            .current-image img,
            .placeholder-preview {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                margin: 0 auto;
            }

            .placeholder-preview {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                font-weight: 600;
                color: white;
            }

            .upload-area {
                border: 2px dashed #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .upload-area:hover {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .upload-area i {
                font-size: 2rem;
                color: #c4b5fd;
                margin-bottom: 10px;
            }

            .btn-select-image {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                cursor: pointer;
                margin-top: 10px;
            }

            .btn-remove-image {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            /* Toast */
            .toast-container {
                position: fixed;
                top: 90px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .toast {
                background: white;
                border-radius: 12px;
                padding: 12px 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
                display: flex;
                align-items: center;
                gap: 12px;
                animation: slideInRight 0.3s ease;
                border-left: 4px solid #10b981;
            }

            .toast.error {
                border-left-color: #ef4444;
            }

            .toast.success i {
                color: #10b981;
            }

            .toast.error i {
                color: #ef4444;
            }

            /* SweetAlert above modal */
            .swal-top-container {
                z-index: 10000 !important;
            }

            .swal2-container {
                z-index: 10000 !important;
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            /* Responsive */
            @media (max-width: 992px) {
                .profile-grid {
                    grid-template-columns: 1fr;
                }

                .profile-sidebar {
                    position: static;
                }

                .form-row {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .profile-container {
                    padding: 15px;
                }

                .profile-section {
                    padding: 20px;
                }

                .image-preview-area {
                    grid-template-columns: 1fr;
                }

                .donor-stats {
                    grid-template-columns: 1fr;
                }
            }

            /* RTL Support */
            body.rtl .profile-menu-item {
                text-align: right;
            }

            body.rtl .password-toggle {
                right: auto;
                left: 12px;
            }

            body.rtl .toast-container {
                right: auto;
                left: 20px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // ==================== TAB SWITCHING ====================
            let activeEditSection = null;

            document.querySelectorAll('.profile-menu-item').forEach(item => {
                item.addEventListener('click', function () {
                    const tabId = this.dataset.tab;

                    document.querySelectorAll('.profile-menu-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    document.querySelectorAll('.profile-tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');

                    if (activeEditSection) {
                        resetEditMode(activeEditSection);
                        activeEditSection = null;
                    }
                });
            });

            // ==================== EDIT SECTION TOGGLE ====================            
            function resetEditMode(section) {
                const form = document.getElementById(`${section}InfoForm`);
                if (!form) return;

                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = true;
                });

                const formActions = form.querySelector('.form-actions');
                const editBtn = document.querySelector(`.btn-edit-section[data-section="${section}"]`);

                if (formActions) formActions.style.display = 'none';
                if (editBtn) editBtn.style.display = 'inline-flex';

                form.querySelectorAll('.error-message').forEach(el => {
                    el.classList.remove('show');
                    el.textContent = '';
                });
                form.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('is-invalid');
                });
            }

            function enableEditMode(section) {
                const form = document.getElementById(`${section}InfoForm`);
                if (!form) return;

                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = false;
                });

                const formActions = form.querySelector('.form-actions');
                const editBtn = document.querySelector(`.btn-edit-section[data-section="${section}"]`);

                if (formActions) formActions.style.display = 'flex';
                if (editBtn) editBtn.style.display = 'none';

                activeEditSection = section;

                const cancelBtn = form.querySelector('.btn-cancel-edit');
                if (cancelBtn) {
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    newCancelBtn.onclick = () => {
                        resetEditMode(section);
                        activeEditSection = null;
                    };
                }
            }

            // Attach edit button listeners
            document.querySelectorAll('.btn-edit-section').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);

                newBtn.addEventListener('click', function () {
                    const section = this.dataset.section;

                    if (activeEditSection && activeEditSection !== section) {
                        resetEditMode(activeEditSection);
                    }

                    enableEditMode(section);
                });
            });

            // ==================== PASSWORD TOGGLE ====================
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // ==================== DOCUMENT TOGGLE ====================
            document.querySelectorAll('.btn-toggle-document').forEach(btn => {
                const docType = btn.dataset.doc;
                const image = document.getElementById(`${docType}Image`);
                if (image) {
                    btn.innerHTML = '🔍 {{ __("Hide") }}';
                    btn.onclick = () => {
                        if (image.style.display === 'none') {
                            image.style.display = 'block';
                            btn.innerHTML = '🔍 {{ __("Hide") }}';
                        } else {
                            image.style.display = 'none';
                            btn.innerHTML = '🔍 {{ __("Show") }}';
                        }
                    };
                }
            });

            // ==================== IMAGE UPLOAD ====================
            const avatarBtn = document.getElementById('changeAvatarBtn');
            const imageModal = document.getElementById('imageUploadModal');
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('profile_image_input');
            const selectImageBtn = document.getElementById('selectImageBtn');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const imageUploadForm = document.getElementById('imageUploadForm');

            if (avatarBtn) {
                avatarBtn.addEventListener('click', () => {
                    imageModal.classList.add('active');
                });
            }

            if (selectImageBtn) {
                selectImageBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                });
            }

            if (uploadArea) {
                uploadArea.addEventListener('click', (e) => {
                    if (e.target !== selectImageBtn && !selectImageBtn.contains(e.target)) {
                        fileInput.click();
                    }
                });

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#7c3aed';
                    uploadArea.style.background = '#f5f3ff';
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.style.borderColor = '#e5e7eb';
                    uploadArea.style.background = 'transparent';
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        fileInput.files = dt.files;
                        previewImage(file);
                    }
                    uploadArea.style.borderColor = '#e5e7eb';
                    uploadArea.style.background = 'transparent';
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        previewImage(this.files[0]);
                    }
                });
            }

            function previewImage(file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.querySelector('#currentImagePreview img, #currentImagePreview .placeholder-preview');
                    const container = document.querySelector('#currentImagePreview');
                    if (preview) {
                        if (preview.tagName === 'IMG') {
                            preview.src = e.target.result;
                        } else {
                            container.innerHTML = `<img src="${e.target.result}" alt="{{ $user->name }}" id="currentPreviewImage">`;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }

            // Image Upload Form Submit
            if (imageUploadForm) {
                imageUploadForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = imageUploadForm.querySelector('.btn-save');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(imageUploadForm);

                    try {
                        const response = await fetch(imageUploadForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        // Keep modal open, show SweetAlert on top
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: '{{ __("Profile image updated successfully!") }}',
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937',
                                customClass: { container: 'swal-top-container' }
                            });
                            imageModal.classList.remove('active');
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Error uploading image") }}',
                                confirmButtonColor: '#7c3aed',
                                background: '#fff',
                                color: '#1f2937',
                                customClass: { container: 'swal-top-container' }
                            });
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937',
                            customClass: { container: 'swal-top-container' }
                        });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Remove Image
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', async () => {
                    const result = await Swal.fire({
                        title: '{{ __("Are you sure?") }}',
                        text: '{{ __("This will remove your profile picture.") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '{{ __("Yes, remove it") }}',
                        cancelButtonText: '{{ __("Cancel") }}',
                        background: '#fff',
                        color: '#1f2937',
                        customClass: { container: 'swal-top-container' }
                    });

                    if (!result.isConfirmed) return;

                    const removeBtn = removeImageBtn;
                    const originalText = removeBtn.innerHTML;
                    removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    removeBtn.disabled = true;

                    try {
                        const response = await fetch('{{ route("profile.image.remove") }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Removed!") }}',
                                text: '{{ __("Profile image removed successfully.") }}',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937',
                                customClass: { container: 'swal-top-container' }
                            });
                            imageModal.classList.remove('active');
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Error removing image") }}',
                                confirmButtonColor: '#7c3aed',
                                background: '#fff',
                                color: '#1f2937',
                                customClass: { container: 'swal-top-container' }
                            });
                            removeBtn.innerHTML = originalText;
                            removeBtn.disabled = false;
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937',
                            customClass: { container: 'swal-top-container' }
                        });
                        removeBtn.innerHTML = originalText;
                        removeBtn.disabled = false;
                    }
                });
            }

            // ==================== PERSONAL INFO FORM SUBMIT ====================
            const personalForm = document.getElementById('personalInfoForm');
            if (personalForm) {
                const newPersonalForm = personalForm.cloneNode(true);
                personalForm.parentNode.replaceChild(newPersonalForm, personalForm);

                newPersonalForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = newPersonalForm.querySelector('.btn-save');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const inputs = newPersonalForm.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.disabled = false;
                    });

                    const formData = new FormData(newPersonalForm);

                    try {
                        const response = await fetch(newPersonalForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: '{{ __("Profile updated successfully!") }}',
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });
                            location.reload();
                        } else {
                            if (data.errors) {
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    const errorDiv = document.getElementById(`${field}-error`);
                                    if (errorDiv) {
                                        errorDiv.textContent = messages[0];
                                        errorDiv.classList.add('show');
                                        const input = document.getElementById(field);
                                        if (input) input.classList.add('is-invalid');
                                    }
                                }
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Validation Error") }}',
                                    text: '{{ __("Please check the form for errors.") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Error updating profile") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            }
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // ==================== PROFESSIONAL INFO FORM SUBMIT ====================
            const professionalForm = document.getElementById('professionalInfoForm');
            if (professionalForm) {
                professionalForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = professionalForm.querySelector('.btn-save');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(professionalForm);

                    try {
                        const response = await fetch(professionalForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: '{{ __("Professional information updated successfully!") }}',
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });
                            location.reload();
                        } else {
                            if (data.errors) {
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    const errorDiv = document.getElementById(`${field}-error`);
                                    if (errorDiv) {
                                        errorDiv.textContent = messages[0];
                                        errorDiv.classList.add('show');
                                        const input = document.getElementById(field);
                                        if (input) input.classList.add('is-invalid');
                                    }
                                }
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Validation Error") }}',
                                    text: '{{ __("Please check the form for errors.") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Error updating professional information") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            }
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // ==================== PASSWORD FORM SUBMIT ====================
            const passwordFormElement = document.getElementById('passwordForm');
            if (passwordFormElement) {
                passwordFormElement.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = passwordFormElement.querySelector('.btn-save-password');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(passwordFormElement);

                    try {
                        const response = await fetch(passwordFormElement.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: '{{ __("Password updated successfully! Please login again.") }}',
                                confirmButtonColor: '#7c3aed',
                                background: '#fff',
                                color: '#1f2937'
                            });
                            window.location.href = '{{ route("login") }}';
                        } else {
                            if (data.errors) {
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    const errorDiv = document.getElementById(`${field}-error`);
                                    if (errorDiv) {
                                        errorDiv.textContent = messages[0];
                                        errorDiv.classList.add('show');
                                        const input = document.getElementById(field);
                                        if (input) input.classList.add('is-invalid');
                                    }
                                }
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Validation Error") }}',
                                    text: '{{ __("Please check the form for errors.") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Error updating password") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            }
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // ==================== DELETE ACCOUNT MODAL ====================
            const deleteBtn = document.getElementById('deleteAccountBtn');
            const deleteModal = document.getElementById('deleteAccountModal');
            const deleteForm = document.getElementById('deleteAccountForm');

            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => {
                    deleteModal.classList.add('active');
                });
            }

            if (deleteForm) {
                deleteForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = deleteForm.querySelector('.btn-danger');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(deleteForm);

                    try {
                        const response = await fetch(deleteForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Close modal first
                            deleteModal.classList.remove('active');

                            // Show SweetAlert then redirect
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Account Deleted") }}',
                                text: '{{ __("Your account has been deleted. Redirecting...") }}',
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937',
                                allowOutsideClick: false
                            });

                            // Redirect to home page after alert
                            window.location.href = '{{ route("home") }}';
                        } else {
                            btnText.style.display = 'inline-block';
                            btnSpinner.style.display = 'none';
                            submitBtn.disabled = false;
                            deleteModal.classList.remove('active');

                            if (data.errors && data.errors.password) {
                                const errorDiv = document.getElementById('delete_password-error');
                                if (errorDiv) {
                                    errorDiv.textContent = data.errors.password[0];
                                    errorDiv.classList.add('show');
                                    const input = document.getElementById('delete_password');
                                    if (input) input.classList.add('is-invalid');
                                }
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.errors.password[0],
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Error deleting account") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                        deleteModal.classList.remove('active');

                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                });
            }

            // ==================== DOCUMENT UPLOAD ====================
            document.getElementById('certificate')?.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const form = this.closest('.document-upload-form');
                    form.submit();
                }
            });

            document.getElementById('license')?.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const form = this.closest('.document-upload-form');
                    form.submit();
                }
            });

            // ==================== MODAL CLOSE HANDLERS ====================
            document.querySelectorAll('.modal-close, .modal-overlay .btn-cancel').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.modal-overlay').forEach(modal => {
                        modal.classList.remove('active');
                    });
                });
            });

            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            });

            // Clear validation errors on input focus
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function () {
                    this.classList.remove('is-invalid');
                    const errorId = this.id + '-error';
                    const errorDiv = document.getElementById(errorId);
                    if (errorDiv) {
                        errorDiv.classList.remove('show');
                        errorDiv.textContent = '';
                    }
                });
            });
        </script>
    @endpush

@endsection