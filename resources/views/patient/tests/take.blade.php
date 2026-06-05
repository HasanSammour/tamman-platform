{{-- resources/views/patient/tests/take.blade.php --}}
@extends('layouts.app')

@section('title', __('Take :test Assessment', ['test' => $testInfo['name']]) . ' - ' . __('Tamman'))

@section('page-title', __('Take :test Assessment', ['test' => $testInfo['name']]))

@section('content')
    <div class="take-test-container">
        <!-- Test Header -->
        <div class="test-header-card" style="border-top: 4px solid {{ $testInfo['color'] }}">
            <div class="test-header-content">
                <div class="test-icon-large" style="background: {{ $testInfo['bg'] }}; color: {{ $testInfo['color'] }}">
                    <i class="{{ $testInfo['icon'] }}"></i>
                </div>
                <div class="test-header-info">
                    <h1>{{ $testInfo['name'] }}</h1>
                    <p>{{ app()->getLocale() === 'ar' ? $testInfo['full_name_ar'] : $testInfo['full_name'] }}</p>
                    <div class="test-meta-details">
                        <span><i class="fas fa-question-circle"></i> {{ $testInfo['questions_count'] }}
                            {{ __('questions') }}</span>
                        <span><i class="fas fa-clock"></i> {{ $testInfo['time_minutes'] }} {{ __('minutes') }}</span>
                        <span><i class="fas fa-star"></i> 10 {{ __('points') }}</span>
                    </div>
                </div>
            </div>
            <div class="test-progress-container">
                <div class="progress-info">
                    <span>{{ __('Question') }} <span id="currentQuestionNum">1</span> {{ __('of') }} <span
                            id="totalQuestions">{{ $testInfo['questions_count'] }}</span></span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Test Form -->
        <form id="testForm" data-test-type="{{ $testType }}">
            @csrf
            <div class="questions-container" id="questionsContainer">
                @foreach($questions as $index => $question)
                    <div class="question-card" data-question-id="{{ $question['id'] }}" data-question-index="{{ $index }}"
                        style="display: {{ $index == 0 ? 'block' : 'none' }}">
                        <div class="question-number">Q{{ $index + 1 }}</div>
                        <h3 class="question-text">
                            {{ app()->getLocale() === 'ar' ? $question['text_ar'] : $question['text_en'] }}</h3>
                        <div class="options-container">
                            @foreach($options as $value => $option)
                                <label class="option-label">
                                    <input type="radio" name="q_{{ $question['id'] }}" value="{{ $value }}" class="option-radio"
                                        data-question="{{ $question['id'] }}">
                                    <span class="option-custom">
                                        <span class="option-value">{{ $value }}</span>
                                        <span
                                            class="option-text">{{ app()->getLocale() === 'ar' ? $option['ar'] : $option['en'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Buttons -->
            <div class="navigation-buttons">
                <button type="button" id="prevBtn" class="nav-btn prev-btn" disabled>
                    <i class="fas fa-arrow-left"></i> {{ __('Previous') }}
                </button>
                <button type="button" id="nextBtn" class="nav-btn next-btn">
                    {{ __('Next') }} <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" id="submitBtn" class="nav-btn submit-btn" style="display: none;">
                    <i class="fas fa-check-circle"></i> {{ __('Submit Test') }}
                </button>
            </div>
        </form>

        <!-- Tips Card -->
        <div class="tips-card">
            <i class="fas fa-lightbulb"></i>
            <div class="tips-content">
                <h4>{{ __('Tips for taking this assessment') }}</h4>
                <p>{{ __('Answer honestly based on how you have been feeling over the past two weeks (or specified time frame). There are no right or wrong answers.') }}
                </p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .take-test-container {
                max-width: 800px;
                margin: 0 auto;
            }

            .test-header-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .test-header-content {
                padding: 30px;
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .test-icon-large {
                width: 70px;
                height: 70px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .test-icon-large i {
                font-size: 2rem;
            }

            .test-header-info h1 {
                font-size: 1.5rem;
                margin-bottom: 5px;
                color: #1f2937;
            }

            .test-header-info p {
                color: #6b7280;
                margin-bottom: 10px;
            }

            .test-meta-details {
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
            }

            .test-meta-details span {
                font-size: 0.75rem;
                color: #6b7280;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .test-progress-container {
                padding: 0 30px 30px 30px;
            }

            .progress-info {
                display: flex;
                justify-content: space-between;
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .progress-bar-wrapper {
                height: 6px;
                background: #e5e7eb;
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
                border-radius: 3px;
                transition: width 0.3s ease;
            }

            .questions-container {
                margin-bottom: 25px;
            }

            .question-card {
                background: white;
                border-radius: 24px;
                padding: 30px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                animation: fadeIn 0.4s ease;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateX(20px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .question-number {
                display: inline-block;
                background: #ede9fe;
                color: #7c3aed;
                font-size: 0.7rem;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                margin-bottom: 15px;
            }

            .question-text {
                font-size: 1.2rem;
                font-weight: 500;
                color: #1f2937;
                margin-bottom: 25px;
                line-height: 1.5;
            }

            .options-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .option-label {
                display: block;
                cursor: pointer;
            }

            .option-radio {
                display: none;
            }

            .option-custom {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 18px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .option-radio:checked+.option-custom {
                background: #ede9fe;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .option-value {
                width: 32px;
                height: 32px;
                background: white;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                color: #4b5563;
                transition: all 0.3s ease;
            }

            .option-radio:checked+.option-custom .option-value {
                background: #7c3aed;
                color: white;
            }

            .option-text {
                flex: 1;
                font-size: 0.85rem;
                color: #374151;
            }

            .navigation-buttons {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-bottom: 25px;
            }

            .nav-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                border-radius: 40px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
            }

            .prev-btn {
                background: #f3f4f6;
                color: #4b5563;
            }

            .prev-btn:hover:not(:disabled) {
                background: #e5e7eb;
                transform: translateX(-2px);
            }

            .prev-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .next-btn {
                background: #7c3aed;
                color: white;
                margin-left: auto;
            }

            .next-btn:hover {
                background: #6d28d9;
                transform: translateX(2px);
            }

            .submit-btn {
                background: #10b981;
                color: white;
                margin-left: auto;
            }

            .submit-btn:hover {
                background: #059669;
                transform: scale(1.02);
            }

            .tips-card {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 20px;
                padding: 20px;
                display: flex;
                gap: 15px;
                align-items: flex-start;
            }

            .tips-card i {
                font-size: 1.5rem;
                color: #d97706;
            }

            .tips-content h4 {
                font-size: 0.9rem;
                margin-bottom: 5px;
                color: #92400e;
            }

            .tips-content p {
                font-size: 0.75rem;
                color: #b45309;
                margin: 0;
            }

            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .loading-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .loading-spinner {
                background: white;
                padding: 30px;
                border-radius: 20px;
                text-align: center;
            }

            .loading-spinner i {
                font-size: 2rem;
                color: #7c3aed;
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

            @media (max-width: 768px) {
                .test-header-content {
                    flex-direction: column;
                    text-align: center;
                }

                .test-meta-details {
                    justify-content: center;
                }

                .question-card {
                    padding: 20px;
                }

                .question-text {
                    font-size: 1rem;
                }

                .option-custom {
                    padding: 10px 14px;
                }

                .navigation-buttons {
                    flex-wrap: wrap;
                }

                .next-btn,
                .submit-btn {
                    margin-left: 0;
                    flex: 1;
                }

                .prev-btn {
                    flex: 1;
                }
            }

            body.rtl .next-btn i,
            body.rtl .prev-btn i {
                transform: rotate(180deg);
            }

            body.rtl .navigation-buttons {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const totalQuestions = {{ $testInfo['questions_count'] }};
            let currentQuestion = 0;
            const answers = {};

            // DOM Elements
            const questionsContainer = document.getElementById('questionsContainer');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const currentQuestionNumSpan = document.getElementById('currentQuestionNum');
            const progressFill = document.getElementById('progressFill');
            const progressPercentSpan = document.getElementById('progressPercent');
            const testForm = document.getElementById('testForm');
            const testType = testForm.dataset.testType;

            // Clear any saved answers from localStorage when page loads (FIX: No pre-selected answers)
            localStorage.removeItem(`test_${testType}_answers`);

            // Update progress bar
            function updateProgress() {
                const answeredCount = Object.keys(answers).length;
                const percent = Math.round((answeredCount / totalQuestions) * 100);
                progressFill.style.width = `${percent}%`;
                progressPercentSpan.innerText = `${percent}%`;
            }

            // Show question by index
            function showQuestion(index) {
                const questions = document.querySelectorAll('.question-card');
                questions.forEach((q, i) => {
                    q.style.display = i === index ? 'block' : 'none';
                });
                currentQuestionNumSpan.innerText = index + 1;
                currentQuestion = index;

                // Update navigation buttons
                prevBtn.disabled = index === 0;

                if (index === totalQuestions - 1) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'flex';
                } else {
                    nextBtn.style.display = 'flex';
                    submitBtn.style.display = 'none';
                }
            }

            // Collect answer for current question
            function collectCurrentAnswer() {
                const currentCard = document.querySelector(`.question-card[data-question-index="${currentQuestion}"]`);
                if (currentCard) {
                    const questionId = currentCard.dataset.questionId;
                    const selectedRadio = currentCard.querySelector(`input[name="q_${questionId}"]:checked`);
                    if (selectedRadio) {
                        answers[`q_${questionId}`] = selectedRadio.value;
                        updateProgress();
                    } else {
                        // If no answer selected, remove from answers object
                        delete answers[`q_${questionId}`];
                        updateProgress();
                    }
                }
            }

            // Event Listeners for radio buttons
            document.querySelectorAll('.option-radio').forEach(radio => {
                radio.addEventListener('change', function () {
                    const questionId = this.name;
                    answers[questionId] = this.value;
                    updateProgress();
                });
            });

            // Next button click
            nextBtn.addEventListener('click', () => {
                collectCurrentAnswer();
                if (currentQuestion < totalQuestions - 1) {
                    showQuestion(currentQuestion + 1);
                }
            });

            // Previous button click
            prevBtn.addEventListener('click', () => {
                if (currentQuestion > 0) {
                    showQuestion(currentQuestion - 1);
                }
            });

            // Submit form with AJAX
            testForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Collect last answer
                collectCurrentAnswer();

                // Check if all questions are answered
                if (Object.keys(answers).length !== totalQuestions) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("Incomplete Test") }}',
                        text: '{{ __("Please answer all questions before submitting.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    return;
                }

                // Show loading
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay active';
                loadingOverlay.innerHTML = `
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>{{ __("Submitting your test...") }}</p>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    Object.keys(answers).forEach(key => {
                        formData.append(key, answers[key]);
                    });

                    const response = await fetch(`/patient/tests/${testType}/submit`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });

                    const data = await response.json();

                    loadingOverlay.remove();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Test Completed!") }}',
                            html: data.message,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.href = `/patient/tests/results/${data.result_id}`;
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    loadingOverlay.remove();
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Something went wrong. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            });

            // Initialize
            showQuestion(0);
        </script>
    @endpush

@endsection