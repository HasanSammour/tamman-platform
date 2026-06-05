{{-- resources/views/patient/treatment-plan/history.blade.php --}}
@extends('layouts.app')

@section('title', __('Completed Plans') . ' - ' . __('Tamman'))

@section('page-title', __('Completed Treatment Plans'))

@section('content')
    <div class="history-container">
        <!-- Encouraging Header -->
        <div class="encouraging-header animate-slide-down">
            <div class="header-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="header-text">
                <h2>{{ __('Your Achievements') }}</h2>
                <p>{{ __('Every completed plan is a milestone in your mental health journey. We\'re proud of you!') }}</p>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="stats-summary animate-fade-in-up">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-number">{{ $completedPlans->total() }}</div>
                <div class="stat-label">{{ __('Completed Plans') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-number">{{ $completedPlans->sum(function ($plan) {
        return $plan->tasks->count(); }) }}</div>
                <div class="stat-label">{{ __('Total Tasks') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-number">
                    {{ $completedPlans->sum(function ($plan) {
        return $plan->total_points_earned ?? 0; }) }}</div>
                <div class="stat-label">{{ __('Points Earned') }}</div>
            </div>
        </div>

        <!-- Plans List -->
        @if($completedPlans->count() > 0)
            <div class="plans-list">
                @foreach($completedPlans as $plan)
                    <div class="plan-history-card animate-fade-in-up">
                        <div class="plan-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="plan-info">
                            <h3>{{ $plan->title }}</h3>
                            <p class="plan-specialist">{{ __('by') }} {{ $plan->specialist->name }}</p>
                            <div class="plan-meta">
                                <span class="completed-date">
                                    <i class="fas fa-calendar-check"></i>
                                    {{ __('Completed on') }} {{ Carbon\Carbon::parse($plan->end_date)->translatedFormat('F d, Y') }}
                                </span>
                                <span class="tasks-count">
                                    <i class="fas fa-tasks"></i>
                                    {{ $plan->tasks->where('is_completed', true)->count() }}/{{ $plan->tasks->count() }}
                                    {{ __('tasks') }}
                                </span>
                                <span class="points-earned">
                                    <i class="fas fa-star"></i>
                                    +{{ $plan->total_points_earned ?? $plan->tasks->sum('points_reward') }} {{ __('points') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('patient.treatment-plan.show', $plan->id) }}" class="btn-view">
                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $completedPlans->links('vendor.pagination.tamman') }}
            </div>
        @else
            <div class="empty-state animate-fade-in">
                <i class="fas fa-trophy"></i>
                <h3>{{ __('No Completed Plans Yet') }}</h3>
                <p>{{ __('Complete your active treatment plans to see them here!') }}</p>
                <a href="{{ route('patient.treatment-plan') }}" class="btn-primary">{{ __('View Active Plans') }}</a>
            </div>
        @endif

        <!-- Back Button -->
        <div class="back-button-container">
            <a href="{{ route('patient.treatment-plan') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Active Plans') }}
            </a>
        </div>
    </div>

    @push('styles')
        <style>
            .history-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Encouraging Header */
            .encouraging-header {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                border-radius: 24px;
                padding: 30px;
                margin-bottom: 30px;
                text-align: center;
                color: white;
            }

            .header-icon i {
                font-size: 3rem;
                color: #fbbf24;
                margin-bottom: 10px;
            }

            .header-text h2 {
                font-size: 1.3rem;
                margin-bottom: 8px;
                color: white;
            }

            .header-text p {
                color: rgba(255, 255, 255, 0.9);
                margin: 0;
                font-size: 0.85rem;
            }

            /* Stats Summary */
            .stats-summary {
                display: flex;
                justify-content: space-around;
                gap: 20px;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }

            .stat-item {
                background: white;
                border-radius: 20px;
                padding: 20px;
                text-align: center;
                flex: 1;
                min-width: 120px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .stat-icon i {
                font-size: 1.5rem;
                color: #7c3aed;
                margin-bottom: 8px;
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
            }

            .stat-label {
                font-size: 0.7rem;
                color: #6b7280;
            }

            /* Plans List */
            .plans-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 30px;
            }

            .plan-history-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 20px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                flex-wrap: wrap;
            }

            .plan-history-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .plan-icon i {
                font-size: 2rem;
                color: #f59e0b;
            }

            .plan-info {
                flex: 1;
            }

            .plan-info h3 {
                font-size: 1rem;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .plan-specialist {
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .plan-meta {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .plan-meta span {
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .completed-date i {
                color: #10b981;
            }

            .tasks-count i {
                color: #7c3aed;
            }

            .points-earned i {
                color: #f59e0b;
            }

            .btn-view {
                background: #f3f4f6;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                color: #374151;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
            }

            .btn-view:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Pagination */
            .pagination-container {
                margin-bottom: 30px;
            }

            /* Back Button */
            .back-button-container {
                text-align: center;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f3f4f6;
                color: #374151;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: #f9fafb;
                border-radius: 24px;
                margin-bottom: 30px;
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
                margin-bottom: 20px;
            }

            /* Animations */
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

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .animate-slide-down {
                animation: slideDown 0.5s ease forwards;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            .animate-fade-in {
                animation: fadeIn 0.5s ease forwards;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .history-container {
                    padding: 15px;
                }

                .stats-summary {
                    flex-direction: column;
                }

                .plan-history-card {
                    flex-direction: column;
                    text-align: center;
                }

                .plan-meta {
                    justify-content: center;
                }

                .btn-view {
                    width: 100%;
                    justify-content: center;
                }
            }

            /* RTL Support */
            body.rtl .btn-back i,
            body.rtl .btn-view i,
            body.rtl .btn-primary i {
                transform: rotate(180deg);
            }

            body.rtl .plan-meta span {
                flex-direction: row;
            }
        </style>
    @endpush

@endsection