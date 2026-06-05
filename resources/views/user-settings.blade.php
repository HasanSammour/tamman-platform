{{-- resources/views/user-settings.blade.php --}}
@extends('layouts.app')

@section('title', __('Settings') . ' - ' . __('Tamman'))

@section('page-title', __('Settings'))

@section('content')
    <div class="settings-container">
        <div class="settings-grid">
            <!-- Sidebar Navigation -->
            <div class="settings-sidebar">
                <div class="settings-card">
                    <div class="settings-menu">
                        <button class="settings-menu-item active" data-tab="profile-redirect">
                            <i class="fas fa-user-circle"></i> {{ __('Profile') }}
                        </button>
                        <button class="settings-menu-item" data-tab="notifications">
                            <i class="fas fa-bell"></i> {{ __('Notifications') }}
                        </button>
                        <button class="settings-menu-item" data-tab="privacy">
                            <i class="fas fa-lock"></i> {{ __('Privacy') }}
                        </button>
                        <button class="settings-menu-item" data-tab="language">
                            <i class="fas fa-language"></i> {{ __('Language') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="settings-main">
                <!-- Profile Redirect Tab -->
                <div class="settings-tab-content active" id="tab-profile-redirect">
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><i class="fas fa-user"></i> {{ __('Profile Settings') }}</h3>
                        </div>
                        <div class="profile-redirect-card">
                            <div class="redirect-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="redirect-info">
                                <h4>{{ __('Manage Your Personal Information') }}</h4>
                                <p>{{ __('Update your name, email, phone number, profile picture, and other personal details.') }}
                                </p>
                                @if($user->hasRole('specialist'))
                                    <p class="text-muted">
                                        {{ __('Professional information, qualifications, and documents are also managed in your profile.') }}
                                    </p>
                                @endif
                                @if($user->hasRole('donor'))
                                    <p class="text-muted">
                                        {{ __('Donor information and donation history are available in your profile.') }}
                                    </p>
                                @endif
                            </div>
                            <a href="{{ route('profile.edit') }}" class="btn-go-to-profile">
                                <i class="fas fa-arrow-right"></i> {{ __('Go to Profile') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="settings-tab-content" id="tab-notifications">
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><i class="fas fa-bell"></i> {{ __('Notification Preferences') }}</h3>
                            <p class="section-desc">{{ __('Choose which notifications you want to receive via email.') }}
                            </p>
                        </div>
                        <form id="notificationsForm" class="settings-form">
                            @csrf
                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <h4>{{ __('Session Reminders') }}</h4>
                                        <p>{{ __('Receive reminders before your scheduled sessions') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="session_reminders" value="1"
                                        id="session_reminders_checkbox" {{ ($notificationSettings['session_reminders'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-star"></i>
                                    <div>
                                        <h4>{{ __('Points Earned') }}</h4>
                                        <p>{{ __('Get notified when you earn Tamman Points') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="points_earned" value="1" id="points_earned_checkbox" {{ ($notificationSettings['points_earned'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <h4>{{ __('New Messages') }}</h4>
                                        <p>{{ __('Get notified when you receive new messages') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="new_messages" value="1" id="new_messages_checkbox" {{ ($notificationSettings['new_messages'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-tasks"></i>
                                    <div>
                                        <h4>{{ __('Treatment Tasks') }}</h4>
                                        <p>{{ __('Get notified about new treatment tasks and assignments') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="treatment_tasks" value="1" id="treatment_tasks_checkbox" {{ ($notificationSettings['treatment_tasks'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-megaphone"></i>
                                    <div>
                                        <h4>{{ __('Promotional Emails') }}</h4>
                                        <p>{{ __('Receive updates about new features and offers') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="promotional_emails" value="1"
                                        id="promotional_emails_checkbox" {{ ($notificationSettings['promotional_emails'] ?? false) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save" id="saveNotificationsBtn">
                                    <span class="btn-text">{{ __('Save Notification Settings') }}</span>
                                    <span class="btn-spinner" style="display: none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy Tab -->
                <div class="settings-tab-content" id="tab-privacy">
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><i class="fas fa-lock"></i> {{ __('Privacy Settings') }}</h3>
                            <p class="section-desc">{{ __('Control who can see your information and contact you.') }}</p>
                        </div>
                        <form id="privacyForm" class="settings-form">
                            @csrf
                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-eye"></i>
                                    <div>
                                        <h4>{{ __('Profile Visibility') }}</h4>
                                        <p>{{ __('Who can view your profile?') }}</p>
                                    </div>
                                </div>
                                <div class="setting-select">
                                    <select name="profile_visibility" class="form-control" id="profile_visibility_select">
                                        <option value="public" {{ ($privacySettings['profile_visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>{{ __('Public - Everyone can see') }}
                                        </option>
                                        <option value="private" {{ ($privacySettings['profile_visibility'] ?? 'public') == 'private' ? 'selected' : '' }}>{{ __('Private - Only me') }}</option>
                                        <option value="contacts_only" {{ ($privacySettings['profile_visibility'] ?? 'public') == 'contacts_only' ? 'selected' : '' }}>
                                            {{ __('Contacts Only - Only my connections') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <h4>{{ __('Show Email') }}</h4>
                                        <p>{{ __('Allow others to see your email address') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="show_email" value="1" id="show_email_checkbox" {{ ($privacySettings['show_email'] ?? false) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <h4>{{ __('Show Phone Number') }}</h4>
                                        <p>{{ __('Allow others to see your phone number') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="show_phone" value="1" id="show_phone_checkbox" {{ ($privacySettings['show_phone'] ?? false) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-circle"></i>
                                    <div>
                                        <h4>{{ __('Show Activity Status') }}</h4>
                                        <p>{{ __('Show when you are online') }}</p>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="show_activity_status" value="1"
                                        id="show_activity_status_checkbox" {{ ($privacySettings['show_activity_status'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="settings-item">
                                <div class="setting-info">
                                    <i class="fas fa-comment-dots"></i>
                                    <div>
                                        <h4>{{ __('Allow Messages From') }}</h4>
                                        <p>{{ __('Who can send you messages?') }}</p>
                                    </div>
                                </div>
                                <div class="setting-select">
                                    <select name="allow_messages_from" class="form-control" id="allow_messages_from_select">
                                        <option value="everyone" {{ ($privacySettings['allow_messages_from'] ?? 'everyone') == 'everyone' ? 'selected' : '' }}>{{ __('Everyone') }}</option>
                                        <option value="only_specialists" {{ ($privacySettings['allow_messages_from'] ?? 'everyone') == 'only_specialists' ? 'selected' : '' }}>
                                            {{ __('Only Specialists') }}
                                        </option>
                                        <option value="only_patients" {{ ($privacySettings['allow_messages_from'] ?? 'everyone') == 'only_patients' ? 'selected' : '' }}>{{ __('Only Patients') }}
                                        </option>
                                        <option value="none" {{ ($privacySettings['allow_messages_from'] ?? 'everyone') == 'none' ? 'selected' : '' }}>{{ __('No One') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save" id="savePrivacyBtn">
                                    <span class="btn-text">{{ __('Save Privacy Settings') }}</span>
                                    <span class="btn-spinner" style="display: none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Language Tab -->
                <div class="settings-tab-content" id="tab-language">
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><i class="fas fa-language"></i> {{ __('Language Preference') }}</h3>
                            <p class="section-desc">{{ __('Choose your preferred language for the platform.') }}</p>
                        </div>
                        <form id="languageForm" class="settings-form">
                            @csrf
                            <div class="language-options">
                                <div class="language-option {{ $preferredLocale == 'ar' ? 'selected' : '' }}"
                                    data-lang="ar">
                                    <div class="lang-flag">
                                        <span class="flag-icon">🇸🇦</span>
                                    </div>
                                    <div class="lang-info">
                                        <h4>{{ __('Arabic') }}</h4>
                                        <p>العربية</p>
                                    </div>
                                    <div class="lang-check">
                                        <i class="fas {{ $preferredLocale == 'ar' ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                    </div>
                                </div>
                                <div class="language-option {{ $preferredLocale == 'en' ? 'selected' : '' }}"
                                    data-lang="en">
                                    <div class="lang-flag">
                                        <span class="flag-icon">🇬🇧</span>
                                    </div>
                                    <div class="lang-info">
                                        <h4>{{ __('English') }}</h4>
                                        <p>English</p>
                                    </div>
                                    <div class="lang-check">
                                        <i class="fas {{ $preferredLocale == 'en' ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="locale" id="selectedLocale" value="{{ $preferredLocale }}">

                            <div class="form-actions">
                                <button type="submit" class="btn-save" id="saveLanguageBtn">
                                    <span class="btn-text">{{ __('Save Language Preference') }}</span>
                                    <span class="btn-spinner" style="display: none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .settings-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            .settings-grid {
                display: grid;
                grid-template-columns: 280px 1fr;
                gap: 30px;
            }

            .settings-sidebar {
                position: sticky;
                top: 100px;
            }

            .settings-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .settings-menu {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .settings-menu-item {
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

            .settings-menu-item:hover {
                background: #f3f4f6;
            }

            .settings-menu-item.active {
                background: #ede9fe;
                color: #7c3aed;
            }

            .settings-menu-item i {
                width: 22px;
            }

            .settings-main {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .settings-tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
            }

            .settings-tab-content.active {
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

            .settings-section {
                padding: 30px;
            }

            .section-header {
                margin-bottom: 25px;
            }

            .section-header h3 {
                font-size: 1.2rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .section-desc {
                font-size: 0.85rem;
                color: #6b7280;
            }

            .profile-redirect-card {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-radius: 20px;
                padding: 30px;
                text-align: center;
            }

            .redirect-icon i {
                font-size: 3rem;
                color: #7c3aed;
                margin-bottom: 15px;
            }

            .redirect-info h4 {
                font-size: 1.1rem;
                margin-bottom: 10px;
                color: #1f2937;
            }

            .redirect-info p {
                font-size: 0.85rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .text-muted {
                font-size: 0.75rem;
                color: #9ca3af;
            }

            .btn-go-to-profile {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #7c3aed;
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                margin-top: 20px;
                transition: all 0.3s ease;
            }

            .btn-go-to-profile:hover {
                background: #6d28d9;
                transform: translateY(-2px);
                color: white;
            }

            .settings-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 0;
                border-bottom: 1px solid #f3f4f6;
                flex-wrap: wrap;
                gap: 15px;
            }

            .setting-info {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .setting-info i {
                width: 40px;
                height: 40px;
                background: #f3f4f6;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7c3aed;
                font-size: 1.1rem;
            }

            .setting-info h4 {
                font-size: 0.9rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .setting-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .setting-select {
                min-width: 180px;
            }

            .setting-select .form-control {
                padding: 8px 12px;
                border-radius: 10px;
                border: 1px solid #e5e7eb;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 52px;
                height: 28px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: 0.4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 22px;
                width: 22px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: 0.4s;
            }

            input:checked+.slider {
                background-color: #7c3aed;
            }

            input:checked+.slider:before {
                transform: translateX(24px);
            }

            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }

            .language-options {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .language-option {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 20px;
                border: 2px solid #e5e7eb;
                border-radius: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .language-option:hover {
                border-color: #c4b5fd;
                transform: translateY(-2px);
            }

            .language-option.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .lang-flag .flag-icon {
                font-size: 2rem;
            }

            .lang-info {
                flex: 1;
            }

            .lang-info h4 {
                font-size: 1rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .lang-info p {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .lang-check i {
                font-size: 1.3rem;
                color: #9ca3af;
            }

            .language-option.selected .lang-check i {
                color: #7c3aed;
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
            }

            .form-actions {
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-save {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 24px;
                border-radius: 40px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-save:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-save:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            @media (max-width: 992px) {
                .settings-grid {
                    grid-template-columns: 1fr;
                }

                .settings-sidebar {
                    position: static;
                }

                .language-options {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .settings-container {
                    padding: 15px;
                }

                .settings-section {
                    padding: 20px;
                }

                .settings-item {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .setting-select {
                    width: 100%;
                }

                .profile-redirect-card {
                    padding: 20px;
                }
            }

            body.rtl .settings-menu-item {
                text-align: right;
            }

            body.rtl .setting-info {
                flex-direction: row;
            }

            body.rtl .language-option {
                flex-direction: row;
            }

            body.rtl .btn-go-to-profile i {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Tab Switching
            document.querySelectorAll('.settings-menu-item').forEach(item => {
                item.addEventListener('click', function () {
                    const tabId = this.dataset.tab;
                    document.querySelectorAll('.settings-menu-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.settings-tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // Language Selection
            document.querySelectorAll('.language-option').forEach(option => {
                option.addEventListener('click', function () {
                    document.querySelectorAll('.language-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('selectedLocale').value = this.dataset.lang;
                });
            });

            // ==================== NOTIFICATIONS FORM (FIXED) ====================
            const notificationsForm = document.getElementById('notificationsForm');
            if (notificationsForm) {
                notificationsForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = document.getElementById('saveNotificationsBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    // Get checkbox values as 0/1
                    const formData = new FormData();
                    formData.append('session_reminders', document.querySelector('input[name="session_reminders"]').checked ? 1 : 0);
                    formData.append('points_earned', document.querySelector('input[name="points_earned"]').checked ? 1 : 0);
                    formData.append('new_messages', document.querySelector('input[name="new_messages"]').checked ? 1 : 0);
                    formData.append('treatment_tasks', document.querySelector('input[name="treatment_tasks"]').checked ? 1 : 0);
                    formData.append('promotional_emails', document.querySelector('input[name="promotional_emails"]').checked ? 1 : 0);

                    try {
                        const response = await fetch('{{ route("settings.notifications") }}', {
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
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });

                            // No reload needed - checkboxes already show the new state
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Something went wrong.") }}',
                                confirmButtonColor: '#7c3aed'
                            });
                            // Revert checkboxes to original state (reload page)
                            window.location.reload();
                        }
                    } catch (error) {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        window.location.reload();
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // ==================== PRIVACY FORM ====================
            const privacyForm = document.getElementById('privacyForm');
            if (privacyForm) {
                privacyForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = document.getElementById('savePrivacyBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    // Build FormData manually to ensure checkbox values are sent correctly
                    const formData = new FormData();

                    // Get select values
                    const profileVisibility = document.querySelector('select[name="profile_visibility"]');
                    if (profileVisibility) formData.append('profile_visibility', profileVisibility.value);

                    const allowMessagesFrom = document.querySelector('select[name="allow_messages_from"]');
                    if (allowMessagesFrom) formData.append('allow_messages_from', allowMessagesFrom.value);

                    // Get checkbox values (send as 0 or 1)
                    const showEmail = document.querySelector('input[name="show_email"]');
                    formData.append('show_email', showEmail ? (showEmail.checked ? 1 : 0) : 0);

                    const showPhone = document.querySelector('input[name="show_phone"]');
                    formData.append('show_phone', showPhone ? (showPhone.checked ? 1 : 0) : 0);

                    const showActivityStatus = document.querySelector('input[name="show_activity_status"]');
                    formData.append('show_activity_status', showActivityStatus ? (showActivityStatus.checked ? 1 : 0) : 0);

                    try {
                        const response = await fetch('{{ route("settings.privacy") }}', {
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
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Something went wrong.") }}',
                                confirmButtonColor: '#7c3aed'
                            });
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        window.location.reload();
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // ==================== LANGUAGE FORM ====================
            // Language Form Submit
            const languageForm = document.getElementById('languageForm');
            if (languageForm) {
                languageForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const submitBtn = document.getElementById('saveLanguageBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');
                    
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;
                    
                    const formData = new FormData(languageForm);
                    
                    try {
                        const response = await fetch('{{ route("settings.language") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Use the title and message from server response
                            await Swal.fire({
                                icon: 'success',
                                title: data.title,
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });
                            
                            // Reload page to apply language change
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Something went wrong.") }}',
                                confirmButtonColor: '#7c3aed'
                            });
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
        </script>
    @endpush

@endsection