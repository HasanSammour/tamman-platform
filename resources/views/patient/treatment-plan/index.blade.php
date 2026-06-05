{{-- resources/views/patient/treatment-plan/index.blade.php --}}
@extends('layouts.app')

@section('title', __('My Treatment Plan') . ' - ' . __('Tamman'))

@section('page-title', __('My Treatment Plan'))

@section('content')
    <div class="treatment-plan-container">
        <!-- Encouraging Header Section -->
        <div class="encouraging-header animate-slide-down">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="header-text">
                    <h2>{{ __('Your Journey to Healing') }}</h2>
                    <p>{{ __('Every task you complete brings you one step closer to feeling better. You\'ve got this!') }}
                    </p>
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-tasks"></i>
                        <span>{{ $stats['completed_tasks'] }}/{{ $stats['total_tasks'] }}</span>
                        <small>{{ __('Tasks Done') }}</small>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-star"></i>
                        <span>{{ $stats['total_points_earned'] }}</span>
                        <small>{{ __('Points Earned') }}</small>
                    </div>
                </div>
            </div>
            <div class="progress-motivation">
                <div class="motivation-text">
                    <i class="fas fa-chart-line"></i>
                    <span>{{ __('Your overall progress') }}</span>
                    <strong
                        id="overallProgressPercent">{{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) : 0 }}%</strong>
                </div>
                <div class="overall-progress-bar">
                    <div class="overall-progress-fill"
                        style="width: {{ $stats['total_tasks'] > 0 ? ($stats['completed_tasks'] / $stats['total_tasks']) * 100 : 0 }}%">
                    </div>
                </div>
                <div class="motivation-quote">
                    <i class="fas fa-quote-right"></i>
                    <span>{{ __('Small steps every day lead to big changes') }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['active_plans']) }}</h3>
                    <p>{{ __('Active Plans') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['completed_plans']) }}</h3>
                    <p>{{ __('Completed Plans') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['completed_tasks']) }}/{{ number_format($stats['total_tasks']) }}</h3>
                    <p>{{ __('Tasks Completed') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_points_earned']) }}</h3>
                    <p>{{ __('Points Earned') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="plan-tabs">
            <button class="tab-btn active" data-tab="active">
                <i class="fas fa-play-circle"></i> {{ __('Active Plans') }}
                @if($stats['active_plans'] > 0)
                    <span class="tab-badge">{{ $stats['active_plans'] }}</span>
                @endif
            </button>
            <button class="tab-btn" data-tab="completed">
                <i class="fas fa-check-circle"></i> {{ __('Completed Plans') }}
            </button>
            <a href="{{ route('patient.treatment-plan.history') }}" class="history-btn">
                <i class="fas fa-history"></i> {{ __('View History') }}
            </a>
        </div>

        <!-- Active Plans Tab -->
        <div class="tab-content active" id="tab-active">
            @if($activePlans->count() > 0)
                <div class="plans-grid">
                    @foreach($activePlans as $plan)
                        <div class="plan-card animate-scale-in" data-plan-id="{{ $plan->id }}">
                            <div class="plan-card-header">
                                <div class="plan-specialist">
                                    <div class="specialist-avatar">
                                        @php
                                            $specialistImage = $plan->specialist->getProfileImageUrl();
                                            $specialistInitial = mb_substr($plan->specialist->name, 0, 1, 'UTF-8');
                                        @endphp
                                        @if($specialistImage)
                                            <img src="{{ $specialistImage }}" alt="{{ $plan->specialist->name }}">
                                        @else
                                            <div class="avatar-placeholder">{{ $specialistInitial }}</div>
                                        @endif
                                    </div>
                                    <div class="specialist-info">
                                        <h4>{{ $plan->specialist->name }}</h4>
                                        <p>{{ $plan->specialist->specialistProfile->specialization ?? __('Psychologist') }}</p>
                                    </div>
                                </div>
                                <div class="plan-status active">
                                    <i class="fas fa-spinner fa-pulse"></i> {{ __('In Progress') }}
                                </div>
                            </div>
                            <div class="plan-card-body">
                                <h3 class="plan-title">{{ $plan->title }}</h3>
                                <p class="plan-description">
                                    {{ Str::limit($plan->description ?? __('No description provided'), 100) }}</p>
                                <div class="plan-progress">
                                    <div class="progress-header">
                                        <span>{{ __('Progress') }}</span>
                                        <span class="progress-percentage">{{ $plan->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $plan->progress_percentage }}%"></div>
                                    </div>
                                    <div class="progress-stats">
                                        <span><i class="fas fa-check-circle"></i>
                                            {{ $plan->completed_tasks }}/{{ $plan->total_tasks }} {{ __('tasks') }}</span>
                                        <span><i class="fas fa-star"></i> +{{ $plan->tasks->sum('points_reward') }}
                                            {{ __('points possible') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-card-footer">
                                <a href="{{ route('patient.treatment-plan.show', $plan->id) }}" class="btn-view-plan">
                                    <i class="fas fa-eye"></i> {{ __('View Plan') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>{{ __('No Active Treatment Plans') }}</h3>
                    <p>{{ __('Your specialist will create a treatment plan for you. Check back soon!') }}</p>
                </div>
            @endif
        </div>

        <!-- Completed Plans Tab -->
        <div class="tab-content" id="tab-completed">
            @if($completedPlans->count() > 0)
                <div class="completed-plans-grid">
                    @foreach($completedPlans as $plan)
                        <div class="completed-plan-card animate-scale-in">
                            <div class="completed-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="completed-info">
                                <h4>{{ $plan->title }}</h4>
                                <p>{{ __('Completed on') }}
                                    {{ Carbon\Carbon::parse($plan->end_date ?? $plan->updated_at)->translatedFormat('M d, Y') }}</p>
                                <div class="completed-stats">
                                    <span><i class="fas fa-check-circle"></i>
                                        {{ $plan->tasks->where('is_completed', true)->count() }}/{{ $plan->tasks->count() }}
                                        {{ __('tasks') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('patient.treatment-plan.show', $plan->id) }}" class="btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if($stats['completed_plans'] > 5)
                    <div class="view-all-container">
                        <a href="{{ route('patient.treatment-plan.history') }}" class="btn-view-all">
                            <i class="fas fa-history"></i> {{ __('View All Completed Plans') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-check-circle"></i>
                    <h3>{{ __('No Completed Plans Yet') }}</h3>
                    <p>{{ __('Complete your active treatment plans to see them here!') }}</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .treatment-plan-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .encouraging-header {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 24px;
                padding: 25px 30px;
                margin-bottom: 30px;
                color: white;
            }

            .header-content {
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
                margin-bottom: 20px;
            }

            .header-icon i {
                font-size: 2.5rem;
                color: #fbbf24;
            }

            .header-text {
                flex: 1;
            }

            .header-text h2 {
                font-size: 1.3rem;
                margin-bottom: 5px;
                color: white;
            }

            .header-text p {
                color: rgba(255, 255, 255, 0.8);
                margin: 0;
                font-size: 0.85rem;
            }

            .header-stats {
                display: flex;
                gap: 15px;
            }

            .stat-badge {
                background: rgba(255, 255, 255, 0.15);
                border-radius: 16px;
                padding: 10px 18px;
                text-align: center;
            }

            .stat-badge i {
                font-size: 1.2rem;
                color: #fbbf24;
                display: block;
                margin-bottom: 5px;
            }

            .stat-badge span {
                font-size: 1.2rem;
                font-weight: 700;
                display: block;
            }

            .stat-badge small {
                font-size: 0.65rem;
                opacity: 0.8;
            }

            .progress-motivation {
                display: flex;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
                padding-top: 15px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .motivation-text {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.8rem;
            }

            .motivation-text strong {
                color: #fbbf24;
                font-size: 0.9rem;
                animation: pulse 1s ease infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1);
                }

                50% {
                    opacity: 0.8;
                    transform: scale(1.05);
                }
            }

            .overall-progress-bar {
                flex: 1;
                height: 8px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 4px;
                overflow: hidden;
            }

            .overall-progress-fill {
                height: 100%;
                background: #fbbf24;
                border-radius: 4px;
                transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .overall-progress-fill::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                animation: shimmer 1.5s infinite;
            }

            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }

            .motivation-quote {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.75rem;
                opacity: 0.9;
            }

            .stats-grid {
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
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon i {
                font-size: 1.5rem;
                color: #7c3aed;
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

            .plan-tabs {
                display: flex;
                gap: 12px;
                margin-bottom: 25px;
                border-bottom: 1px solid #e5e7eb;
                align-items: center;
            }

            .tab-btn {
                background: none;
                border: none;
                padding: 12px 24px;
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

            .history-btn {
                margin-left: auto;
                background: #f3f4f6;
                color: #374151;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .history-btn:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .tab-badge {
                display: inline-block;
                background: #7c3aed;
                color: white;
                font-size: 0.7rem;
                padding: 2px 8px;
                border-radius: 20px;
                margin-left: 8px;
            }

            .tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
            }

            .tab-content.active {
                display: block;
            }

            .plans-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .plan-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .plan-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
            }

            .plan-card-header {
                padding: 20px;
                background: #f9fafb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                border-bottom: 1px solid #e5e7eb;
            }

            .plan-specialist {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .specialist-avatar img,
            .avatar-placeholder {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1rem;
                font-weight: 600;
            }

            .specialist-info h4 {
                font-size: 0.9rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .specialist-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .plan-status {
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 600;
            }

            .plan-status.active {
                background: #ede9fe;
                color: #7c3aed;
            }

            .plan-card-body {
                padding: 20px;
            }

            .plan-title {
                font-size: 1.1rem;
                font-weight: 700;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .plan-description {
                font-size: 0.8rem;
                color: #6b7280;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            .plan-progress {
                margin-top: 15px;
            }

            .progress-header {
                display: flex;
                justify-content: space-between;
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .progress-percentage {
                font-weight: 600;
                color: #7c3aed;
            }

            .progress-bar {
                height: 8px;
                background: #e5e7eb;
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
                border-radius: 4px;
                transition: width 0.5s ease;
            }

            .progress-stats {
                display: flex;
                justify-content: space-between;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .progress-stats i {
                margin-right: 4px;
            }

            .plan-card-footer {
                padding: 15px 20px;
                background: #f9fafb;
                display: flex;
                gap: 12px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-view-plan {
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                flex: 1;
                justify-content: center;
                border: none;
            }

            .btn-view-plan:hover {
                background: linear-gradient(135deg, #6d28d9, #5b21b6);
                transform: translateY(-2px);
                color: white;
            }

            .completed-plans-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .completed-plan-card {
                background: #f9fafb;
                border-radius: 16px;
                padding: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
                transition: all 0.3s ease;
            }

            .completed-plan-card:hover {
                transform: translateY(-2px);
                background: #f3f4f6;
            }

            .completed-icon i {
                font-size: 2rem;
                color: #10b981;
            }

            .completed-info {
                flex: 1;
            }

            .completed-info h4 {
                font-size: 0.9rem;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .completed-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .completed-stats {
                display: flex;
                gap: 15px;
                font-size: 0.65rem;
                color: #6b7280;
            }

            .completed-stats i {
                color: #10b981;
                margin-right: 3px;
            }

            .btn-view {
                padding: 8px 12px;
                background: white;
                border-radius: 10px;
                color: #7c3aed;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-view:hover {
                background: #ede9fe;
                transform: scale(1.05);
            }

            .view-all-container {
                text-align: center;
                margin-top: 20px;
            }

            .btn-view-all {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f3f4f6;
                color: #7c3aed;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-view-all:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: #f9fafb;
                border-radius: 24px;
            }

            .empty-state i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-state h3 {
                font-size: 1.1rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .empty-state p {
                color: #6b7280;
                font-size: 0.85rem;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
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

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
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

            .animate-slide-down {
                animation: slideDown 0.5s ease forwards;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            .animate-scale-in {
                animation: scaleIn 0.3s ease forwards;
            }

            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .plans-grid {
                    grid-template-columns: 1fr;
                }

                .completed-plans-grid {
                    grid-template-columns: 1fr;
                }

                .plan-tabs {
                    flex-wrap: wrap;
                }

                .history-btn {
                    margin-left: 0;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .header-content {
                    flex-direction: column;
                    text-align: center;
                }

                .header-stats {
                    justify-content: center;
                }

                .progress-motivation {
                    flex-direction: column;
                    text-align: center;
                }

                .overall-progress-bar {
                    width: 100%;
                }
            }

            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .btn-view-plan i {
                margin-left: 6px;
                margin-right: 0;
            }

            body.rtl .history-btn i {
                margin-left: 6px;
                margin-right: 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.dataset.tab;

                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });
        </script>
    @endpush

@endsection