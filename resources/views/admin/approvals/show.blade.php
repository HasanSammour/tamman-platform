{{-- resources/views/admin/approvals/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Application Details') . ' - ' . __('Tamman'))

@section('page-title', __('Application Details'))

@section('content')
    <div class="application-details-container">
        <!-- Back Button Row -->
        <div class="top-bar">
            <a href="{{ route('admin.approvals', ['status' => 'pending']) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Applications') }}
            </a>
            <div class="application-id">
                <span class="id-label">{{ __('Application ID') }}</span>
                <span class="id-value">#{{ $application->id }}</span>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="status-banner {{ $application->application_status }}">
            <i
                class="fas {{ $application->application_status == 'pending' ? 'fa-clock' : ($application->application_status == 'approved' ? 'fa-check-circle' : 'fa-times-circle') }}"></i>
            <span>{{ __('Status') }}:
                @if($application->application_status == 'pending')
                    {{ __('Pending Review') }}
                @elseif($application->application_status == 'approved')
                    {{ __('Approved') }}
                @else
                    {{ __('Rejected') }}
                @endif
            </span>
            @if($application->application_status == 'pending')
                <span class="badge-pending">{{ __('Awaiting Review') }}</span>
            @elseif($application->application_status == 'approved')
                <span class="badge-approved">{{ __('Verified') }}</span>
            @else
                <span class="badge-rejected">{{ __('Not Approved') }}</span>
            @endif
        </div>

        <!-- Two Column Layout -->
        <div class="details-grid">
            <!-- Left Column - Applicant Info -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> {{ __('Applicant Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="applicant-profile">
                        <div class="applicant-avatar">
                            @php
                                $profileImage = $application->user->getProfileImageUrl();
                                $applicantInitial = mb_substr($application->user->name, 0, 1, 'UTF-8');
                            @endphp
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $application->user->name }}">
                            @else
                                <div class="avatar-placeholder">{{ $applicantInitial }}</div>
                            @endif
                        </div>
                        <div class="applicant-basic-info">
                            <h2>{{ $application->user->name }}</h2>
                            <p><i class="fas fa-envelope"></i> {{ $application->user->email }}</p>
                            <p><i class="fas fa-phone"></i> {{ $application->user->phone ?? '—' }}</p>
                            <p><i class="fas fa-calendar-alt"></i> {{ __('Applied') }}:
                                {{ $application->created_at->translatedFormat('F d, Y') . ' ' . __('at') . ' ' . $application->created_at->format('h:i') . ' ' . ($application->created_at->format('A') == 'AM' ? __('AM') : __('PM')) }}
                            </p>
                        </div>
                    </div>
                    <div class="info-divider"></div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Gender') }}</span>
                        <span
                            class="info-value">{{ $application->user->gender ? __(ucfirst($application->user->gender)) : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Date of Birth') }}</span>
                        <span
                            class="info-value">{{ $application->user->date_of_birth ? $application->user->date_of_birth->translatedFormat('F d, Y') : '—' }}</span>
                    </div>
                    @if($application->user->date_of_birth)
                        <div class="info-row">
                            <span class="info-label">{{ __('Age') }}</span>
                            <span class="info-value">{{ $application->user->date_of_birth->age }} {{ __('years') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Professional Info -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-briefcase"></i> {{ __('Professional Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('Specialization') }}</span>
                        <span class="info-value"><span
                                class="badge-specialization">{{ $application->specialization }}</span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('License Number') }}</span>
                        <span class="info-value">{{ $application->license_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Years of Experience') }}</span>
                        <span class="info-value">{{ $application->experience_years ?? 0 }} {{ __('years') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Consultation Fee') }}</span>
                        <span class="info-value">${{ number_format($application->consultation_fee ?? 0, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('Languages') }}</span>
                        <span class="info-value">{{ $application->languages ?? '—' }}</span>
                    </div>
                    <div class="info-row-full">
                        <span class="info-label-full">{{ __('Qualifications') }}</span>
                        <div class="info-value-full">{{ $application->qualifications ?? '—' }}</div>
                    </div>
                    <div class="info-row-full">
                        <span class="info-label-full">{{ __('Professional Bio') }}</span>
                        <div class="info-value-full bio-text">{{ $application->bio ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="documents-card">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> {{ __('Uploaded Documents') }}</h3>
                <span class="documents-hint">{{ __('Click to preview document') }}</span>
            </div>
            <div class="card-body">
                <div class="documents-grid">
                    <!-- Professional License -->
                    <div class="document-item" id="licenseDocument">
                        <div class="document-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="document-info">
                            <h4>{{ __('Professional License') }}</h4>
                            @if($licenseInfo['has_file'])
                                <p class="document-filename">{{ $licenseInfo['filename'] }}</p>
                                <div class="document-actions">
                                    <button class="btn-preview" onclick="previewDocument('license')">
                                        <i class="fas fa-eye"></i> {{ __('Preview') }}
                                    </button>
                                    <button class="btn-download" onclick="downloadDocument('license')">
                                        <i class="fas fa-download"></i> {{ __('Download') }}
                                    </button>
                                </div>
                            @else
                                <p class="document-empty">{{ __('No license uploaded') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Professional Certificate -->
                    <div class="document-item" id="certificateDocument">
                        <div class="document-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="document-info">
                            <h4>{{ __('Professional Certificate') }}</h4>
                            @if($certificateInfo['has_file'])
                                <p class="document-filename">{{ $certificateInfo['filename'] }}</p>
                                <div class="document-actions">
                                    <button class="btn-preview" onclick="previewDocument('certificate')">
                                        <i class="fas fa-eye"></i> {{ __('Preview') }}
                                    </button>
                                    <button class="btn-download" onclick="downloadDocument('certificate')">
                                        <i class="fas fa-download"></i> {{ __('Download') }}
                                    </button>
                                </div>
                            @else
                                <p class="document-empty">{{ __('No certificate uploaded') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Notes Section -->
        @if($application->application_status != 'pending' && $application->application_notes)
            <div class="notes-card">
                <div class="card-header">
                    <h3><i class="fas fa-sticky-note"></i> {{ __('Application History & Notes') }}</h3>
                </div>
                <div class="card-body">
                    <div class="notes-content">
                        <i class="fas fa-quote-right"></i>
                        <div class="notes-text">
                            @php
                                $notesLines = explode("\n", $application->application_notes);
                            @endphp
                            @foreach($notesLines as $line)
                                @if(trim($line))
                                    <div class="note-line">{{ $line }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Application Timeline -->
        <div class="timeline-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> {{ __('Application Timeline') }}</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon submitted">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>{{ __('Application Submitted') }}</h4>
                            <p>
                                {{ $application->created_at->translatedFormat('F d, Y') . ' ' . __('at') . ' ' . $application->created_at->format('h:i A') }}
                            </p>
                        </div>
                    </div>

                    @if($application->application_status != 'pending')
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $application->application_status }}">
                                <i
                                    class="fas {{ $application->application_status == 'approved' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>{{ $application->application_status == 'approved' ? __('Application Approved') : __('Application Rejected') }}
                                </h4>
                                <p>{{ $application->updated_at->translatedFormat('F d, Y \a\t h:i A') }}</p>
                                @if($application->verified_at && $application->application_status == 'approved')
                                    <span class="verified-badge">{{ __('Verified by Admin') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if($canApprove || $canReject || $canRequestInfo)
            <div class="action-buttons-container">
                <div class="action-buttons-title">
                    <i class="fas fa-gavel"></i> {{ __('Review Application') }}
                </div>
                <div class="action-buttons-grid">
                    @if($canApprove)
                        <button class="action-btn approve-btn" onclick="openApproveModal()">
                            <i class="fas fa-check-circle"></i> {{ __('Approve Application') }}
                        </button>
                    @endif
                    @if($canReject)
                        <button class="action-btn reject-btn" onclick="openRejectModal()">
                            <i class="fas fa-times-circle"></i> {{ __('Reject Application') }}
                        </button>
                    @endif
                    @if($canRequestInfo)
                        <button class="action-btn request-btn" onclick="openRequestModal()">
                            <i class="fas fa-question-circle"></i> {{ __('Request More Information') }}
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if(!$canApprove && !$canReject && !$canRequestInfo)
            <div class="processed-message">
                <i class="fas fa-info-circle"></i>
                <span>{{ __('This application has already been processed and cannot be modified.') }}</span>
            </div>
        @endif
    </div>

    <!-- ==================== MODAL 1: APPROVE (WHITE THEME) ==================== -->
    <div id="approveModal" class="modal-overlay action-modal">
        <div class="modal-container approve-modal">
            <div class="modal-header approve-header">
                <div class="modal-title-wrapper">
                    <div class="modal-icon approve-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>{{ __('Approve Application') }}</h3>
                </div>
                <button class="modal-close action-close">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-message">{{ __('Are you sure you want to approve this application?') }}</p>
                <div class="success-info-box">
                    <i class="fas fa-envelope"></i>
                    <span>{{ __('The specialist will receive a welcome email with login instructions.') }}</span>
                </div>
                <div class="form-group">
                    <label for="approveNotes">{{ __('Admin Notes (Optional)') }}</label>
                    <textarea id="approveNotes" class="form-control" rows="3"
                        placeholder="{{ __('Add any internal notes about this approval...') }}"></textarea>
                    <small class="form-hint">{{ __('These notes will be saved in the application history.') }}</small>
                </div>
                <input type="hidden" id="approveId" value="{{ $application->id }}">
            </div>
            <div class="modal-footer approve-footer">
                <button class="btn-cancel-modal action-cancel"> {{ __('Cancel') }}</button>
                <button class="btn-confirm-approve" id="confirmApproveBtn">
                    <span class="btn-text">{{ __('Approve Application') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 2: REJECT (WHITE THEME) ==================== -->
    <div id="rejectModal" class="modal-overlay action-modal">
        <div class="modal-container reject-modal">
            <div class="modal-header reject-header">
                <div class="modal-title-wrapper">
                    <div class="modal-icon reject-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3>{{ __('Reject Application') }}</h3>
                </div>
                <button class="modal-close action-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rejectReason">{{ __('Reason for Rejection') }} <span class="required">*</span></label>
                    <textarea id="rejectReason" class="form-control" rows="4"
                        placeholder="{{ __('Please provide a clear reason for rejection...') }}"></textarea>
                    <small class="form-hint warning-hint">{{ __('This will be sent to the applicant via email.') }}</small>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label for="rejectNotes">{{ __('Admin Notes (Optional)') }}</label>
                    <textarea id="rejectNotes" class="form-control" rows="2"
                        placeholder="{{ __('Add internal notes for admin reference...') }}"></textarea>
                    <small class="form-hint">{{ __('These notes will be saved in the application history.') }}</small>
                </div>
                <input type="hidden" id="rejectId" value="{{ $application->id }}">
            </div>
            <div class="modal-footer reject-footer">
                <button class="btn-cancel-modal action-cancel"> {{ __('Cancel') }}</button>
                <button class="btn-confirm-reject" id="confirmRejectBtn">
                    <span class="btn-text">{{ __('Reject Application') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 3: REQUEST INFO (WHITE THEME) ==================== -->
    <div id="requestModal" class="modal-overlay action-modal">
        <div class="modal-container request-modal">
            <div class="modal-header request-header">
                <div class="modal-title-wrapper">
                    <div class="modal-icon request-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3>{{ __('Request Additional Information') }}</h3>
                </div>
                <button class="modal-close action-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="requestMessage">{{ __('Message to Applicant') }} <span class="required">*</span></label>
                    <textarea id="requestMessage" class="form-control" rows="5"
                        placeholder="{{ __('Please explain what additional information is needed...') }}"></textarea>
                    <small class="form-hint info-hint">{{ __('This will be sent to the applicant via email.') }}</small>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label for="requestNotes">{{ __('Admin Notes (Optional)') }}</label>
                    <textarea id="requestNotes" class="form-control" rows="2"
                        placeholder="{{ __('Add internal notes for admin reference...') }}"></textarea>
                    <small class="form-hint">{{ __('These notes will be saved in the application history.') }}</small>
                </div>
                <input type="hidden" id="requestId" value="{{ $application->id }}">
            </div>
            <div class="modal-footer request-footer">
                <button class="btn-cancel-modal action-cancel"> {{ __('Cancel') }}</button>
                <button class="btn-confirm-request" id="confirmRequestBtn">
                    <span class="btn-text">{{ __('Send Request') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== PREVIEW MODAL (BLACK THEME - KEPT AS IS) ==================== -->
    <div id="previewModal" class="modal-overlay preview-modal">
        <div class="modal-container modal-preview">
            <div class="modal-header preview-header">
                <div class="preview-title-wrapper">
                    <i class="fas fa-file-image" id="previewIcon"></i>
                    <h3 id="previewTitle">{{ __('Document Preview') }}</h3>
                </div>
                <div class="zoom-controls">
                    <button class="zoom-btn" id="zoomOutBtn" title="{{ __('Zoom Out') }}">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <span id="zoomLevel" class="zoom-level">100%</span>
                    <button class="zoom-btn" id="zoomInBtn" title="{{ __('Zoom In') }}">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button class="zoom-btn" id="zoomResetBtn" title="{{ __('Reset Zoom') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="zoom-btn" id="zoomFitBtn" title="{{ __('Fit to Screen') }}">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
                <button class="modal-close preview-close">&times;</button>
            </div>
            <div class="modal-body preview-body" id="previewBody">
                <div id="previewContent" class="preview-content">
                    <div class="loading-preview">
                        <div class="spinner"></div>
                        <p>{{ __('Loading preview...') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer preview-footer">
                <div class="document-info-preview">
                    <span id="previewFilename" class="preview-filename"></span>
                </div>
                <div class="preview-actions">
                    <button class="btn-download-preview" id="previewDownloadBtn">
                        <i class="fas fa-download"></i> {{ __('Download') }}
                    </button>
                    <button class="btn-close-preview" id="previewCloseBtn">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .application-details-container {
                max-width: 1200px;
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

            .application-id {
                background: #f3f4f6;
                padding: 6px 16px;
                border-radius: 40px;
            }

            .id-label {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .id-value {
                font-size: 0.85rem;
                font-weight: 600;
                color: #1f2937;
                margin-left: 5px;
            }

            /* Status Banner */
            .status-banner {
                border-radius: 16px;
                padding: 15px 20px;
                margin-bottom: 25px;
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .status-banner.pending {
                background: #fef3c7;
                color: #d97706;
            }

            .status-banner.approved {
                background: #d1fae5;
                color: #065f46;
            }

            .status-banner.rejected {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-pending,
            .badge-approved,
            .badge-rejected {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                background: rgba(0, 0, 0, 0.1);
            }

            /* Details Grid */
            .details-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 25px;
            }

            .info-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
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

            .card-body {
                padding: 20px;
            }

            /* Applicant Profile */
            .applicant-profile {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
            }

            .applicant-avatar img,
            .avatar-placeholder {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                font-weight: 600;
                color: white;
            }

            .applicant-basic-info h2 {
                font-size: 1.2rem;
                margin: 0 0 8px;
                color: #1f2937;
            }

            .applicant-basic-info p {
                margin: 5px 0;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .applicant-basic-info p i {
                width: 20px;
                color: #7c3aed;
            }

            .info-divider {
                height: 1px;
                background: #f0f0f0;
                margin: 15px 0;
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

            .info-row-full {
                display: flex;
                flex-direction: column;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .info-row-full:last-child {
                border-bottom: none;
            }

            .info-label-full {
                font-weight: 600;
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .info-value-full {
                font-size: 0.8rem;
                color: #1f2937;
                line-height: 1.5;
                word-wrap: break-word;
                white-space: normal;
            }

            .bio-text {
                background: #f9fafb;
                padding: 12px;
                border-radius: 12px;
                line-height: 1.6;
            }

            .badge-specialization {
                background: #ede9fe;
                color: #7c3aed;
                padding: 4px 12px;
                border-radius: 20px;
                display: inline-block;
            }

            /* Documents */
            .documents-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .documents-hint {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .documents-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .document-item {
                display: flex;
                gap: 15px;
                padding: 15px;
                background: #f9fafb;
                border-radius: 16px;
                transition: all 0.3s ease;
            }

            .document-item:hover {
                background: #f3f4f6;
                transform: translateY(-2px);
            }

            .document-icon i {
                font-size: 2rem;
                color: #7c3aed;
            }

            .document-info {
                flex: 1;
            }

            .document-info h4 {
                margin: 0 0 5px;
                font-size: 0.85rem;
                color: #1f2937;
            }

            .document-filename {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0 0 8px;
                word-break: break-all;
            }

            .document-actions {
                display: flex;
                gap: 10px;
            }

            .btn-preview,
            .btn-download {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .btn-preview {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-preview:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            .btn-download {
                background: #e5e7eb;
                color: #374151;
            }

            .btn-download:hover {
                background: #d1d5db;
                transform: translateY(-2px);
            }

            .document-empty {
                font-size: 0.7rem;
                color: #9ca3af;
                margin: 0;
            }

            /* Notes Card */
            .notes-card {
                background: #fffbeb;
                border-radius: 20px;
                overflow: hidden;
                margin-bottom: 25px;
                border-left: 4px solid #f59e0b;
            }

            .notes-card .card-header {
                background: #fef3c7;
                border-bottom-color: #fde68a;
            }

            .notes-content {
                display: flex;
                gap: 15px;
            }

            .notes-content i {
                font-size: 1.2rem;
                color: #d97706;
                flex-shrink: 0;
            }

            .notes-text {
                flex: 1;
                font-size: 0.8rem;
                color: #92400e;
                line-height: 1.6;
            }

            .note-line {
                margin-bottom: 8px;
                padding-bottom: 8px;
                border-bottom: 1px dashed #fde68a;
            }

            .note-line:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            /* Timeline */
            .timeline-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .timeline {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .timeline-item {
                display: flex;
                gap: 20px;
            }

            .timeline-icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .timeline-icon.submitted {
                background: #ede9fe;
                color: #7c3aed;
            }

            .timeline-icon.approved {
                background: #d1fae5;
                color: #059669;
            }

            .timeline-icon.rejected {
                background: #fee2e2;
                color: #dc2626;
            }

            .timeline-content h4 {
                font-size: 0.9rem;
                margin: 0 0 5px;
                color: #1f2937;
            }

            .timeline-content p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .verified-badge {
                display: inline-block;
                background: #d1fae5;
                color: #065f46;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
                margin-top: 5px;
            }

            /* Action Buttons Container */
            .action-buttons-container {
                background: white;
                border-radius: 20px;
                padding: 20px 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .action-buttons-title {
                font-size: 0.9rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #f0f0f0;
            }

            .action-buttons-title i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .action-buttons-grid {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .action-btn {
                padding: 12px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .approve-btn {
                background: #10b981;
                color: white;
            }

            .approve-btn:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            .reject-btn {
                background: #ef4444;
                color: white;
            }

            .reject-btn:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            .request-btn {
                background: #f59e0b;
                color: white;
            }

            .request-btn:hover {
                background: #d97706;
                transform: translateY(-2px);
            }

            .processed-message {
                background: #f3f4f6;
                border-radius: 16px;
                padding: 15px 20px;
                display: flex;
                align-items: center;
                gap: 12px;
                color: #6b7280;
                font-size: 0.85rem;
            }

            /* ==================== ACTION MODALS (WHITE THEME) - NEW STYLES ==================== */
            .modal-overlay.action-modal {
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
            }

            .modal-container.approve-modal,
            .modal-container.reject-modal,
            .modal-container.request-modal {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                transform: scale(0.9);
                transition: transform 0.3s ease;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                overflow: hidden;
            }

            .modal-overlay.action-modal.active .modal-container {
                transform: scale(1);
            }

            /* Modal Headers */
            .modal-header.approve-header,
            .modal-header.reject-header,
            .modal-header.request-header {
                padding: 20px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f0f0f0;
            }

            .approve-header {
                background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            }

            .reject-header {
                background: linear-gradient(135deg, #fef2f2, #fef2f2);
            }

            .request-header {
                background: linear-gradient(135deg, #fffbeb, #fef3c7);
            }

            .modal-title-wrapper {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .modal-icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .approve-icon {
                background: #d1fae5;
                color: #10b981;
            }

            .reject-icon {
                background: #fee2e2;
                color: #ef4444;
            }

            .request-icon {
                background: #fef3c7;
                color: #f59e0b;
            }

            .modal-icon i {
                font-size: 1.3rem;
            }

            .modal-title-wrapper h3 {
                margin: 0;
                font-size: 1.2rem;
                font-weight: 600;
                color: #1f2937;
            }

            .modal-close.action-close {
                background: #f3f4f6;
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                cursor: pointer;
                color: #6b7280;
                font-size: 1.2rem;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modal-close.action-close:hover {
                background: #e5e7eb;
                transform: rotate(90deg);
            }

            /* Modal Body */
            .modal-body {
                padding: 24px;
            }

            .modal-message {
                font-size: 0.9rem;
                color: #374151;
                margin-bottom: 16px;
            }

            .success-info-box {
                background: #f0fdf4;
                border-left: 4px solid #10b981;
                padding: 12px 16px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
            }

            .success-info-box i {
                color: #10b981;
                font-size: 1rem;
            }

            .success-info-box span {
                font-size: 0.8rem;
                color: #065f46;
            }

            .warning-hint {
                color: #dc2626;
            }

            .info-hint {
                color: #d97706;
            }

            .form-group {
                margin-bottom: 0;
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
                resize: vertical;
                background: #f9fafb;
                transition: all 0.2s ease;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-hint {
                font-size: 0.65rem;
                color: #9ca3af;
                margin-top: 5px;
                display: block;
            }

            .required {
                color: #ef4444;
            }

            /* Modal Footers */
            .modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .approve-footer {
                background: #f9fafb;
            }

            .reject-footer {
                background: #f9fafb;
            }

            .request-footer {
                background: #f9fafb;
            }

            .btn-cancel-modal.action-cancel {
                background: white;
                border: 1px solid #e5e7eb;
                padding: 8px 20px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 500;
                color: #6b7280;
                transition: all 0.2s ease;
            }

            .btn-cancel-modal.action-cancel:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .btn-confirm-approve {
                background: #10b981;
                color: white;
                border: none;
                padding: 8px 24px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 600;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-confirm-approve:hover:not(:disabled) {
                background: #059669;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .btn-confirm-reject {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 24px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 600;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-confirm-reject:hover:not(:disabled) {
                background: #dc2626;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }

            .btn-confirm-request {
                background: #f59e0b;
                color: white;
                border: none;
                padding: 8px 24px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 600;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-confirm-request:hover:not(:disabled) {
                background: #d97706;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            }

            .btn-confirm-approve:disabled,
            .btn-confirm-reject:disabled,
            .btn-confirm-request:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            .btn-spinner {
                display: none;
            }

            /* ==================== PREVIEW MODAL (BLACK THEME) - KEPT AS IS ==================== */
            .modal-overlay.preview-modal {
                background: rgba(0, 0, 0, 0.9);
                backdrop-filter: blur(8px);
            }

            .modal-container.modal-preview {
                background: #1e1e2e;
                border-radius: 20px;
                width: 90vw;
                max-width: 1300px;
                height: 85vh;
                max-height: 85vh;
                display: flex;
                flex-direction: column;
                transform: scale(0.95);
                transition: transform 0.3s ease;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            .modal-overlay.preview-modal.active .modal-container.modal-preview {
                transform: scale(1);
            }

            .preview-header {
                padding: 16px 24px;
                background: #2d2d3d;
                border-radius: 20px 20px 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                border-bottom: 1px solid #3d3d4d;
            }

            .preview-title-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .preview-title-wrapper i {
                font-size: 1.3rem;
                color: #7c3aed;
            }

            .preview-title-wrapper h3 {
                margin: 0;
                font-size: 1.1rem;
                color: white;
                font-weight: 500;
            }

            .zoom-controls {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #3d3d4d;
                padding: 5px 15px;
                border-radius: 40px;
            }

            .zoom-btn {
                background: #4d4d5d;
                border: none;
                width: 34px;
                height: 34px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: white;
            }

            .zoom-btn:hover {
                background: #7c3aed;
                transform: scale(1.05);
            }

            .zoom-level {
                font-size: 0.8rem;
                font-weight: 600;
                color: white;
                min-width: 55px;
                text-align: center;
            }

            .preview-close {
                background: #4d4d5d;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                cursor: pointer;
                color: white;
                font-size: 1.2rem;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .preview-close:hover {
                background: #ef4444;
                transform: rotate(90deg);
            }

            .preview-body {
                flex: 1;
                overflow: hidden;
                position: relative;
                background: #1a1a2a;
                cursor: grab;
            }

            .preview-body:active {
                cursor: grabbing;
            }

            .preview-content {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: auto;
                background: #1a1a2a;
                position: relative;
            }

            .preview-wrapper {
                display: inline-block;
                position: relative;
                transform-origin: center center;
                transition: transform 0.05s linear;
                cursor: grab;
            }

            .preview-wrapper:active {
                cursor: grabbing;
            }

            .preview-content img {
                display: block;
                max-width: none;
                max-height: none;
                width: auto;
                height: auto;
                user-select: none;
                pointer-events: auto;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .preview-content iframe {
                width: 100%;
                height: 100%;
                min-height: 500px;
                border: none;
                background: white;
                border-radius: 8px;
            }

            .loading-preview {
                text-align: center;
                padding: 50px;
            }

            .spinner {
                width: 50px;
                height: 50px;
                border: 3px solid #3d3d4d;
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

            .loading-preview p {
                color: #9ca3af;
            }

            .preview-footer {
                padding: 16px 24px;
                background: #2d2d3d;
                border-radius: 0 0 20px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                border-top: 1px solid #3d3d4d;
            }

            .document-info-preview {
                flex: 1;
            }

            .preview-filename {
                font-size: 0.8rem;
                color: #9ca3af;
                word-break: break-all;
            }

            .preview-actions {
                display: flex;
                gap: 12px;
            }

            .btn-download-preview {
                background: #7c3aed;
                color: white;
                padding: 8px 20px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-download-preview:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-close-preview {
                background: #4d4d5d;
                color: white;
                padding: 8px 24px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-close-preview:hover {
                background: #ef4444;
            }

            .swal2-container {
                z-index: 20000 !important;
            }
            
            .swal2-popup {
                z-index: 20001 !important;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .details-grid {
                    grid-template-columns: 1fr;
                }

                .documents-grid {
                    grid-template-columns: 1fr;
                }

                .action-buttons-grid {
                    flex-direction: column;
                }

                .action-btn {
                    justify-content: center;
                    width: 100%;
                }

                .modal-container.modal-preview {
                    width: 95vw;
                    height: 90vh;
                }
            }

            @media (max-width: 768px) {
                .application-details-container {
                    padding: 15px;
                }

                .top-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .applicant-profile {
                    flex-direction: column;
                    text-align: center;
                }

                .timeline-item {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .action-buttons-container {
                    padding: 15px;
                }

                .preview-header {
                    flex-wrap: wrap;
                }

                .zoom-controls {
                    order: 1;
                    width: 100%;
                    justify-content: center;
                }

                .preview-title-wrapper {
                    order: 0;
                }

                .preview-close {
                    order: 2;
                }

                .preview-content iframe {
                    min-height: 350px;
                }

                .modal-header.approve-header,
                .modal-header.reject-header,
                .modal-header.request-header {
                    flex-wrap: wrap;
                }
            }

            body.rtl .info-row {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .info-row {
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Store document URLs for download
            let licenseUrl = '';
            let certificateUrl = '';
            let currentPreviewUrl = '';
            let currentPreviewFilename = '';

            // Preview modal variables for drag and zoom
            let currentZoom = 1;
            let isDragging = false;
            let startX, startY, startTranslateX = 0, startTranslateY = 0;
            let currentTranslateX = 0, currentTranslateY = 0;
            let previewWrapper = null;
            let previewImage = null;
            let isIframe = false;

            @if($licenseInfo['has_file'])
                licenseUrl = '{{ $licenseInfo['url'] }}';
            @endif

            @if($certificateInfo['has_file'])
                certificateUrl = '{{ $certificateInfo['url'] }}';
            @endif

                // Force download using fetch API
                function forceDownload(url, filename) {
                    fetch(url, { mode: 'cors', credentials: 'same-origin' })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.blob();
                        })
                        .then(blob => {
                            const blobUrl = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = blobUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            window.URL.revokeObjectURL(blobUrl);
                        })
                        .catch(error => {
                            console.error('Download failed:', error);
                            window.open(url, '_blank');
                        });
                }

            // Update zoom and transform
            function updateZoomAndTransform() {
                if (!previewWrapper) return;
                previewWrapper.style.transform = `translate(${currentTranslateX}px, ${currentTranslateY}px) scale(${currentZoom})`;
                document.getElementById('zoomLevel').innerText = Math.round(currentZoom * 100) + '%';
            }

            // Reset zoom and position
            function resetZoomAndPosition() {
                currentZoom = 1;
                currentTranslateX = 0;
                currentTranslateY = 0;
                if (previewWrapper) {
                    previewWrapper.style.transform = `translate(0px, 0px) scale(1)`;
                }
                document.getElementById('zoomLevel').innerText = '100%';
            }

            // Fit to screen
            function fitToScreen() {
                if (!previewImage && !isIframe) return;

                const container = document.querySelector('.preview-body');
                const containerRect = container.getBoundingClientRect();

                let contentWidth, contentHeight;

                if (isIframe) {
                    contentWidth = previewWrapper ? previewWrapper.clientWidth : 800;
                    contentHeight = previewWrapper ? previewWrapper.clientHeight : 600;
                } else if (previewImage) {
                    contentWidth = previewImage.naturalWidth;
                    contentHeight = previewImage.naturalHeight;
                } else {
                    return;
                }

                if (contentWidth > 0 && contentHeight > 0) {
                    const widthRatio = (containerRect.width - 60) / contentWidth;
                    const heightRatio = (containerRect.height - 60) / contentHeight;
                    currentZoom = Math.min(widthRatio, heightRatio, 1.5);
                    currentZoom = Math.max(currentZoom, 0.3);
                    currentTranslateX = 0;
                    currentTranslateY = 0;
                    updateZoomAndTransform();
                }
            }

            // Zoom in
            function zoomIn() {
                currentZoom = Math.min(currentZoom + 0.15, 3);
                updateZoomAndTransform();
            }

            // Zoom out
            function zoomOut() {
                currentZoom = Math.max(currentZoom - 0.15, 0.3);
                updateZoomAndTransform();
            }

            // Mouse wheel zoom handler
            function handleWheelZoom(e) {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.1 : 0.1;
                const newZoom = Math.min(Math.max(currentZoom + delta, 0.3), 3);

                if (newZoom !== currentZoom) {
                    currentZoom = newZoom;
                    updateZoomAndTransform();
                }
            }

            // Drag handlers
            function startDrag(e) {
                if (currentZoom <= 1) return;
                isDragging = true;
                startX = e.clientX - currentTranslateX;
                startY = e.clientY - currentTranslateY;
                document.querySelector('.preview-body').style.cursor = 'grabbing';
                e.preventDefault();
            }

            function onDrag(e) {
                if (!isDragging) return;
                currentTranslateX = e.clientX - startX;
                currentTranslateY = e.clientY - startY;
                updateZoomAndTransform();
                e.preventDefault();
            }

            function stopDrag() {
                isDragging = false;
                document.querySelector('.preview-body').style.cursor = 'grab';
            }

            // Initialize drag and zoom for preview
            function initDragAndZoom() {
                const previewBody = document.querySelector('.preview-body');
                if (!previewBody) return;

                previewBody.style.cursor = 'grab';
                previewBody.addEventListener('mousedown', startDrag);
                window.addEventListener('mousemove', onDrag);
                window.addEventListener('mouseup', stopDrag);
                previewBody.addEventListener('wheel', handleWheelZoom, { passive: false });

                // Zoom buttons
                document.getElementById('zoomInBtn')?.addEventListener('click', zoomIn);
                document.getElementById('zoomOutBtn')?.addEventListener('click', zoomOut);
                document.getElementById('zoomResetBtn')?.addEventListener('click', resetZoomAndPosition);
                document.getElementById('zoomFitBtn')?.addEventListener('click', fitToScreen);
            }

            // Cleanup drag and zoom
            function cleanupDragAndZoom() {
                const previewBody = document.querySelector('.preview-body');
                if (previewBody) {
                    previewBody.removeEventListener('mousedown', startDrag);
                    previewBody.removeEventListener('wheel', handleWheelZoom);
                }
                window.removeEventListener('mousemove', onDrag);
                window.removeEventListener('mouseup', stopDrag);
                document.getElementById('zoomInBtn')?.removeEventListener('click', zoomIn);
                document.getElementById('zoomOutBtn')?.removeEventListener('click', zoomOut);
                document.getElementById('zoomResetBtn')?.removeEventListener('click', resetZoomAndPosition);
                document.getElementById('zoomFitBtn')?.removeEventListener('click', fitToScreen);
            }

            // Document Preview with drag-to-move and scroll-to-zoom
            function previewDocument(type) {
                const modal = document.getElementById('previewModal');
                const title = document.getElementById('previewTitle');
                const icon = document.getElementById('previewIcon');
                const filenameSpan = document.getElementById('previewFilename');
                const content = document.getElementById('previewContent');

                let url = '';
                let docName = '';

                @if($certificateInfo['has_file'] && $licenseInfo['has_file'])
                    if (type === 'certificate') {
                        url = '{{ $certificateInfo['url'] }}';
                        docName = '{{ $certificateInfo['filename'] }}';
                        title.innerHTML = '{{ __("Certificate Preview") }}';
                        icon.className = 'fas fa-certificate';
                    } else {
                        url = '{{ $licenseInfo['url'] }}';
                        docName = '{{ $licenseInfo['filename'] }}';
                        title.innerHTML = '{{ __("License Preview") }}';
                        icon.className = 'fas fa-id-card';
                    }
                @elseif($certificateInfo['has_file'])
                    url = '{{ $certificateInfo['url'] }}';
                    docName = '{{ $certificateInfo['filename'] }}';
                    title.innerHTML = '{{ __("Certificate Preview") }}';
                    icon.className = 'fas fa-certificate';
                @elseif($licenseInfo['has_file'])
                    url = '{{ $licenseInfo['url'] }}';
                    docName = '{{ $licenseInfo['filename'] }}';
                    title.innerHTML = '{{ __("License Preview") }}';
                    icon.className = 'fas fa-id-card';
                @else
                    return;
                @endif

                currentPreviewUrl = url;
                currentPreviewFilename = docName;
                filenameSpan.textContent = docName;

                // Cleanup previous drag handlers
                cleanupDragAndZoom();

                // Reset zoom and position
                currentZoom = 1;
                currentTranslateX = 0;
                currentTranslateY = 0;
                isDragging = false;

                const ext = url.split('.').pop().toLowerCase();

                if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif') {
                    isIframe = false;
                    content.innerHTML = `<div class="preview-wrapper" style="display: inline-block; transform: translate(0px, 0px) scale(1);"><img src="${url}" alt="${docName}" style="display: block; max-width: none; max-height: none; width: auto; height: auto;"></div>`;
                    previewWrapper = content.querySelector('.preview-wrapper');
                    previewImage = previewWrapper?.querySelector('img');

                    if (previewImage) {
                        previewImage.onload = function () {
                            fitToScreen();
                        };
                    }
                } else if (ext === 'pdf') {
                    isIframe = true;
                    content.innerHTML = `<div class="preview-wrapper" style="display: block; transform: translate(0px, 0px) scale(1);"><iframe src="${url}" width="100%" height="100%" style="min-height: 55vh; border: none;"></iframe></div>`;
                    previewWrapper = content.querySelector('.preview-wrapper');
                    setTimeout(() => fitToScreen(), 100);
                } else {
                    isIframe = false;
                    content.innerHTML = `<div class="loading-preview"><i class="fas fa-file" style="font-size:3rem;color:#7c3aed;"></i><p>{{ __("Preview not available for this file type.") }}</p><a href="${url}" target="_blank" class="btn-download-preview" style="display:inline-block;margin-top:15px;">{{ __("Open File") }}</a></div>`;
                    previewWrapper = null;
                    previewImage = null;
                }

                // Reinitialize drag and zoom
                initDragAndZoom();

                modal.classList.add('active');
            }

            // Download Document
            function downloadDocument(type) {
                let url = '';
                let filename = '';

                @if($licenseInfo['has_file'])
                    if (type === 'license') {
                        url = '{{ $licenseInfo['url'] }}';
                        filename = '{{ $licenseInfo['filename'] }}';
                    }
                @endif

                @if($certificateInfo['has_file'])
                    if (type === 'certificate') {
                        url = '{{ $certificateInfo['url'] }}';
                        filename = '{{ $certificateInfo['filename'] }}';
                    }
                @endif

                                        if (url) {
                    forceDownload(url, filename);
                }
            }

            // Preview Modal Download Button
            const previewDownloadBtn = document.getElementById('previewDownloadBtn');
            if (previewDownloadBtn) {
                previewDownloadBtn.addEventListener('click', function () {
                    if (currentPreviewUrl && currentPreviewFilename) {
                        forceDownload(currentPreviewUrl, currentPreviewFilename);
                    }
                });
            }

            // Close preview modal
            function closePreviewModal() {
                const modal = document.getElementById('previewModal');
                modal.classList.remove('active');
                cleanupDragAndZoom();
                currentZoom = 1;
                currentTranslateX = 0;
                currentTranslateY = 0;
                isDragging = false;
            }

            document.getElementById('previewModalClose')?.addEventListener('click', closePreviewModal);
            document.getElementById('previewCloseBtn')?.addEventListener('click', closePreviewModal);

            document.getElementById('previewModal')?.addEventListener('click', (e) => {
                if (e.target === e.currentTarget) {
                    closePreviewModal();
                }
            });

            // Approve Modal
            function openApproveModal() {
                document.getElementById('approveNotes').value = '';
                document.getElementById('approveModal').classList.add('active');
            }

            document.getElementById('confirmApproveBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('approveId').value;
                const notes = document.getElementById('approveNotes').value;
                const btn = document.getElementById('confirmApproveBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                btn.disabled = true;

                try {
                    const res = await fetch(`/admin/approvals/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ notes: notes })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Approved") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        document.getElementById('approveModal').classList.remove('active');
                        setTimeout(() => window.location.href = '{{ route("admin.approvals", ["status" => "pending"]) }}', 2000);
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    btn.disabled = false;
                }
            });

            // Reject Modal
            function openRejectModal() {
                document.getElementById('rejectReason').value = '';
                document.getElementById('rejectNotes').value = '';
                document.getElementById('rejectModal').classList.add('active');
            }

            document.getElementById('confirmRejectBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('rejectId').value;
                const reason = document.getElementById('rejectReason').value;
                const notes = document.getElementById('rejectNotes').value;

                if (!reason) {
                    Swal.fire({ icon: 'warning', title: '{{ __("Missing Reason") }}', text: '{{ __("Please provide a reason for rejection.") }}' });
                    return;
                }

                const btn = document.getElementById('confirmRejectBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                btn.disabled = true;

                try {
                    const res = await fetch(`/admin/approvals/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ reason: reason, notes: notes })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Rejected") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        document.getElementById('rejectModal').classList.remove('active');
                        setTimeout(() => window.location.href = '{{ route("admin.approvals", ["status" => "pending"]) }}', 2000);
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    btn.disabled = false;
                }
            });

            // Request Info Modal
            function openRequestModal() {
                document.getElementById('requestMessage').value = '';
                document.getElementById('requestNotes').value = '';
                document.getElementById('requestModal').classList.add('active');
            }

            document.getElementById('confirmRequestBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('requestId').value;
                const message = document.getElementById('requestMessage').value;
                const notes = document.getElementById('requestNotes').value;

                if (!message) {
                    Swal.fire({ icon: 'warning', title: '{{ __("Missing Message") }}', text: '{{ __("Please provide a message for the applicant.") }}' });
                    return;
                }

                const btn = document.getElementById('confirmRequestBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                btn.disabled = true;

                try {
                    const res = await fetch(`/admin/approvals/${id}/request-info`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ message: message, notes: notes })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Request Sent") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        document.getElementById('requestModal').classList.remove('active');
                        location.reload();
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    btn.disabled = false;
                }
            });

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.modal-overlay').forEach(modal => modal.classList.remove('active'));
                });
            });

            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                        if (modal.id === 'previewModal') {
                            closePreviewModal();
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection