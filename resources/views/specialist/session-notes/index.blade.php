{{-- resources/views/specialist/session-notes/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Session Notes') . ' - ' . __('Tamman'))

@section('page-title', __('Session Notes'))

@section('content')
    <div class="session-notes-container">

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon green">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['with_notes']) }}</h3>
                    <p>{{ __('With Notes') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon orange">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['without_notes']) }}</h3>
                    <p>{{ __('Missing Notes') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon blue">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['completed_sessions']) }}</h3>
                    <p>{{ __('Completed') }}</p>
                </div>
            </div>
        </div>

        <!-- Modern Filters Bar -->
        <div class="filters-card">
            <div class="filters-header">
                <i class="fas fa-sliders-h"></i>
                <span>{{ __('Filter Sessions') }}</span>
            </div>

            <div class="filters-grid">
                <!-- Row 1 -->
                <div class="filter-field">
                    <label><i class="fas fa-user"></i> {{ __('Patient') }}</label>
                    <select id="patientFilter">
                        <option value="all">{{ __('All Patients') }}</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label><i class="fas fa-notes-medical"></i> {{ __('Notes Status') }}</label>
                    <select id="notesStatusFilter">
                        <option value="all">{{ __('All Sessions') }}</option>
                        <option value="has_notes">{{ __('With Notes') }}</option>
                        <option value="no_notes">{{ __('Missing Notes') }}</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label><i class="fas fa-flag-checkered"></i> {{ __('Session Status') }}</label>
                    <select id="sessionStatusFilter">
                        <option value="all">{{ __('All Status') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="scheduled">{{ __('Scheduled') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Row 2 -->
                <div class="filter-field date-range-field">
                    <label><i class="fas fa-calendar-alt"></i> {{ __('Date Range') }}</label>
                    <div class="date-range-wrapper">
                        <div class="date-input-group">
                            <span class="date-label">{{ __('From') }}</span>
                            <input type="date" id="dateFrom" placeholder="{{ __('From date') }}">
                        </div>
                        <span class="date-arrow">
                            @if(app()->getLocale() === 'ar')
                                <i class="fas fa-arrow-left"></i>
                            @else
                                <i class="fas fa-arrow-right"></i>
                            @endif
                        </span>
                        <div class="date-input-group">
                            <span class="date-label">{{ __('To') }}</span>
                            <input type="date" id="dateTo" placeholder="{{ __('To date') }}">
                        </div>
                    </div>
                </div>

                <div class="filter-field search-field">
                    <label><i class="fas fa-search"></i> {{ __('Search') }}</label>
                    <input type="text" id="searchInput" placeholder="{{ __('Search by patient name...') }}">
                </div>

                <div class="filter-field reset-field">
                    <button class="btn-reset" id="resetFilters">
                        <i class="fas fa-undo-alt"></i> {{ __('Reset All Filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Sessions Table (Desktop) / Cards (Mobile) -->
        <div class="table-card">
            <!-- Desktop Table View -->
            <div class="desktop-view">
                <div class="table-responsive">
                    <table class="sessions-table" id="sessionsTable">
                        <thead>
                            <tr>
                                <th data-sort="session_datetime" class="sortable">{{ __('Date & Time') }} <i
                                        class="fas fa-sort"></i></th>
                                <th data-sort="patient_name" class="sortable">{{ __('Patient') }} <i
                                        class="fas fa-sort"></i></th>
                                <th data-sort="session_type">{{ __('Type') }}</th>
                                <th data-sort="status">{{ __('Status') }}</th>
                                <th>{{ __('Notes Preview') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sessionsTableBody">
                            <tr class="loading-row">
                                <td colspan="6">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading sessions...') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="mobile-view" id="mobileCardsContainer">
                <!-- Cards will be inserted here dynamically -->
            </div>

            <div class="table-footer">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
        </div>

        <!-- Info Note -->
        <div class="info-note">
            <i class="fas fa-info-circle"></i>
            <span>{{ __('Session notes are private and only visible to you. They help you track patient progress between sessions.') }}</span>
        </div>
    </div>

    @push('styles')
        <style>
            .session-notes-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Animations */
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
                opacity: 0;
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

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon i {
                font-size: 1.4rem;
                color: white;
            }

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

            /* Modern Filters Card */
            .filters-card {
                background: white;
                border-radius: 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .filters-header {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                padding: 12px 20px;
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .filters-header i {
                font-size: 1rem;
            }

            .filters-grid {
                padding: 20px;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            .filter-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .filter-field label {
                font-size: 0.7rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .filter-field label i {
                font-size: 0.7rem;
                color: #7c3aed;
            }

            .filter-field select,
            .filter-field input {
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                background: white;
                transition: all 0.3s ease;
            }

            .filter-field select:focus,
            .filter-field input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            /* Reset Button - Same height as inputs */
            .filter-field.reset-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
                justify-content: flex-start;
            }

            .btn-reset {
                background: #f3f4f6;
                border: none;
                padding: 10px 20px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
                width: 100%;
                height: 44px;
                margin-top: auto;
                transform: translateY(-6px);
            }

            .btn-reset:hover {
                background: #e5e7eb;
                transform: translateY(-8px);
            }

            /* Date Range Styles */
            .date-range-wrapper {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .date-input-group {
                flex: 1;
                display: flex;
                align-items: center;
                gap: 8px;
                background: #f9fafb;
                border-radius: 12px;
                padding: 4px 12px;
                border: 1px solid #e5e7eb;
            }

            .date-input-group .date-label {
                font-size: 0.7rem;
                font-weight: 600;
                color: #7c3aed;
                background: #ede9fe;
                padding: 4px 8px;
                border-radius: 8px;
            }

            .date-input-group input {
                flex: 1;
                border: none;
                padding: 8px 0;
                background: transparent;
            }

            .date-input-group input:focus {
                outline: none;
                box-shadow: none;
            }

            .date-arrow {
                color: #9ca3af;
                font-size: 0.8rem;
            }

            /* Table Card */
            .table-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .sessions-table {
                width: 100%;
                border-collapse: collapse;
            }

            .sessions-table th,
            .sessions-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
            }

            .sessions-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .sessions-table td {
                font-size: 0.8rem;
                color: #4b5563;
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

            .badge-scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .badge-ongoing {
                background: #fef3c7;
                color: #d97706;
            }

            .badge-completed {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-no-show {
                background: #f3f4f6;
                color: #6b7280;
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

            /* Notes Preview */
            .notes-preview {
                max-width: 250px;
                font-size: 0.75rem;
                color: #6b7280;
                line-height: 1.4;
            }

            .notes-preview i {
                color: #10b981;
                margin-right: 5px;
            }

            .missing-notes {
                color: #ef4444;
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 5px;
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

            /* Mobile Card View */
            .mobile-view {
                display: none;
            }

            .session-card {
                background: white;
                border-radius: 16px;
                padding: 16px;
                margin: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                border: 1px solid #f0f0f0;
            }

            .session-card-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #f0f0f0;
            }

            .session-date {
                font-weight: 600;
                color: #1f2937;
            }

            .session-time {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .session-card-body {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .session-card-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.8rem;
            }

            .session-card-label {
                color: #6b7280;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .session-card-label i {
                width: 20px;
                color: #7c3aed;
            }

            .session-card-value {
                font-weight: 500;
                color: #1f2937;
            }

            .session-notes-preview {
                background: #f9fafb;
                padding: 10px;
                border-radius: 12px;
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 8px;
            }

            .session-card-actions {
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
            }

            /* Mobile Card Button - Styled properly */
            .session-card-actions .btn-icon {
                width: auto;
                padding: 8px 16px;
                gap: 8px;
                background: #ede9fe;
                color: #7c3aed;
                border-radius: 12px;
                font-size: 0.8rem;
            }

            .session-card-actions .btn-icon i {
                font-size: 0.8rem;
            }

            .session-card-actions .btn-icon:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            /* Table Footer */
            .table-footer {
                padding: 16px 20px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
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

            /* Loading */
            .loading-row td {
                text-align: center !important;
                padding: 60px 20px !important;
            }

            .loading-row td p {
                text-align: center !important;
                margin: 0 auto;
            }

            .loading-row td .loading-spinner {
                margin: 0 auto 15px;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }

            .btn-disabled {
                width: 32px;
                height: 32px;
                background: #f3f4f6;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #9ca3af;
                cursor: not-allowed;
                opacity: 0.6;
            }

            .btn-disabled i {
                font-size: 0.8rem;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
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
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .filters-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .date-range-field {
                    grid-column: span 2;
                }
            }

            @media (max-width: 768px) {
                .session-notes-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .filters-grid {
                    grid-template-columns: 1fr;
                }

                .date-range-field {
                    grid-column: span 1;
                }

                .desktop-view {
                    display: none;
                }

                .mobile-view {
                    display: block;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                }

                .date-range-wrapper {
                    flex-direction: column;
                }

                .date-arrow {
                    transform: rotate(-90deg);
                }

                .date-input-group {
                    width: 100%;
                }
            }

            @media (min-width: 769px) {
                .desktop-view {
                    display: block;
                }

                .mobile-view {
                    display: none;
                }
            }

            /* ======================================== */
            /* RTL SUPPORT - Full RTL/LTR compatibility */
            /* ======================================== */
            body.rtl .sessions-table th,
            body.rtl .sessions-table td {
                text-align: right;
            }

            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .notes-preview i {
                margin-right: 0;
                margin-left: 5px;
            }

            body.rtl .filter-field label {
                text-align: right;
            }

            body.rtl .session-card-label i {
                margin-left: 6px;
                margin-right: 0;
            }

            body.rtl .session-card-row {
                flex-direction: row;
            }

            body.rtl .date-arrow i {
                transform: scaleX(-1);
            }

            body.rtl .btn-reset i,
            body.rtl .filters-header i {
                margin-left: 4px;
                margin-right: 0;
            }

            body.rtl .session-card-header {
                direction: rtl;
            }

            body.rtl .action-buttons {
                justify-content: flex-start;
            }

            body.rtl .session-card-actions {
                justify-content: flex-start;
            }

            body.rtl .info-note {
                direction: rtl;
            }

            body.rtl .info-note i {
                margin-left: 8px;
                margin-right: 0;
            }

            body.rtl .pagination-controls .page-btn i {
                transform: scaleX(-1);
            }

            body.rtl .missing-notes i {
                margin-left: 5px;
                margin-right: 0;
            }

            body.rtl .stat-card {
                direction: rtl;
            }

            body.rtl .filters-header {
                direction: rtl;
            }

            body.rtl .filter-field select,
            body.rtl .filter-field input {
                text-align: right;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1, perPage = 15, sortField = 'session_datetime', sortDirection = 'desc';
            let search = '', patientId = 'all', notesStatus = 'all', sessionStatus = 'all', dateFrom = '', dateTo = '';

            const tableBody = document.getElementById('sessionsTableBody');
            const mobileContainer = document.getElementById('mobileCardsContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');
            const patientFilter = document.getElementById('patientFilter');
            const notesStatusFilter = document.getElementById('notesStatusFilter');
            const sessionStatusFilter = document.getElementById('sessionStatusFilter');
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            const resetBtn = document.getElementById('resetFilters');

            // Sort
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
                    loadSessions();
                });
            });

            // Filters
            if (searchInput) searchInput.addEventListener('input', () => { search = searchInput.value; currentPage = 1; loadSessions(); });
            if (patientFilter) patientFilter.addEventListener('change', () => { patientId = patientFilter.value; currentPage = 1; loadSessions(); });
            if (notesStatusFilter) notesStatusFilter.addEventListener('change', () => { notesStatus = notesStatusFilter.value; currentPage = 1; loadSessions(); });
            if (sessionStatusFilter) sessionStatusFilter.addEventListener('change', () => { sessionStatus = sessionStatusFilter.value; currentPage = 1; loadSessions(); });
            if (dateFromInput) dateFromInput.addEventListener('change', () => { dateFrom = dateFromInput.value; currentPage = 1; loadSessions(); });
            if (dateToInput) dateToInput.addEventListener('change', () => { dateTo = dateToInput.value; currentPage = 1; loadSessions(); });

            if (resetBtn) resetBtn.addEventListener('click', () => {
                // Reset all filter inputs
                if (searchInput) searchInput.value = '';
                if (patientFilter) patientFilter.value = 'all';
                if (notesStatusFilter) notesStatusFilter.value = 'all';
                if (sessionStatusFilter) sessionStatusFilter.value = 'all';
                if (dateFromInput) dateFromInput.value = '';
                if (dateToInput) dateToInput.value = '';

                // Reset all filter variables
                search = '';
                patientId = 'all';
                notesStatus = 'all';
                sessionStatus = 'all';
                dateFrom = '';
                dateTo = '';
                currentPage = 1;

                // Clear URL parameter completely
                const currentUrl = window.location.href;
                const urlWithoutParam = currentUrl.split('?')[0];
                window.history.replaceState({}, '', urlWithoutParam);

                // Reload sessions with cleared filters
                loadSessions();
            });

            async function loadSessions() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="6"><div class="loading-spinner"></div><p>{{ __('Loading sessions...') }}</p></td></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = '<div class="loading-spinner"></div>';

                try {
                    const url = `/specialist/session-notes/data?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}&patient_id=${patientId}&notes_status=${notesStatus}&session_status=${sessionStatus}&date_from=${dateFrom}&date_to=${dateTo}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();

                    if (data.success) {
                        renderTable(data.data);
                        renderMobileCards(data.data);
                        renderPagination(data);
                    } else {
                        showError();
                    }
                } catch (error) {
                    showError();
                }
            }

            function renderTable(sessions) {
                if (!sessions || !sessions.length) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="6"><div class="empty-state" style="text-align:center;padding:40px;"><i class="fas fa-calendar-alt" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No sessions found') }}</p></div></td></tr>`;
                    return;
                }

                function getStatusIcon(status) {
                    const icons = { scheduled: 'fa-clock', ongoing: 'fa-spinner fa-pulse', completed: 'fa-check-circle', cancelled: 'fa-times-circle', no_show: 'fa-user-slash' };
                    return icons[status] || 'fa-circle';
                }

                function getStatusClass(status) {
                    const classes = { scheduled: 'badge-scheduled', ongoing: 'badge-ongoing', completed: 'badge-completed', cancelled: 'badge-cancelled', no_show: 'badge-no-show' };
                    return classes[status] || '';
                }

                function getTypeIcon(type) {
                    const icons = { video: 'fa-video', audio: 'fa-phone-alt', text: 'fa-comment-dots' };
                    return icons[type] || 'fa-circle';
                }

                function getTypeClass(type) {
                    const classes = { video: 'video', audio: 'audio', text: 'text' };
                    return classes[type] || '';
                }

                tableBody.innerHTML = sessions.map(session => {
                    const statusIcon = getStatusIcon(session.status_class);
                    const statusClass = getStatusClass(session.status_class);
                    const typeIcon = getTypeIcon(session.type_class);
                    const typeClass = getTypeClass(session.type_class);

                    const statusBadge = `<span class="badge ${statusClass}"><i class="fas ${statusIcon}"></i> ${escapeHtml(session.status_text)}</span>`;
                    const typeBadge = `<span class="type-badge ${typeClass}"><i class="fas ${typeIcon}"></i> ${escapeHtml(session.type_text)}</span>`;

                    // Only show edit button for scheduled or completed sessions
                    const canEditNotes = session.status_class === 'scheduled' || session.status_class === 'completed';

                    const notesHtml = session.has_notes
                        ? `<div class="notes-preview"><i class="fas fa-file-alt"></i> ${escapeHtml(session.notes_preview)}</div>`
                        : `<div class="missing-notes"><i class="fas fa-exclamation-triangle"></i> {{ __('No notes added') }}</div>`;

                    return `<tr>
                                                                        <td style="white-space: nowrap;"><div><strong>${escapeHtml(session.date_formatted)}</strong></div><div><small>${escapeHtml(session.time_formatted)}</small></div></td>
                                                                        <td><strong>${escapeHtml(session.patient_name)}</strong></td>
                                                                        <td>${typeBadge}</td>
                                                                        <td>${statusBadge}</td>
                                                                        <td>${notesHtml}</td>
                                                                        <td>

                                                                        <div class="action-buttons">
                                                                                ${canEditNotes ? `
                                                                                    <a href="/specialist/session-notes/${session.id}" class="btn-icon" title="${session.has_notes ? '{{ __("Edit Notes") }}' : '{{ __("Create Notes") }}'}"><i class="fas ${session.has_notes ? 'fa-edit' : 'fa-plus'}"></i></a>
                                                                                ` : `
                                                                                    <span class="btn-disabled" title="{{ __('Notes cannot be added for this session') }}">
                                                                                        <i class="fas fa-ban"></i>
                                                                                    </span>
                                                                                `}
                                                                        </div>
                                                                    </td>
                                                                    </tr>`;
                }).join('');
            }

            function renderMobileCards(sessions) {
                if (!mobileContainer) return;

                if (!sessions || !sessions.length) {
                    mobileContainer.innerHTML = `<div class="empty-state" style="text-align:center;padding:40px;"><i class="fas fa-calendar-alt" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No sessions found') }}</p></div>`;
                    return;
                }

                function getStatusIcon(status) {
                    const icons = { scheduled: 'fa-clock', ongoing: 'fa-spinner fa-pulse', completed: 'fa-check-circle', cancelled: 'fa-times-circle', no_show: 'fa-user-slash' };
                    return icons[status] || 'fa-circle';
                }

                function getStatusClass(status) {
                    const classes = { scheduled: 'badge-scheduled', ongoing: 'badge-ongoing', completed: 'badge-completed', cancelled: 'badge-cancelled', no_show: 'badge-no-show' };
                    return classes[status] || '';
                }

                function getTypeIcon(type) {
                    const icons = { video: 'fa-video', audio: 'fa-phone-alt', text: 'fa-comment-dots' };
                    return icons[type] || 'fa-circle';
                }

                function getTypeClass(type) {
                    const classes = { video: 'video', audio: 'audio', text: 'text' };
                    return classes[type] || '';
                }

                mobileContainer.innerHTML = sessions.map(session => {
                    const statusIcon = getStatusIcon(session.status_class);
                    const statusClass = getStatusClass(session.status_class);
                    const typeIcon = getTypeIcon(session.type_class);
                    const typeClass = getTypeClass(session.type_class);

                    const statusBadge = `<span class="badge ${statusClass}"><i class="fas ${statusIcon}"></i> ${escapeHtml(session.status_text)}</span>`;
                    const typeBadge = `<span class="type-badge ${typeClass}"><i class="fas ${typeIcon}"></i> ${escapeHtml(session.type_text)}</span>`;

                    const notesHtml = session.has_notes
                        ? `<div class="session-notes-preview"><i class="fas fa-file-alt"></i> ${escapeHtml(session.notes_preview)}</div>`
                        : `<div class="session-notes-preview missing-notes"><i class="fas fa-exclamation-triangle"></i> {{ __('No notes added') }}</div>`;

                    const buttonTitle = session.has_notes ? '{{ __("Edit Notes") }}' : '{{ __("Create Notes") }}';
                    const buttonIcon = session.has_notes ? 'fa-edit' : 'fa-plus';

                    return `<div class="session-card">
                                                                                                                <div class="session-card-header">
                                                                                                                    <div>
                                                                                                                        <div class="session-date">${escapeHtml(session.date_formatted)}</div>
                                                                                                                        <div class="session-time">${escapeHtml(session.time_formatted)}</div>
                                                                                                                    </div>
                                                                                                                    ${statusBadge}
                                                                                                                </div>
                                                                                                                <div class="session-card-body">
                                                                                                                    <div class="session-card-row">
                                                                                                                        <span class="session-card-label"><i class="fas fa-user"></i> {{ __('Patient') }}</span>
                                                                                                                        <span class="session-card-value">${escapeHtml(session.patient_name)}</span>
                                                                                                                    </div>
                                                                                                                    <div class="session-card-row">
                                                                                                                        <span class="session-card-label"><i class="fas ${typeIcon}"></i> {{ __('Type') }}</span>
                                                                                                                        <span class="session-card-value">${typeBadge}</span>
                                                                                                                    </div>
                                                                                                                    ${notesHtml}
                                                                                                                </div>
                                                                                                                <div class="session-card-actions">
                                                                                                                    <a href="/specialist/session-notes/${session.id}" class="btn-icon" title="${buttonTitle}">
                                                                                                                        <i class="fas ${buttonIcon}"></i> ${buttonTitle}
                                                                                                                    </a>
                                                                                                                </div>
                                                                                                            </div>`;
                }).join('');
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                paginationInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('sessions') }}`;

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

            function goToPage(page) { currentPage = page; loadSessions(); }
            function showError() {
                const errorHtml = `<tr><td colspan="6"><div class="empty-state" style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:3rem;color:#ef4444;"></i><p style="margin-top:10px;">{{ __('Error loading sessions') }}</p><button class="btn-primary-sm" onclick="loadSessions()">{{ __('Retry') }}</button></div></td></tr>`;
                tableBody.innerHTML = errorHtml;
                if (mobileContainer) mobileContainer.innerHTML = errorHtml;
            }

            function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); }

            // Get pre-selected client from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const preSelectedClient = urlParams.get('client');

            // If there's a pre-selected client, set the filter and load
            if (preSelectedClient && preSelectedClient !== 'all') {
                patientId = preSelectedClient;
                // Set the select dropdown value
                const patientFilter = document.getElementById('patientFilter');
                if (patientFilter) {
                    patientFilter.value = preSelectedClient;
                }
            }

            // Then load sessions (this will use the pre-selected patientId)
            loadSessions();
        </script>
    @endpush
@endsection