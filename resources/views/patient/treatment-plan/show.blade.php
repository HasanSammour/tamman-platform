{{-- resources/views/patient/treatment-plan/show.blade.php --}}
@extends('layouts.app')

@section('title', $plan->title . ' - ' . __('Tamman'))

@section('page-title', $plan->title)

@section('content')
    <div class="plan-details-container">
        <!-- Encouraging Header -->
        <div class="encouraging-header animate-slide-down">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="header-text">
                    <h2>{{ __('You\'re making progress!') }}</h2>
                    <p>{{ __('Every task you complete brings you closer to your goals. Keep going!') }}</p>
                </div>
                <div class="header-progress">
                    <div class="progress-circle" data-progress="{{ $progress['percentage'] }}">
                        <svg viewBox="0 0 100 100" class="progress-svg">
                            <circle class="circle-bg" cx="50" cy="50" r="45" />
                            <circle class="circle-progress" cx="50" cy="50" r="45"
                                stroke-dasharray="{{ $progress['percentage'] }}, 100" />
                            <text x="50" y="55" class="percentage-text" dominant-baseline="middle" text-anchor="middle" style="font-size: 2rem;">
                                {{ $progress['percentage'] }}%
                            </text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Info Card -->
        <div class="plan-info-card animate-fade-in-up">
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
                    <h3>{{ $plan->specialist->name }}</h3>
                    <p>{{ $plan->specialist->specialistProfile->specialization ?? __('Psychologist') }}</p>
                </div>
            </div>
            <div class="plan-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ __('Started') }}:
                        {{ Carbon\Carbon::parse($plan->start_date)->translatedFormat('M d, Y') }}</span>
                </div>
                @if($plan->end_date)
                    <div class="date-item">
                        <i class="fas fa-flag-checkered"></i>
                        <span>{{ __('Completed') }}:
                            {{ Carbon\Carbon::parse($plan->end_date)->translatedFormat('M d, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Plan Description -->
        @if($plan->description)
            <div class="plan-description-card animate-fade-in-up" style="animation-delay: 0.1s">
                <h3><i class="fas fa-info-circle"></i> {{ __('About This Plan') }}</h3>
                <p>{{ $plan->description }}</p>
            </div>
        @endif

        <!-- Tasks Section -->
        <div class="tasks-section animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="tasks-header">
                <h3><i class="fas fa-tasks"></i> {{ __('Your Tasks') }}</h3>
                <div class="tasks-summary" id="tasksSummary">
                    <span class="completed-badge"><i class="fas fa-check-circle"></i> <span
                            id="completedCount">{{ $progress['completed'] }}</span>/<span
                            id="totalCount">{{ $progress['total'] }}</span> {{ __('completed') }}</span>
                    <span class="points-badge"><i class="fas fa-star"></i> {{ $plan->tasks->sum('points_reward') }}
                        {{ __('points total') }}</span>
                </div>
            </div>

            @if($pendingTasks->count() > 0)
                <div class="tasks-list" id="pendingTasksList">
                    <h4 class="tasks-subtitle">{{ __('Tasks to Complete') }}</h4>
                    @foreach($pendingTasks as $task)
                        <div class="task-item" data-task-id="{{ $task->id }}" id="task-{{ $task->id }}">
                            <div class="task-check">
                                <button class="task-complete-btn" data-task-id="{{ $task->id }}"
                                    data-points="{{ $task->points_reward }}">
                                    <i class="far fa-circle"></i>
                                </button>
                            </div>
                            <div class="task-content">
                                <div class="task-title">{{ $task->title }}</div>
                                @if($task->description)
                                    <div class="task-description">{{ $task->description }}</div>
                                @endif
                                @if($task->due_date)
                                    <div class="task-due-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ __('Due') }}: {{ Carbon\Carbon::parse($task->due_date)->translatedFormat('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                            <div class="task-points">
                                <span class="points-value">+{{ $task->points_reward }}</span>
                                <span class="points-label">{{ __('points') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($completedTasksList->count() > 0)
                <div class="completed-tasks-list">
                    <h4 class="tasks-subtitle completed">
                        <i class="fas fa-check-circle"></i> {{ __('Completed Tasks') }}
                    </h4>
                    @foreach($completedTasksList as $task)
                        <div class="task-item completed" data-task-id="{{ $task->id }}">
                            <div class="task-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="task-content">
                                <div class="task-title">{{ $task->title }}</div>
                                @if($task->description)
                                    <div class="task-description">{{ $task->description }}</div>
                                @endif
                                <div class="task-completed-date">
                                    <i class="fas fa-check"></i>
                                    {{ __('Completed on') }}
                                    {{ Carbon\Carbon::parse($task->completed_at)->translatedFormat('M d, Y') }}
                                </div>
                            </div>
                            <div class="task-points earned">
                                <span class="points-value">+{{ $task->points_reward }}</span>
                                <span class="points-label">{{ __('points earned') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="back-button-container">
            <a href="{{ route('patient.treatment-plan') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to My Treatment Plans') }}
            </a>
        </div>
    </div>

    <!-- Loading Overlay for Task Completion -->
    <div id="taskLoadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>{{ __('Completing task...') }}</p>
        </div>
    </div>

    @push('styles')
        <style>
            .plan-details-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 20px;
            }

            .encouraging-header {
                background: linear-gradient(135deg, #10b981, #059669);
                border-radius: 24px;
                padding: 25px 30px;
                margin-bottom: 30px;
                color: white;
            }

            .header-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 20px;
            }

            .header-icon i {
                font-size: 2rem;
                color: #fbbf24;
            }

            .header-text {
                flex: 1;
            }

            .header-text h2 {
                font-size: 1.2rem;
                margin-bottom: 5px;
                color: white;
            }

            .header-text p {
                color: rgba(255, 255, 255, 0.9);
                margin: 0;
                font-size: 0.8rem;
            }

            /* Progress Circle - Fixed with proper centering */
            .progress-circle {
                width: 90px;
                height: 90px;
                position: relative;
            }

            .progress-svg {
                width: 90px;
                height: 90px;
                transform: rotate(-90deg);
            }

            .circle-bg {
                fill: none;
                stroke: rgba(255, 255, 255, 0.3);
                stroke-width: 4;
            }

            .circle-progress {
                fill: none;
                stroke: #fbbf24;
                stroke-width: 4;
                stroke-linecap: round;
                transition: stroke-dasharray 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .percentage-text {
                fill: white;
                font-size: 18px;
                font-weight: bold;
                text-anchor: middle;
                dominant-baseline: middle;
                transform: rotate(90deg);
                transform-origin: center;
            }

            .plan-info-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .plan-specialist {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .specialist-avatar img,
            .avatar-placeholder {
                width: 55px;
                height: 55px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.2rem;
                font-weight: 600;
            }

            .specialist-info h3 {
                font-size: 1rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .specialist-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .plan-dates {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .date-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.75rem;
                color: #6b7280;
            }

            .date-item i {
                color: #7c3aed;
            }

            .plan-description-card {
                background: #f9fafb;
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
            }

            .plan-description-card h3 {
                font-size: 0.9rem;
                margin-bottom: 10px;
                color: #1f2937;
            }

            .plan-description-card p {
                font-size: 0.85rem;
                color: #6b7280;
                line-height: 1.5;
                margin: 0;
            }

            .tasks-section {
                background: white;
                border-radius: 20px;
                padding: 25px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .tasks-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e5e7eb;
            }

            .tasks-header h3 {
                font-size: 1rem;
                margin: 0;
                color: #1f2937;
            }

            .tasks-summary {
                display: flex;
                gap: 15px;
            }

            .completed-badge,
            .points-badge {
                font-size: 0.75rem;
                padding: 4px 12px;
                border-radius: 20px;
            }

            .completed-badge {
                background: #d1fae5;
                color: #065f46;
            }

            .points-badge {
                background: #fef3c7;
                color: #d97706;
            }

            .tasks-subtitle {
                font-size: 0.85rem;
                color: #374151;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 1px solid #f3f4f6;
            }

            .tasks-subtitle.completed {
                color: #10b981;
            }

            .task-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                padding: 15px 0;
                border-bottom: 1px solid #f3f4f6;
                transition: all 0.3s ease;
            }

            .task-item:last-child {
                border-bottom: none;
            }

            .task-item.completed {
                opacity: 0.7;
            }

            .task-check {
                flex-shrink: 0;
            }

            .task-complete-btn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
                font-size: 1.3rem;
                color: #9ca3af;
                transition: all 0.3s ease;
            }

            .task-complete-btn:hover {
                color: #10b981;
                transform: scale(1.1);
            }

            .task-complete-btn:disabled {
                cursor: not-allowed;
                opacity: 0.5;
            }

            .task-check .fa-check-circle {
                color: #10b981;
                font-size: 1.3rem;
            }

            .task-content {
                flex: 1;
            }

            .task-title {
                font-size: 0.9rem;
                font-weight: 500;
                color: #1f2937;
                margin-bottom: 4px;
            }

            .task-description {
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 6px;
            }

            .task-due-date,
            .task-completed-date {
                font-size: 0.65rem;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .task-completed-date {
                color: #10b981;
            }

            .task-points {
                text-align: right;
                flex-shrink: 0;
            }

            .points-value {
                font-size: 1rem;
                font-weight: 700;
                color: #f59e0b;
            }

            .points-label {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .task-points.earned .points-value {
                color: #10b981;
            }

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

            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(4px);
                z-index: 2000;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .loading-spinner {
                text-align: center;
                background: white;
                padding: 30px;
                border-radius: 20px;
            }

            .spinner {
                width: 50px;
                height: 50px;
                border: 4px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }

            .loading-spinner p {
                color: #6b7280;
                margin: 0;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
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

            @media (max-width: 768px) {
                .plan-details-container {
                    padding: 15px;
                }

                .encouraging-header {
                    padding: 20px;
                }

                .header-content {
                    flex-direction: column;
                    text-align: center;
                }

                .plan-info-card {
                    flex-direction: column;
                    text-align: center;
                }

                .plan-specialist {
                    flex-direction: column;
                }

                .plan-dates {
                    justify-content: center;
                }

                .tasks-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .task-item {
                    flex-wrap: wrap;
                }

                .task-points {
                    text-align: left;
                    margin-left: 40px;
                }

                .progress-circle {
                    width: 80px;
                    height: 80px;
                }

                .progress-svg {
                    width: 80px;
                    height: 80px;
                }

                .percentage-text {
                    font-size: 16px;
                }
            }

            @media (max-width: 480px) {
                .progress-circle {
                    width: 70px;
                    height: 70px;
                }

                .progress-svg {
                    width: 70px;
                    height: 70px;
                }

                .percentage-text {
                    font-size: 14px;
                }
            }

            body.rtl .task-points {
                text-align: left;
            }

            body.rtl .btn-back i {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Function to update progress circle with animation
            function updateProgressCircle(percentage) {
                const circleProgress = document.querySelector('.circle-progress');
                const percentageText = document.querySelector('.percentage-text');

                // Calculate circumference
                const radius = 45;
                const circumference = 2 * Math.PI * radius;
                const offset = circumference - (percentage / 100) * circumference;

                if (circleProgress) {
                    circleProgress.style.transition = 'stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                    circleProgress.style.strokeDasharray = `${circumference}`;
                    circleProgress.style.strokeDashoffset = offset;
                }

                if (percentageText) {
                    // Animate the text
                    const startValue = parseInt(percentageText.textContent) || 0;
                    const endValue = percentage;
                    const duration = 800;
                    const stepTime = 20;
                    const steps = duration / stepTime;
                    const increment = (endValue - startValue) / steps;
                    let current = startValue;
                    let step = 0;

                    const timer = setInterval(() => {
                        step++;
                        current += increment;
                        if (step >= steps) {
                            current = endValue;
                            clearInterval(timer);
                        }
                        percentageText.textContent = Math.round(current) + '%';
                    }, stepTime);
                }
            }

            // Initialize progress circle on page load
            document.addEventListener('DOMContentLoaded', function () {
                const initialPercentage = {{ $progress['percentage'] }};
                const radius = 45;
                const circumference = 2 * Math.PI * radius;
                const circleProgress = document.querySelector('.circle-progress');
                if (circleProgress) {
                    const offset = circumference - (initialPercentage / 100) * circumference;
                    circleProgress.style.strokeDasharray = `${circumference}`;
                    circleProgress.style.strokeDashoffset = offset;
                }
            });

            // Function to update tasks summary
            function updateTasksSummary(completed, total) {
                const completedSpan = document.getElementById('completedCount');
                const totalSpan = document.getElementById('totalCount');
                const completedBadge = document.querySelector('.completed-badge');

                if (completedSpan) completedSpan.textContent = completed;
                if (totalSpan) totalSpan.textContent = total;
                if (completedBadge) {
                    completedBadge.innerHTML = `<i class="fas fa-check-circle"></i> ${completed}/${total} {{ __('completed') }}`;
                }
            }

            // Task Completion Handler
            document.querySelectorAll('.task-complete-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const taskId = this.dataset.taskId;
                    const points = parseInt(this.dataset.points);
                    const taskItem = document.getElementById(`task-${taskId}`);
                    const originalHtml = this.innerHTML;

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    try {
                        const response = await fetch(`/patient/treatment-plans/task/${taskId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update UI for completed task
                            const taskCheck = taskItem.querySelector('.task-check');
                            taskCheck.innerHTML = '<i class="fas fa-check-circle"></i>';

                            // Remove the complete button
                            this.remove();

                            // Update task appearance
                            taskItem.classList.add('completed');
                            const dueDateElement = taskItem.querySelector('.task-due-date');
                            if (dueDateElement) dueDateElement.remove();

                            // Add completed date
                            const taskContent = taskItem.querySelector('.task-content');
                            const completedDate = document.createElement('div');
                            completedDate.className = 'task-completed-date';
                            completedDate.innerHTML = '<i class="fas fa-check"></i> {{ __("Completed just now") }}';
                            taskContent.appendChild(completedDate);

                            // Update points display
                            const taskPoints = taskItem.querySelector('.task-points');
                            taskPoints.classList.add('earned');
                            taskPoints.innerHTML = `<span class="points-value">+${data.points_earned}</span><span class="points-label">{{ __("points earned") }}</span>`;

                            // Update progress circle and summary
                            if (data.progress) {
                                updateProgressCircle(data.progress.percentage);
                                updateTasksSummary(data.progress.completed, data.progress.total);
                            }

                            // Show success message
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Task Completed!") }}',
                                html: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });

                            // If plan is completed, show special message and reload
                            if (data.plan_completed) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: '🎉 {{ __("Treatment Plan Completed!") }}',
                                    html: '{{ __("Congratulations! You have completed all tasks in this treatment plan. You earned bonus points!") }}',
                                    confirmButtonColor: '#7c3aed',
                                    background: '#fff',
                                    color: '#1f2937'
                                });
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error") }}',
                                text: data.message,
                                confirmButtonColor: '#7c3aed'
                            });
                            this.disabled = false;
                            this.innerHTML = originalHtml;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                });
            });
        </script>
    @endpush

@endsection