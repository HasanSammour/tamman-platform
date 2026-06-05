{{-- resources/views/specialist/treatment-plans/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Treatment Plan Details') . ' - ' . __('Tamman'))

@section('page-title', __('Treatment Plan Details'))

@section('content')
    <div class="plan-details-container">
        <!-- Back Button -->
        <div class="back-button-container">
            <a href="{{ route('specialist.treatment-plans.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Treatment Plans') }}
            </a>
        </div>

        <!-- Plan Header Card -->
        <div class="plan-header-card animate-slide-down">
            <div class="plan-header-content">
                <div class="plan-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="plan-title-section">
                    <h1>{{ $plan->title }}</h1>
                    <div class="plan-meta">
                        <span class="meta-item">
                            <i class="fas fa-user"></i> {{ __('Patient') }}: <strong>{{ $plan->patient->name }}</strong>
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt"></i> {{ __('Created') }}:
                            {{ $plan->created_at->translatedFormat('M d, Y') }}
                        </span>
                        @if($plan->start_date)
                            <span class="meta-item">
                                <i class="fas fa-play-circle"></i> {{ __('Start') }}:
                                {{ \Carbon\Carbon::parse($plan->start_date)->translatedFormat('M d, Y') }}
                            </span>
                        @endif
                        @if($plan->end_date)
                            <span class="meta-item">
                                <i class="fas fa-flag-checkered"></i> {{ __('End') }}:
                                {{ \Carbon\Carbon::parse($plan->end_date)->translatedFormat('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="plan-status-badge">
                    @if($plan->status == 'active')
                        <span class="status-badge active"><i class="fas fa-play-circle"></i> {{ __('Active') }}</span>
                    @elseif($plan->status == 'completed')
                        <span class="status-badge completed"><i class="fas fa-check-circle"></i> {{ __('Completed') }}</span>
                    @else
                        <span class="status-badge cancelled"><i class="fas fa-times-circle"></i> {{ __('Cancelled') }}</span>
                    @endif
                </div>
            </div>

            <!-- Progress Section -->
            <div class="plan-progress-section">
                <div class="progress-info">
                    <span class="progress-label">{{ __('Overall Progress') }}</span>
                    <span class="progress-percentage">{{ $progress }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ $progress }}%; background: {{ $progress >= 75 ? '#10b981' : ($progress >= 50 ? '#3b82f6' : ($progress >= 25 ? '#f59e0b' : '#ef4444')) }};">
                        </div>
                    </div>
                </div>
                <div class="progress-stats">
                    <span><i class="fas fa-tasks"></i> {{ $plan->tasks->count() }} {{ __('Total Tasks') }}</span>
                    <span><i class="fas fa-check-circle"></i> {{ $plan->tasks->where('is_completed', true)->count() }}
                        {{ __('Completed') }}</span>
                    <span><i class="fas fa-clock"></i> {{ $plan->tasks->where('is_completed', false)->count() }}
                        {{ __('Pending') }}</span>
                </div>
            </div>
        </div>

        <!-- Plan Description Card -->
        @if($plan->description)
            <div class="description-card animate-fade-in">
                <div class="description-header">
                    <i class="fas fa-align-left"></i>
                    <h3>{{ __('Plan Description') }}</h3>
                </div>
                <div class="description-content">
                    <p>{{ $plan->description }}</p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons-container animate-fade-in" style="animation-delay: 0.1s">
            <a href="{{ route('specialist.treatment-plans.edit', $plan->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> {{ __('Edit Plan') }}
            </a>
            <button class="btn-delete" onclick="deletePlan({{ $plan->id }}, '{{ addslashes($plan->title) }}')">
                <i class="fas fa-trash-alt"></i> {{ __('Delete Plan') }}
            </button>
        </div>

        <!-- Tasks Section -->
        <div class="tasks-section animate-fade-in" style="animation-delay: 0.2s">
            <div class="tasks-header">
                <h2><i class="fas fa-tasks"></i> {{ __('Treatment Tasks') }}</h2>
                <div class="tasks-header-info">
                    <span class="tasks-count">{{ $plan->tasks->count() }} {{ __('tasks') }}</span>
                </div>
            </div>

            <!-- Pending Tasks -->
            @if($pendingTasks->count() > 0)
                <div class="tasks-group">
                    <div class="tasks-group-header">
                        <i class="fas fa-clock"></i>
                        <h3>{{ __('Pending Tasks') }}</h3>
                        <span class="tasks-group-count">{{ $pendingTasks->count() }}</span>
                    </div>
                    <div class="tasks-list">
                        @foreach($pendingTasks as $task)
                            <div class="task-card pending" data-task-id="{{ $task->id }}">
                                <div class="task-status">
                                    <div class="status-indicator pending"></div>
                                </div>
                                <div class="task-content">
                                    <div class="task-title">
                                        <h4>{{ $task->title }}</h4>
                                        @if($task->is_overdue)
                                            <span class="overdue-badge"><i class="fas fa-exclamation-triangle"></i>
                                                {{ __('Overdue') }}</span>
                                        @endif
                                    </div>
                                    @if($task->description)
                                        <p class="task-description">{{ $task->description }}</p>
                                    @endif
                                    <div class="task-meta">
                                        @if($task->due_date)
                                            <span class="task-due-date {{ $task->is_overdue ? 'overdue' : '' }}">
                                                <i class="fas fa-calendar-alt"></i> {{ __('Due') }}:
                                                {{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('M d, Y') }}
                                            </span>
                                        @endif
                                        <span class="task-points">
                                            <i class="fas fa-star"></i> {{ $task->points_reward }} {{ __('points') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Completed Tasks -->
            @if($completedTasksList->count() > 0)
                <div class="tasks-group">
                    <div class="tasks-group-header">
                        <i class="fas fa-check-circle"></i>
                        <h3>{{ __('Completed Tasks') }}</h3>
                        <span class="tasks-group-count completed">{{ $completedTasksList->count() }}</span>
                    </div>
                    <div class="tasks-list">
                        @foreach($completedTasksList as $task)
                            <div class="task-card completed" data-task-id="{{ $task->id }}">
                                <div class="task-status">
                                    <div class="status-indicator completed"></div>
                                </div>
                                <div class="task-content">
                                    <div class="task-title">
                                        <h4>{{ $task->title }}</h4>
                                        <span class="completed-badge"><i class="fas fa-check-circle"></i>
                                            {{ __('Completed') }}</span>
                                    </div>
                                    @if($task->description)
                                        <p class="task-description">{{ $task->description }}</p>
                                    @endif
                                    <div class="task-meta">
                                        @if($task->due_date)
                                            <span class="task-due-date completed">
                                                <i class="fas fa-calendar-alt"></i> {{ __('Due') }}:
                                                {{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('M d, Y') }}
                                            </span>
                                        @endif
                                        <span class="task-points">
                                            <i class="fas fa-star"></i> +{{ $task->points_reward }} {{ __('points earned') }}
                                        </span>
                                        @if($task->completed_at)
                                            <span class="task-completed-date">
                                                <i class="fas fa-check-circle"></i> {{ __('Completed') }}:
                                                {{ \Carbon\Carbon::parse($task->completed_at)->translatedFormat('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- No Tasks Empty State -->
            @if($plan->tasks->count() == 0)
                <div class="empty-tasks-state">
                    <i class="fas fa-tasks"></i>
                    <p>{{ __('No tasks have been added to this treatment plan yet.') }}</p>
                    <a href="{{ route('specialist.treatment-plans.edit', $plan->id) }}" class="btn-add-tasks">
                        <i class="fas fa-plus-circle"></i> {{ __('Add Tasks') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Info Note for Specialist -->
        <div class="info-note animate-fade-in" style="animation-delay: 0.3s">
            <i class="fas fa-info-circle"></i>
            <span>{{ __('Note: Patients mark tasks as complete from their dashboard. When a task is completed, they earn the associated points automatically.') }}</span>
        </div>
    </div>

    @push('styles')
        <style>
            .plan-details-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
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

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .animate-slide-down {
                animation: slideDown 0.4s ease;
            }

            .animate-fade-in {
                animation: fadeIn 0.4s ease forwards;
                opacity: 0;
            }

            /* Back Button */
            .back-button-container {
                margin-bottom: 20px;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f3f4f6;
                padding: 8px 20px;
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

            /* Plan Header Card */
            .plan-header-card {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                margin-bottom: 25px;
            }

            .plan-header-content {
                padding: 25px 30px;
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .plan-icon {
                width: 60px;
                height: 60px;
                background: #7c3aed;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .plan-icon i {
                font-size: 1.8rem;
                color: white;
            }

            .plan-title-section {
                flex: 1;
            }

            .plan-title-section h1 {
                font-size: 1.4rem;
                margin: 0 0 8px;
                color: #1f2937;
            }

            .plan-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }

            .meta-item {
                font-size: 0.75rem;
                color: #6b7280;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .meta-item i {
                color: #7c3aed;
            }

            .meta-item strong {
                color: #1f2937;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .status-badge.active {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Progress Section */
            .plan-progress-section {
                padding: 20px 30px;
                border-top: 1px solid #e5e7eb;
            }

            .progress-info {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 10px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .progress-label {
                font-size: 0.8rem;
                font-weight: 500;
                color: #374151;
            }

            .progress-percentage {
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .progress-bar-container {
                margin-bottom: 15px;
            }

            .progress-bar {
                height: 8px;
                background: #e5e7eb;
                border-radius: 4px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 4px;
                transition: width 0.5s ease;
            }

            .progress-stats {
                display: flex;
                gap: 25px;
                flex-wrap: wrap;
            }

            .progress-stats span {
                font-size: 0.7rem;
                color: #6b7280;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .progress-stats i {
                color: #7c3aed;
            }

            /* Description Card */
            .description-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 20px;
                overflow: hidden;
            }

            .description-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .description-header i {
                font-size: 1.1rem;
                color: #7c3aed;
            }

            .description-header h3 {
                margin: 0;
                font-size: 0.9rem;
                color: #1f2937;
            }

            .description-content {
                padding: 20px;
            }

            .description-content p {
                margin: 0;
                font-size: 0.85rem;
                color: #4b5563;
                line-height: 1.6;
            }

            /* Action Buttons */
            .action-buttons-container {
                display: flex;
                gap: 15px;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }

            .btn-edit,
            .btn-delete {
                padding: 10px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                border: none;
            }

            .btn-edit {
                background: #7c3aed;
                color: white;
            }

            .btn-edit:hover {
                background: #6d28d9;
                color: white;
                transform: translateY(-2px);
            }

            .btn-delete {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-delete:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            /* Tasks Section */
            .tasks-section {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .tasks-header {
                padding: 20px 25px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .tasks-header h2 {
                margin: 0;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #1f2937;
            }

            .tasks-header h2 i {
                color: #7c3aed;
            }

            .tasks-header-info {
                display: flex;
                gap: 15px;
            }

            .tasks-count {
                font-size: 0.75rem;
                color: #6b7280;
                padding: 4px 10px;
                background: #f3f4f6;
                border-radius: 20px;
            }

            /* Tasks Group */
            .tasks-group {
                border-bottom: 1px solid #f0f0f0;
            }

            .tasks-group:last-child {
                border-bottom: none;
            }

            .tasks-group-header {
                padding: 15px 25px;
                background: #fafafa;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .tasks-group-header i {
                font-size: 1rem;
                color: #7c3aed;
            }

            .tasks-group-header h3 {
                margin: 0;
                font-size: 0.9rem;
                font-weight: 500;
                color: #374151;
            }

            .tasks-group-count {
                background: #e5e7eb;
                color: #6b7280;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .tasks-group-count.completed {
                background: #d1fae5;
                color: #065f46;
            }

            /* Tasks List */
            .tasks-list {
                padding: 10px 20px;
            }

            .task-card {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                padding: 16px;
                border-bottom: 1px solid #f0f0f0;
                transition: all 0.3s ease;
            }

            .task-card:last-child {
                border-bottom: none;
            }

            .task-card:hover {
                background: #fafafa;
            }

            .task-card.completed {
                opacity: 0.8;
            }

            .task-status {
                flex-shrink: 0;
            }

            .status-indicator {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                margin-top: 5px;
            }

            .status-indicator.pending {
                background: #f59e0b;
                box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
            }

            .status-indicator.completed {
                background: #10b981;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
            }

            .task-content {
                flex: 1;
            }

            .task-title {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-bottom: 6px;
            }

            .task-title h4 {
                margin: 0;
                font-size: 0.9rem;
                font-weight: 600;
                color: #1f2937;
            }

            .overdue-badge,
            .completed-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 0.65rem;
                padding: 2px 8px;
                border-radius: 20px;
            }

            .overdue-badge {
                background: #fee2e2;
                color: #dc2626;
            }

            .completed-badge {
                background: #d1fae5;
                color: #059669;
            }

            .task-description {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 8px;
                line-height: 1.5;
            }

            .task-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 8px;
            }

            .task-meta span {
                font-size: 0.7rem;
                color: #6b7280;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .task-due-date.overdue {
                color: #dc2626;
            }

            .task-due-date.completed {
                color: #059669;
            }

            .task-points {
                color: #f59e0b !important;
            }

            .task-completed-date {
                color: #10b981 !important;
            }

            /* Empty Tasks State */
            .empty-tasks-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-tasks-state i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-tasks-state p {
                color: #6b7280;
                margin-bottom: 20px;
            }

            .btn-add-tasks {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #7c3aed;
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-add-tasks:hover {
                background: #6d28d9;
                transform: translateY(-2px);
                color: white;
            }

            /* Info Note */
            .info-note {
                background: #fef3c7;
                border-radius: 16px;
                padding: 15px 20px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 0.8rem;
                color: #92400e;
            }

            .info-note i {
                font-size: 1.1rem;
                color: #d97706;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .plan-details-container {
                    padding: 15px;
                }

                .plan-header-content {
                    flex-direction: column;
                    text-align: center;
                    padding: 20px;
                }

                .plan-title-section h1 {
                    font-size: 1.2rem;
                }

                .plan-meta {
                    justify-content: center;
                }

                .plan-progress-section {
                    padding: 15px 20px;
                }

                .tasks-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .tasks-group-header {
                    padding: 12px 20px;
                }

                .task-card {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .task-status {
                    display: none;
                }

                .action-buttons-container {
                    justify-content: stretch;
                }

                .btn-edit,
                .btn-delete {
                    flex: 1;
                    justify-content: center;
                }

                .progress-stats {
                    justify-content: center;
                }

                .info-note {
                    font-size: 0.7rem;
                }
            }

            /* RTL Support */
            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            body.rtl .task-title {
                flex-direction: row;
            }

            body.rtl .task-meta {
                flex-direction: row;
            }

            body.rtl .tasks-header h2 i {
                margin-right: 0;
                margin-left: 10px;
            }

            body.rtl .meta-item i {
                margin-right: 0;
                margin-left: 6px;
            }

            body.rtl .info-note {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Delete Plan
            async function deletePlan(planId, planTitle) {
                const result = await Swal.fire({
                    title: '{{ __("Delete Treatment Plan") }}',
                    html: `{{ __('Are you sure you want to delete the plan') }} "<strong>${planTitle}</strong>"?<br><span style="color: #f59e0b;">{{ __('This action cannot be undone. All tasks will also be deleted.') }}</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, Delete Plan") }}',
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
                        window.location.href = '{{ route("specialist.treatment-plans.index") }}';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error!") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            }
        </script>
    @endpush
@endsection