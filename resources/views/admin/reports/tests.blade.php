{{-- resources/views/admin/reports/tests.blade.php --}}
@extends('layouts.app')

@section('title', __('Tests Report') . ' - ' . __('Tamman'))

@section('page-title', __('Tests Report'))

@section('content')
    <div class="tests-report-wrapper">
        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryTotal">{{ number_format($globalStats['total_tests'] ?? 0) }}</h3>
                    <p>{{ __('Total Tests') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryUnique">{{ number_format($globalStats['unique_users'] ?? 0) }}</h3>
                    <p>{{ __('Unique Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryAvgScore">{{ number_format($globalStats['avg_score'] ?? 0, 1) }}</h3>
                    <p>{{ __('Average Score') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryMostCommon">{{ $globalStats['most_common_test'] ?? '-' }}</h3>
                    <p>{{ __('Most Common Test') }}</p>
                </div>
            </div>
        </div>

        <!-- Test Types Summary Cards - 3x2 Grid -->
        <div class="test-types-summary">
            <div class="test-type-card phq9">
                <div class="test-type-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="test-type-info">
                    <h4>PHQ-9</h4>
                    <p>{{ __('Depression') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="phq9Count">{{ number_format($testTypeCounts['phq9'] ?? 0) }}</span>
                    <span class="stat-avg" id="phq9Avg">{{ number_format($testTypeAverages['phq9'] ?? 0, 1) }}</span>
                </div>
            </div>
            <div class="test-type-card gad7">
                <div class="test-type-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="test-type-info">
                    <h4>GAD-7</h4>
                    <p>{{ __('Anxiety') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="gad7Count">{{ number_format($testTypeCounts['gad7'] ?? 0) }}</span>
                    <span class="stat-avg" id="gad7Avg">{{ number_format($testTypeAverages['gad7'] ?? 0, 1) }}</span>
                </div>
            </div>
            <div class="test-type-card pcl5">
                <div class="test-type-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="test-type-info">
                    <h4>PCL-5</h4>
                    <p>{{ __('PTSD') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="pcl5Count">{{ number_format($testTypeCounts['pcl5'] ?? 0) }}</span>
                    <span class="stat-avg" id="pcl5Avg">{{ number_format($testTypeAverages['pcl5'] ?? 0, 1) }}</span>
                </div>
            </div>
            <div class="test-type-card isi">
                <div class="test-type-icon">
                    <i class="fas fa-moon"></i>
                </div>
                <div class="test-type-info">
                    <h4>ISI</h4>
                    <p>{{ __('Insomnia') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="isiCount">{{ number_format($testTypeCounts['isi'] ?? 0) }}</span>
                    <span class="stat-avg" id="isiAvg">{{ number_format($testTypeAverages['isi'] ?? 0, 1) }}</span>
                </div>
            </div>
            <div class="test-type-card pss">
                <div class="test-type-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="test-type-info">
                    <h4>PSS</h4>
                    <p>{{ __('Stress') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="pssCount">{{ number_format($testTypeCounts['pss'] ?? 0) }}</span>
                    <span class="stat-avg" id="pssAvg">{{ number_format($testTypeAverages['pss'] ?? 0, 1) }}</span>
                </div>
            </div>
            <div class="test-type-card cis">
                <div class="test-type-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="test-type-info">
                    <h4>CIS</h4>
                    <p>{{ __('Functioning') }}</p>
                </div>
                <div class="test-type-stats">
                    <span class="stat-count" id="cisCount">{{ number_format($testTypeCounts['cis'] ?? 0) }}</span>
                    <span class="stat-avg" id="cisAvg">{{ number_format($testTypeAverages['cis'] ?? 0, 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="filters-container">
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
                <select id="filterTestType" class="filter-select">
                    <option value="all">{{ __('All Test Types') }}</option>
                    <option value="phq9">PHQ-9 ({{ __('Depression') }})</option>
                    <option value="gad7">GAD-7 ({{ __('Anxiety') }})</option>
                    <option value="pcl5">PCL-5 ({{ __('PTSD') }})</option>
                    <option value="isi">ISI ({{ __('Insomnia') }})</option>
                    <option value="pss">PSS ({{ __('Stress') }})</option>
                    <option value="cis">CIS ({{ __('Functioning') }})</option>
                </select>
                <select id="filterResultLevel" class="filter-select">
                    <option value="all">{{ __('All Results') }}</option>
                    <option value="minimal">{{ __('Minimal') }}</option>
                    <option value="mild">{{ __('Mild') }}</option>
                    <option value="moderate">{{ __('Moderate') }}</option>
                    <option value="severe">{{ __('Severe') }}</option>
                </select>
            </div>

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
                <h4><i class="fas fa-list"></i> {{ __('Tests List') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <div class="table-scroll-wrapper">
                <div class="table-responsive-inner">
                    <table class="tests-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="user_name">{{ __('User') }}</th>
                                <th data-sort="test_type">{{ __('Test Type') }}</th>
                                <th data-sort="score" class="sortable">{{ __('Score') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="result_level">{{ __('Result Level') }}</th>
                                <th data-sort="test_date" class="sortable">{{ __('Date') }} <i class="fas fa-sort"></i></th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr class="loading-row">
                                <td colspan="6">
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
            .tests-report-wrapper {
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

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
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
                           TEST TYPES SUMMARY CARDS - 3x2 GRID
                           ============================================ */
            .test-types-summary {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .test-type-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.2s;
                position: relative;
                overflow: hidden;
            }

            .test-type-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 6px;
                height: 100%;
            }

            .test-type-card.phq9::before {
                background: #7c3aed;
            }

            .test-type-card.gad7::before {
                background: #10b981;
            }

            .test-type-card.pcl5::before {
                background: #f59e0b;
            }

            .test-type-card.isi::before {
                background: #ef4444;
            }

            .test-type-card.pss::before {
                background: #ec4899;
            }

            .test-type-card.cis::before {
                background: #06b6d4;
            }

            .test-type-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .test-type-icon {
                width: 55px;
                height: 55px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .test-type-card.phq9 .test-type-icon {
                background: rgba(124, 58, 237, 0.12);
                color: #7c3aed;
            }

            .test-type-card.gad7 .test-type-icon {
                background: rgba(16, 185, 129, 0.12);
                color: #10b981;
            }

            .test-type-card.pcl5 .test-type-icon {
                background: rgba(245, 158, 11, 0.12);
                color: #f59e0b;
            }

            .test-type-card.isi .test-type-icon {
                background: rgba(239, 68, 68, 0.12);
                color: #ef4444;
            }

            .test-type-card.pss .test-type-icon {
                background: rgba(236, 72, 153, 0.12);
                color: #ec4899;
            }

            .test-type-card.cis .test-type-icon {
                background: rgba(6, 182, 212, 0.12);
                color: #06b6d4;
            }

            .test-type-icon i {
                font-size: 1.4rem;
            }

            .test-type-info {
                flex: 1;
            }

            .test-type-info h4 {
                font-size: 1rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .test-type-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 4px 0 0;
            }

            .test-type-stats {
                text-align: right;
            }

            .test-type-stats .stat-count {
                display: block;
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .test-type-stats .stat-avg {
                display: block;
                font-size: 0.7rem;
                color: #9ca3af;
                margin-top: 2px;
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

            /* Table Scroll */
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
                min-width: 800px;
                width: 100%;
            }

            .tests-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 100%;
            }

            .tests-table th,
            .tests-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                white-space: nowrap;
            }

            .tests-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
                cursor: pointer;
            }

            .tests-table th i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .tests-table td {
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

            /* Test Type Badge */
            .test-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .test-phq9 {
                background: rgba(124, 58, 237, 0.12);
                color: #7c3aed;
            }

            .test-gad7 {
                background: rgba(16, 185, 129, 0.12);
                color: #10b981;
            }

            .test-pcl5 {
                background: rgba(245, 158, 11, 0.12);
                color: #f59e0b;
            }

            .test-isi {
                background: rgba(239, 68, 68, 0.12);
                color: #ef4444;
            }

            .test-pss {
                background: rgba(236, 72, 153, 0.12);
                color: #ec4899;
            }

            .test-cis {
                background: rgba(6, 182, 212, 0.12);
                color: #06b6d4;
            }

            /* Result Level Badge */
            .level-badge {
                display: inline-flex;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .level-minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .level-mild {
                background: #fef3c7;
                color: #92400e;
            }

            .level-moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .level-severe {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Loading */
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

            @media (max-width: 1200px) {
                .test-types-summary {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

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

                .test-types-summary {
                    grid-template-columns: 1fr;
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

            body.rtl .tests-table th,
            body.rtl .tests-table td {
                text-align: right;
            }

            body.rtl .tests-table th i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .clear-search-btn {
                right: auto;
                left: 10px;
            }

            body.rtl .test-type-stats {
                text-align: left;
            }

            body.rtl .user-avatar-cell {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1;
            let perPage = 15;
            let sortField = 'test_date';
            let sortDirection = 'desc';

            let filters = {
                date_from: '',
                date_to: '',
                test_type: 'all',
                result_level: 'all',
                search: ''
            };

            // DOM Elements
            const tableBody = document.getElementById('reportTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');

            // Stats Elements
            const summaryTotal = document.getElementById('summaryTotal');
            const summaryUnique = document.getElementById('summaryUnique');
            const summaryAvgScore = document.getElementById('summaryAvgScore');
            const summaryMostCommon = document.getElementById('summaryMostCommon');

            // Test Type Stats Elements
            const phq9Count = document.getElementById('phq9Count');
            const phq9Avg = document.getElementById('phq9Avg');
            const gad7Count = document.getElementById('gad7Count');
            const gad7Avg = document.getElementById('gad7Avg');
            const pcl5Count = document.getElementById('pcl5Count');
            const pcl5Avg = document.getElementById('pcl5Avg');
            const isiCount = document.getElementById('isiCount');
            const isiAvg = document.getElementById('isiAvg');
            const pssCount = document.getElementById('pssCount');
            const pssAvg = document.getElementById('pssAvg');
            const cisCount = document.getElementById('cisCount');
            const cisAvg = document.getElementById('cisAvg');

            // Filter Elements
            const filterDateFrom = document.getElementById('filterDateFrom');
            const filterDateTo = document.getElementById('filterDateTo');
            const filterTestType = document.getElementById('filterTestType');
            const filterResultLevel = document.getElementById('filterResultLevel');
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
                filters.test_type = filterTestType.value;
                filters.result_level = filterResultLevel.value;
                filters.search = filterSearch.value;
                currentPage = 1;
                loadReport();
            }

            function resetFilters() {
                filterDateFrom.value = '';
                filterDateTo.value = '';
                filterTestType.value = 'all';
                filterResultLevel.value = 'all';
                filterSearch.value = '';
                clearSearchBtn.style.display = 'none';
                updateFilters();
            }

            async function loadReport() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="6"><div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading data...') }}</p></div></td></tr>`;

                try {
                    const params = new URLSearchParams({
                        page: currentPage,
                        per_page: perPage,
                        sort_field: sortField,
                        sort_direction: sortDirection,
                        date_from: filters.date_from,
                        date_to: filters.date_to,
                        test_type: filters.test_type,
                        result_level: filters.result_level,
                        search: filters.search
                    });

                    const response = await fetch(`{{ route("admin.reports.tests.data") }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success) {
                        renderTable(data.data);
                        renderPagination(data);
                        updateStats(data.stats);
                        updateTestTypeStats(data.test_type_counts, data.test_type_averages);
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
                    if (summaryTotal) summaryTotal.textContent = (stats.total_tests || 0).toLocaleString();
                    if (summaryUnique) summaryUnique.textContent = (stats.unique_users || 0).toLocaleString();
                    if (summaryAvgScore) summaryAvgScore.textContent = parseFloat(stats.avg_score || 0).toFixed(1);
                    if (summaryMostCommon) summaryMostCommon.textContent = stats.most_common_test || '-';
                }
            }

            function updateTestTypeStats(counts, averages) {
                if (counts) {
                    // Display counts correctly
                    if (phq9Count) phq9Count.textContent = (counts.phq9 || 0).toLocaleString();
                    if (gad7Count) gad7Count.textContent = (counts.gad7 || 0).toLocaleString();
                    if (pcl5Count) pcl5Count.textContent = (counts.pcl5 || 0).toLocaleString();
                    if (isiCount) isiCount.textContent = (counts.isi || 0).toLocaleString();
                    if (pssCount) pssCount.textContent = (counts.pss || 0).toLocaleString();
                    if (cisCount) cisCount.textContent = (counts.cis || 0).toLocaleString();
                }
                if (averages) {
                    // Display averages directly from controller (NOT calculated)
                    // Use parseFloat to ensure number type, then format to 1 decimal
                    if (phq9Avg) phq9Avg.textContent = (typeof averages.phq9 === 'number' ? averages.phq9 : parseFloat(averages.phq9 || 0)).toFixed(1);
                    if (gad7Avg) gad7Avg.textContent = (typeof averages.gad7 === 'number' ? averages.gad7 : parseFloat(averages.gad7 || 0)).toFixed(1);
                    if (pcl5Avg) pcl5Avg.textContent = (typeof averages.pcl5 === 'number' ? averages.pcl5 : parseFloat(averages.pcl5 || 0)).toFixed(1);
                    if (isiAvg) isiAvg.textContent = (typeof averages.isi === 'number' ? averages.isi : parseFloat(averages.isi || 0)).toFixed(1);
                    if (pssAvg) pssAvg.textContent = (typeof averages.pss === 'number' ? averages.pss : parseFloat(averages.pss || 0)).toFixed(1);
                    if (cisAvg) cisAvg.textContent = (typeof averages.cis === 'number' ? averages.cis : parseFloat(averages.cis || 0)).toFixed(1);
                }
            }

            function renderTable(tests) {
                if (!tests || tests.length === 0) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="6"><div style="text-align:center;padding:40px;"><i class="fas fa-clipboard-list" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No test results found') }}</p></div>纽约<\/td><\/tr>`;
                    return;
                }

                tableBody.innerHTML = tests.map(test => {
                    // User Avatar
                    let userAvatarHtml = '';
                    if (test.user_profile_image) {
                        userAvatarHtml = `<img src="${test.user_profile_image}" alt="${escapeHtml(test.user_name)}" onerror="this.style.display='none';this.parentElement.textContent='${escapeHtml((test.user_name?.charAt(0) || 'U').toUpperCase())}'">`;
                    } else {
                        const initial = (test.user_name?.charAt(0) || 'U').toUpperCase();
                        userAvatarHtml = initial;
                    }

                    const testDate = test.test_date ? new Date(test.test_date) : null;
                    const formattedDate = testDate ? testDate.toLocaleDateString() : '-';

                    let testTypeClass = '';
                    let testTypeText = '';
                    let testTypeIcon = '';
                    switch (test.test_type) {
                        case 'phq9': testTypeClass = 'test-phq9'; testTypeText = 'PHQ-9'; testTypeIcon = 'fa-heartbeat'; break;
                        case 'gad7': testTypeClass = 'test-gad7'; testTypeText = 'GAD-7'; testTypeIcon = 'fa-brain'; break;
                        case 'pcl5': testTypeClass = 'test-pcl5'; testTypeText = 'PCL-5'; testTypeIcon = 'fa-shield-alt'; break;
                        case 'isi': testTypeClass = 'test-isi'; testTypeText = 'ISI'; testTypeIcon = 'fa-moon'; break;
                        case 'pss': testTypeClass = 'test-pss'; testTypeText = 'PSS'; testTypeIcon = 'fa-tachometer-alt'; break;
                        case 'cis': testTypeClass = 'test-cis'; testTypeText = 'CIS'; testTypeIcon = 'fa-chart-bar'; break;
                        default: testTypeClass = ''; testTypeText = test.test_type; testTypeIcon = 'fa-clipboard-list';
                    }

                    let levelClass = '';
                    let levelText = '';
                    switch (test.result_level) {
                        case 'minimal': levelClass = 'level-minimal'; levelText = '{{ __("Minimal") }}'; break;
                        case 'mild': levelClass = 'level-mild'; levelText = '{{ __("Mild") }}'; break;
                        case 'moderate': levelClass = 'level-moderate'; levelText = '{{ __("Moderate") }}'; break;
                        case 'severe': levelClass = 'level-severe'; levelText = '{{ __("Severe") }}'; break;
                        default: levelClass = ''; levelText = test.result_level || '-';
                    }

                    return `
                                <tr>
                                    <td>#${test.id}</td>
                                    <td>
                                        <div class="user-avatar-cell">
                                            <div class="avatar-img">${userAvatarHtml}</div>
                                            <div>
                                                <div class="user-name">${escapeHtml(test.user_name || '-')}</div>
                                                <div class="user-email-small">${escapeHtml(test.user_email || '-')}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="test-badge ${testTypeClass}"><i class="fas ${testTypeIcon}"></i> ${testTypeText}</span></td>
                                    <td><strong>${parseInt(test.score) || 0}</strong></td>
                                    <td><span class="level-badge ${levelClass}">${levelText}</span></td>
                                    <td>${formattedDate}</td>
                                </tr>
                            `;
                }).join('');
            }

            function renderPagination(data) {
                const total = data.total;
                const current = data.current_page;
                const last = data.last_page;

                if (total === 0) {
                    tableInfo.innerHTML = '{{ __("No test results found") }}';
                    paginationInfo.innerHTML = '';
                    paginationControls.innerHTML = '';
                    return;
                }

                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total.toLocaleString()} {{ __('test results') }}`;
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
                tableBody.innerHTML = `<tr class="empty-row"><td colspan="6"><div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:3rem;color:#ef4444;"></i><p style="margin-top:10px;">{{ __("Error loading data") }}</p><button onclick="loadReport()" style="margin-top:15px;background:#7c3aed;color:white;border:none;padding:8px 20px;border-radius:30px;cursor:pointer;">{{ __("Try Again") }}</button></div></td></tr>`;
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
                    test_type: filters.test_type,
                    result_level: filters.result_level,
                    search: filters.search
                });

                try {
                    window.open(`{{ route("admin.reports.tests.export") }}?${params}`, '_blank');
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