{{-- resources/views/patient/rewards/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Rewards') . ' - ' . __('Tamman'))

@section('page-title', __('Tamman Points'))

@section('content')
    <div class="rewards-container">
        <!-- Hero Section with Points Card -->
        <div class="points-hero animate-fade-in-up">
            <div class="points-card">
                <div class="points-card-content">
                    <div class="points-info">
                        <span class="points-label">{{ __('Your Balance') }}</span>
                        <div class="points-value">
                            <span class="points-number"
                                id="totalPoints">{{ number_format($stats['current_balance']) }}</span>
                            <span class="points-unit">{{ __('Points') }}</span>
                        </div>
                        <div class="credit-balance">
                            <i class="fas fa-coins"></i>
                            <span>{{ __('Credit Balance') }}: ${{ number_format($stats['credit_balance'], 2) }}</span>
                        </div>
                    </div>
                    <div class="points-rank">
                        <div class="rank-icon" style="background: {{ $rank['color'] }}20; color: {{ $rank['color'] }}">
                            <i class="fas {{ $rank['icon'] }}"></i>
                        </div>
                        <div class="rank-info">
                            <span class="rank-label">{{ __('Your Rank') }}</span>
                            <span
                                class="rank-name">{{ app()->getLocale() === 'ar' ? $rank['name_ar'] : $rank['name'] }}</span>
                        </div>
                    </div>
                </div>

                @if($nextMilestone)
                    <div class="milestone-progress">
                        <div class="milestone-info">
                            <span>{{ __('Next Milestone') }}: {{ number_format($nextMilestone['next']) }}
                                {{ __('Points') }}</span>
                            <span>{{ number_format($nextMilestone['current']) }} /
                                {{ number_format($nextMilestone['next']) }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $nextMilestone['progress'] }}%"></div>
                        </div>
                        <div class="milestone-message">
                            <i class="fas fa-star"></i>
                            <span>{{ __('Only') }} {{ number_format($nextMilestone['needed']) }}
                                {{ __('more points to reach the next level!') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid-milestones">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.05s">
                <div class="stat-icon" style="background: #10b98120; color: #10b981">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_earned']) }}</h3>
                    <p>{{ __('Total Points Earned') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon" style="background: #7c3aed20; color: #7c3aed">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_redeemed']) }}</h3>
                    <p>{{ __('Points Redeemed') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.15s">
                <div class="stat-icon" style="background: #f59e0b20; color: #f59e0b">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_redemptions']) }}</h3>
                    <p>{{ __('Rewards Claimed') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon" style="background: #06b6d420; color: #06b6d4">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ round($user->created_at->diffInDays(now())) }}</h3>
                    <p>{{ __('Days Active') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs - Centered -->
        <div class="rewards-tabs">
            <button class="tab-btn active" data-tab="rewards">
                <i class="fas fa-gift"></i> {{ __('Rewards') }}
            </button>
            <button class="tab-btn" data-tab="history">
                <i class="fas fa-history"></i> {{ __('Redemption History') }}
            </button>
            <button class="tab-btn" data-tab="points-history">
                <i class="fas fa-coins"></i> {{ __('Points History') }}
            </button>
        </div>

        <!-- Rewards Tab -->
        <div class="tab-content active" id="tab-rewards">
            <!-- Credit Rewards - 2x2 Grid -->
            @if($groupedRewards['credit']->count() > 0)
                <div class="rewards-section animate-fade-in-up">
                    <div class="section-header">
                        <div class="section-icon" style="background: #10b98120; color: #10b981">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="section-text">
                            <h3>{{ __('Credit Rewards') }}</h3>
                            <p>{{ __('Redeem your points for credit to use on sessions') }}</p>
                        </div>
                    </div>
                    <div class="rewards-grid credit-grid">
                        @foreach($groupedRewards['credit'] as $reward)
                            @php
                                $nameArray = json_decode($reward->name, true);
                                $rewardName = is_array($nameArray) ? (app()->getLocale() === 'ar' ? ($nameArray['ar'] ?? $reward->name) : ($nameArray['en'] ?? $reward->name)) : $reward->name;
                                $descArray = json_decode($reward->description, true);
                                $rewardDesc = is_array($descArray) ? (app()->getLocale() === 'ar' ? ($descArray['ar'] ?? '') : ($descArray['en'] ?? '')) : '';
                            @endphp
                            <div class="reward-card" data-reward-id="{{ $reward->id }}" data-points="{{ $reward->points_needed }}"
                                data-type="credit">
                                <div class="reward-icon" style="background: #10b98120; color: #10b981">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div class="reward-info">
                                    <h4>{{ $rewardName }}</h4>
                                    <p class="reward-desc">{{ $rewardDesc }}</p>
                                    <div class="reward-points">
                                        <i class="fas fa-star"></i>
                                        <span>{{ number_format($reward->points_needed) }} {{ __('Points') }}</span>
                                    </div>
                                </div>
                                <button class="btn-redeem credit-btn" data-reward-id="{{ $reward->id }}"
                                    data-points="{{ $reward->points_needed }}" data-name="{{ $rewardName }}"
                                    data-desc="{{ $rewardDesc }}" data-icon="fa-coins" data-color="#10b981" {{ $stats['current_balance'] < $reward->points_needed ? 'disabled' : '' }}>
                                    <i class="fas fa-exchange-alt"></i> {{ __('Redeem') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Free Sessions Rewards - 2x2 Grid -->
            @if($groupedRewards['free_session']->count() > 0)
                <div class="rewards-section animate-fade-in-up">
                    <div class="section-header">
                        <div class="section-icon" style="background: #7c3aed20; color: #7c3aed">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="section-text">
                            <h3>{{ __('Free Session Rewards') }}</h3>
                            <p>{{ __('Get free therapy sessions by redeeming your points') }}</p>
                        </div>
                    </div>
                    <div class="rewards-grid free-grid">
                        @foreach($groupedRewards['free_session'] as $reward)
                            @php
                                $nameArray = json_decode($reward->name, true);
                                $rewardName = is_array($nameArray) ? (app()->getLocale() === 'ar' ? ($nameArray['ar'] ?? $reward->name) : ($nameArray['en'] ?? $reward->name)) : $reward->name;
                                $descArray = json_decode($reward->description, true);
                                $rewardDesc = is_array($descArray) ? (app()->getLocale() === 'ar' ? ($descArray['ar'] ?? '') : ($descArray['en'] ?? '')) : '';
                                $sessionIcon = $reward->session_type == 'video' ? 'fa-video' : ($reward->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots');
                                $sessionColor = $reward->session_type == 'video' ? '#7c3aed' : ($reward->session_type == 'audio' ? '#10b981' : '#f59e0b');
                            @endphp
                            <div class="reward-card free-card" data-reward-id="{{ $reward->id }}"
                                data-points="{{ $reward->points_needed }}" data-type="free_session">
                                <div class="reward-icon" style="background: {{ $sessionColor }}20; color: {{ $sessionColor }}">
                                    <i class="fas {{ $sessionIcon }}"></i>
                                </div>
                                <div class="reward-info">
                                    <h4>{{ $rewardName }}</h4>
                                    <p class="reward-desc">{{ $rewardDesc }}</p>
                                    <div class="reward-points">
                                        <i class="fas fa-star"></i>
                                        <span>{{ number_format($reward->points_needed) }} {{ __('Points') }}</span>
                                    </div>
                                </div>
                                <button class="btn-redeem free-btn" data-reward-id="{{ $reward->id }}"
                                    data-points="{{ $reward->points_needed }}" data-name="{{ $rewardName }}"
                                    data-desc="{{ $rewardDesc }}" data-icon="{{ $sessionIcon }}" data-color="{{ $sessionColor }}" {{ $stats['current_balance'] < $reward->points_needed ? 'disabled' : '' }}>
                                    <i class="fas fa-gift"></i> {{ __('Redeem') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Donate Rewards - Full Width Card -->
            @if($groupedRewards['donate']->count() > 0)
                <div class="donate-section animate-fade-in-up">
                    <div class="section-header">
                        <div class="section-icon" style="background: #ec489920; color: #ec4899">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="section-text">
                            <h3>{{ __('Donate Points') }}</h3>
                            <p>{{ __('Share your points to help patients in need') }}</p>
                        </div>
                    </div>
                    <div class="donate-grid">
                        @foreach($groupedRewards['donate'] as $reward)
                            @php
                                $nameArray = json_decode($reward->name, true);
                                $rewardName = is_array($nameArray) ? (app()->getLocale() === 'ar' ? ($nameArray['ar'] ?? $reward->name) : ($nameArray['en'] ?? $reward->name)) : $reward->name;
                                $descArray = json_decode($reward->description, true);
                                $rewardDesc = is_array($descArray) ? (app()->getLocale() === 'ar' ? ($descArray['ar'] ?? '') : ($descArray['en'] ?? '')) : '';
                            @endphp
                            <div class="donate-card-full" data-reward-id="{{ $reward->id }}"
                                data-points="{{ $reward->points_needed }}" data-type="donate">
                                <div class="donate-card-content">
                                    <div class="donate-icon" style="background: #ec489920; color: #ec4899">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </div>
                                    <div class="donate-info">
                                        <h4>{{ $rewardName }}</h4>
                                        <p class="donate-desc">{{ $rewardDesc }}</p>
                                        <div class="donate-points-full">
                                            <i class="fas fa-heart"></i>
                                            <span>{{ number_format($reward->points_needed) }} {{ __('Points') }}</span>
                                        </div>
                                    </div>
                                    <button class="btn-donate-full" data-reward-id="{{ $reward->id }}"
                                        data-points="{{ $reward->points_needed }}" data-name="{{ $rewardName }}"
                                        data-desc="{{ $rewardDesc }}" data-icon="fa-hand-holding-heart" data-color="#ec4899" {{ $stats['current_balance'] < $reward->points_needed ? 'disabled' : '' }}>
                                        <i class="fas fa-hand-holding-heart"></i> {{ __('Donate') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            @if($recentRedemptions->count() > 0)
                <div class="recent-activity animate-fade-in-up">
                    <div class="section-header">
                        <div class="section-icon" style="background: #f59e0b20; color: #f59e0b">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="section-text">
                            <h3>{{ __('Recent Activity') }}</h3>
                            <p>{{ __('Your latest reward redemptions') }}</p>
                        </div>
                    </div>
                    <div class="activity-list">
                        @foreach($recentRedemptions as $redemption)
                            @php
                                $rewardNameArray = json_decode($redemption->reward->name, true);
                                $displayName = is_array($rewardNameArray) ? (app()->getLocale() === 'ar' ? ($rewardNameArray['ar'] ?? $redemption->reward->name) : ($rewardNameArray['en'] ?? $redemption->reward->name)) : $redemption->reward->name;
                            @endphp
                            <div class="activity-item">
                                <div class="activity-icon" style="background: #10b98120; color: #10b981">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="activity-details">
                                    <h4>{{ $displayName }}</h4>
                                    <p>{{ __('Redeemed on') }} {{ $redemption->redeemed_at->translatedFormat('M d, Y') }}</p>
                                </div>
                                <div class="activity-points">
                                    <span>-{{ number_format($redemption->points_spent) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Redemption History Tab -->
        <div class="tab-content" id="tab-history">
            <div class="history-container">
                <div class="loading-spinner" id="historyLoader">
                    <div class="spinner"></div>
                    <p>{{ __('Loading history...') }}</p>
                </div>
                <div id="historyContent" style="display: none;"></div>
                <div class="pagination-container" id="historyPagination"></div>
            </div>
        </div>

        <!-- Points History Tab -->
        <div class="tab-content" id="tab-points-history">
            <div class="history-container">
                <div class="points-stats-header">
                    <div class="points-chart-container">
                        <canvas id="pointsChart" class="points-chart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
                <div class="loading-spinner" id="pointsLoader">
                    <div class="spinner"></div>
                    <p>{{ __('Loading points history...') }}</p>
                </div>
                <div id="pointsHistoryContent" style="display: none;"></div>
                <div class="pagination-container" id="pointsPagination"></div>
            </div>
        </div>
    </div>

    <!-- Redeem Confirmation Modal -->
    <div id="redeemModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-gift"></i> <span id="modalTitle">{{ __('Redeem Reward') }}</span></h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="reward-preview" id="rewardPreview">
                    <div class="preview-icon" id="previewIcon"></div>
                    <h4 id="previewName"></h4>
                    <p id="previewDescription"></p>
                    <div class="preview-points">
                        <i class="fas fa-star"></i>
                        <span id="previewPoints"></span>
                    </div>
                </div>
                <div class="confirm-message">
                    <p>{{ __('Are you sure you want to redeem this reward?') }}</p>
                    <p class="warning-text">{{ __('This action cannot be undone.') }}</p>
                </div>
                <input type="hidden" id="redeemRewardId">
                <input type="hidden" id="redeemRewardType">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">{{ __('Cancel') }}</button>
                <button type="button" class="btn-confirm" id="confirmRedeemBtn">
                    <span class="btn-text">{{ __('Confirm Redemption') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-container success-modal">
            <div class="modal-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="modal-body text-center">
                <h3 id="successTitle">{{ __('Reward Redeemed!') }}</h3>
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-success-close">{{ __('Great!') }}</button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .rewards-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .points-hero {
                margin-bottom: 30px;
            }

            .points-card {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 28px;
                padding: 30px;
                color: white;
            }

            .points-card-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 25px;
            }

            .points-label {
                font-size: 0.85rem;
                opacity: 0.8;
                display: block;
                margin-bottom: 8px;
            }

            .points-value {
                display: flex;
                align-items: baseline;
                gap: 8px;
            }

            .points-number {
                font-size: 3rem;
                font-weight: 800;
            }

            .points-unit {
                font-size: 1rem;
                opacity: 0.8;
            }

            .credit-balance {
                margin-top: 10px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.85rem;
                opacity: 0.9;
            }

            .points-rank {
                display: flex;
                align-items: center;
                gap: 15px;
                background: rgba(255, 255, 255, 0.15);
                padding: 12px 20px;
                border-radius: 50px;
            }

            .rank-icon {
                width: 48px;
                height: 48px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
            }

            .rank-label {
                font-size: 0.7rem;
                opacity: 0.7;
                display: block;
            }

            .rank-name {
                font-size: 1rem;
                font-weight: 600;
            }

            .milestone-progress {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 15px 20px;
            }

            .milestone-info {
                display: flex;
                justify-content: space-between;
                font-size: 0.75rem;
                margin-bottom: 10px;
            }

            .progress-bar {
                height: 8px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .progress-fill {
                height: 100%;
                background: #fbbf24;
                border-radius: 4px;
                transition: width 0.5s ease;
            }

            .milestone-message {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.7rem;
                opacity: 0.8;
            }

            .stats-grid-milestones {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon i {
                font-size: 1.5rem;
            }

            .stat-info h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .rewards-tabs {
                display: flex;
                justify-content: center;
                gap: 12px;
                margin-bottom: 30px;
                border-bottom: 1px solid #e5e7eb;
            }

            .tab-btn {
                background: none;
                border: none;
                padding: 12px 28px;
                font-size: 0.9rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #6b7280;
                position: relative;
                border-radius: 40px 40px 0 0;
            }

            .tab-btn i {
                margin-right: 8px;
            }

            .tab-btn:hover {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .tab-btn.active {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .tab-btn.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background: #7c3aed;
            }

            .tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
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

            .rewards-section {
                margin-bottom: 40px;
                background: white;
                border-radius: 24px;
                padding: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .section-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid #f3f4f6;
            }

            .section-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .section-icon i {
                font-size: 1.5rem;
            }

            .section-text h3 {
                font-size: 1.2rem;
                margin: 0 0 4px 0;
                color: #1f2937;
            }

            .section-text p {
                font-size: 0.8rem;
                color: #6b7280;
                margin: 0;
            }

            .rewards-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .credit-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .free-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .reward-card {
                background: #f9fafb;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }

            .reward-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
                background: white;
            }

            .reward-card.free-card:hover {
                background: #f5f3ff;
            }

            .reward-icon {
                width: 55px;
                height: 55px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .reward-icon i {
                font-size: 1.5rem;
            }

            .reward-info {
                flex: 1;
            }

            .reward-info h4 {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .reward-desc {
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 8px;
                line-height: 1.4;
            }

            .reward-points {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #fef3c7;
                padding: 4px 10px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 500;
                color: #d97706;
            }

            .btn-redeem {
                border: none;
                padding: 8px 18px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .credit-btn {
                background: #10b981;
                color: white;
            }

            .credit-btn:hover:not(:disabled) {
                background: #059669;
                transform: translateY(-2px);
            }

            .free-btn {
                background: #7c3aed;
                color: white;
            }

            .free-btn:hover:not(:disabled) {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-redeem:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }

            /* Donate Section - Full Width */
            .donate-section {
                margin-bottom: 40px;
                background: white;
                border-radius: 24px;
                padding: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .donate-grid {
                display: flex;
                justify-content: center;
            }

            .donate-card-full {
                background: linear-gradient(135deg, #fdf2f8, #fce7f3);
                border-radius: 20px;
                padding: 25px;
                width: 100%;
                max-width: 450px;
                transition: all 0.3s ease;
                border: 1px solid #fbcfe8;
            }

            .donate-card-full:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 30px -10px rgba(236, 72, 153, 0.2);
            }

            .donate-card-content {
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .donate-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .donate-icon i {
                font-size: 1.8rem;
            }

            .donate-info {
                flex: 1;
            }

            .donate-info h4 {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 5px;
                color: #1f2937;
            }

            .donate-desc {
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .donate-points-full {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #fce7f3;
                padding: 5px 12px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 500;
                color: #db2777;
            }

            .btn-donate-full {
                background: #ec4899;
                color: white;
                border: none;
                padding: 10px 25px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                flex-shrink: 0;
            }

            .btn-donate-full:hover:not(:disabled) {
                background: #db2777;
                transform: translateY(-2px);
            }

            .btn-donate-full:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            /* Recent Activity */
            .recent-activity {
                background: white;
                border-radius: 24px;
                padding: 25px;
                border: 1px solid #e5e7eb;
                margin-top: 10px;
            }

            .activity-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .activity-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .activity-details {
                flex: 1;
            }

            .activity-details h4 {
                font-size: 0.9rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .activity-details p {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .activity-points span {
                font-size: 0.9rem;
                font-weight: 700;
                color: #ef4444;
            }

            /* History Container */
            .history-container {
                background: white;
                border-radius: 24px;
                padding: 20px;
            }

            .loading-spinner {
                text-align: center;
                padding: 50px;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .empty-state {
                text-align: center;
                padding: 50px 20px;
            }

            .empty-state i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-state p {
                color: #6b7280;
            }

            .history-table {
                width: 100%;
                border-collapse: collapse;
            }

            .history-table th,
            .history-table td {
                padding: 12px 15px;
                text-align: center;
                border-bottom: 1px solid #f3f4f6;
            }

            .history-table th {
                font-weight: 600;
                color: #374151;
                background: #f9fafb;
            }

            /* Table Column Widths */
            .history-table th:nth-child(1),
            .history-table td:nth-child(1) {
                width: 25%;
            }

            .history-table th:nth-child(2),
            .history-table td:nth-child(2) {
                width: 15%;
            }

            .history-table th:nth-child(3),
            .history-table td:nth-child(3) {
                width: 40%;
            }

            .history-table th:nth-child(4),
            .history-table td:nth-child(4) {
                width: 20%;
            }

            .status-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.pending {
                background: #fef3c7;
                color: #92400e;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .points-positive {
                color: #10b981;
                font-weight: 600;
            }

            .points-negative {
                color: #ef4444;
                font-weight: 600;
            }

            .pagination-container {
                display: flex;
                justify-content: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .pagination {
                display: flex;
                gap: 8px;
                list-style: none;
            }

            .pagination li a,
            .pagination li span {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 8px;
                text-decoration: none;
                color: #374151;
                font-size: 0.8rem;
            }

            .pagination li.active span {
                background: #7c3aed;
                color: white;
            }

            .pagination li.disabled span {
                color: #9ca3af;
                cursor: not-allowed;
            }

            .points-stats-header {
                margin-bottom: 25px;
            }

            .points-chart-container {
                background: white;
                border-radius: 16px;
                padding: 20px;
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
                max-width: 450px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
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
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-confirm {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-confirm:hover:not(:disabled) {
                background: #6d28d9;
            }

            .btn-confirm:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .reward-preview {
                text-align: center;
                margin-bottom: 20px;
            }

            .preview-icon {
                width: 70px;
                height: 70px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px;
            }

            .preview-icon i {
                font-size: 2rem;
            }

            .reward-preview h4 {
                font-size: 1.1rem;
                margin-bottom: 8px;
            }

            .reward-preview p {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 10px;
            }

            .preview-points {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #fef3c7;
                padding: 6px 12px;
                border-radius: 30px;
                font-size: 0.8rem;
                color: #d97706;
            }

            .confirm-message {
                text-align: center;
                border-top: 1px solid #e5e7eb;
                padding-top: 20px;
                margin-top: 10px;
            }

            .warning-text {
                font-size: 0.75rem;
                color: #ef4444;
                margin-top: 8px;
            }

            .success-modal {
                text-align: center;
            }

            .success-icon {
                width: 70px;
                height: 70px;
                background: #10b98120;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
            }

            .success-icon i {
                font-size: 2.5rem;
                color: #10b981;
            }

            .text-center {
                text-align: center;
            }

            .btn-success-close {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 30px;
                border-radius: 40px;
                cursor: pointer;
                width: 100%;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            @media (max-width: 992px) {
                .stats-grid-milestones {
                    grid-template-columns: repeat(2, 1fr);
                }

                .rewards-grid {
                    grid-template-columns: 1fr;
                }

                .credit-grid {
                    grid-template-columns: 1fr;
                }

                .free-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .rewards-container {
                    padding: 15px;
                }

                .points-card-content {
                    flex-direction: column;
                    text-align: center;
                }

                .stats-grid-milestones {
                    grid-template-columns: 1fr;
                }

                .rewards-tabs {
                    flex-wrap: wrap;
                }

                .tab-btn {
                    padding: 8px 16px;
                    font-size: 0.8rem;
                }

                .donate-card-content {
                    flex-direction: column;
                    text-align: center;
                }

                .section-header {
                    flex-wrap: wrap;
                }

                .history-table {
                    font-size: 0.7rem;
                }

                .history-table th,
                .history-table td {
                    padding: 8px;
                }

                .activity-item {
                    flex-wrap: wrap;
                }
            }

            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .section-header {
                flex-direction: row;
            }

            body.rtl .reward-points {
                flex-direction: row;
            }

            body.rtl .activity-item {
                flex-direction: row;
            }

            body.rtl .btn-redeem i {
                margin-left: 4px;
                margin-right: 0;
            }

            body.rtl .donate-card-content {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .donate-card-content {
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Helper function to get localized name from JSON
            function getLocalizedName(nameData) {
                if (!nameData) return '';
                if (typeof nameData === 'string') {
                    try {
                        const parsed = JSON.parse(nameData);
                        const locale = document.documentElement.lang === 'ar' ? 'ar' : 'en';
                        return parsed[locale] || parsed.en || nameData;
                    } catch (e) {
                        return nameData;
                    }
                }
                return nameData;
            }

            // Tab Switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.dataset.tab;

                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');

                    if (tabId === 'history') {
                        loadRedemptionHistory(1);
                    } else if (tabId === 'points-history') {
                        loadPointsHistory(1);
                        loadPointsChart();
                    }
                });
            });

            // Redeem Button Handlers
            document.querySelectorAll('.btn-redeem, .btn-donate-full').forEach(btn => {
                btn.addEventListener('click', function () {
                    const rewardId = this.dataset.rewardId;
                    const points = this.dataset.points;
                    const rewardName = this.dataset.name;
                    const rewardDesc = this.dataset.desc;
                    const rewardIcon = this.dataset.icon;
                    const rewardColor = this.dataset.color;
                    let rewardType = 'credit';

                    if (this.classList.contains('donate-btn') || this.classList.contains('btn-donate-full')) {
                        rewardType = 'donate';
                    } else if (this.classList.contains('free-btn')) {
                        rewardType = 'free_session';
                    }

                    document.getElementById('redeemRewardId').value = rewardId;
                    document.getElementById('redeemRewardType').value = rewardType;
                    document.getElementById('previewName').innerText = rewardName;
                    document.getElementById('previewDescription').innerText = rewardDesc;
                    document.getElementById('previewPoints').innerText = Number(points).toLocaleString() + ' {{ __("Points") }}';
                    document.getElementById('previewIcon').innerHTML = `<i class="fas ${rewardIcon}"></i>`;
                    document.getElementById('previewIcon').style.background = `${rewardColor}20`;
                    document.getElementById('previewIcon').style.color = rewardColor;

                    document.getElementById('modalTitle').innerText = rewardType === 'donate' ? '{{ __("Donate Points") }}' : '{{ __("Redeem Reward") }}';
                    document.getElementById('redeemModal').classList.add('active');
                });
            });

            // Confirm Redemption
            document.getElementById('confirmRedeemBtn')?.addEventListener('click', async () => {
                const rewardId = document.getElementById('redeemRewardId').value;
                const confirmBtn = document.getElementById('confirmRedeemBtn');
                const btnText = confirmBtn.querySelector('.btn-text');
                const btnSpinner = confirmBtn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                confirmBtn.disabled = true;

                try {
                    const response = await fetch('{{ route("patient.rewards.redeem") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ reward_id: rewardId })
                    });

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('redeemModal').classList.remove('active');
                        document.getElementById('successMessage').innerText = data.message;
                        document.getElementById('successModal').classList.add('active');

                        if (data.redemption && data.redemption.remaining_points !== undefined) {
                            document.getElementById('totalPoints').innerText = Number(data.redemption.remaining_points).toLocaleString();
                        }

                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    await Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    confirmBtn.disabled = false;
                }
            });

            // Load Redemption History
            async function loadRedemptionHistory(page = 1) {
                const loader = document.getElementById('historyLoader');
                const content = document.getElementById('historyContent');
                const pagination = document.getElementById('historyPagination');

                loader.style.display = 'block';
                content.style.display = 'none';

                try {
                    const response = await fetch(`{{ route("patient.rewards.history") }}?page=${page}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.redemptions && data.redemptions.length > 0) {
                        let html = `<table class="history-table">
                            <thead>
                                <tr>
                                    <th>{{ __("Reward") }}</th>
                                    <th>{{ __("Points Spent") }}</th>
                                    <th>{{ __("Status") }}</th>
                                    <th>{{ __("Date") }}</th>
                                </thead>
                            <tbody>`;

                        data.redemptions.forEach(redemption => {
                            html += `<tr>
                                <td>${redemption.reward_name}</td>
                                <td class="points-negative">${redemption.points_spent_formatted}</td>
                                <td><span class="status-badge ${redemption.status}">${redemption.status_text}</span></td>
                                <td>${redemption.redeemed_at}</td>
                            </tr>`;
                        });

                        html += `</tbody></table>`;
                        content.innerHTML = html;
                        content.style.display = 'block';

                        if (data.pagination && data.pagination.last_page > 1) {
                            let paginationHtml = '<ul class="pagination">';
                            for (let i = 1; i <= data.pagination.last_page; i++) {
                                paginationHtml += `<li class="${i === data.pagination.current_page ? 'active' : ''}">
                                    <a href="#" onclick="loadRedemptionHistory(${i}); return false;">${i}</a>
                                </li>`;
                            }
                            paginationHtml += '</ul>';
                            pagination.innerHTML = paginationHtml;
                        } else {
                            pagination.innerHTML = '';
                        }
                    } else {
                        content.innerHTML = '<div class="empty-state"><i class="fas fa-gift"></i><p>{{ __("No redemptions yet. Start redeeming your points!") }}</p></div>';
                        content.style.display = 'block';
                        pagination.innerHTML = '';
                    }
                } catch (error) {
                    content.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading history. Please try again.") }}</p></div>';
                    content.style.display = 'block';
                } finally {
                    loader.style.display = 'none';
                }
            }

            // Load Points History
            async function loadPointsHistory(page = 1) {
                const loader = document.getElementById('pointsLoader');
                const content = document.getElementById('pointsHistoryContent');
                const pagination = document.getElementById('pointsPagination');

                loader.style.display = 'block';
                content.style.display = 'none';

                try {
                    const response = await fetch(`{{ route("patient.rewards.points-history") }}?page=${page}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.transactions && data.transactions.length > 0) {
                        let html = `<table class="history-table">
                            <thead>
                                <tr>
                                    <th>{{ __("Source") }}</th>
                                    <th>{{ __("Points") }}</th>
                                    <th>{{ __("Description") }}</th>
                                    <th>{{ __("Date") }}</th>
                                </thead>
                            <tbody>`;

                        data.transactions.forEach(transaction => {
                            html += `<tr>
                                <td><i class="fas ${transaction.icon}" style="color: ${transaction.color}"></i> ${transaction.source_name}</td>
                                <td class="${transaction.points_class}">${transaction.points_formatted}</td>
                                <td>${transaction.description || '—'}</td>
                                <td>${transaction.created_at_formatted}</td>
                            </tr>`;
                        });

                        html += `</tbody></table>`;
                        content.innerHTML = html;
                        content.style.display = 'block';

                        if (data.pagination && data.pagination.last_page > 1) {
                            let paginationHtml = '<ul class="pagination">';
                            for (let i = 1; i <= data.pagination.last_page; i++) {
                                paginationHtml += `<li class="${i === data.pagination.current_page ? 'active' : ''}">
                                    <a href="#" onclick="loadPointsHistory(${i}); return false;">${i}</a>
                                </li>`;
                            }
                            paginationHtml += '</ul>';
                            pagination.innerHTML = paginationHtml;
                        } else {
                            pagination.innerHTML = '';
                        }
                    } else {
                        content.innerHTML = '<div class="empty-state"><i class="fas fa-coins"></i><p>{{ __("No points history yet. Complete activities to earn points!") }}</p></div>';
                        content.style.display = 'block';
                        pagination.innerHTML = '';
                    }
                } catch (error) {
                    content.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading points history. Please try again.") }}</p></div>';
                    content.style.display = 'block';
                } finally {
                    loader.style.display = 'none';
                }
            }

            // Load Points Chart
            async function loadPointsChart() {
                const ctx = document.getElementById('pointsChart');
                if (!ctx) return;

                try {
                    const response = await fetch('{{ route("patient.rewards.points-history") }}?per_page=30');
                    const data = await response.json();

                    if (data.success && data.transactions) {
                        const grouped = {};
                        data.transactions.forEach(t => {
                            const date = t.created_at_formatted;
                            if (!grouped[date]) {
                                grouped[date] = { earned: 0, redeemed: 0 };
                            }
                            if (t.points > 0) {
                                grouped[date].earned += t.points;
                            } else {
                                grouped[date].redeemed += Math.abs(t.points);
                            }
                        });

                        const labels = Object.keys(grouped).reverse();
                        const earnedData = labels.map(l => grouped[l].earned);
                        const redeemedData = labels.map(l => grouped[l].redeemed);

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: '{{ __("Points Earned") }}',
                                        data: earnedData,
                                        backgroundColor: '#10b981',
                                        borderRadius: 8,
                                        barPercentage: 0.6
                                    },
                                    {
                                        label: '{{ __("Points Redeemed") }}',
                                        data: redeemedData,
                                        backgroundColor: '#ef4444',
                                        borderRadius: 8,
                                        barPercentage: 0.6
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: { display: true, text: '{{ __("Points") }}' }
                                    },
                                    x: {
                                        title: { display: true, text: '{{ __("Date") }}' }
                                    }
                                },
                                plugins: {
                                    legend: { position: 'top' },
                                    tooltip: { mode: 'index', intersect: false }
                                }
                            }
                        });
                    }
                } catch (error) {
                    console.error('Chart error:', error);
                }
            }

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .btn-cancel, .btn-success-close').forEach(btn => {
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

            if (document.getElementById('tab-points-history').classList.contains('active')) {
                loadPointsChart();
            }
        </script>
    @endpush

@endsection