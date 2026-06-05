{{-- resources/views/specialist/treatment-plans/create.blade.php --}}
@extends('layouts.app')

@section('title', __('Create Treatment Plan') . ' - ' . __('Tamman'))

@section('page-title', __('Create New Treatment Plan'))

@section('content')
    <div class="create-plan-container">
        <div class="create-plan-card animate-slide-up">

            <!-- Header -->
            <div class="form-header">
                <div class="header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h2>{{ __('Create Treatment Plan') }}</h2>
                <p>{{ __('Create a personalized treatment plan for your patient with tasks and rewards') }}</p>
            </div>

            <!-- Form -->
            <form id="treatmentPlanForm" class="treatment-plan-form">
                @csrf

                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        <h3>{{ __('Basic Information') }}</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="patient_id">{{ __('Select Patient') }} <span class="required">*</span></label>
                            <div class="select-wrapper">
                                <i class="fas fa-user select-icon"></i>
                                <select name="patient_id" id="patient_id" class="form-control" required>
                                    <option value="">{{ __('Select a patient') }}</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ (isset($selectedPatientId) && $selectedPatientId == $patient->id) ? 'selected' : '' }}>
                                            {{ $patient->name }} ({{ $patient->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                            <div class="error-message" id="patient_id-error"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">{{ __('Plan Title') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" name="title" id="title" class="form-control"
                                    placeholder="{{ __('e.g., Anxiety Management Plan') }}" required>
                            </div>
                            <div class="error-message" id="title-error"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <div class="input-wrapper">
                                <i class="fas fa-align-left input-icon textarea-icon"></i>
                                <textarea name="description" id="description" class="form-control" rows="4"
                                    placeholder="{{ __('Describe the overall goals and objectives of this treatment plan...') }}"></textarea>
                            </div>
                            <div class="error-message" id="description-error"></div>
                        </div>
                    </div>

                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="start_date">{{ __('Start Date') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar-alt input-icon"></i>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-hint">{{ __('Start date cannot be in the past') }}</div>
                            <div class="error-message" id="start_date-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="end_date">{{ __('End Date') }}</label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar-check input-icon"></i>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-hint">{{ __('Optional - Leave empty for ongoing plan') }}</div>
                            <div class="error-message" id="end_date-error"></div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="form-section tasks-section">
                    <div class="section-title">
                        <i class="fas fa-tasks"></i>
                        <h3>{{ __('Treatment Tasks') }}</h3>
                        <p>{{ __('Add tasks for your patient to complete. Each task can have points reward (3-15 points).') }}
                        </p>
                    </div>

                    <div id="tasks-container">
                        <div class="tasks-list" id="tasks-list">
                            <!-- Task templates will be added here dynamically -->
                        </div>

                        <button type="button" class="btn-add-task" id="addTaskBtn">
                            <i class="fas fa-plus-circle"></i> {{ __('Add Another Task') }}
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('specialist.treatment-plans.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text"><i class="fas fa-save"></i> {{ __('Create Treatment Plan') }}</span>
                        <span class="btn-spinner"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .create-plan-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 20px;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease;
            }

            .create-plan-card {
                background: white;
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            /* Form Header */
            .form-header {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                padding: 30px;
                text-align: center;
                color: white;
            }

            .header-icon {
                width: 70px;
                height: 70px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px;
            }

            .header-icon i {
                font-size: 2rem;
                color: white;
            }

            .form-header h2 {
                color: white;
                font-size: 1.5rem;
                margin-bottom: 8px;
            }

            .form-header p {
                color: rgba(255, 255, 255, 0.8);
                margin: 0;
            }

            /* Form Sections */
            .form-section {
                padding: 25px 30px;
                border-bottom: 1px solid #f0f0f0;
            }

            .section-title {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 25px;
                flex-wrap: wrap;
            }

            .section-title i {
                font-size: 1.3rem;
                color: #7c3aed;
            }

            .section-title h3 {
                margin: 0;
                font-size: 1.1rem;
                color: #1f2937;
            }

            .section-title p {
                margin: 0;
                font-size: 0.75rem;
                color: #6b7280;
                width: 100%;
                margin-top: 5px;
            }

            /* Form Row */
            .form-row {
                margin-bottom: 20px;
            }

            .form-row.two-cols {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
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

            /* Input Wrappers */
            .input-wrapper,
            .select-wrapper {
                position: relative;
            }

            .input-icon,
            .select-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.9rem;
                pointer-events: none;
            }

            .textarea-icon {
                top: 18px;
                transform: none;
            }

            .arrow-icon {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
            }

            .form-control {
                width: 100%;
                padding: 12px 16px 12px 42px;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                font-size: 0.875rem;
                transition: all 0.3s ease;
                background: #f9fafb;
            }

            textarea.form-control {
                padding-top: 14px;
                resize: vertical;
            }

            select.form-control {
                appearance: none;
                cursor: pointer;
                padding-right: 40px;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control.error {
                border-color: #ef4444;
                background: #fef2f2;
            }

            .form-hint {
                font-size: 0.65rem;
                color: #9ca3af;
                margin-top: 5px;
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

            /* Tasks Section */
            .tasks-section {
                background: #fafafa;
            }

            .tasks-list {
                display: flex;
                flex-direction: column;
                gap: 20px;
                margin-bottom: 20px;
            }

            .task-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                position: relative;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }

            .task-card:hover {
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .task-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .task-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                background: #ede9fe;
                color: #7c3aed;
                border-radius: 8px;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .btn-remove-task {
                background: none;
                border: none;
                color: #ef4444;
                cursor: pointer;
                padding: 6px;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .btn-remove-task:hover {
                background: #fee2e2;
                transform: scale(1.05);
            }

            .task-form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 15px;
            }

            .task-form-row.full-width {
                grid-template-columns: 1fr;
            }

            .task-form-group {
                margin-bottom: 0;
            }

            .task-form-group label {
                font-size: 0.75rem;
                margin-bottom: 5px;
            }

            .points-input-wrapper {
                position: relative;
            }

            .points-input-wrapper .currency-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.8rem;
                pointer-events: none;
            }

            .points-input-wrapper input {
                padding-right: 35px;
            }

            /* Points Range Indicator */
            .points-range {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 8px;
                font-size: 0.65rem;
                color: #6b7280;
            }

            .points-range .range-bar {
                flex: 1;
                height: 3px;
                background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);
                border-radius: 2px;
            }

            .range-min,
            .range-max {
                font-size: 0.6rem;
            }

            /* Add Task Button */
            .btn-add-task {
                width: 100%;
                padding: 14px;
                background: #f3f4f6;
                border: 2px dashed #c4b5fd;
                border-radius: 16px;
                color: #7c3aed;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-add-task:hover {
                background: #ede9fe;
                border-color: #7c3aed;
                transform: translateY(-2px);
            }

            /* Form Actions */
            .form-actions {
                padding: 20px 30px;
                background: #f9fafb;
                display: flex;
                justify-content: flex-end;
                gap: 15px;
            }

            .btn-cancel,
            .btn-submit {
                padding: 12px 28px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-cancel {
                background: #f3f4f6;
                color: #374151;
                text-decoration: none;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-submit {
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
            }

            .btn-submit:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .btn-submit:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            /* Loading Spinner */
            .btn-spinner {
                display: none;
            }

            .btn-spinner i {
                color: white;
                font-size: 1rem;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .btn-loading .btn-text {
                display: none;
            }

            .btn-loading .btn-spinner {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* Ensure spinner visibility on all button types */
            .btn-submit .btn-spinner i,
            .btn-primary .btn-spinner i,
            .btn-save .btn-spinner i {
                color: white;
            }

            /* For cancel button if needed */
            .btn-cancel .btn-spinner i {
                color: #374151;
            }

            /* Mobile Responsive - 375px */
            @media (max-width: 480px) {
                .create-plan-container {
                    padding: 10px;
                }

                .create-plan-card {
                    border-radius: 20px;
                }

                .form-header {
                    padding: 20px;
                }

                .header-icon {
                    width: 50px;
                    height: 50px;
                }

                .header-icon i {
                    font-size: 1.5rem;
                }

                .form-header h2 {
                    font-size: 1.2rem;
                }

                .form-header p {
                    font-size: 0.75rem;
                }

                .form-section {
                    padding: 20px;
                }

                .section-title {
                    margin-bottom: 15px;
                }

                .section-title i {
                    font-size: 1.1rem;
                }

                .section-title h3 {
                    font-size: 1rem;
                }

                .form-row {
                    margin-bottom: 15px;
                }

                .form-row.two-cols {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .form-control {
                    padding: 10px 12px 10px 38px;
                    font-size: 0.8rem;
                }

                .task-form-row {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .task-card {
                    padding: 15px;
                }

                .task-header {
                    margin-bottom: 10px;
                }

                .btn-add-task {
                    padding: 10px;
                    font-size: 0.75rem;
                }

                .form-actions {
                    padding: 15px 20px;
                    flex-direction: column;
                }

                .btn-cancel,
                .btn-submit {
                    justify-content: center;
                    width: 100%;
                    padding: 10px 20px;
                }

                .points-range .range-min,
                .points-range .range-max {
                    font-size: 0.55rem;
                }
            }

            /* Tablet Responsive */
            @media (min-width: 481px) and (max-width: 768px) {
                .create-plan-container {
                    padding: 15px;
                }

                .form-section {
                    padding: 20px;
                }

                .form-row.two-cols {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .task-form-row {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .form-actions {
                    flex-direction: column;
                }

                .btn-cancel,
                .btn-submit {
                    justify-content: center;
                    width: 100%;
                }
            }

            /* RTL Support */
            body.rtl .input-icon,
            body.rtl .select-icon {
                left: auto;
                right: 14px;
            }

            body.rtl .arrow-icon {
                right: auto;
                left: 14px;
            }

            body.rtl .form-control {
                padding: 12px 42px 12px 16px;
            }

            body.rtl .points-input-wrapper .currency-icon {
                right: auto;
                left: 12px;
            }

            body.rtl .points-input-wrapper input {
                padding-right: 16px;
                padding-left: 35px;
            }

            body.rtl .section-title {
                flex-direction: row;
            }

            body.rtl .task-header {
                flex-direction: row;
            }

            @media (max-width: 480px) {
                body.rtl .form-control {
                    padding: 10px 38px 10px 12px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let taskCounter = 1;

            // Task Template
            function createTaskTemplate(taskNumber, taskData = null) {
                const taskId = 'task_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const title = taskData?.title || '';
                const description = taskData?.description || '';
                const dueDate = taskData?.due_date || '';
                const pointsReward = taskData?.points_reward || 5;

                const today = new Date().toISOString().split('T')[0];

                return `
                            <div class="task-card" data-task-id="${taskId}">
                                <div class="task-header">
                                    <span class="task-number">${taskNumber}</span>
                                    <button type="button" class="btn-remove-task" onclick="removeTask(this)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <div class="task-form-row full-width">
                                    <div class="task-form-group">
                                        <label>${escapeHtml('{{ __("Task Title") }}')} <span class="required">*</span></label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-tasks input-icon"></i>
                                            <input type="text" name="tasks[${taskId}][title]" class="form-control task-title" 
                                                placeholder="${escapeHtml('{{ __("e.g., Practice deep breathing exercises") }}')}" 
                                                value="${escapeHtml(title)}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="task-form-row full-width">
                                    <div class="task-form-group">
                                        <label>${escapeHtml('{{ __("Description") }}')}</label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-align-left input-icon textarea-icon"></i>
                                            <textarea name="tasks[${taskId}][description]" class="form-control" rows="2" 
                                                placeholder="${escapeHtml('{{ __("Detailed instructions for completing this task...") }}')}">${escapeHtml(description)}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="task-form-row">
                                    <div class="task-form-group">
                                        <label>${escapeHtml('{{ __("Due Date") }}')}</label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-calendar-alt input-icon"></i>
                                            <input type="date" name="tasks[${taskId}][due_date]" class="form-control" min="${today}" value="${dueDate}">
                                        </div>
                                    </div>
                                    <div class="task-form-group">
                                        <label>${escapeHtml('{{ __("Points Reward") }}')} <span class="required">*</span></label>
                                        <div class="points-input-wrapper">
                                            <input type="number" name="tasks[${taskId}][points_reward]" class="form-control task-points" 
                                                min="3" max="15" step="1" value="${pointsReward}" required>
                                            <span class="currency-icon"><i class="fas fa-star"></i></span>
                                        </div>
                                        <div class="points-range">
                                            <span class="range-min">3</span>
                                            <div class="range-bar"></div>
                                            <span class="range-max">15</span>
                                        </div>
                                        <div class="error-message task-points-error" style="display: none;">{{ __("Points must be between 3 and 15") }}</div>
                                    </div>
                                </div>
                            </div>
                        `;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function (m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Add Task
            const tasksList = document.getElementById('tasks-list');
            const addTaskBtn = document.getElementById('addTaskBtn');

            function updateTaskNumbers() {
                const tasks = document.querySelectorAll('.task-card');
                tasks.forEach((task, index) => {
                    const numberSpan = task.querySelector('.task-number');
                    if (numberSpan) {
                        numberSpan.textContent = index + 1;
                    }
                });
            }

            function addTask(taskData = null) {
                if (!tasksList) return;
                const taskNumber = document.querySelectorAll('.task-card').length + 1;
                const newTask = createTaskTemplate(taskNumber, taskData);
                tasksList.insertAdjacentHTML('beforeend', newTask);

                // Add validation for points input
                const newTaskElement = tasksList.lastElementChild;
                const pointsInput = newTaskElement.querySelector('.task-points');
                if (pointsInput) {
                    pointsInput.addEventListener('input', function () {
                        validatePointsInput(this);
                    });
                }
                updateTaskNumbers();
            }

            function validatePointsInput(input) {
                const value = parseInt(input.value);
                const errorDiv = input.closest('.task-form-group').querySelector('.task-points-error');
                if (value < 3 || value > 15 || isNaN(value)) {
                    if (errorDiv) errorDiv.style.display = 'block';
                    input.classList.add('error');
                    return false;
                } else {
                    if (errorDiv) errorDiv.style.display = 'none';
                    input.classList.remove('error');
                    return true;
                }
            }

            window.removeTask = function (button) {
                const taskCard = button.closest('.task-card');
                if (taskCard) {
                    taskCard.remove();
                    updateTaskNumbers();
                }
            };

            if (addTaskBtn) {
                addTaskBtn.addEventListener('click', () => addTask());
            }

            // Add initial empty task
            addTask();

            // ==================== FORM SUBMIT ====================
            const form = document.getElementById('treatmentPlanForm');
            const submitBtn = document.getElementById('submitBtn');

            // Validation functions
            function validatePointsFields() {
                let isValid = true;
                const pointsInputs = document.querySelectorAll('.task-points');
                pointsInputs.forEach(input => {
                    if (!validatePointsInput(input)) {
                        isValid = false;
                    }
                });
                return isValid;
            }

            function validateRequiredFields() {
                let isValid = true;

                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => {
                    el.classList.remove('show');
                });
                document.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('error');
                });

                // Patient
                const patientSelect = document.getElementById('patient_id');
                if (patientSelect && !patientSelect.value) {
                    showError('patient_id', '{{ __("Please select a patient") }}');
                    isValid = false;
                } else {
                    clearError('patient_id');
                }

                // Title
                const titleInput = document.getElementById('title');
                if (titleInput && !titleInput.value.trim()) {
                    showError('title', '{{ __("Please enter a plan title") }}');
                    isValid = false;
                } else {
                    clearError('title');
                }

                // Start Date
                const startDateInput = document.getElementById('start_date');
                if (startDateInput && !startDateInput.value) {
                    showError('start_date', '{{ __("Please select a start date") }}');
                    isValid = false;
                } else {
                    clearError('start_date');
                }

                // End Date validation (if provided)
                const endDateInput = document.getElementById('end_date');
                if (startDateInput && startDateInput.value && endDateInput && endDateInput.value && endDateInput.value < startDateInput.value) {
                    showError('end_date', '{{ __("End date must be after start date") }}');
                    isValid = false;
                } else {
                    clearError('end_date');
                }

                // Task titles
                const taskTitles = document.querySelectorAll('.task-title');
                if (taskTitles.length === 0) {
                    // No tasks added
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("No Tasks") }}',
                        text: '{{ __("Please add at least one task to the treatment plan.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    return false;
                }

                taskTitles.forEach((titleInput, index) => {
                    if (!titleInput.value.trim()) {
                        titleInput.classList.add('error');
                        isValid = false;
                    }
                });

                return isValid;
            }

            function showError(fieldId, message) {
                const errorDiv = document.getElementById(`${fieldId}-error`);
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.classList.add('show');
                }
                const input = document.getElementById(fieldId);
                if (input) input.classList.add('error');
            }

            function clearError(fieldId) {
                const errorDiv = document.getElementById(`${fieldId}-error`);
                if (errorDiv) {
                    errorDiv.classList.remove('show');
                }
                const input = document.getElementById(fieldId);
                if (input) input.classList.remove('error');
            }

            // Form Submit
            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    // Validate all fields
                    if (!validateRequiredFields()) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Validation Error") }}',
                            text: '{{ __("Please fill in all required fields correctly.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    if (!validatePointsFields()) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Validation Error") }}',
                            text: '{{ __("Task points must be between 3 and 15.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    // Prepare form data
                    const formData = new FormData();
                    formData.append('patient_id', document.getElementById('patient_id').value);
                    formData.append('title', document.getElementById('title').value);
                    formData.append('description', document.getElementById('description').value);
                    formData.append('start_date', document.getElementById('start_date').value);
                    formData.append('end_date', document.getElementById('end_date').value || '');

                    // Process tasks - send as array, not JSON string
                    const tasksData = [];
                    document.querySelectorAll('.task-card').forEach((taskCard, index) => {
                        const titleInput = taskCard.querySelector('.task-title');
                        const descTextarea = taskCard.querySelector('textarea[name*="[description]"]');
                        const dateInput = taskCard.querySelector('input[type="date"]');
                        const pointsInput = taskCard.querySelector('.task-points');

                        if (titleInput && titleInput.value.trim()) {
                            tasksData.push({
                                title: titleInput.value.trim(),
                                description: descTextarea ? descTextarea.value : '',
                                due_date: dateInput ? dateInput.value : '',
                                points_reward: pointsInput ? parseInt(pointsInput.value) : 5
                            });
                        }
                    });

                    // Append each task individually as array elements
                    tasksData.forEach((task, idx) => {
                        formData.append(`tasks[${idx}][title]`, task.title);
                        formData.append(`tasks[${idx}][description]`, task.description);
                        formData.append(`tasks[${idx}][due_date]`, task.due_date);
                        formData.append(`tasks[${idx}][points_reward]`, task.points_reward);
                    });

                    // Show loading state
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;

                    // Ensure spinner icon has white color
                    const spinnerIcon = submitBtn.querySelector('.btn-spinner i');
                    if (spinnerIcon) {
                        spinnerIcon.style.color = 'white';
                    }

                    try {
                        const response = await fetch('{{ route("specialist.treatment-plans.store") }}', {
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
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: data.message,
                                confirmButtonColor: '#7c3aed'
                            });
                            submitBtn.classList.remove('btn-loading');
                            submitBtn.disabled = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                    }
                });
            }

            // Clear validation errors on input
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function () {
                    this.classList.remove('error');
                    const errorDiv = this.closest('.form-group')?.querySelector('.error-message');
                    if (errorDiv) errorDiv.classList.remove('show');
                });
            });
        </script>
    @endpush
@endsection