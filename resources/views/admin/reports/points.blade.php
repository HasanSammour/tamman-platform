{{-- resources/views/admin/reports/points.blade.php --}}
@extends('layouts.app')

@section('title', __('Points Report') . ' - ' . __('Tamman'))

@section('page-title', __('Points Report'))

@section('content')
    <div class="points-report-wrapper">
        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryTotalEarned">{{ number_format($globalStats['total_earned'] ?? 0) }}</h3>
                    <p>{{ __('Total Earned') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryTotalRedeemed">{{ number_format($globalStats['total_redeemed'] ?? 0) }}</h3>
                    <p>{{ __('Total Redeemed') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryNetPoints">{{ number_format($globalStats['net_points'] ?? 0) }}</h3>
                    <p>{{ __('Net Points') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryActiveUsers">{{ number_format($globalStats['active_users'] ?? 0) }}</h3>
                    <p>{{ __('Active Users') }}</p>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="filters-container">
            <!-- Row 1: Date Range & Transaction Type & Source -->
            <div class="filters-row">
                <div class="filter-item date-range-item">
                    <div class="date-range-wrapper">
                        <div class="date-input">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" id="filterDateFrom" placeholder="{{ __('From Date') }}">
                        </div>
                        <span class="date-separator">—</span>
                        <div class="date-input">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" id="filterDateTo" placeholder="{{ __('To Date') }}">
                        </div>
                    </div>
                </div>
                <select id="filterType" class="filter-select">
                    <option value="all">{{ __('All Types') }}</option>
                    <option value="earned">{{ __('Earned') }}</option>
                    <option value="redeemed">{{ __('Redeemed') }}</option>
                </select>
                <select id="filterSource" class="filter-select">
                    <option value="all">{{ __('All Sources') }}</option>
                    <option value="mood_tracking">{{ __('Mood Tracking') }}</option>
                    <option value="session_attendance">{{ __('Session Attendance') }}</option>
                    <option value="test_completed">{{ __('Test Completed') }}</option>
                    <option value="task_completed">{{ __('Task Completed') }}</option>
                    <option value="specialist_rating">{{ __('Specialist Rating') }}</option>
                    <option value="referral">{{ __('Referral') }}</option>
                    <option value="streak_bonus">{{ __('Streak Bonus') }}</option>
                    <option value="booking">{{ __('Booking') }}</option>
                    <option value="reward_credit">{{ __('Reward - Credit') }}</option>
                    <option value="reward_free_session">{{ __('Reward - Free Session') }}</option>
                    <option value="reward_donate">{{ __('Reward - Donate') }}</option>
                </select>
            </div>

            <!-- Row 2: User Search & Action Buttons -->
            <div class="filters-row">
                <div class="search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filterSearch" placeholder="{{ __('Search by user name or email...') }}">
                    <button id="clearSearchBtn" class="clear-search-btn" style="display: none;">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <button id="applyFiltersBtn" class="btn-apply">
                    <i class="fas fa-check-circle"></i> {{ __('Apply Filters') }}
                </button>
                <button id="resetFiltersBtn" class="btn-reset">
                    <i class="fas fa-undo-alt"></i> {{ __('Reset') }}
                </button>
                <button id="exportReportBtn" class="btn-export">
                    <i class="fas fa-file-pdf"></i> {{ __('Export PDF') }}
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> {{ __('Points Transactions') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <!-- Horizontal scroll wrapper -->
            <div class="table-scroll-wrapper">
                <div class="table-responsive-inner">
                    <table class="points-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="user_name">{{ __('User') }}</th>
                                <th data-sort="points" class="sortable">{{ __('Points') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="type" class="sortable">{{ __('Type') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="source">{{ __('Source') }}</th>
                                <th data-sort="description">{{ __('Description') }}</th>
                                <th data-sort="created_at" class="sortable">{{ __('Date') }} <i class="fas fa-sort"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr class="loading-row">
                                <td colspan="7">
                                    <div class="loading-container">
                                        <div class="loading-spinner"></div>
                                        <p>{{ __('Loading data...') }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-footer">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* ============================================
                           IMPORTANT: Fix main layout (same as User Management)
                           ============================================ */
            .app-main {
                flex: 1;
                margin-left: 280px;
                transition: margin-left 0.3s ease-in-out;
                overflow-x: hidden;
                width: calc(100% - 280px);
                max-width: calc(100% - 280px);
            }

            .app-content {
                padding: 24px;
                min-height: calc(100vh - 73px);
                overflow-x: hidden;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }

            /* For collapsed sidebar */
            .app-sidebar.collapsed+.app-main {
                margin-left: 80px;
                width: calc(100% - 80px);
                max-width: calc(100% - 80px);
            }

            /* Mobile responsive fix */
            @media (max-width: 768px) {
                .app-main {
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }
            }

            /* ============================================
                           MAIN WRAPPER
                           ============================================ */
            .points-report-wrapper {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            /* ============================================
                           STATS GRID
                           ============================================ */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
                width: 100%;
                overflow-x: hidden;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
                min-width: 0;
                width: 100%;
                box-sizing: border-box;
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
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.4rem;
                color: white;
            }

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-data h3 {
                font-size: 1.6rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 0;
            }

            /* ============================================
                           FILTERS CONTAINER
                           ============================================ */
            .filters-container {
                background: white;
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                width: 100%;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            .filters-row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 12px;
                width: 100%;
                margin-bottom: 15px;
            }

            .filters-row:last-child {
                margin-bottom: 0;
            }

            /* Date Range Item */
            .date-range-item {
                flex: 2;
                min-width: 280px;
            }

            .date-range-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 0 12px;
                height: 42px;
            }

            .date-input {
                display: flex;
                align-items: center;
                gap: 8px;
                flex: 1;
            }

            .date-input i {
                color: #9ca3af;
                font-size: 0.85rem;
            }

            .date-input input {
                flex: 1;
                border: none;
                padding: 10px 0;
                background: transparent;
                font-size: 0.85rem;
            }

            .date-input input:focus {
                outline: none;
            }

            .date-input input::placeholder {
                color: #9ca3af;
                font-size: 0.8rem;
            }

            .date-separator {
                color: #9ca3af;
                font-weight: 500;
            }

            /* Filter Selects */
            .filter-select {
                flex: 1;
                min-width: 140px;
                padding: 10px 30px 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                background: white;
                cursor: pointer;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 10px center;
                height: 42px;
            }

            .filter-select:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            /* Search Field */
            .search-field {
                flex: 3;
                min-width: 250px;
                position: relative;
            }

            .search-field i {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.85rem;
            }

            .search-field input {
                width: 100%;
                padding: 10px 12px 10px 38px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                height: 42px;
            }

            .search-field input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            .clear-search-btn {
                position: absolute;
                right: 10px;
                top: 68%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #9ca3af;
                cursor: pointer;
            }

            .clear-search-btn:hover {
                color: #ef4444;
            }

            /* Buttons */
            .btn-apply,
            .btn-reset,
            .btn-export {
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
                height: 42px;
                white-space: nowrap;
            }

            .btn-apply {
                background: #7c3aed;
                color: white;
            }

            .btn-apply:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-reset {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-reset:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-export {
                background: #ef4444;
                color: white;
            }

            .btn-export:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* ============================================
                           TABLE CARD
                           ============================================ */
            .table-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                width: 100%;
                box-sizing: border-box;
            }

            .table-header {
                padding: 18px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .table-header h4 {
                margin: 0;
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
            }

            .table-header h4 i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .table-info {
                font-size: 0.75rem;
                color: #6b7280;
            }

            /* ========== CRITICAL: HORIZONTAL SCROLL WRAPPER ========== */
            .table-scroll-wrapper {
                width: 100%;
                overflow-x: auto;
                overflow-y: visible;
                -webkit-overflow-scrolling: touch;
                position: relative;
            }

            .table-scroll-wrapper::-webkit-scrollbar {
                height: 8px;
            }

            .table-scroll-wrapper::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .table-scroll-wrapper::-webkit-scrollbar-thumb {
                background: #c4b5fd;
                border-radius: 10px;
            }

            .table-responsive-inner {
                min-width: 1000px;
                width: 100%;
            }

            .points-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 100%;
            }

            .points-table th,
            .points-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                white-space: nowrap;
            }

            .points-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
                cursor: pointer;
            }

            .points-table th i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .points-table td {
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

            /* User Avatar Cell */
            .user-avatar-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .avatar-img {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
                flex-shrink: 0;
                overflow: hidden;
            }

            .avatar-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .user-name {
                font-weight: 600;
                color: #1f2937;
            }

            .user-email-small {
                font-size: 0.65rem;
                color: #9ca3af;
                display: block;
            }

            /* Points Badges */
            .points-positive {
                color: #10b981;
                font-weight: 600;
            }

            .points-negative {
                color: #ef4444;
                font-weight: 600;
            }

            .badge-earned {
                background: #d1fae5;
                color: #065f46;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .badge-redeemed {
                background: #fee2e2;
                color: #991b1b;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            /* Source Badges */
            .source-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .source-mood_tracking {
                background: #ede9fe;
                color: #7c3aed;
            }

            .source-session_attendance {
                background: #d1fae5;
                color: #059669;
            }

            .source-test_completed {
                background: #fef3c7;
                color: #d97706;
            }

            .source-task_completed {
                background: #cffafe;
                color: #0891b2;
            }

            .source-specialist_rating {
                background: #fce7f3;
                color: #db2777;
            }

            .source-referral {
                background: #dbeafe;
                color: #2563eb;
            }

            .source-streak_bonus {
                background: #fed7aa;
                color: #9a3412;
            }

            .source-booking {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .source-reward_credit {
                background: #d1fae5;
                color: #065f46;
            }

            .source-reward_free_session {
                background: #fef3c7;
                color: #d97706;
            }

            .source-reward_donate {
                background: #fce7f3;
                color: #db2777;
            }

            /* ========== LOADING CENTER FIX ========== */
            .loading-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                width: 100%;
                padding: 60px 20px;
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

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .loading-row td {
                text-align: center !important;
                padding: 0 !important;
            }

            /* Empty State */
            .empty-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            /* Pagination */
            .table-footer {
                padding: 16px 24px;
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
                transition: all 0.2s;
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

            /* ============================================
                           RESPONSIVE BREAKPOINTS
                           ============================================ */

            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                }

                .date-range-item {
                    flex: 1;
                    min-width: 250px;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .filters-row {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 10px;
                }

                .date-range-item {
                    width: 100%;
                    min-width: auto;
                }

                .date-range-wrapper {
                    width: 100%;
                }

                .filter-select {
                    width: 100%;
                    min-width: auto;
                }

                .search-field {
                    width: 100%;
                    min-width: auto;
                }

                .btn-apply,
                .btn-reset,
                .btn-export {
                    width: 100%;
                    text-align: center;
                    justify-content: center;
                }

                .table-header {
                    padding: 15px;
                    flex-direction: column;
                    text-align: center;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                    padding: 16px;
                }

                .pagination-controls {
                    justify-content: center;
                    flex-wrap: wrap;
                }
            }

            @media (max-width: 480px) {
                .app-content {
                    padding: 12px;
                }

                .filters-container {
                    padding: 15px;
                }

                .date-range-wrapper {
                    flex-direction: column;
                    background: transparent;
                    border: none;
                    padding: 0;
                    gap: 8px;
                    height: auto;
                }

                .date-input {
                    width: 100%;
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 0 12px;
                    height: 42px;
                }

                .date-separator {
                    display: none;
                }

                .stat-card {
                    padding: 15px;
                }

                .stat-icon {
                    width: 42px;
                    height: 42px;
                }

                .stat-icon i {
                    font-size: 1.1rem;
                }

                .stat-data h3 {
                    font-size: 1.3rem;
                }

                .loading-container {
                    padding: 40px 20px;
                }

                .loading-spinner {
                    width: 32px;
                    height: 32px;
                }
            }

            /* RTL Support */
            body.rtl .search-field i {
                left: auto;
                right: 12px;
            }

            body.rtl .search-field input {
                padding: 10px 38px 10px 12px;
            }

            body.rtl .filter-select {
                background-position: left 10px center;
                padding: 10px 12px 10px 30px;
            }

            body.rtl .points-table th,
            body.rtl .points-table td {
                text-align: right;
            }

            body.rtl .points-table th i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .clear-search-btn {
                right: auto;
                left: 10px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1;
            let perPage = 15;
            let sortField = 'created_at';
            let sortDirection = 'desc';

            let filters = {
                date_from: '',
                date_to: '',
                type: 'all',
                source: 'all',
                search: ''
            };

            // DOM Elements
            const tableBody = document.getElementById('reportTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');

            // Stats Elements
            const summaryTotalEarned = document.getElementById('summaryTotalEarned');
            const summaryTotalRedeemed = document.getElementById('summaryTotalRedeemed');
            const summaryNetPoints = document.getElementById('summaryNetPoints');
            const summaryActiveUsers = document.getElementById('summaryActiveUsers');

            // Filter Elements
            const filterDateFrom = document.getElementById('filterDateFrom');
            const filterDateTo = document.getElementById('filterDateTo');
            const filterType = document.getElementById('filterType');
            const filterSource = document.getElementById('filterSource');
            const filterSearch = document.getElementById('filterSearch');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const applyBtn = document.getElementById('applyFiltersBtn');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const exportBtn = document.getElementById('exportReportBtn');

            // Sort functionality
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
                    loadReport();
                });
            });

            // Source name mapping for display
            const sourceNames = {
                'mood_tracking': '{{ __("Mood Tracking") }}',
                'session_attendance': '{{ __("Session Attendance") }}',
                'test_completed': '{{ __("Test Completed") }}',
                'task_completed': '{{ __("Task Completed") }}',
                'specialist_rating': '{{ __("Specialist Rating") }}',
                'referral': '{{ __("Referral") }}',
                'streak_bonus': '{{ __("Streak Bonus") }}',
                'booking': '{{ __("Booking") }}',
                'reward_credit': '{{ __("Reward - Credit") }}',
                'reward_free_session': '{{ __("Reward - Free Session") }}',
                'reward_donate': '{{ __("Reward - Donate") }}'
            };

            function getSourceClass(source) {
                if (!source) return '';
                return source.replace(/_/g, '-');
            }

            // Show/hide clear search button
            filterSearch.addEventListener('input', function () {
                clearSearchBtn.style.display = this.value ? 'block' : 'none';
            });

            clearSearchBtn.addEventListener('click', function () {
                filterSearch.value = '';
                clearSearchBtn.style.display = 'none';
                updateFilters();
            });

            function updateFilters() {
                filters.date_from = filterDateFrom.value;
                filters.date_to = filterDateTo.value;
                filters.type = filterType.value;
                filters.source = filterSource.value;
                filters.search = filterSearch.value;
                currentPage = 1;
                loadReport();
            }

            function resetFilters() {
                filterDateFrom.value = '';
                filterDateTo.value = '';
                filterType.value = 'all';
                filterSource.value = 'all';
                filterSearch.value = '';
                clearSearchBtn.style.display = 'none';
                updateFilters();
            }

            async function loadReport() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="7"><div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading data...') }}</p></div> </tr>`;

                try {
                    const params = new URLSearchParams({
                        page: currentPage,
                        per_page: perPage,
                        sort_field: sortField,
                        sort_direction: sortDirection,
                        date_from: filters.date_from,
                        date_to: filters.date_to,
                        type: filters.type,
                        source: filters.source,
                        search: filters.search
                    });

                    const response = await fetch(`{{ route("admin.reports.points.data") }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success) {
                        renderTable(data.data);
                        renderPagination(data);
                        updateStats(data.stats);
                    } else {
                        showError();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError();
                }
            }

            function updateStats(stats) {
                if (stats) {
                    if (summaryTotalEarned) summaryTotalEarned.textContent = (stats.total_earned || 0).toLocaleString();
                    if (summaryTotalRedeemed) summaryTotalRedeemed.textContent = (stats.total_redeemed || 0).toLocaleString();
                    if (summaryNetPoints) summaryNetPoints.textContent = (stats.net_points || 0).toLocaleString();
                    if (summaryActiveUsers) summaryActiveUsers.textContent = (stats.active_users || 0).toLocaleString();
                }
            }

            function renderTable(transactions) {
                if (!transactions || transactions.length === 0) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="7"><div style="text-align:center;padding:40px;"><i class="fas fa-star" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No points transactions found') }}</p></div> </tr>`;
                    return;
                }

                tableBody.innerHTML = transactions.map(transaction => {
                    // User Avatar
                    let avatarHtml = '';
                    if (transaction.profile_image_url) {
                        avatarHtml = `<img src="${transaction.profile_image_url}" alt="${escapeHtml(transaction.user_name)}" onerror="this.style.display='none';this.parentElement.textContent='${escapeHtml((transaction.user_name?.charAt(0) || 'U').toUpperCase())}'">`;
                    } else {
                        const initial = (transaction.user_name?.charAt(0) || 'U').toUpperCase();
                        avatarHtml = initial;
                    }

                    const points = parseInt(transaction.points) || 0;
                    const pointsFormatted = points > 0 ? '+' + points.toLocaleString() : '-' + Math.abs(points).toLocaleString();
                    const pointsClass = points > 0 ? 'points-positive' : 'points-negative';

                    const typeClass = transaction.type === 'earned' ? 'badge-earned' : 'badge-redeemed';
                    const typeText = transaction.type === 'earned' ? '{{ __("Earned") }}' : '{{ __("Redeemed") }}';

                    const sourceClass = getSourceClass(transaction.source);
                    const sourceName = sourceNames[transaction.source] || (transaction.source ? transaction.source.replace(/_/g, ' ') : '-');

                    const date = transaction.created_at ? new Date(transaction.created_at) : null;
                    const formattedDate = date ? date.toLocaleDateString() : '-';
                    const formattedTime = date ? date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '-';

                    return `
                                <tr>
                                    <td>#${transaction.id}</td>
                                    <td>
                                        <div class="user-avatar-cell">
                                            <div class="avatar-img">${avatarHtml}</div>
                                            <div>
                                                <div class="user-name">${escapeHtml(transaction.user_name || '-')}</div>
                                                <div class="user-email-small">${escapeHtml(transaction.user_email || '-')}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="${pointsClass}">${pointsFormatted}</span></td>
                                    <td><span class="${typeClass}">${typeText}</span></td>
                                    <td><span class="source-badge source-${sourceClass}">${escapeHtml(sourceName)}</span></td>
                                    <td>${escapeHtml(transaction.description || '-')}</td>
                                    <td>
                                        ${formattedDate}<br>
                                        <small style="color:#9ca3af;">${formattedTime}</small>
                                    </td>
                                </tr>
                            `;
                }).join('');
            }

            function renderPagination(data) {
                const total = data.total;
                const current = data.current_page;
                const last = data.last_page;

                if (total === 0) {
                    tableInfo.innerHTML = '{{ __("No transactions found") }}';
                    paginationInfo.innerHTML = '';
                    paginationControls.innerHTML = '';
                    return;
                }

                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total.toLocaleString()} {{ __('transactions') }}`;
                paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;

                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;

                let startPage = Math.max(1, current - 2);
                let endPage = Math.min(last, current + 2);

                for (let i = startPage; i <= endPage; i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                }

                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;

                paginationControls.innerHTML = html;
            }

            function goToPage(page) {
                currentPage = page;
                loadReport();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function showError() {
                tableBody.innerHTML = `<tr class="empty-row"><td colspan="7"><div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:3rem;color:#ef4444;"></i><p style="margin-top:10px;">{{ __("Error loading data") }}</p><button onclick="loadReport()" style="margin-top:15px;background:#7c3aed;color:white;border:none;padding:8px 20px;border-radius:30px;cursor:pointer;">{{ __("Try Again") }}</button></div> </td>`;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            async function exportReport() {
                exportBtn.disabled = true;
                const originalHtml = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Exporting...") }}';

                const params = new URLSearchParams({
                    date_from: filters.date_from,
                    date_to: filters.date_to,
                    type: filters.type,
                    source: filters.source,
                    search: filters.search
                });

                try {
                    window.open(`{{ route("admin.reports.points.export") }}?${params}`, '_blank');
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Export Started") }}',
                        text: '{{ __("Your report is being downloaded") }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Export Failed") }}',
                        text: '{{ __("Please try again") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    exportBtn.disabled = false;
                    exportBtn.innerHTML = originalHtml;
                }
            }

            // Event Listeners
            applyBtn.addEventListener('click', updateFilters);
            resetBtn.addEventListener('click', resetFilters);
            exportBtn.addEventListener('click', exportReport);
            filterSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') updateFilters();
            });

            // Initialize
            loadReport();
            window.goToPage = goToPage;
        </script>
    @endpush
@endsection