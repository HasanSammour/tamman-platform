{{-- resources/views/admin/specialists/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Specialist Details') . ' - ' . __('Tamman'))

@section('page-title', __('Specialist Details'))

@section('content')
    <div class="specialist-details-container">
        <!-- Back Button Row -->
        <div class="top-bar">
            <a href="{{ route('admin.specialists') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Specialists') }}
            </a>
            <div class="action-buttons">
                <a href="{{ route('admin.specialists.edit', $specialist->id) }}" class="btn-edit-header">
                    <i class="fas fa-edit"></i> {{ __('Edit Specialist') }}
                </a>
                <button class="btn-email-header"
                    onclick="openEmailModal({{ $specialist->id }}, '{{ addslashes($specialist->name) }}')">
                    <i class="fas fa-envelope"></i> {{ __('Send Email') }}
                </button>
            </div>
        </div>

        <!-- ROW 1: Profile Header (Full Width) -->
        <div class="profile-header">
            <div class="profile-avatar">
                @php
                    $profileImage = $specialist->getProfileImageUrl();
                    $specialistInitial = mb_substr($specialist->name, 0, 1, 'UTF-8');
                @endphp
                @if($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $specialist->name }}">
                @else
                    <div class="avatar-placeholder">{{ $specialistInitial }}</div>
                @endif
                <div class="status-badge {{ $specialist->is_active ? 'active' : 'suspended' }}">
                    {{ $specialist->is_active ? __('Active') : __('Suspended') }}
                </div>
                @php
                    $isOnline = false;
                    if ($specialist->last_login_at) {
                        $isOnline = \Carbon\Carbon::parse($specialist->last_login_at)->diffInMinutes(now()) < 5;
                    }
                @endphp
                @if($specialist->is_active && $isOnline)
                    <div class="online-badge">
                        <span class="online-dot"></span> {{ __('Online') }}
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h1>{{ $specialist->name }}</h1>
                <div class="profile-meta">
                    <span><i class="fas fa-envelope"></i> {{ $specialist->email }}</span>
                    @if($specialist->phone)
                        <span><i class="fas fa-phone"></i> {{ $specialist->phone }}</span>
                    @endif
                    <span><i class="fas fa-calendar-alt"></i> {{ __('Joined') }}:
                        {{ $specialist->created_at->translatedFormat('M d, Y') }}</span>
                    <span><i class="fas fa-id-card"></i> ID: #{{ $specialist->id }}</span>
                </div>
                <div class="profile-tags">
                    <span class="tag"><i class="fas fa-stethoscope"></i>
                        {{ $profile->specialization ?? __('Not specified') }}</span>
                    <span class="tag"><i class="fas fa-id-card"></i> {{ __('License') }}:
                        {{ $profile->license_number ?? '—' }}</span>
                    @if($profile->experience_years)
                        <span class="tag"><i class="fas fa-briefcase"></i> {{ $profile->experience_years }}
                            {{ __('years') }}</span>
                    @endif
                    @if($stats['is_donor'])
                        <span class="tag donor"><i class="fas fa-hand-holding-heart"></i> {{ __('Donor') }}</span>
                    @endif
                    @if($specialist->email_verified_at)
                        <span class="tag verified"><i class="fas fa-check-circle"></i> {{ __('Email Verified') }}</span>
                    @else
                        <span class="tag unverified"><i class="fas fa-times-circle"></i> {{ __('Email Not Verified') }}</span>
                    @endif
                    @if($profile->is_verified)
                        <span class="tag verified"><i class="fas fa-user-check"></i> {{ __('Verified Specialist') }}</span>
                    @else
                        <span class="tag unverified"><i class="fas fa-clock"></i> {{ __('Pending Verification') }}</span>
                    @endif
                </div>
                @if($specialist->last_login_at)
                    <div class="last-login">
                        <i class="fas fa-history"></i> {{ __('Last seen') }}:
                        {{ \Carbon\Carbon::parse($specialist->last_login_at)->diffForHumans() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ROW 2: Stats Cards (4 in one row) -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['completed_sessions']) }}</h3>
                    <p>{{ __('Completed') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-users"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_clients']) }}</h3>
                    <p>{{ __('Total Clients') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-data">
                    <h3>${{ number_format($stats['total_earnings'], 2) }}</h3>
                    <p>{{ __('Total Earnings') }}</p>
                </div>
            </div>
        </div>

        <!-- ROW 3: Average Rating + Financial Info (2 cards in one row) -->
        <div class="two-col-grid">
            <!-- Average Rating Card -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> {{ __('Average Rating') }}</h3>
                </div>
                <div class="card-body rating-body">
                    <div class="rating-summary">
                        <div class="avg-rating">
                            <span class="avg-value">{{ number_format($stats['average_rating'], 1) }}</span>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($stats['average_rating']))
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="total-reviews">({{ $stats['total_reviews'] }} {{ __('reviews') }})</span>
                        </div>
                    </div>
                    <div class="rating-stats">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="rating-row">
                                <div class="rating-stars-label">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="fas fa-star {{ $j <= $i ? 'filled' : '' }}"></i>
                                    @endfor
                                </div>
                                <div class="rating-bar">
                                    <div class="rating-fill"
                                        style="width: {{ $stats['total_reviews'] > 0 ? ($ratingDistribution[$i] / $stats['total_reviews']) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <div class="rating-count">{{ number_format($ratingDistribution[$i]) }}</div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Financial Info Card (Expanded) -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-coins"></i> {{ __('Financial Information') }}</h3>
                </div>
                <div class="card-body financial-body">
                    <div class="financial-grid">
                        <div class="financial-item">
                            <span class="financial-label">{{ __('Consultation Fee') }}</span>
                            <span class="financial-value">${{ number_format($profile->consultation_fee ?? 0, 2) }}</span>
                        </div>
                        <div class="financial-item">
                            <span class="financial-label">{{ __('Total Earnings') }}</span>
                            <span class="financial-value">${{ number_format($stats['total_earnings'], 2) }}</span>
                        </div>
                        @if($stats['is_donor'])
                            <div class="financial-item">
                                <span class="financial-label">{{ __('Credit Balance') }}</span>
                                <span class="financial-value">${{ number_format($stats['total_credit'], 2) }}</span>
                            </div>
                            <div class="financial-item">
                                <span class="financial-label">{{ __('Total Donated') }}</span>
                                <span class="financial-value">${{ number_format($stats['total_donated'], 2) }}</span>
                            </div>
                            <div class="financial-item full-width">
                                <span class="financial-label">{{ __('Donor Status') }}</span>
                                <span class="financial-value donor-badge-text">
                                    <i class="fas fa-hand-holding-heart"></i> {{ __('Active Donor') }}
                                </span>
                            </div>
                        @else
                            <div class="financial-item full-width">
                                <span class="financial-label">{{ __('Donor Status') }}</span>
                                <span class="financial-value">{{ __('Not a Donor') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 4: Two Charts Side by Side -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Monthly Sessions') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                </div>
                <div class="chart-body">
                    <div id="sessionsChart" class="apex-chart"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> {{ __('Monthly Earnings') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                </div>
                <div class="chart-body">
                    <div id="earningsChart" class="apex-chart"></div>
                </div>
            </div>
        </div>

        <!-- ROW 5: Professional Information (Full Width) -->
        <div class="info-card full-width">
            <div class="card-header">
                <h3><i class="fas fa-briefcase"></i> {{ __('Professional Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="info-grid-2col">
                    <div class="info-row">
                        <span class="info-label">{{ __('Specialization') }}</span>
                        <span class="info-value">{{ $profile->specialization ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('License Number') }}</span>
                        <span class="info-value">{{ $profile->license_number ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Experience') }}</span>
                        <span class="info-value">{{ $profile->experience_years ?? 0 }} {{ __('years') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Languages') }}</span>
                        <span class="info-value">{{ $profile->languages ?? '—' }}</span>
                    </div>
                    <div class="info-row full-width">
                        <span class="info-label">{{ __('Qualifications') }}</span>
                        <span class="info-value">{{ $profile->qualifications ?? '—' }}</span>
                    </div>
                    <div class="info-row full-width">
                        <span class="info-label">{{ __('Bio') }}</span>
                        <span class="info-value">{{ $profile->bio ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 6: Recent Reviews (Full Width) -->
        <div class="info-card full-width">
            <div class="card-header">
                <h3><i class="fas fa-comments"></i> {{ __('Recent Reviews') }}</h3>
                @if($stats['total_reviews'] > 0)
                    <span class="total-badge">{{ $stats['total_reviews'] }} {{ __('reviews') }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($specialist->reviewsReceived->count() > 0)
                    <div class="reviews-grid">
                        @foreach($specialist->reviewsReceived->take(6) as $review)
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-name">{{ $review->reviewer->name }}</div>
                                    <div class="review-date">{{ $review->created_at->translatedFormat('M d, Y') }}</div>
                                </div>
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="review-comment">{{ $review->comment ?? __('No comment provided') }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-placeholder">
                        <i class="fas fa-star"></i>
                        <p>{{ __('No reviews yet') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ROW 7: Recent Clients + Availability Schedule (2 cards in one row) -->
        <div class="two-col-grid">
            <!-- Recent Clients -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-friends"></i> {{ __('Recent Clients') }}</h3>
                </div>
                <div class="card-body">
                    @if($recentClients->count() > 0)
                        <div class="clients-list">
                            @foreach($recentClients as $client)
                                <div class="client-row">
                                    <div class="client-avatar">
                                        @php
                                            $clientImage = $client->getProfileImageUrl();
                                            $clientInitial = mb_substr($client->name, 0, 1, 'UTF-8');
                                        @endphp
                                        @if($clientImage)
                                            <img src="{{ $clientImage }}" alt="{{ $client->name }}">
                                        @else
                                            <div class="avatar-placeholder-small">{{ $clientInitial }}</div>
                                        @endif
                                    </div>
                                    <div class="client-info">
                                        <div class="client-name">{{ $client->name }}</div>
                                        <div class="client-sessions">
                                            {{ $client->therapySessionsAsPatient->where('specialist_id', $specialist->id)->count() }}
                                            {{ __('sessions') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-users"></i>
                            <p>{{ __('No clients yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Availability Schedule -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-week"></i> {{ __('Availability Schedule') }}</h3>
                </div>
                <div class="card-body">
                    @if($specialist->availability->count() > 0)
                        <div class="availability-list">
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $availabilityByDay = [];
                                foreach ($specialist->availability as $avail) {
                                    if ($avail->is_recurring && $avail->is_available && $avail->day_of_week !== null) {
                                        $availabilityByDay[$avail->day_of_week][] = $avail;
                                    }
                                }
                            @endphp
                            @foreach($days as $index => $day)
                                <div class="availability-row">
                                    <div class="day-name">{{ __($day) }}</div>
                                    <div class="day-slots">
                                        @if(isset($availabilityByDay[$index]) && count($availabilityByDay[$index]) > 0)
                                            @foreach($availabilityByDay[$index] as $slot)
                                                <span class="time-slot">
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="no-slots">{{ __('Not available') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-calendar-alt"></i>
                            <p>{{ __('No availability set') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ROW 8: Recent Sessions + Recent Notifications (2 cards in one row) -->
        <div class="two-col-grid">
            <!-- Recent Sessions -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('Recent Sessions') }}</h3>
                    <a href="{{ route('admin.reports.sessions') }}?specialist={{ $specialist->id }}"
                        class="view-link">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($recentSessions->count() > 0)
                        <div class="sessions-list">
                            @foreach($recentSessions->take(6) as $session)
                                <div class="session-row">
                                    <div class="session-date">
                                        <div class="day">{{ \Carbon\Carbon::parse($session->session_datetime)->format('d') }}</div>
                                        <div class="month">
                                            {{ \Carbon\Carbon::parse($session->session_datetime)->translatedFormat('M') }}</div>
                                    </div>
                                    <div class="session-info">
                                        <div class="session-patient">{{ $session->patient->name }}</div>
                                        <div class="session-time">
                                            {{ \Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }}</div>
                                    </div>
                                    <div class="session-type">
                                        <span class="type-badge {{ $session->session_type }}">
                                            {{ __(ucfirst($session->session_type)) }}
                                        </span>
                                    </div>
                                    <div class="session-status">
                                        <span class="status-badge {{ $session->status }}">
                                            {{ __(ucfirst($session->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-calendar-alt"></i>
                            <p>{{ __('No sessions found') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> {{ __('Recent Notifications') }}</h3>
                </div>
                <div class="card-body">
                    @if($specialist->notifications->count() > 0)
                        <div class="notifications-list">
                            @foreach($specialist->notifications->take(7) as $notification)
                                <div class="notification-row {{ $notification->is_read ? '' : 'unread' }}">
                                    <div class="notification-icon">
                                        <i
                                            class="fas {{ $notification->type == 'session_reminder' ? 'fa-calendar' : ($notification->type == 'points_earned' ? 'fa-star' : 'fa-bell') }}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p>{{ $notification->message }}</p>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-bell-slash"></i>
                            <p>{{ __('No notifications found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ROW 9: Documents (Full Width with 2 columns inside) -->
        <div class="info-card full-width">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> {{ __('Professional Documents') }}</h3>
            </div>
            <div class="card-body">
                <div class="documents-grid">
                    <!-- Certificate -->
                    <div class="document-card">
                        <h4><i class="fas fa-certificate"></i> {{ __('Professional Certificate') }}</h4>
                        @php
                            $certificateInfo = $profile->getCertificateInfo();
                        @endphp
                        @if($certificateInfo['has_file'])
                            @if($certificateInfo['is_image'])
                                <img src="{{ $certificateInfo['url'] }}" alt="{{ __('Certificate') }}" class="document-img">
                            @else
                                <div class="document-file-info">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>{{ $certificateInfo['filename'] }}</span>
                                    <a href="{{ $certificateInfo['url'] }}" target="_blank"
                                        class="btn-view-file">{{ __('View') }}</a>
                                </div>
                            @endif
                        @else
                            <div class="document-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>{{ __('No certificate uploaded') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- License -->
                    <div class="document-card">
                        <h4><i class="fas fa-id-card"></i> {{ __('Professional License') }}</h4>
                        @php
                            $licenseInfo = $profile->getLicenseInfo();
                        @endphp
                        @if($licenseInfo['has_file'])
                            @if($licenseInfo['is_image'])
                                <img src="{{ $licenseInfo['url'] }}" alt="{{ __('License') }}" class="document-img">
                            @else
                                <div class="document-file-info">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>{{ $licenseInfo['filename'] }}</span>
                                    <a href="{{ $licenseInfo['url'] }}" target="_blank" class="btn-view-file">{{ __('View') }}</a>
                                </div>
                            @endif
                        @else
                            <div class="document-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>{{ __('No license uploaded') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Email Modal -->
    <div id="sendEmailModal" class="custom-modal">
        <div class="custom-modal-content" style="max-width: 550px;">
            <div class="custom-modal-header">
                <h3><i class="fas fa-envelope"></i> {{ __('Send Email to Specialist') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="sendEmailForm">
                @csrf
                <div class="custom-modal-body">
                    <input type="hidden" id="emailSpecialistId">
                    <div class="form-group">
                        <label for="emailSubject">{{ __('Subject') }} <span class="required">*</span></label>
                        <input type="text" id="emailSubject" class="form-control"
                            placeholder="{{ __('Enter email subject...') }}" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="emailMessage">{{ __('Message') }} <span class="required">*</span></label>
                        <textarea id="emailMessage" class="form-control" rows="6"
                            placeholder="{{ __('Write your message here...') }}" required></textarea>
                        <small class="form-hint">{{ __('The specialist will receive this message via email.') }}</small>
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-send-email" id="sendEmailSubmitBtn">
                        <span class="btn-text"><i class="fas fa-paper-plane"></i> {{ __('Send Email') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .specialist-details-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Top Bar */
            .top-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 18px;
                background: #f3f4f6;
                border-radius: 40px;
                color: #374151;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateX(-3px);
            }

            .action-buttons {
                display: flex;
                gap: 12px;
            }

            .btn-edit-header,
            .btn-email-header {
                padding: 8px 18px;
                border-radius: 40px;
                font-size: 0.85rem;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-edit-header {
                background: #7c3aed;
                color: white;
            }

            .btn-edit-header:hover {
                background: #6d28d9;
                transform: translateY(-2px);
                color: white;
            }

            .btn-email-header {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .btn-email-header:hover {
                background: #c7d2fe;
                transform: translateY(-2px);
            }

            /* Profile Header */
            .profile-header {
                background: white;
                border-radius: 24px;
                padding: 30px;
                display: flex;
                gap: 30px;
                margin-bottom: 25px;
                flex-wrap: wrap;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .profile-avatar {
                position: relative;
            }

            .profile-avatar img,
            .avatar-placeholder {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                font-weight: 600;
                color: white;
            }

            .status-badge {
                position: absolute;
                bottom: 5px;
                right: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                background: #10b981;
                color: white;
            }

            .status-badge.suspended {
                background: #ef4444;
            }

            .online-badge {
                position: absolute;
                top: 5px;
                right: 5px;
                background: #10b981;
                padding: 4px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                color: white;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .online-dot {
                width: 8px;
                height: 8px;
                background: white;
                border-radius: 50%;
                animation: pulse 1.5s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            .profile-info {
                flex: 1;
            }

            .profile-info h1 {
                font-size: 1.6rem;
                margin: 0 0 10px;
                color: #1f2937;
            }

            .profile-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 12px;
            }

            .profile-meta span {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .profile-meta i {
                color: #7c3aed;
            }

            .profile-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 10px;
            }

            .tag {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                background: #f3f4f6;
                color: #374151;
            }

            .tag.donor {
                background: #fef3c7;
                color: #d97706;
            }

            .tag.verified {
                background: #d1fae5;
                color: #065f46;
            }

            .tag.unverified {
                background: #fee2e2;
                color: #991b1b;
            }

            .last-login {
                font-size: 0.75rem;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            /* Stats Row */
            .stats-row {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
            }

            .stat-card:hover {
                transform: translateY(-3px);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.3rem;
                color: white;
            }

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-data h3 {
                font-size: 1.3rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 4px 0 0;
            }

            /* Two Column Grid */
            .two-col-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 25px;
            }

            /* Info Cards */
            .info-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .info-card.full-width {
                grid-column: span 2;
                margin-bottom: 25px;
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .card-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .card-header h3 i {
                color: #7c3aed;
            }

            .view-link {
                font-size: 0.7rem;
                color: #7c3aed;
                text-decoration: none;
            }

            .total-badge {
                background: #ede9fe;
                color: #7c3aed;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .card-body {
                padding: 16px 20px;
            }

            /* Rating Section */
            .rating-body {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .rating-summary {
                text-align: center;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .avg-rating .avg-value {
                font-size: 2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .avg-rating .stars {
                color: #fbbf24;
                font-size: 0.8rem;
            }

            .total-reviews {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .rating-stats {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .rating-row {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .rating-stars-label {
                width: 80px;
                font-size: 0.65rem;
                color: #d1d5db;
            }

            .rating-stars-label .filled {
                color: #fbbf24;
            }

            .rating-bar {
                flex: 1;
                height: 6px;
                background: #f3f4f6;
                border-radius: 3px;
                overflow: hidden;
            }

            .rating-fill {
                height: 100%;
                background: #fbbf24;
                border-radius: 3px;
            }

            .rating-count {
                width: 40px;
                font-size: 0.7rem;
                color: #6b7280;
                text-align: right;
            }

            /* Financial Info Grid */
            .financial-body {
                padding: 20px;
            }

            .financial-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .financial-item {
                background: #f9fafb;
                border-radius: 12px;
                padding: 12px;
                text-align: center;
            }

            .financial-item.full-width {
                grid-column: span 2;
            }

            .financial-label {
                display: block;
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .financial-value {
                display: block;
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .donor-badge-text {
                color: #d97706;
            }

            .donor-badge-text i {
                color: #d97706;
                margin-right: 5px;
            }

            /* Info Rows */
            .info-grid-2col {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .info-row {
                display: flex;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .info-row:last-child {
                border-bottom: none;
            }

            .info-label {
                width: 130px;
                font-weight: 600;
                font-size: 0.75rem;
                color: #6b7280;
                flex-shrink: 0;
            }

            .info-value {
                flex: 1;
                font-size: 0.75rem;
                color: #1f2937;
            }

            .info-row.full-width {
                grid-column: span 2;
            }

            /* Charts */
            .charts-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 25px;
            }

            .chart-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .chart-header {
                padding: 18px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .chart-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .chart-header h3 i {
                color: #7c3aed;
            }

            .chart-subtitle {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .chart-body {
                padding: 20px;
                min-height: 300px;
            }

            .apex-chart {
                width: 100%;
                min-height: 280px;
            }

            /* Reviews Grid */
            .reviews-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .review-card {
                padding: 15px;
                background: #f9fafb;
                border-radius: 16px;
            }

            .review-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .reviewer-name {
                font-weight: 600;
                font-size: 0.8rem;
                color: #1f2937;
            }

            .review-date {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            .review-rating {
                color: #fbbf24;
                font-size: 0.7rem;
                margin-bottom: 8px;
            }

            .review-comment {
                font-size: 0.75rem;
                color: #6b7280;
                line-height: 1.4;
            }

            /* Clients List */
            .clients-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .client-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .client-avatar img,
            .avatar-placeholder-small {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder-small {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .client-info {
                flex: 1;
            }

            .client-name {
                font-weight: 600;
                font-size: 0.8rem;
                color: #1f2937;
            }

            .client-sessions {
                font-size: 0.65rem;
                color: #6b7280;
            }

            /* Availability List */
            .availability-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .availability-row {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 6px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .availability-row:last-child {
                border-bottom: none;
            }

            .day-name {
                width: 100px;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .day-slots {
                flex: 1;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .time-slot {
                background: #ede9fe;
                color: #7c3aed;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .no-slots {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Sessions List */
            .sessions-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .session-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .session-date {
                text-align: center;
                min-width: 50px;
            }

            .session-date .day {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .session-date .month {
                font-size: 0.6rem;
                color: #6b7280;
            }

            .session-info {
                flex: 1;
            }

            .session-patient {
                font-weight: 600;
                font-size: 0.8rem;
                color: #1f2937;
            }

            .session-time {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .type-badge {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .type-badge.video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .type-badge.audio {
                background: #d1fae5;
                color: #059669;
            }

            .type-badge.text {
                background: #fef3c7;
                color: #d97706;
            }

            .status-badge {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .status-badge.scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Notifications */
            .notifications-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .notification-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .notification-row.unread {
                background: #f5f3ff;
                margin: 0 -20px;
                padding: 10px 20px;
            }

            .notification-icon i {
                color: #7c3aed;
            }

            .notification-content {
                flex: 1;
            }

            .notification-content p {
                font-size: 0.8rem;
                margin: 0 0 3px;
                color: #374151;
            }

            .notification-content small {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Documents */
            .documents-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .document-card {
                text-align: center;
            }

            .document-card h4 {
                margin-bottom: 15px;
                font-size: 0.85rem;
                color: #1f2937;
            }

            .document-img {
                max-width: 100%;
                max-height: 200px;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .document-file-info {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 20px;
                background: #f9fafb;
                border-radius: 12px;
            }

            .document-file-info i {
                font-size: 2rem;
                color: #ef4444;
            }

            .btn-view-file {
                background: #7c3aed;
                color: white;
                padding: 4px 12px;
                border-radius: 6px;
                text-decoration: none;
                font-size: 0.7rem;
            }

            .document-placeholder {
                padding: 30px;
                background: #f9fafb;
                border-radius: 12px;
                text-align: center;
            }

            .document-placeholder i {
                font-size: 2rem;
                color: #c4b5fd;
                margin-bottom: 10px;
            }

            .document-placeholder p {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Empty State */
            .empty-placeholder {
                text-align: center;
                padding: 30px 20px;
            }

            .empty-placeholder i {
                font-size: 2rem;
                color: #c4b5fd;
                margin-bottom: 10px;
                display: block;
            }

            .empty-placeholder p {
                color: #6b7280;
                font-size: 0.8rem;
                margin: 0;
            }

            /* Modal */
            .custom-modal {
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
                transition: all 0.2s;
            }

            .custom-modal.active {
                opacity: 1;
                visibility: visible;
            }

            .custom-modal-content {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                transform: scale(0.9);
                transition: transform 0.2s;
            }

            .custom-modal.active .custom-modal-content {
                transform: scale(1);
            }

            .custom-modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                color: #6b7280;
            }

            .custom-modal-body {
                padding: 24px;
            }

            .custom-modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                font-size: 0.8rem;
                color: #374151;
            }

            .form-control {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.85rem;
            }

            .form-hint {
                font-size: 0.7rem;
                color: #9ca3af;
                margin-top: 5px;
                display: block;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-send-email {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-spinner {
                display: none;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-row {
                    grid-template-columns: repeat(2, 1fr);
                }

                .two-col-grid {
                    grid-template-columns: 1fr;
                }

                .charts-row {
                    grid-template-columns: 1fr;
                }

                .reviews-grid {
                    grid-template-columns: 1fr;
                }

                .info-grid-2col {
                    grid-template-columns: 1fr;
                }

                .financial-grid {
                    grid-template-columns: 1fr;
                }

                .financial-item.full-width {
                    grid-column: span 1;
                }

                .info-row.full-width {
                    grid-column: span 1;
                }

                .availability-row {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .day-name {
                    width: 100%;
                    margin-bottom: 8px;
                }
            }

            @media (max-width: 768px) {
                .specialist-details-container {
                    padding: 15px;
                }

                .profile-header {
                    flex-direction: column;
                    text-align: center;
                }

                .profile-meta {
                    justify-content: center;
                }

                .profile-tags {
                    justify-content: center;
                }

                .stats-row {
                    grid-template-columns: 1fr;
                }

                .top-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .action-buttons {
                    justify-content: center;
                }

                .documents-grid {
                    grid-template-columns: 1fr;
                }
            }

            body.rtl .info-row {
                flex-direction: row;
            }

            body.rtl .rating-row {
                flex-direction: row;
            }

            body.rtl .availability-row {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .info-row {
                    flex-direction: column;
                }

                body.rtl .availability-row {
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Charts data
            const sessionsMonths = @json($monthlySessions['months']);
            const sessionsCounts = @json($monthlySessions['sessions']);
            const earningsMonths = @json($monthlyEarnings['months']);
            const earningsAmounts = @json($monthlyEarnings['earnings']);
            const currentLocale = '{{ app()->getLocale() }}';

            let sessionsChart = null;
            let earningsChart = null;
            let chartsRendered = false;

            function renderSessionsChart() {
                const element = document.querySelector("#sessionsChart");
                if (!element) return;
                if (sessionsChart) sessionsChart.destroy();

                sessionsChart = new ApexCharts(element, {
                    series: [{ name: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions', data: sessionsCounts }],
                    chart: { type: 'line', height: 280, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true }, background: 'transparent' },
                    stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
                    markers: { size: 6, hover: { size: 9 }, colors: ['#7c3aed'], strokeColors: '#ffffff', strokeWidth: 2 },
                    tooltip: { theme: 'dark', y: { formatter: (val) => val } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: sessionsMonths, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions' } },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.1 } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                });
                sessionsChart.render().catch(err => console.log('Chart render error:', err));
            }

            function renderEarningsChart() {
                const element = document.querySelector("#earningsChart");
                if (!element) return;
                if (earningsChart) earningsChart.destroy();

                earningsChart = new ApexCharts(element, {
                    series: [{ name: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)', data: earningsAmounts }],
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                    plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                    colors: ['#10b981'],
                    tooltip: { theme: 'dark', y: { formatter: (val) => '$' + val.toFixed(2) } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: earningsMonths, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)' }, labels: { formatter: (val) => '$' + val } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                });
                earningsChart.render().catch(err => console.log('Chart render error:', err));
            }

            function renderCharts() {
                if (chartsRendered) return;
                renderSessionsChart();
                renderEarningsChart();
                chartsRendered = true;
            }

            // Wait for DOM, sidebar animation, and window load
            document.addEventListener('DOMContentLoaded', function () {
                // Initial delay for sidebar animation
                setTimeout(renderCharts, 500);

                // Also wait for window load to ensure all resources are loaded
                window.addEventListener('load', function () {
                    setTimeout(renderCharts, 200);
                });

                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function () {
                        chartsRendered = false;
                        setTimeout(renderCharts, 400);
                    });
                }

                const mobileToggle = document.getElementById('mobileSidebarToggle');
                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function () {
                        chartsRendered = false;
                        setTimeout(renderCharts, 450);
                    });
                }

                let resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        if (sessionsChart) {
                            sessionsChart.updateOptions({
                                chart: { width: '100%' }
                            });
                        }
                        if (earningsChart) {
                            earningsChart.updateOptions({
                                chart: { width: '100%' }
                            });
                        }
                    }, 250);
                });
            });

            // Send Email Modal
            function openEmailModal(specialistId, specialistName) {
                document.getElementById('emailSpecialistId').value = specialistId;
                document.getElementById('emailSubject').value = '';
                document.getElementById('emailMessage').value = '';
                document.getElementById('sendEmailModal').classList.add('active');
            }

            document.getElementById('sendEmailForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const specialistId = document.getElementById('emailSpecialistId').value;
                const subject = document.getElementById('emailSubject').value;
                const message = document.getElementById('emailMessage').value;
                const submitBtn = document.getElementById('sendEmailSubmitBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnSpinner = submitBtn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/admin/specialists/${specialistId}/send-email`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ subject, message })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Email Sent") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        document.getElementById('sendEmailModal').classList.remove('active');
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error. Please try again.") }}' });
                } finally {
                    btnText.style.display = 'inline-flex';
                    btnSpinner.style.display = 'none';
                    submitBtn.disabled = false;
                }
            });

            // Modal Close
            document.querySelectorAll('.modal-close, .custom-modal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('active')));
            });
        </script>
    @endpush
@endsection