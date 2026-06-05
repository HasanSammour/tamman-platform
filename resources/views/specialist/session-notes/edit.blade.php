{{-- resources/views/specialist/session-notes/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Session Notes') . ' - ' . __('Tamman'))

@section('page-title', __('Session Notes'))

@section('content')
    <div class="edit-notes-container">
        <div class="edit-notes-card animate-slide-up">

            <!-- Header -->
            <div class="form-header">
                <div class="header-icon">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <h2>{{ __('Session Notes') }}</h2>
                <p>{{ __('Add or edit clinical notes for this session') }}</p>
            </div>

            <!-- Session Info Card -->
            <div class="session-info-card">
                <div class="session-info-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>{{ __('Session Information') }}</h3>
                </div>
                <div class="session-info-grid">
                    <div class="info-item">
                        <span class="info-label">{{ __('Patient') }}</span>
                        <span class="info-value">{{ $session->patient->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Date & Time') }}</span>
                        <span
                            class="info-value">{{ \Carbon\Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y') }}
                            {{ __('at') }} {{ \Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Session Type') }}</span>
                        <span class="info-value">
                            <span class="type-badge {{ $session->session_type }}">
                                <i
                                    class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                {{ __(ucfirst($session->session_type)) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('Status') }}</span>
                        <span class="info-value">
                            <span class="status-badge {{ $session->status }}">
                                <i class="fas {{ $session->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                                {{ __(ucfirst($session->status)) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notes Form -->
            <form id="notesForm" class="notes-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="notes">{{ __('Clinical Notes') }}</label>
                    <div class="textarea-wrapper">
                        <i class="fas fa-pen input-icon textarea-icon"></i>
                        <textarea name="notes" id="notes" class="form-control" rows="12"
                            placeholder="{{ __('Enter your clinical notes here. Include observations, progress, concerns, and next steps...') }}">{{ old('notes', $session->notes) }}</textarea>
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        {{ __('These notes are private and only visible to you. They help you track patient progress.') }}
                    </div>
                    <div class="error-message" id="notes-error"></div>
                </div>

                <!-- Tips Section -->
                <div class="tips-section">
                    <div class="tips-header">
                        <i class="fas fa-lightbulb"></i>
                        <h4>{{ __('Note Writing Tips') }}</h4>
                    </div>
                    <div class="tips-list">
                        <div class="tip-item">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Document key observations from the session') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Note patient\'s progress or setbacks') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Record any homework or tasks assigned') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Mention any concerns or follow-up needs') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Keep notes professional and objective') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('specialist.session-notes.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text"><i class="fas fa-save"></i> {{ __('Save Notes') }}</span>
                        <span class="btn-spinner"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .edit-notes-container {
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

            .edit-notes-card {
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

            /* Session Info Card */
            .session-info-card {
                margin: 20px 25px;
                padding: 20px;
                background: #f8fafc;
                border-radius: 20px;
                border: 1px solid #e5e7eb;
            }

            .session-info-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #e5e7eb;
            }

            .session-info-header i {
                font-size: 1.2rem;
                color: #7c3aed;
            }

            .session-info-header h3 {
                margin: 0;
                font-size: 0.9rem;
                color: #1f2937;
            }

            .session-info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .info-item {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .info-label {
                font-size: 0.7rem;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .info-value {
                font-size: 0.85rem;
                font-weight: 500;
                color: #1f2937;
            }

            .type-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
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
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Notes Form */
            .notes-form {
                padding: 0 25px 25px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 10px;
                font-weight: 600;
                font-size: 0.9rem;
                color: #1f2937;
            }

            .textarea-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 14px;
                top: 18px;
                color: #9ca3af;
                font-size: 0.9rem;
                pointer-events: none;
            }

            .form-control {
                width: 100%;
                padding: 14px 16px 14px 42px;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                font-size: 0.875rem;
                transition: all 0.3s ease;
                background: #f9fafb;
                resize: vertical;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-hint {
                font-size: 0.7rem;
                color: #9ca3af;
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .form-hint i {
                color: #7c3aed;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.75rem;
                margin-top: 5px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            /* Tips Section */
            .tips-section {
                background: #fef3c7;
                border-radius: 16px;
                padding: 16px 20px;
                margin-bottom: 25px;
            }

            .tips-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 12px;
            }

            .tips-header i {
                font-size: 1.1rem;
                color: #d97706;
            }

            .tips-header h4 {
                margin: 0;
                font-size: 0.85rem;
                color: #92400e;
            }

            .tips-list {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .tip-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.75rem;
                color: #b45309;
            }

            .tip-item i {
                font-size: 0.7rem;
                color: #10b981;
            }

            /* Form Actions */
            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 15px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-cancel,
            .btn-submit {
                padding: 10px 24px;
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

            .btn-loading .btn-text {
                display: none;
            }

            .btn-loading .btn-spinner {
                display: inline-block;
            }

            .btn-spinner i {
                color: white;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .edit-notes-container {
                    padding: 15px;
                }

                .session-info-grid {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }

                .tips-list {
                    grid-template-columns: 1fr;
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
            body.rtl .input-icon {
                left: auto;
                right: 14px;
            }

            body.rtl .form-control {
                padding: 14px 42px 14px 16px;
            }

            body.rtl .tip-item {
                flex-direction: row;
            }

            body.rtl .form-actions {
                flex-direction: row-reverse;
            }

            @media (max-width: 768px) {
                body.rtl .form-actions {
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const form = document.getElementById('notesForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const notes = document.getElementById('notes').value;

                    // Show loading state
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;

                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('notes', notes);

                    try {
                        const response = await fetch('{{ route("specialist.session-notes.update", $session->id) }}', {
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
        </script>
    @endpush
@endsection