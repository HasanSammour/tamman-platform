{{-- resources/views/specialist/treatment-plans/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Treatment Plans') . ' - ' . __('Tamman'))

@section('page-title', __('Treatment Plans'))

@section('content')
    <div class="treatment-plans-container">

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>{{ __('Total Plans') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon green">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['active']) }}</h3>
                    <p>{{ __('Active Plans') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon blue">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['completed']) }}</h3>
                    <p>{{ __('Completed Plans') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon orange">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['active'] > 0 ? round(($stats['completed'] / max($stats['active'] + $stats['completed'], 1)) * 100) : 0 }}%
                    </h3>
                    <p>{{ __('Success Rate') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="actions-bar">
            <a href="{{ route('specialist.treatment-plans.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> {{ __('Create New Plan') }}
            </a>
            <div class="filters">
                <div class="filter-group">
                    <i class="fas fa-filter"></i>
                    <select id="statusFilter">
                        <option value="all">{{ __('All Status') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="filter-group">
                    <i class="fas fa-user"></i>
                    <select id="patientFilter">
                        <option value="all">{{ __('All Patients') }}</option>
                    </select>
                </div>
                <div class="search-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="{{ __('Search plans...') }}">
                </div>
                <button class="btn-reset" id="resetFilters">
                    <i class="fas fa-undo-alt"></i> {{ __('Reset') }}
                </button>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-card desktop-view">
            <div class="table-responsive-wrapper">
                <div class="table-responsive">
                    <table class="plans-table" id="plansTable">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">{{ __('ID') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="title" class="sortable">{{ __('Plan Title') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="patient_name">{{ __('Patient') }}</th>
                                <th data-sort="status">{{ __('Status') }}</th>
                                <th>{{ __('Progress') }}</th>
                                <th data-sort="tasks_count">{{ __('Tasks') }}</th>
                                <th data-sort="start_date">{{ __('Start Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="plansTableBody">
                            <tr class="loading-row">
                                <td colspan="8" class="loading-cell">
                                    <div class="loading-container">
                                        <div class="loading-spinner"></div>
                                        <p>{{ __('Loading treatment plans...') }}</p>
                                    </div>
                                
    
                                </tr>
                            </table>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile Cards View -->
        <div id="plansCardsContainer" class="plans-cards-container mobile-view">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>{{ __('Loading treatment plans...') }}</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="table-footer">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- Recent Plans Section -->
        <div class="recent-plans-section">
            <div class="section-header">
                <h3><i class="fas fa-history"></i> {{ __('Recent Plans') }}</h3>
                <a href="{{ route('specialist.treatment-plans.index') }}" class="view-all">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="recent-plans-grid">
                @forelse($recentPlans as $plan)
                    @php
                        $completedTasks = $plan->tasks->where('is_completed', true)->count();
                        $totalTasks = $plan->tasks->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                        if ($progress >= 75) $progressColor = '#10b981';
                        elseif ($progress >= 50) $progressColor = '#3b82f6';
                        elseif ($progress >= 25) $progressColor = '#f59e0b';
                        else $progressColor = '#ef4444';

                        $startDateFormatted = $plan->start_date ? \Carbon\Carbon::parse($plan->start_date)->translatedFormat('M d, Y') : '—';
                    @endphp
                    <div class="recent-plan-card">
                        <div class="plan-header">
                            <h4>{{ Str::limit($plan->title, 40) }}</h4>
                            <span class="status-badge status-{{ $plan->status }}">{{ __(ucfirst($plan->status)) }}</span>
                        </div>
                        <div class="plan-patient">
                            <i class="fas fa-user-circle"></i> {{ $plan->patient->name }}
                        </div>
                        <div class="plan-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress }}%; background: {{ $progressColor }};">
                                </div>
                            </div>
                            <span class="progress-text" style="color: {{ $progressColor }};">{{ $progress }}%</span>
                        </div>
                        <div class="plan-footer-recent">
                            <span class="tasks-count"><i class="fas fa-tasks"></i>
                                {{ $completedTasks }}/{{ $totalTasks }} {{ __('tasks') }}
                            </span>
                            <span class="start-date"><i class="fas fa-calendar-alt"></i> {{ $startDateFormatted }}</span>
                            <a href="{{ route('specialist.treatment-plans.show', $plan->id) }}" class="btn-view">
                                <i class="fas fa-eye"></i> {{ __('View') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <p>{{ __('No treatment plans created yet') }}</p>
                        <a href="{{ route('specialist.treatment-plans.create') }}"
                            class="btn-primary-sm">{{ __('Create First Plan') }}</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .treatment-plans-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                animation: fadeInUp 0.5s ease forwards;
                opacity: 0;
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

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon.purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
            .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
            .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
            .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
            .stat-icon i { font-size: 1.4rem; color: white; }

            .stat-info h3 {
                font-size: 1.6rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }
            .stat-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            /* Actions Bar */
            .actions-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 25px;
            }

            .btn-primary {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 10px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
                color: white;
            }

            .filters {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
            }

            .filter-group, .search-group {
                position: relative;
            }

            .filter-group i, .search-group i {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.8rem;
                pointer-events: none;
            }

            .filter-group select, .search-group input {
                padding: 8px 12px 8px 35px;
                border: 1px solid #e5e7eb;
                border-radius: 30px;
                font-size: 0.8rem;
                background: white;
                cursor: pointer;
            }

            .search-group input {
                width: 200px;
            }

            .btn-reset {
                background: #f3f4f6;
                border: none;
                padding: 8px 16px;
                border-radius: 30px;
                font-size: 0.75rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-reset:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Desktop Table Styles */
            .desktop-view {
                display: block;
            }
            
            .mobile-view {
                display: none;
            }

            .table-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                margin-bottom: 25px;
            }

            .table-responsive-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive {
                min-width: 800px;
                width: 100%;
            }

            .plans-table {
                width: 100%;
                border-collapse: collapse;
            }

            .plans-table th,
            .plans-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
            }

            .plans-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .plans-table td {
                font-size: 0.8rem;
                color: #4b5563;
            }

            .loading-cell {
                text-align: center !important;
                vertical-align: middle !important;
            }

            .loading-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                text-align: center;
            }

            .sortable {
                cursor: pointer;
                user-select: none;
                transition: color 0.2s;
            }

            .sortable:hover {
                color: #7c3aed;
            }

            .sortable i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Progress Bar */
            .progress-bar-container {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .progress-bar {
                flex: 1;
                height: 6px;
                background: #e5e7eb;
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 3px;
                transition: width 0.3s ease;
            }

            .progress-text {
                font-size: 0.7rem;
                color: #6b7280;
                min-width: 35px;
            }

            /* Badges */
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .badge-active, .status-badge.status-active {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-completed, .status-badge.status-completed {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-cancelled, .status-badge.status-cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 8px;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
                background: #f3f4f6;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #6b7280;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .btn-icon:hover {
                background: #ede9fe;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            .btn-delete:hover {
                background: #fee2e2;
                color: #dc2626;
            }

            /* Mobile Cards Styles */
            .plans-cards-container {
                display: grid;
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 0;
                margin-bottom: 25px;
            }

            .plan-card {
                background: white;
                border-radius: 16px;
                border: 1px solid #eef2ff;
                overflow: hidden;
                transition: all 0.3s ease;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .plan-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                border-color: #c4b5fd;
            }

            .plan-card-header {
                background: linear-gradient(135deg, #f8fafc, #f1f5f9);
                padding: 14px 16px;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
            }

            .plan-card-id {
                font-size: 0.7rem;
                color: #7c3aed;
                font-weight: 600;
                background: #ede9fe;
                padding: 3px 10px;
                border-radius: 20px;
            }

            .plan-card-status {
                font-size: 0.65rem;
                padding: 3px 10px;
                border-radius: 20px;
                font-weight: 500;
            }

            .plan-card-status.active {
                background: #d1fae5;
                color: #065f46;
            }

            .plan-card-status.completed {
                background: #dbeafe;
                color: #1e40af;
            }

            .plan-card-status.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .plan-card-body {
                padding: 16px;
            }

            .plan-card-title {
                font-weight: 700;
                font-size: 1rem;
                color: #1f2937;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .plan-card-title i {
                color: #7c3aed;
            }

            .plan-card-patient {
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .plan-card-patient i {
                color: #7c3aed;
            }

            .plan-card-progress {
                margin: 14px 0;
            }

            .plan-card-progress-header {
                display: flex;
                justify-content: space-between;
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 6px;
            }

            .plan-card-progress-bar {
                height: 8px;
                background: #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
            }

            .plan-card-progress-fill {
                height: 100%;
                border-radius: 10px;
                transition: width 0.3s ease;
            }

            .plan-card-stats {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid #f0f0f0;
            }

            .plan-card-stat {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .plan-card-stat i {
                color: #7c3aed;
            }

            .plan-card-footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 12px 16px;
                background: #fafcff;
                border-top: 1px solid #eef2ff;
            }

            .plan-card-btn {
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                border: none;
            }

            .plan-card-btn.view {
                background: #ede9fe;
                color: #7c3aed;
            }

            .plan-card-btn.view:hover {
                background: #7c3aed;
                color: white;
                transform: translateY(-2px);
            }

            .plan-card-btn.edit {
                background: #f3f4f6;
                color: #6b7280;
            }

            .plan-card-btn.edit:hover {
                background: #e5e7eb;
                color: #374151;
                transform: translateY(-2px);
            }

            .plan-card-btn.delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .plan-card-btn.delete:hover {
                background: #dc2626;
                color: white;
                transform: translateY(-2px);
            }

            /* Pagination */
            .table-footer {
                background: white;
                border-radius: 20px;
                padding: 16px 20px;
                margin-bottom: 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .pagination-info {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .pagination-controls {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }

            .page-btn {
                min-width: 36px;
                height: 36px;
                padding: 0 10px;
                border: 1px solid #e5e7eb;
                background: white;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.8rem;
            }

            .page-btn:hover:not(:disabled) {
                background: #ede9fe;
                border-color: #7c3aed;
                color: #7c3aed;
            }

            .page-btn.active {
                background: #7c3aed;
                border-color: #7c3aed;
                color: white;
            }

            .page-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            /* Recent Plans Section */
            .recent-plans-section {
                background: white;
                border-radius: 20px;
                padding: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .section-header h3 {
                margin: 0;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
            }

            .view-all {
                font-size: 0.75rem;
                color: #7c3aed;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .view-all:hover {
                color: #6d28d9;
                transform: translateX(3px);
            }

            body.rtl .view-all:hover {
                transform: translateX(-3px);
            }

            .recent-plans-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 20px;
            }

            .recent-plan-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 18px;
                transition: all 0.3s ease;
                border: 1px solid #eef2ff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .recent-plan-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 24px rgba(124, 58, 237, 0.12);
                border-color: #c4b5fd;
            }

            .plan-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .plan-header h4 {
                margin: 0;
                font-size: 0.95rem;
                color: #1f2937;
                font-weight: 700;
                line-height: 1.3;
            }

            .status-badge {
                font-size: 0.65rem;
                padding: 4px 12px;
                border-radius: 20px;
                font-weight: 600;
                white-space: nowrap;
            }

            .plan-patient {
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 14px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .plan-patient i {
                color: #7c3aed;
                font-size: 0.75rem;
            }

            .plan-progress {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 14px;
            }

            .plan-progress .progress-bar {
                flex: 1;
                height: 6px;
                background: #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
            }

            .plan-progress .progress-fill {
                height: 100%;
                border-radius: 10px;
                transition: width 0.3s ease;
            }

            .plan-progress .progress-text {
                font-size: 0.7rem;
                font-weight: 600;
                min-width: 40px;
                text-align: right;
            }

            .plan-footer-recent {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                padding-top: 12px;
                border-top: 1px solid #f0f0f0;
            }

            .tasks-count, .start-date {
                font-size: 0.7rem;
                color: #6b7280;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .tasks-count i, .start-date i {
                color: #7c3aed;
            }

            .btn-view {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 5px 14px;
                border-radius: 20px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-weight: 500;
            }

            .btn-view:hover {
                transform: translateY(-2px);
                box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
                color: white;
            }

            /* Loading */
            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-state i {
                font-size: 4rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-state p {
                color: #6b7280;
                margin-bottom: 15px;
            }

            .btn-primary-sm {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.75rem;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .btn-primary-sm:hover {
                transform: translateY(-2px);
                color: white;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .recent-plans-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .treatment-plans-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .actions-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .filters {
                    justify-content: space-between;
                }

                .filter-group select,
                .search-group input {
                    width: auto;
                    min-width: 100px;
                    font-size: 0.7rem;
                }

                .search-group input {
                    width: 130px;
                }

                /* Hide desktop, show mobile */
                .desktop-view {
                    display: none !important;
                }
                
                .mobile-view {
                    display: grid !important;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                }

                .recent-plans-grid {
                    grid-template-columns: 1fr;
                }

                .pagination-controls {
                    gap: 3px;
                }
                
                .page-btn {
                    min-width: 30px;
                    height: 30px;
                    padding: 0 6px;
                    font-size: 0.7rem;
                }
                
                .pagination-info {
                    font-size: 0.7rem;
                }
            }

            @media (min-width: 769px) {
                .desktop-view {
                    display: block !important;
                }
                
                .mobile-view {
                    display: none !important;
                }
            }

            @media (max-width: 480px) {
                .filters {
                    flex-direction: column;
                    width: 100%;
                }
                
                .filter-group,
                .search-group {
                    width: 100%;
                }
                
                .filter-group select,
                .search-group input {
                    width: 100%;
                }
                
                .btn-reset {
                    width: 100%;
                }
                
                .page-btn {
                    min-width: 28px;
                    height: 28px;
                    padding: 0 4px;
                    font-size: 0.65rem;
                }

                .plan-footer-recent {
                    flex-direction: column;
                    align-items: stretch;
                }

                .btn-view {
                    text-align: center;
                    justify-content: center;
                }
            }

            /* RTL Support */
            body.rtl .plans-table th,
            body.rtl .plans-table td {
                text-align: right;
            }

            body.rtl .filter-group i,
            body.rtl .search-group i {
                left: auto;
                right: 12px;
            }

            body.rtl .filter-group select,
            body.rtl .search-group input {
                padding: 8px 35px 8px 12px;
            }

            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .plan-progress .progress-text {
                text-align: left;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let search = '', status = 'all', patientId = 'all';
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

            // Load patients for filter dropdown
            async function loadPatientsFilter() {
                try {
                    const response = await fetch('/specialist/treatment-plans/patients', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    if (data.success && data.patients) {
                        const patientFilter = document.getElementById('patientFilter');
                        data.patients.forEach(patient => {
                            const option = document.createElement('option');
                            option.value = patient.id;
                            option.textContent = patient.name;
                            patientFilter.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error loading patients:', error);
                }
            }

            function formatDate(dateString) {
                if (!dateString) return '—';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '—';
                return date.toLocaleDateString(isRTL ? 'ar' : 'en', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            const tableBody = document.getElementById('plansTableBody');
            const cardsContainer = document.getElementById('plansCardsContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const patientFilter = document.getElementById('patientFilter');
            const resetBtn = document.getElementById('resetFilters');

            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    const field = th.dataset.sort;
                    if (sortField === field) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortField = field;
                        sortDirection = 'asc';
                    }
                    document.querySelectorAll('.sortable i').forEach(icon => icon.className = 'fas fa-sort');
                    th.querySelector('i').className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    currentPage = 1;
                    loadPlans();
                });
            });

            if (searchInput) searchInput.addEventListener('input', () => { search = searchInput.value; currentPage = 1; loadPlans(); });
            if (statusFilter) statusFilter.addEventListener('change', () => { status = statusFilter.value; currentPage = 1; loadPlans(); });
            if (patientFilter) patientFilter.addEventListener('change', () => { patientId = patientFilter.value; currentPage = 1; loadPlans(); });
            if (resetBtn) resetBtn.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = 'all';
                if (patientFilter) patientFilter.value = 'all';
                search = ''; status = 'all'; patientId = 'all';
                currentPage = 1;
                loadPlans();
            });

            function getProgressColor(progress) {
                if (progress >= 75) return '#10b981';
                if (progress >= 50) return '#3b82f6';
                if (progress >= 25) return '#f59e0b';
                return '#ef4444';
            }

            function getStatusClass(status) {
                if (status === 'active') return 'active';
                if (status === 'completed') return 'completed';
                return 'cancelled';
            }

            function getStatusText(status) {
                if (status === 'active') return '{{ __("Active") }}';
                if (status === 'completed') return '{{ __("Completed") }}';
                return '{{ __("Cancelled") }}';
            }

            async function loadPlans() {
                if (tableBody) {
                    tableBody.innerHTML = `<tr class="loading-row"><td colspan="8" class="loading-cell"><div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading treatment plans...') }}</p></div></tr>`;
                }
                if (cardsContainer) {
                    cardsContainer.innerHTML = `<div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading treatment plans...') }}</p></div>`;
                }

                try {
                    const url = `/specialist/treatment-plans/data?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}&status=${status}&patient_id=${patientId}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();

                    if (data.success) {
                        renderTable(data.data || []);
                        renderCards(data.data || []);
                        renderPagination(data);
                    } else {
                        showError();
                    }
                } catch (error) {
                    console.error('Error loading plans:', error);
                    showError();
                }
            }

            function renderTable(plans) {
                if (!tableBody) return;
                
                if (!plans || plans.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="empty-cell"><div class="empty-state"><i class="fas fa-tasks"></i><p>{{ __('No treatment plans found') }}</p></div></tr>`;
                    return;
                }

                tableBody.innerHTML = plans.map(plan => {
                    const progressColor = getProgressColor(plan.progress);
                    const statusClass = getStatusClass(plan.status);
                    const statusText = getStatusText(plan.status);

                    return `<tr>
                        <td>#${plan.id}</td>
                        <td><strong>${escapeHtml(plan.title)}</strong></td>
                        <td>${escapeHtml(plan.patient_name)}</td>
                        <td><span class="badge badge-${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar"><div class="progress-fill" style="width: ${plan.progress}%; background: ${progressColor};"></div></div>
                                <span class="progress-text" style="color: ${progressColor};">${plan.progress}%</span>
                            </div>
                        </td>
                        <td>${plan.completed_tasks}/${plan.tasks_count}</td>
                        <td>${plan.start_date ? formatDate(plan.start_date) : '—'}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="/specialist/treatment-plans/${plan.id}" class="btn-icon" title="{{ __('View') }}"><i class="fas fa-eye"></i></a>
                                <a href="/specialist/treatment-plans/${plan.id}/edit" class="btn-icon" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                                <button class="btn-icon btn-delete" onclick="deletePlan(${plan.id}, '${escapeHtml(plan.title)}')" title="{{ __('Delete') }}"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </table>`;
                }).join('');
            }

            function renderCards(plans) {
                if (!cardsContainer) return;
                
                if (!plans || plans.length === 0) {
                    cardsContainer.innerHTML = `<div class="empty-state"><i class="fas fa-tasks"></i><p>{{ __('No treatment plans found') }}</p></div>`;
                    return;
                }

                cardsContainer.innerHTML = plans.map(plan => {
                    const progressColor = getProgressColor(plan.progress);
                    const statusClass = getStatusClass(plan.status);
                    const statusText = getStatusText(plan.status);

                    return `
                        <div class="plan-card" data-href="/specialist/treatment-plans/${plan.id}">
                            <div class="plan-card-header">
                                <span class="plan-card-id"><i class="fas fa-hashtag"></i> ${plan.id}</span>
                                <span class="plan-card-status ${statusClass}">${statusText}</span>
                            </div>
                            <div class="plan-card-body">
                                <div class="plan-card-title">
                                    <i class="fas fa-clinic-medical"></i>
                                    ${escapeHtml(plan.title)}
                                </div>
                                <div class="plan-card-patient">
                                    <i class="fas fa-user-circle"></i>
                                    ${escapeHtml(plan.patient_name)}
                                </div>
                                <div class="plan-card-progress">
                                    <div class="plan-card-progress-header">
                                        <span>{{ __('Progress') }}</span>
                                        <span style="color: ${progressColor};">${plan.progress}%</span>
                                    </div>
                                    <div class="plan-card-progress-bar">
                                        <div class="plan-card-progress-fill" style="width: ${plan.progress}%; background: ${progressColor};"></div>
                                    </div>
                                </div>
                                <div class="plan-card-stats">
                                    <div class="plan-card-stat">
                                        <i class="fas fa-tasks"></i>
                                        <span>${plan.completed_tasks}/${plan.tasks_count} {{ __('tasks') }}</span>
                                    </div>
                                    <div class="plan-card-stat">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>${plan.start_date ? formatDate(plan.start_date) : '{{ __("No start date") }}'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-card-footer">
                                <a href="/specialist/treatment-plans/${plan.id}" class="plan-card-btn view" onclick="event.stopPropagation()">
                                    <i class="fas fa-eye"></i> {{ __('View') }}
                                </a>
                                <a href="/specialist/treatment-plans/${plan.id}/edit" class="plan-card-btn edit" onclick="event.stopPropagation()">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                                <button class="plan-card-btn delete" onclick="event.stopPropagation(); deletePlan(${plan.id}, '${escapeHtml(plan.title)}')">
                                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                document.querySelectorAll('.plan-card').forEach(card => {
                    card.addEventListener('click', function(e) {
                        if (e.target.closest('.plan-card-footer')) return;
                        window.location.href = this.dataset.href;
                    });
                });
            }

            function renderPagination(data) {
                if (!paginationInfo || !paginationControls) return;
                
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                paginationInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('plans') }}`;

                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;

                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                }

                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;

                paginationControls.innerHTML = html;
            }

            function goToPage(page) { currentPage = page; loadPlans(); }
            
            function showError() { 
                const errorHtml = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading plans') }}</p><button class="btn-primary-sm" onclick="loadPlans()">{{ __('Retry') }}</button></div>`;
                if (tableBody) tableBody.innerHTML = `<tr><td colspan="8" class="empty-cell">${errorHtml}</td></tr>`;
                if (cardsContainer) cardsContainer.innerHTML = errorHtml;
            }
            
            function escapeHtml(str) { 
                if (!str) return ''; 
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); 
            }

            window.deletePlan = async (planId, planTitle) => {
                const result = await Swal.fire({
                    title: '{{ __("Delete Treatment Plan") }}',
                    html: `{{ __('Are you sure you want to delete the plan') }} "<strong>${planTitle}</strong>"?<br><span style="color:#dc2626; font-size:0.75rem;">{{ __('This action cannot be undone. All tasks will also be deleted.') }}</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, Delete") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                Swal.fire({
                    title: '{{ __("Processing...") }}',
                    text: '{{ __("Deleting treatment plan...") }}',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    const response = await fetch(`/specialist/treatment-plans/${planId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Deleted!") }}',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadPlans();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error!") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            };

            loadPatientsFilter();
            loadPlans();
        </script>
    @endpush
@endsection