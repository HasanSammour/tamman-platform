{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Edit User') . ' - ' . __('Tamman'))

@section('page-title', __('Edit User'))

@section('content')
    <div class="user-edit-container">
        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">
                <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <a href="{{ route('admin.users') }}" class="breadcrumb-link">
                <i class="fas fa-users"></i> {{ __('Users Management') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <a href="{{ route('admin.users.show', $user->id) }}" class="breadcrumb-link">
                <i class="fas fa-user"></i> {{ $user->name }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <span class="breadcrumb-current">{{ __('Edit User') }}</span>
        </div>

        <div class="edit-layout">
            <!-- Left Panel - Profile Card -->
            <div class="profile-card-panel">
                <div class="profile-card-inner">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar-large" id="profileAvatarContainer">
                            @php
                                $profileImage = $user->getProfileImageUrl();
                                $userInitial = mb_substr($user->name, 0, 1, 'UTF-8');
                            @endphp
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $user->name }}" id="profileAvatarPreview">
                            @else
                                <div class="avatar-placeholder-large" id="profileAvatarPreview">{{ $userInitial }}</div>
                            @endif
                            <button type="button" class="avatar-edit-btn" id="openAvatarModalBtn">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3 class="profile-name">{{ $user->name }}</h3>
                        <p class="profile-email">{{ $user->email }}</p>
                        <div class="profile-badges">
                            @if($user->is_active)
                                <span class="badge-active-small"><i class="fas fa-check-circle"></i> {{ __('Active') }}</span>
                            @else
                                <span class="badge-suspended-small"><i class="fas fa-ban"></i> {{ __('Suspended') }}</span>
                            @endif
                            @if($user->hasRole('donor'))
                                <span class="badge-donor-small"><i class="fas fa-hand-holding-heart"></i>
                                    {{ __('Donor') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Stats in 2x2 Grid -->
                    <div class="profile-stats-grid">
                        <div class="profile-stat-item">
                            <div class="stat-icon-small purple"><i class="fas fa-calendar-check"></i></div>
                            <div class="stat-info-small">
                                <span class="stat-value">{{ number_format($stats['total_sessions']) }}</span>
                                <span class="stat-label">{{ __('Sessions') }}</span>
                            </div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="stat-icon-small orange"><i class="fas fa-star"></i></div>
                            <div class="stat-info-small">
                                <span class="stat-value">{{ number_format($stats['total_points']) }}</span>
                                <span class="stat-label">{{ __('Points') }}</span>
                            </div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="stat-icon-small teal"><i class="fas fa-coins"></i></div>
                            <div class="stat-info-small">
                                <span class="stat-value">${{ number_format($stats['total_credit'], 2) }}</span>
                                <span class="stat-label">{{ __('Credit') }}</span>
                            </div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="stat-icon-small indigo"><i class="fas fa-smile"></i></div>
                            <div class="stat-info-small">
                                <span class="stat-value">{{ number_format($stats['total_mood_entries']) }}</span>
                                <span class="stat-label">{{ __('Mood') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-list">
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <span class="info-label">{{ __('Joined') }}</span>
                                <span class="info-value">{{ $user->created_at->translatedFormat('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-id-card"></i>
                            <div>
                                <span class="info-label">{{ __('User ID') }}</span>
                                <span class="info-value">#{{ $user->id }}</span>
                            </div>
                        </div>
                        @if($user->last_login_at)
                            <div class="info-item">
                                <i class="fas fa-history"></i>
                                <div>
                                    <span class="info-label">{{ __('Last Login') }}</span>
                                    <span class="info-value">{{ $user->last_login_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel - Edit Form -->
            <div class="edit-form-panel">
                <div class="form-tabs">
                    <button class="form-tab active" data-tab="personal">
                        <i class="fas fa-user"></i> {{ __('Personal Info') }}
                    </button>
                    <button class="form-tab" data-tab="account">
                        <i class="fas fa-shield-alt"></i> {{ __('Account') }}
                    </button>
                    <button class="form-tab" data-tab="financial">
                        <i class="fas fa-chart-line"></i> {{ __('Financial') }}
                    </button>
                    @if($user->hasRole('donor') || old('is_donor') == '1')
                        <button class="form-tab" data-tab="donor">
                            <i class="fas fa-hand-holding-heart"></i> {{ __('Donor Info') }}
                        </button>
                    @endif
                </div>

                <form id="editUserForm" method="POST" action="{{ route('admin.users.update', $user->id) }}"
                    class="edit-form">
                    @csrf
                    @method('PUT')

                    <!-- Tab 1: Personal Information -->
                    <div class="tab-content active" id="tab-personal">
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">{{ __('Full Name') }} <span class="required">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                    <div class="error-message" id="name-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                    <div class="error-message" id="email-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">{{ __('Phone Number') }}</label>
                                    <input type="tel" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}">
                                    <div class="error-message" id="phone-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="gender">{{ __('Gender') }}</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                            {{ __('Male') }}</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                            {{ __('Other') }}</option>
                                    </select>
                                    <div class="error-message" id="gender-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_of_birth">{{ __('Date of Birth') }}</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                                    <div class="error-message" id="date_of_birth-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Account Status -->
                    <div class="tab-content" id="tab-account">
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="is_active">{{ __('Account Status') }}</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>
                                            {{ __('Active') }}</option>
                                        <option value="0" {{ old('is_active', $user->is_active) ? '' : 'selected' }}>
                                            {{ __('Suspended') }}</option>
                                    </select>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('Suspended users cannot login or book sessions') }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="is_donor">{{ __('Donor Status') }}</label>
                                    <select name="is_donor" id="is_donor" class="form-control">
                                        <option value="0" {{ !$user->donorProfile ? 'selected' : '' }}>
                                            {{ __('Not a Donor') }}</option>
                                        <option value="1" {{ $user->donorProfile ? 'selected' : '' }}>{{ __('Donor') }}
                                        </option>
                                    </select>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('Donors can support other patients financially') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Financial Information -->
                    <div class="tab-content" id="tab-financial">
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="credit_balance">{{ __('Credit Balance (USD)') }}</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                        <input type="number" name="credit_balance" id="credit_balance" class="form-control"
                                            step="0.01" value="{{ old('credit_balance', $user->credit_balance) }}">
                                    </div>
                                    <div class="form-hint"><i class="fas fa-info-circle"></i>
                                        {{ __('User can use this credit to book sessions') }}</div>
                                    <div class="error-message" id="credit_balance-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="total_points">{{ __('Total Points') }}</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-star"></i>
                                        <input type="number" name="total_points" id="total_points" class="form-control"
                                            step="1" value="{{ old('total_points', $user->total_points) }}">
                                    </div>
                                    <div class="form-hint"><i class="fas fa-info-circle"></i>
                                        {{ __('Points earned from activities and referrals') }}</div>
                                    <div class="error-message" id="total_points-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Donor Information -->
                    <div class="tab-content" id="tab-donor">
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="organization_name">{{ __('Organization Name') }}</label>
                                    <input type="text" name="organization_name" id="organization_name" class="form-control"
                                        value="{{ old('organization_name', $user->donorProfile?->organization_name) }}">
                                    <div class="form-hint"><i class="fas fa-info-circle"></i>
                                        {{ __('Optional: For organizational donors') }}</div>
                                </div>
                                <div class="form-group">
                                    <label for="total_donated">{{ __('Total Donated (USD)') }}</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                        <input type="number" name="total_donated" id="total_donated" class="form-control"
                                            step="0.01"
                                            value="{{ old('total_donated', $user->donorProfile?->total_donated) }}">
                                    </div>
                                    <div class="error-message" id="total_donated-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn-cancel">
                            <i class="fas fa-times"></i> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-save" id="saveBtn">
                            <span class="btn-text"><i class="fas fa-save"></i> {{ __('Save Changes') }}</span>
                            <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Avatar Modal -->
    <div id="avatarModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-camera"></i> {{ __('Update Profile Picture') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" method="POST" action="{{ route('admin.users.upload-image', $user->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="avatar-upload-container">
                        <div class="current-avatar-preview">
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $user->name }}" id="avatarPreviewImage">
                            @else
                                <div class="avatar-placeholder-preview" id="avatarPreviewPlaceholder">{{ $userInitial }}</div>
                            @endif
                        </div>
                        <div class="upload-controls">
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>{{ __('Click or drag image here') }}</p>
                                <span class="upload-hint">{{ __('JPG, PNG, GIF (Max 2MB)') }}</span>
                                <input type="file" name="profile_image" id="profileImageInput" accept="image/*"
                                    style="display: none;">
                                <button type="button" class="btn-select-file" id="selectFileBtn">
                                    <i class="fas fa-folder-open"></i> {{ __('Browse') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-avatar">
                        @if($profileImage)
                            <button type="button" class="btn-remove-image" id="removeImageBtn">{{ __('Remove Image') }}</button>
                        @endif
                        <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-save-avatar" id="saveAvatarBtn">
                            <span class="btn-text">{{ __('Save Image') }}</span>
                            <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .user-edit-container {
                max-width: 1300px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Breadcrumb */
            .breadcrumb-nav {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 25px;
                padding: 12px 0;
            }

            .breadcrumb-link {
                color: #6b7280;
                text-decoration: none;
                font-size: 0.8rem;
                transition: color 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .breadcrumb-link:hover {
                color: #7c3aed;
            }

            .breadcrumb-sep {
                font-size: 0.7rem;
                color: #cbd5e1;
            }

            .breadcrumb-current {
                color: #7c3aed;
                font-size: 0.8rem;
                font-weight: 500;
            }

            /* Edit Layout */
            .edit-layout {
                display: grid;
                grid-template-columns: 300px 1fr;
                gap: 25px;
                align-items: start;
            }

            /* Left Panel */
            .profile-card-panel {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                position: sticky;
                top: 100px;
            }

            .profile-card-inner {
                padding: 25px;
            }

            .profile-avatar-wrapper {
                text-align: center;
                margin-bottom: 20px;
                position: relative;
            }

            .profile-avatar-large {
                position: relative;
                display: inline-block;
            }

            .profile-avatar-large img,
            .avatar-placeholder-large {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid white;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .avatar-placeholder-large {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                font-weight: 600;
                color: white;
            }

            .avatar-edit-btn {
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 32px;
                height: 32px;
                background: white;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6b7280;
            }

            .avatar-edit-btn:hover {
                background: #7c3aed;
                color: white;
                transform: scale(1.1);
            }

            .profile-name {
                font-size: 1.1rem;
                font-weight: 700;
                margin: 0 0 5px;
                color: #1f2937;
                text-align: center;
            }

            .profile-email {
                font-size: 0.75rem;
                color: #6b7280;
                text-align: center;
                margin-bottom: 12px;
                word-break: break-all;
            }

            .profile-badges {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .badge-active-small,
            .badge-suspended-small,
            .badge-donor-small {
                padding: 3px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .badge-active-small {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-suspended-small {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-donor-small {
                background: #fef3c7;
                color: #d97706;
            }

            /* Profile Stats Grid */
            .profile-stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 15px 0;
                border-top: 1px solid #f0f0f0;
                border-bottom: 1px solid #f0f0f0;
                margin-bottom: 15px;
            }

            .profile-stat-item {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .stat-icon-small {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon-small i {
                font-size: 1rem;
                color: white;
            }

            .stat-icon-small.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon-small.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon-small.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-icon-small.indigo {
                background: linear-gradient(135deg, #6366f1, #4f46e5);
            }

            .stat-info-small {
                display: flex;
                flex-direction: column;
            }

            .stat-info-small .stat-value {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
                line-height: 1.2;
            }

            .stat-info-small .stat-label {
                font-size: 0.6rem;
                color: #6b7280;
            }

            /* Profile Info List */
            .profile-info-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .info-item {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.75rem;
            }

            .info-item i {
                width: 28px;
                color: #7c3aed;
                font-size: 0.85rem;
            }

            .info-item div {
                flex: 1;
            }

            .info-label {
                display: block;
                font-size: 0.6rem;
                color: #9ca3af;
            }

            .info-value {
                display: block;
                font-size: 0.75rem;
                color: #1f2937;
                font-weight: 500;
            }

            /* Right Panel */
            .edit-form-panel {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            /* Form Tabs */
            .form-tabs {
                display: flex;
                gap: 5px;
                padding: 16px 20px 0;
                background: white;
                border-bottom: 1px solid #f0f0f0;
                flex-wrap: wrap;
            }

            .form-tab {
                padding: 10px 18px;
                background: none;
                border: none;
                font-size: 0.8rem;
                font-weight: 500;
                color: #6b7280;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 40px 40px 0 0;
                position: relative;
            }

            .form-tab i {
                margin-right: 6px;
                font-size: 0.8rem;
            }

            .form-tab:hover {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .form-tab.active {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .form-tab.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background: #7c3aed;
            }

            /* Tab Content */
            .tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
                height: auto;
            }

            .tab-content.active {
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

            /* Form Sections */
            .form-section {
                padding: 20px;
            }

            .form-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }

            .form-row:last-child {
                margin-bottom: 0;
            }

            .form-group {
                display: flex;
                flex-direction: column;
            }

            .form-group label {
                font-size: 0.75rem;
                font-weight: 500;
                margin-bottom: 6px;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .form-control {
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.8rem;
                transition: all 0.3s ease;
                background: #f9fafb;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            /* Input with icon */
            .input-with-icon {
                position: relative;
                display: flex;
                align-items: center;
            }

            .input-with-icon i {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
                font-size: 0.8rem;
                z-index: 1;
            }

            .input-with-icon .form-control {
                padding-left: 32px;
                width: 100%;
            }

            .form-hint {
                font-size: 0.6rem;
                color: #9ca3af;
                margin-top: 5px;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .form-hint i {
                font-size: 0.6rem;
                color: #7c3aed;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.65rem;
                margin-top: 5px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            /* Form Actions */
            .form-actions {
                padding: 16px 20px;
                background: #f9fafb;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .btn-cancel,
            .btn-save {
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
            }

            .btn-cancel {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-save {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .btn-save:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            }

            .btn-save:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
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
                z-index: 10000;
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
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-header {
                padding: 18px 22px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h3 {
                margin: 0;
                font-size: 1.1rem;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.3rem;
                cursor: pointer;
                color: #6b7280;
            }

            .avatar-upload-container {
                display: flex;
                gap: 25px;
                align-items: center;
                flex-wrap: wrap;
                justify-content: center;
            }

            .current-avatar-preview img,
            .avatar-placeholder-preview {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder-preview {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                font-weight: 600;
                color: white;
            }

            .upload-controls {
                flex: 1;
                min-width: 200px;
            }

            .upload-area {
                border: 2px dashed #e5e7eb;
                border-radius: 16px;
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
                font-size: 1.8rem;
                color: #c4b5fd;
                margin-bottom: 8px;
            }

            .upload-area p {
                margin: 0 0 5px;
                font-size: 0.8rem;
                color: #374151;
            }

            .upload-hint {
                font-size: 0.65rem;
                color: #9ca3af;
                display: block;
                margin-bottom: 12px;
            }

            .btn-select-file {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.75rem;
                transition: all 0.3s ease;
            }

            .btn-select-file:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .modal-footer-avatar {
                padding: 16px 22px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                flex-wrap: wrap;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 6px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.75rem;
            }

            .btn-cancel-modal:hover {
                background: #e5e7eb;
            }

            .btn-save-avatar {
                background: #10b981;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.75rem;
            }

            .btn-save-avatar:hover:not(:disabled) {
                background: #059669;
                transform: translateY(-2px);
            }

            .btn-save-avatar:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .btn-remove-image {
                background: #ef4444;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.75rem;
            }

            .btn-remove-image:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* SweetAlert z-index fix - ensure it appears above modal */
            .swal2-container {
                z-index: 10001 !important;
            }

            .swal2-popup {
                z-index: 10002 !important;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .edit-layout {
                    grid-template-columns: 1fr;
                }

                .profile-card-panel {
                    position: static;
                    margin-bottom: 20px;
                }

                .form-row {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
            }

            @media (max-width: 768px) {
                .user-edit-container {
                    padding: 15px;
                }

                .form-section {
                    padding: 15px;
                }

                .form-actions {
                    flex-direction: column;
                }

                .btn-cancel,
                .btn-save {
                    justify-content: center;
                }

                .profile-stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .form-tabs {
                    justify-content: center;
                }

                .form-tab {
                    padding: 8px 14px;
                    font-size: 0.7rem;
                }
            }

            /* RTL Support */
            body.rtl .input-with-icon i {
                left: auto;
                right: 12px;
            }

            body.rtl .input-with-icon .form-control {
                padding-left: 12px;
                padding-right: 32px;
            }

            body.rtl .form-tab i {
                margin-right: 0;
                margin-left: 6px;
            }

            body.rtl .info-item {
                flex-direction: row;
            }

            body.rtl .breadcrumb-sep {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Tab Switching
            const tabs = document.querySelectorAll('.form-tab');
            const tabContents = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.dataset.tab;
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    tabContents.forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // Avatar Modal
            const openAvatarBtn = document.getElementById('openAvatarModalBtn');
            const avatarModal = document.getElementById('avatarModal');
            const modalClose = document.querySelectorAll('.modal-close, .btn-cancel-modal');
            const selectFileBtn = document.getElementById('selectFileBtn');
            const avatarFile = document.getElementById('profileImageInput');
            const uploadArea = document.getElementById('uploadArea');
            const saveAvatarBtn = document.getElementById('saveAvatarBtn');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const avatarPreviewImage = document.getElementById('avatarPreviewImage');
            const avatarPreviewPlaceholder = document.getElementById('avatarPreviewPlaceholder');
            const avatarForm = document.getElementById('avatarForm');

            function openAvatarModal() { avatarModal.classList.add('active'); }
            function closeAvatarModal() {
                avatarModal.classList.remove('active');
                if (avatarFile) avatarFile.value = '';
                if (saveAvatarBtn) saveAvatarBtn.disabled = true;
            }

            if (openAvatarBtn) openAvatarBtn.addEventListener('click', openAvatarModal);
            modalClose.forEach(btn => btn.addEventListener('click', closeAvatarModal));
            avatarModal.addEventListener('click', (e) => { if (e.target === avatarModal) closeAvatarModal(); });

            // File explorer - opens only once (fixed)
            if (selectFileBtn) {
                selectFileBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    avatarFile.click();
                });
            }

            if (uploadArea) {
                uploadArea.addEventListener('click', (e) => {
                    if (e.target !== selectFileBtn && !selectFileBtn.contains(e.target)) {
                        avatarFile.click();
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
                        avatarFile.files = dt.files;
                        previewAvatarImage(file);
                        if (saveAvatarBtn) saveAvatarBtn.disabled = false;
                    }
                    uploadArea.style.borderColor = '#e5e7eb';
                    uploadArea.style.background = 'transparent';
                });
            }

            if (avatarFile) {
                avatarFile.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        previewAvatarImage(this.files[0]);
                        if (saveAvatarBtn) saveAvatarBtn.disabled = false;
                    }
                });
            }

            function previewAvatarImage(file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (avatarPreviewImage) {
                        avatarPreviewImage.src = e.target.result;
                    } else if (avatarPreviewPlaceholder) {
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'Preview';
                        newImg.id = 'avatarPreviewImage';
                        newImg.style.width = '100px';
                        newImg.style.height = '100px';
                        newImg.style.borderRadius = '50%';
                        newImg.style.objectFit = 'cover';
                        avatarPreviewPlaceholder.parentNode.replaceChild(newImg, avatarPreviewPlaceholder);
                    }
                };
                reader.readAsDataURL(file);
            }

            // Save Avatar - FIXED with dedicated route
            if (saveAvatarBtn && avatarForm) {
                saveAvatarBtn.addEventListener('click', async () => {
                    if (!avatarFile.files || !avatarFile.files[0]) {
                        closeAvatarModal();
                        return;
                    }

                    const formData = new FormData();
                    formData.append('profile_image', avatarFile.files[0]);

                    saveAvatarBtn.disabled = true;
                    const btnText = saveAvatarBtn.querySelector('.btn-text');
                    const btnSpinner = saveAvatarBtn.querySelector('.btn-spinner');
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';

                    try {
                        const response = await fetch('{{ route("admin.users.upload-image", $user->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            closeAvatarModal();
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Failed to update image") }}'
                            });
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}'
                        });
                    } finally {
                        btnText.style.display = 'inline-flex';
                        btnSpinner.style.display = 'none';
                        saveAvatarBtn.disabled = false;
                    }
                });
            }

            // Remove Image - FIXED with dedicated route
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', async () => {
                    const result = await Swal.fire({
                        title: '{{ __("Are you sure?") }}',
                        text: '{{ __("This will remove the profile picture.") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '{{ __("Yes, remove it") }}',
                        cancelButtonText: '{{ __("Cancel") }}'
                    });

                    if (!result.isConfirmed) return;

                    removeImageBtn.disabled = true;
                    const originalText = removeImageBtn.innerHTML;
                    removeImageBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    try {
                        const response = await fetch('{{ route("admin.users.remove-image", $user->id) }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Removed!") }}',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Failed to remove image") }}'
                            });
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}'
                        });
                    } finally {
                        removeImageBtn.disabled = false;
                        removeImageBtn.innerHTML = originalText;
                    }
                });
            }

            // Form Submit
            const editForm = document.getElementById('editUserForm');
            if (editForm) {
                editForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const submitBtn = document.getElementById('saveBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;
                    const formData = new FormData(editForm);
                    try {
                        const response = await fetch(editForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            window.location.href = '{{ route("admin.users.show", $user->id) }}';
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
                                    text: '{{ __("Please check the form for errors.") }}'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Failed to update user") }}'
                                });
                            }
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}'
                        });
                    } finally {
                        btnText.style.display = 'inline-flex';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Clear validation errors on focus
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