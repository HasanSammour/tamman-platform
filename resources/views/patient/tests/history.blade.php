{{-- resources/views/patient/tests/history.blade.php --}}
@extends('layouts.app')

@section('title', __('Test History') . ' - ' . __('Tamman'))

@section('page-title', __('Test History'))

@section('content')
    <div class="history-container">
        <!-- Filter Section - Fixed Layout -->
        <div class="filter-card">
            <div class="filter-header">
                <i class="fas fa-filter"></i>
                <h3>{{ __('Filter Tests') }}</h3>
            </div>
            <div class="filter-controls">
                <div class="filter-select-wrapper">
                    <select id="testTypeFilter" class="filter-select">
                        <option value="all">{{ __('All Tests') }}</option>
                        @foreach($testTypes as $key => $test)
                            <option value="{{ $key }}" {{ request('test_type') == $key ? 'selected' : '' }}>
                                {{ $test['name'] }} -
                                {{ app()->getLocale() === 'ar' ? $test['full_name_ar'] : $test['full_name'] }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down filter-arrow"></i>
                </div>
            </div>
        </div>

        <!-- History Table Container -->
        <div id="historyTableContainer">
            @include('patient.tests.partials.history_table', ['testResults' => $testResults])
        </div>

        <!-- Pagination Container -->
        <div id="paginationContainer" class="pagination-wrapper">
            {{ $testResults->links() }}
        </div>
    </div>

    @push('styles')
        <style>
            .history-container {
                max-width: 100%;
                margin: 0 auto;
            }

            .filter-card {
                background: white;
                border-radius: 20px;
                padding: 16px 25px;
                margin-bottom: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .filter-header {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .filter-header i {
                font-size: 1.2rem;
                color: #7c3aed;
            }

            .filter-header h3 {
                font-size: 1rem;
                margin: 0;
                color: #1f2937;
                font-weight: 600;
            }

            .filter-controls {
                display: flex;
                align-items: center;
            }

            .filter-select-wrapper {
                position: relative;
                min-width: 260px;
            }

            .filter-select {
                width: 100%;
                padding: 10px 16px;
                padding-right: 35px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: white;
                font-size: 0.85rem;
                cursor: pointer;
                transition: all 0.3s ease;
                appearance: none;
                -webkit-appearance: none;
                color: #1f2937;
            }

            .filter-select:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .filter-arrow {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 0.75rem;
                color: #9ca3af;
                pointer-events: none;
            }

            .history-table-container {
                overflow-x: auto;
                background: white;
                border-radius: 20px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .history-table {
                width: 100%;
                border-collapse: collapse;
            }

            .history-table th,
            .history-table td {
                padding: 16px 20px;
                text-align: left;
                border-bottom: 1px solid #f3f4f6;
            }

            .history-table th {
                font-weight: 600;
                color: #374151;
                background: #f9fafb;
            }

            .history-row:hover {
                background: #f9fafb;
            }

            .date-display {
                display: flex;
                flex-direction: column;
                align-items: center;
                line-height: 1.2;
            }

            .date-display .day {
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .date-display .month {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .date-display .year {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            .test-info-mini {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .test-icon-mini {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .test-icon-mini i {
                font-size: 1.1rem;
            }

            .test-details {
                display: flex;
                flex-direction: column;
            }

            .test-details strong {
                font-size: 0.85rem;
                color: #1f2937;
            }

            .test-details small {
                font-size: 0.65rem;
                color: #6b7280;
            }

            .score-badge {
                display: inline-block;
                padding: 6px 12px;
                background: #f3f4f6;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 700;
                color: #1f2937;
            }

            .level-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .level-badge.minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .level-badge.mild {
                background: #fef3c7;
                color: #92400e;
            }

            .level-badge.moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .level-badge.moderately_severe {
                background: #fed7aa;
                color: #9a3412;
            }

            .level-badge.severe {
                background: #fee2e2;
                color: #991b1b;
            }

            .level-badge.none {
                background: #d1fae5;
                color: #065f46;
            }

            .level-badge.subthreshold {
                background: #fef3c7;
                color: #92400e;
            }

            .level-badge.low {
                background: #d1fae5;
                color: #065f46;
            }

            .level-badge.high {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-view-results {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                background: #f3f4f6;
                border-radius: 20px;
                color: #7c3aed;
                text-decoration: none;
                font-size: 0.7rem;
                transition: all 0.3s ease;
            }

            .btn-view-results:hover {
                background: #ede9fe;
                color: #6d28d9;
            }

            .pagination-wrapper {
                margin-top: 25px;
                text-align: center;
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 20px;
            }

            .empty-state i {
                font-size: 3rem;
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
                font-size: 0.8rem;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .btn-primary-sm:hover {
                transform: translateY(-2px);
                color: white;
            }

            /* Loading Spinner */
            .loading-spinner-table {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 20px;
            }

            .loading-spinner-table i {
                font-size: 2rem;
                color: #7c3aed;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            /* RTL Support */
            body.rtl .filter-header {
                flex-direction: row;
            }

            body.rtl .filter-arrow {
                right: auto;
                left: 15px;
            }

            body.rtl .filter-select {
                padding-right: 16px;
                padding-left: 35px;
            }

            body.rtl .test-info-mini {
                flex-direction: row;
            }

            body.rtl .history-table th,
            body.rtl .history-table td {
                text-align: right;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .filter-card {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 12px;
                }

                .filter-header {
                    justify-content: flex-start;
                }

                .filter-controls {
                    width: 100%;
                }

                .filter-select-wrapper {
                    width: 100%;
                    min-width: auto;
                }

                .filter-select {
                    width: 100%;
                }

                .history-table th,
                .history-table td {
                    padding: 12px 15px;
                }

                .test-info-mini {
                    flex-direction: column;
                    text-align: center;
                }

                .date-display {
                    min-width: 50px;
                }

                .btn-view-results {
                    white-space: nowrap;
                }
            }

            @media (max-width: 480px) {
                .filter-card {
                    padding: 15px 20px;
                }

                .filter-header i {
                    font-size: 1rem;
                }

                .filter-header h3 {
                    font-size: 0.9rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterSelect = document.getElementById('testTypeFilter');
                const tableContainer = document.getElementById('historyTableContainer');
                const paginationContainer = document.getElementById('paginationContainer');
                let currentType = filterSelect?.value || 'all';
                let isLoading = false;

                function fetchHistory() {
                    if (isLoading) return;
                    isLoading = true;

                    tableContainer.innerHTML = `
                        <div class="loading-spinner-table">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>{{ __('Loading...') }}</p>
                        </div>
                    `;
                    paginationContainer.innerHTML = '';

                    const url = '{{ route("patient.tests.history") }}?test_type=' + currentType + '&ajax=1';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                tableContainer.innerHTML = data.html;
                                paginationContainer.innerHTML = data.pagination;
                                attachPaginationEvents();
                            } else {
                                tableContainer.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p>{{ __("Error loading history") }}</p>
                                </div>
                            `;
                            }
                            isLoading = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tableContainer.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>{{ __("Error loading history") }}</p>
                            </div>
                        `;
                            isLoading = false;
                        });
                }

                function attachPaginationEvents() {
                    document.querySelectorAll('.pagination .page-link').forEach(link => {
                        const newLink = link.cloneNode(true);
                        link.parentNode.replaceChild(newLink, link);

                        newLink.addEventListener('click', function (e) {
                            e.preventDefault();
                            const url = this.getAttribute('href');
                            if (url && !this.parentElement.classList.contains('disabled')) {
                                fetchPaginatedPage(url);
                            }
                        });
                    });
                }

                function fetchPaginatedPage(url) {
                    if (isLoading) return;
                    isLoading = true;

                    tableContainer.innerHTML = `
                        <div class="loading-spinner-table">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>{{ __('Loading...') }}</p>
                        </div>
                    `;
                    paginationContainer.innerHTML = '';

                    const separator = url.includes('?') ? '&' : '?';
                    url = url + separator + 'test_type=' + currentType + '&ajax=1';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                tableContainer.innerHTML = data.html;
                                paginationContainer.innerHTML = data.pagination;
                                attachPaginationEvents();
                            }
                            isLoading = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            isLoading = false;
                        });
                }

                if (filterSelect) {
                    filterSelect.addEventListener('change', function () {
                        currentType = this.value;
                        fetchHistory();
                    });
                }

                attachPaginationEvents();
            });
        </script>
    @endpush

@endsection