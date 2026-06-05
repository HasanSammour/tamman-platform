{{-- resources/views/admin/reports/financial.blade.php --}}
@extends('layouts.app')

@section('title', __('Financial Report') . ' - ' . __('Tamman'))

@section('page-title', __('Financial Report'))

@section('content')
    <div class="financial-report-wrapper">
        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryRevenue">${{ number_format($globalStats['total_revenue'] ?? 0, 2) }}</h3>
                    <p>{{ __('Total Revenue') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryDonations">${{ number_format($globalStats['total_donations'] ?? 0, 2) }}</h3>
                    <p>{{ __('Total Donations') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryCredits">${{ number_format($globalStats['total_credits_allocated'] ?? 0, 2) }}</h3>
                    <p>{{ __('Credits Allocated') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-data">
                    <h3 id="summaryPlatformFee">${{ number_format($globalStats['platform_fee'] ?? 0, 2) }}</h3>
                    <p>{{ __('Platform Fee') }}</p>
                    <small>{{ $globalStats['platform_percent'] ?? 10 }}%</small>
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
                <select id="filterSessionType" class="filter-select">
                    <option value="all">{{ __('All Session Types') }}</option>
                    <option value="video">{{ __('Video Sessions') }}</option>
                    <option value="audio">{{ __('Audio Sessions') }}</option>
                    <option value="text">{{ __('Text Sessions') }}</option>
                </select>
                <select id="filterPaymentMethod" class="filter-select">
                    <option value="all">{{ __('All Payment Methods') }}</option>
                    <option value="credit">{{ __('Credit Balance') }}</option>
                    <option value="cash">{{ __('Cash / Bank Transfer') }}</option>
                </select>
            </div>
            <div class="filters-row">
                <div class="search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filterSearch" placeholder="{{ __('Search by patient or specialist...') }}">
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

        <!-- Platform Fee Card (Separate Card) -->
        <div class="platform-fee-card">
            <div class="platform-fee-header">
                <i class="fas fa-percent"></i>
                <h3>{{ __('Platform Fee Settings') }}</h3>
            </div>
            <div class="platform-fee-body">
                <div class="fee-control">
                    <label for="platformPercent">{{ __('Platform Fee Percentage') }}</label>
                    <div class="fee-input-wrapper">
                        <input type="number" id="platformPercent" class="platform-fee-input"
                            value="{{ $globalStats['platform_percent'] ?? 10 }}" step="1" min="0" max="10">
                        <span class="fee-percent-sign">%</span>
                        <span class="fee-note">{{ __('This percentage is deducted from each session') }}</span>
                    </div>
                </div>
                <div class="fee-stats">
                    <div class="fee-stat">
                        <span class="fee-label">{{ __('Current Platform Fee') }}</span>
                        <span class="fee-value" id="currentPlatformFee">$0.00</span>
                    </div>
                    <div class="fee-stat">
                        <span class="fee-label">{{ __('Specialists Total Earnings') }}</span>
                        <span class="fee-value" id="specialistEarnings">$0.00</span>
                    </div>
                </div>
                <button id="applyFeeBtn" class="btn-apply-fee">
                    <i class="fas fa-sync-alt"></i> {{ __('Recalculate with New Fee') }}
                </button>
            </div>
        </div>

        <!-- Revenue Breakdown Section -->
        <div class="revenue-breakdown">
            <div class="section-header">
                <h3><i class="fas fa-chart-pie"></i> {{ __('Revenue Breakdown by Session Type') }}</h3>
            </div>
            <div class="breakdown-cards">
                <div class="breakdown-card video">
                    <div class="breakdown-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="breakdown-info">
                        <h4>{{ __('Video Sessions') }}</h4>
                        <div class="breakdown-stats">
                            <span class="count" id="videoCount">0</span>
                            <span class="separator">|</span>
                            <span class="revenue" id="videoRevenue">$0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="videoProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="breakdown-card audio">
                    <div class="breakdown-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="breakdown-info">
                        <h4>{{ __('Audio Sessions') }}</h4>
                        <div class="breakdown-stats">
                            <span class="count" id="audioCount">0</span>
                            <span class="separator">|</span>
                            <span class="revenue" id="audioRevenue">$0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="audioProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="breakdown-card text">
                    <div class="breakdown-icon">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="breakdown-info">
                        <h4>{{ __('Text Sessions') }}</h4>
                        <div class="breakdown-stats">
                            <span class="count" id="textCount">0</span>
                            <span class="separator">|</span>
                            <span class="revenue" id="textRevenue">$0</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="textProgress" style: "width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Revenue Trend (Last 6 Months)') }}</h3>
                </div>
                <div class="chart-body">
                    <div id="revenueTrendChart" class="apex-chart"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Revenue Distribution') }}</h3>
                </div>
                <div class="chart-body">
                    <div id="revenueDistributionChart" class="apex-chart"></div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="table-card">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> {{ __('Recent Transactions') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <div class="table-scroll-wrapper">
                <div class="table-responsive-inner">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="session_datetime" class="sortable">{{ __('Date') }} <i
                                        class="fas fa-sort"></i></th>
                                <th data-sort="patient_name">{{ __('Patient') }}</th>
                                <th data-sort="specialist_name">{{ __('Specialist') }}</th>
                                <th data-sort="session_type">{{ __('Type') }}</th>
                                <th data-sort="amount" class="sortable">{{ __('Amount') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="payment_method">{{ __('Payment') }}</th>
                                <th data-sort="platform_fee">{{ __('Platform Fee') }}</th>
                                <th data-sort="specialist_earning">{{ __('Specialist Earning') }}</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr class="loading-row">
                                <td colspan="9">
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
                           IMPORTANT: Fix main layout
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

            .app-sidebar.collapsed+.app-main {
                margin-left: 80px;
                width: calc(100% - 80px);
                max-width: calc(100% - 80px);
            }

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
            .financial-report-wrapper {
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

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
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

            .stat-data small {
                font-size: 0.6rem;
                color: #9ca3af;
                display: block;
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

            .filter-select {
                flex: 1;
                min-width: 160px;
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
                           PLATFORM FEE CARD (Separate Card)
                           ============================================ */
            .platform-fee-card {
                background: white;
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                border: 1px solid #f0f0f0;
            }

            .platform-fee-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .platform-fee-header i {
                font-size: 1.2rem;
                color: #7c3aed;
            }

            .platform-fee-header h3 {
                margin: 0;
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
            }

            .platform-fee-body {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            }

            .fee-control {
                flex: 2;
                min-width: 250px;
            }

            .fee-control label {
                display: block;
                font-size: 0.75rem;
                font-weight: 500;
                color: #374151;
                margin-bottom: 8px;
            }

            .fee-input-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .platform-fee-input {
                width: 80px;
                padding: 8px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.9rem;
                text-align: center;
            }

            .platform-fee-input:focus {
                outline: none;
                border-color: #7c3aed;
            }

            .fee-percent-sign {
                font-size: 0.9rem;
                color: #6b7280;
            }

            .fee-note {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .fee-stats {
                flex: 1;
                min-width: 200px;
                display: flex;
                gap: 20px;
            }

            .fee-stat {
                background: #f9fafb;
                padding: 12px 16px;
                border-radius: 12px;
                text-align: center;
                flex: 1;
            }

            .fee-label {
                display: block;
                font-size: 0.65rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .fee-value {
                display: block;
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .btn-apply-fee {
                background: #ede9fe;
                color: #7c3aed;
                border: none;
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .btn-apply-fee:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            /* ============================================
                           REVENUE BREAKDOWN
                           ============================================ */
            .revenue-breakdown {
                margin-bottom: 25px;
            }

            .section-header {
                margin-bottom: 16px;
            }

            .section-header h3 {
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .section-header h3 i {
                color: #7c3aed;
            }

            .breakdown-cards {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            .breakdown-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
            }

            .breakdown-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .breakdown-card.video .breakdown-icon {
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                color: #7c3aed;
            }

            .breakdown-card.audio .breakdown-icon {
                background: linear-gradient(135deg, #d1fae5, #a7f3d0);
                color: #059669;
            }

            .breakdown-card.text .breakdown-icon {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                color: #d97706;
            }

            .breakdown-icon {
                width: 55px;
                height: 55px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .breakdown-icon i {
                font-size: 1.5rem;
            }

            .breakdown-info {
                flex: 1;
            }

            .breakdown-info h4 {
                font-size: 0.9rem;
                font-weight: 600;
                margin: 0 0 5px;
                color: #1f2937;
            }

            .breakdown-stats {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
            }

            .breakdown-stats .count {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .breakdown-stats .separator {
                color: #e5e7eb;
            }

            .breakdown-stats .revenue {
                font-size: 0.9rem;
                font-weight: 600;
                color: #1f2937;
            }

            .progress-bar {
                height: 6px;
                background: #e5e7eb;
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 3px;
                transition: width 0.5s ease;
            }

            .breakdown-card.video .progress-fill {
                background: #7c3aed;
            }

            .breakdown-card.audio .progress-fill {
                background: #10b981;
            }

            .breakdown-card.text .progress-fill {
                background: #f59e0b;
            }

            /* ============================================
                           CHARTS ROW
                           ============================================ */
            .charts-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .chart-card {
                background: white;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .chart-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
            }

            .chart-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
            }

            .chart-header h3 i {
                color: #7c3aed;
            }

            .chart-body {
                padding: 16px;
                min-height: 300px;
            }

            .apex-chart {
                width: 100%;
                min-height: 280px;
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

            .table-scroll-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive-inner {
                min-width: 1000px;
                width: 100%;
            }

            .transactions-table {
                width: 100%;
                border-collapse: collapse;
            }

            .transactions-table th,
            .transactions-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                white-space: nowrap;
            }

            .transactions-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
                cursor: pointer;
            }

            .transactions-table th i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .transactions-table td {
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

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .badge-video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .badge-audio {
                background: #d1fae5;
                color: #059669;
            }

            .badge-text {
                background: #fef3c7;
                color: #d97706;
            }

            .payment-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .payment-credit {
                background: #d1fae5;
                color: #065f46;
            }

            .payment-cash {
                background: #fef3c7;
                color: #d97706;
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

            .empty-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            /* Pagination - Fixed to show 5-7 buttons */
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

            body.rtl .sessions-table th,
            body.rtl .sessions-table td {
                text-align: right;
            }

            body.rtl .sessions-table th i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .clear-search-btn {
                right: auto;
                left: 10px;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .breakdown-cards {
                    gap: 15px;
                }
            }

            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                }

                .breakdown-cards {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .charts-row {
                    grid-template-columns: 1fr;
                }

                .platform-fee-body {
                    flex-direction: column;
                    align-items: stretch;
                }

                .fee-stats {
                    justify-content: space-between;
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

                .fee-stats {
                    flex-direction: column;
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
            }

            /* RTL Support */
            body.rtl .transactions-table th,
            body.rtl .transactions-table td {
                text-align: right;
            }

            body.rtl .transactions-table th i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .breakdown-card {
                flex-direction: row;
            }

            body.rtl .section-header h3 {
                justify-content: flex-start;
            }

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
        </style>
    @endpush

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let currentPage = 1;
    let perPage = 15;
    let sortField = 'session_datetime';
    let sortDirection = 'desc';
    let revenueTrendChart = null;
    let revenueDistributionChart = null;

    let filters = {
        date_from: '',
        date_to: '',
        session_type: 'all',
        payment_method: 'all',
        search: '',
        platform_percent: {{ $globalStats['platform_percent'] ?? 10 }}
    };

    // DOM Elements
    const tableBody = document.getElementById('reportTableBody');
    const tableInfo = document.getElementById('tableInfo');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    const filterSessionType = document.getElementById('filterSessionType');
    const filterPaymentMethod = document.getElementById('filterPaymentMethod');
    const filterSearch = document.getElementById('filterSearch');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const applyBtn = document.getElementById('applyFiltersBtn');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const exportBtn = document.getElementById('exportReportBtn');
    const platformPercentInput = document.getElementById('platformPercent');
    const applyFeeBtn = document.getElementById('applyFeeBtn');
    const currentPlatformFeeSpan = document.getElementById('currentPlatformFee');
    const specialistEarningsSpan = document.getElementById('specialistEarnings');

    // Stats Elements
    const summaryRevenue = document.getElementById('summaryRevenue');
    const summaryDonations = document.getElementById('summaryDonations');
    const summaryCredits = document.getElementById('summaryCredits');
    const summaryPlatformFee = document.getElementById('summaryPlatformFee');

    // Breakdown Elements
    const videoCount = document.getElementById('videoCount');
    const videoRevenue = document.getElementById('videoRevenue');
    const videoProgress = document.getElementById('videoProgress');
    const audioCount = document.getElementById('audioCount');
    const audioRevenue = document.getElementById('audioRevenue');
    const audioProgress = document.getElementById('audioProgress');
    const textCount = document.getElementById('textCount');
    const textRevenue = document.getElementById('textRevenue');
    const textProgress = document.getElementById('textProgress');

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
    if (filterSearch) {
        filterSearch.addEventListener('input', function () {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = this.value ? 'block' : 'none';
            }
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function () {
            if (filterSearch) {
                filterSearch.value = '';
                clearSearchBtn.style.display = 'none';
                updateFilters();
            }
        });
    }

    function updateFilters() {
        filters.date_from = filterDateFrom ? filterDateFrom.value : '';
        filters.date_to = filterDateTo ? filterDateTo.value : '';
        filters.session_type = filterSessionType ? filterSessionType.value : 'all';
        filters.payment_method = filterPaymentMethod ? filterPaymentMethod.value : 'all';
        filters.search = filterSearch ? filterSearch.value : '';
        filters.platform_percent = platformPercentInput ? (parseInt(platformPercentInput.value) || 0) : 10;
        currentPage = 1;
        loadReport();
        renderRevenueTrendChart();
    }

    function resetFilters() {
        if (filterDateFrom) filterDateFrom.value = '';
        if (filterDateTo) filterDateTo.value = '';
        if (filterSessionType) filterSessionType.value = 'all';
        if (filterPaymentMethod) filterPaymentMethod.value = 'all';
        if (filterSearch) filterSearch.value = '';
        if (clearSearchBtn) clearSearchBtn.style.display = 'none';
        if (platformPercentInput) platformPercentInput.value = '{{ $globalStats['platform_percent'] ?? 10 }}';
        updateFilters();
    }

    async function loadReport() {
        if (!tableBody) return;
        
        tableBody.innerHTML = `<tr class="loading-row"><td colspan="9"><div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading data...') }}</p></div> </td></tr>`;

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
                date_from: filters.date_from,
                date_to: filters.date_to,
                session_type: filters.session_type,
                payment_method: filters.payment_method,
                search: filters.search,
                platform_percent: filters.platform_percent
            });

            const response = await fetch(`{{ route("admin.reports.financial.data") }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success) {
                renderTable(data.data);
                renderPagination(data);
                updateStats(data.stats);
                updateBreakdown(data.breakdown);
                updateFeeStats(data.stats);
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
            if (summaryRevenue) summaryRevenue.textContent = '$' + (stats.total_revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (summaryDonations) summaryDonations.textContent = '$' + (stats.total_donations || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (summaryCredits) summaryCredits.textContent = '$' + (stats.total_credits_allocated || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (summaryPlatformFee) summaryPlatformFee.textContent = '$' + (stats.platform_fee || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function updateFeeStats(stats) {
        if (stats) {
            if (currentPlatformFeeSpan) currentPlatformFeeSpan.textContent = '$' + (stats.platform_fee || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (specialistEarningsSpan) specialistEarningsSpan.textContent = '$' + (stats.specialist_earnings || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function updateBreakdown(breakdown) {
        if (breakdown) {
            const total = breakdown.video_revenue + breakdown.audio_revenue + breakdown.text_revenue;
            
            if (videoCount) videoCount.textContent = breakdown.video_count || 0;
            if (videoRevenue) videoRevenue.textContent = '$' + (breakdown.video_revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (videoProgress) videoProgress.style.width = total > 0 ? ((breakdown.video_revenue / total) * 100) + '%' : '0%';
            
            if (audioCount) audioCount.textContent = breakdown.audio_count || 0;
            if (audioRevenue) audioRevenue.textContent = '$' + (breakdown.audio_revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (audioProgress) audioProgress.style.width = total > 0 ? ((breakdown.audio_revenue / total) * 100) + '%' : '0%';
            
            if (textCount) textCount.textContent = breakdown.text_count || 0;
            if (textRevenue) textRevenue.textContent = '$' + (breakdown.text_revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (textProgress) textProgress.style.width = total > 0 ? ((breakdown.text_revenue / total) * 100) + '%' : '0%';
        }
    }

    function renderTable(transactions) {
        if (!transactions || transactions.length === 0) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="9"><div style="text-align:center;padding:40px;"><i class="fas fa-chart-line" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No transactions found') }}</p></div>纽约<\/td><\/tr>`;
            return;
        }

        tableBody.innerHTML = transactions.map(transaction => {
            // Patient Avatar
            let patientAvatarHtml = '';
            if (transaction.patient_profile_image) {
                patientAvatarHtml = `<img src="${transaction.patient_profile_image}" alt="${escapeHtml(transaction.patient_name)}" onerror="this.style.display='none';this.parentElement.textContent='${escapeHtml((transaction.patient_name?.charAt(0) || 'P').toUpperCase())}'">`;
            } else {
                const initial = (transaction.patient_name?.charAt(0) || 'P').toUpperCase();
                patientAvatarHtml = initial;
            }

            // Specialist Avatar
            let specialistAvatarHtml = '';
            if (transaction.specialist_profile_image) {
                specialistAvatarHtml = `<img src="${transaction.specialist_profile_image}" alt="${escapeHtml(transaction.specialist_name)}" onerror="this.style.display='none';this.parentElement.textContent='${escapeHtml((transaction.specialist_name?.charAt(0) || 'S').toUpperCase())}'">`;
            } else {
                const initial = (transaction.specialist_name?.charAt(0) || 'S').toUpperCase();
                specialistAvatarHtml = initial;
            }

            const transactionDate = transaction.session_datetime ? new Date(transaction.session_datetime) : null;
            const formattedDate = transactionDate ? transactionDate.toLocaleDateString() : '-';

            let typeClass = transaction.session_type === 'video' ? 'badge-video' : (transaction.session_type === 'audio' ? 'badge-audio' : 'badge-text');
            let typeText = transaction.session_type === 'video' ? '{{ __("Video") }}' : (transaction.session_type === 'audio' ? '{{ __("Audio") }}' : '{{ __("Text") }}');
            let paymentClass = transaction.is_paid_by_credit ? 'payment-credit' : 'payment-cash';
            let paymentText = transaction.is_paid_by_credit ? '{{ __("Credit") }}' : '{{ __("Cash") }}';

            return `
                <tr>
                    <td>#${transaction.id}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <div class="user-avatar-cell">
                            <div class="avatar-img">${patientAvatarHtml}</div>
                            <div>
                                <div class="user-name">${escapeHtml(transaction.patient_name || '-')}</div>
                                <div class="user-email-small">${escapeHtml(transaction.patient_email || '-')}</div>
                            </div>
                        </div>
                    </div>
                    <td>
                        <div class="user-avatar-cell">
                            <div class="avatar-img">${specialistAvatarHtml}</div>
                            <div>
                                <div class="user-name">${escapeHtml(transaction.specialist_name || '-')}</div>
                                <div class="user-email-small">${escapeHtml(transaction.specialist_email || '-')}</div>
                            </div>
                        </div>
                    </div>
                    <td><span class="badge ${typeClass}">${typeText}</span></td>
                    <td><strong>$${parseFloat(transaction.amount || 0).toFixed(2)}</strong></td>
                    <td><span class="payment-badge ${paymentClass}">${paymentText}</span></td>
                    <td>$${parseFloat(transaction.platform_fee || 0).toFixed(2)}</div>
                    <td><strong>$${parseFloat(transaction.specialist_earning || 0).toFixed(2)}</strong></td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(data) {
        const total = data.total;
        const current = data.current_page;
        const last = data.last_page;

        if (total === 0) {
            if (tableInfo) tableInfo.innerHTML = '{{ __("No transactions found") }}';
            if (paginationInfo) paginationInfo.innerHTML = '';
            if (paginationControls) paginationControls.innerHTML = '';
            return;
        }

        const from = (current - 1) * perPage + 1;
        const to = Math.min(current * perPage, total);

        if (tableInfo) tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total.toLocaleString()} {{ __('transactions') }}`;
        if (paginationInfo) paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;

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

        if (paginationControls) paginationControls.innerHTML = html;
    }

    function goToPage(page) {
        currentPage = page;
        loadReport();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showError() {
        if (tableBody) {
            tableBody.innerHTML = `<tr class="empty-row"><td colspan="9"><div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:3rem;color:#ef4444;"></i><p style="margin-top:10px;">{{ __("Error loading data") }}</p><button onclick="loadReport()" style="margin-top:15px;background:#7c3aed;color:white;border:none;padding:8px 20px;border-radius:30px;cursor:pointer;">{{ __("Try Again") }}</button></div></td></tr>`;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
    }

    // Revenue Trend Chart
    async function renderRevenueTrendChart() {
        const params = new URLSearchParams({
            date_from: filters.date_from,
            date_to: filters.date_to,
            session_type: filters.session_type,
            payment_method: filters.payment_method,
            search: filters.search,
            platform_percent: filters.platform_percent,
            chart: 'true'
        });

        try {
            const response = await fetch(`{{ route("admin.reports.financial.data") }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success && data.chart_data && document.getElementById("revenueTrendChart") && document.getElementById("revenueDistributionChart")) {
                const currentLocale = '{{ app()->getLocale() }}';
                
                const trendOptions = {
                    series: [{
                        name: currentLocale === 'ar' ? 'الإيرادات' : 'Revenue',
                        data: data.chart_data.revenue
                    }],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
                    markers: { size: 4, hover: { size: 7 }, colors: ['#7c3aed'], strokeColors: '#ffffff' },
                    tooltip: { y: { formatter: (val) => '$' + val.toFixed(2) } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: data.chart_data.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'الإيرادات (دولار)' : 'Revenue (USD)' }, labels: { formatter: (val) => '$' + val.toFixed(0) } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                };

                const distributionOptions = {
                    series: [data.chart_data.video_revenue, data.chart_data.audio_revenue, data.chart_data.text_revenue],
                    chart: {
                        type: 'donut',
                        height: 280,
                        toolbar: { show: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter'
                    },
                    labels: [
                        currentLocale === 'ar' ? 'جلسات فيديو' : 'Video Sessions',
                        currentLocale === 'ar' ? 'جلسات صوتية' : 'Audio Sessions',
                        currentLocale === 'ar' ? 'جلسات نصية' : 'Text Sessions'
                    ],
                    colors: ['#7c3aed', '#10b981', '#f59e0b'],
                    legend: { position: 'bottom', labels: { colors: '#374151' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '13px' },
                                    value: { show: true, fontSize: '14px', fontWeight: 'bold', formatter: (val) => '$' + val.toFixed(2) },
                                    total: { show: true, label: currentLocale === 'ar' ? 'المجموع' : 'Total', fontSize: '13px', formatter: (val) => '$' + val.toFixed(2) }
                                }
                            }
                        }
                    },
                    tooltip: { y: { formatter: (val) => '$' + val.toFixed(2), theme: 'dark' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                };

                const trendElement = document.querySelector("#revenueTrendChart");
                const distributionElement = document.querySelector("#revenueDistributionChart");

                if (trendElement && typeof ApexCharts !== 'undefined') {
                    if (revenueTrendChart) revenueTrendChart.destroy();
                    revenueTrendChart = new ApexCharts(trendElement, trendOptions);
                    revenueTrendChart.render();
                }

                if (distributionElement && typeof ApexCharts !== 'undefined') {
                    if (revenueDistributionChart) revenueDistributionChart.destroy();
                    revenueDistributionChart = new ApexCharts(distributionElement, distributionOptions);
                    revenueDistributionChart.render();
                }
            }
        } catch (error) {
            console.error('Chart error:', error);
        }
    }

    // Platform Fee Input Validation with SweetAlert
    if (platformPercentInput) {
        platformPercentInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            let showAlert = false;
            let alertMessage = '';
            let correctedValue = value;
            
            if (isNaN(value)) {
                correctedValue = {{ $globalStats['platform_percent'] ?? 10 }};
                showAlert = true;
                alertMessage = '{{ __("Please enter a valid number") }}';
            } else if (value < 0) {
                correctedValue = 0;
                showAlert = true;
                alertMessage = '{{ __("Platform fee cannot be less than 0%") }}';
            } else if (value > 10) {
                correctedValue = 10;
                showAlert = true;
                alertMessage = '{{ __("Platform fee cannot exceed 10%") }}';
            }
            
            if (showAlert) {
                this.value = correctedValue;
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("Invalid Percentage") }}',
                    text: alertMessage,
                    confirmButtonColor: '#7c3aed',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update filters and reload
                filters.platform_percent = correctedValue;
                loadReport();
                renderRevenueTrendChart();
            }
        });
    }

    // Apply Fee Button with Validation
    if (applyFeeBtn) {
        applyFeeBtn.addEventListener('click', () => {
            let value = parseInt(platformPercentInput.value);
            
            if (isNaN(value)) {
                platformPercentInput.value = {{ $globalStats['platform_percent'] ?? 10 }};
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("Invalid Input") }}',
                    text: '{{ __("Please enter a valid number") }}',
                    confirmButtonColor: '#7c3aed'
                });
                return;
            }
            
            if (value < 0) {
                platformPercentInput.value = 0;
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("Invalid Percentage") }}',
                    text: '{{ __("Platform fee cannot be less than 0%") }}',
                    confirmButtonColor: '#7c3aed'
                });
                return;
            }
            
            if (value > 10) {
                platformPercentInput.value = 10;
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("Invalid Percentage") }}',
                    text: '{{ __("Platform fee cannot exceed 10%") }}',
                    confirmButtonColor: '#7c3aed'
                });
                return;
            }
            
            updateFilters();
        });
    }

    async function exportReport() {
        if (!exportBtn) return;
        
        exportBtn.disabled = true;
        const originalHtml = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Exporting...") }}';

        const params = new URLSearchParams({
            date_from: filters.date_from,
            date_to: filters.date_to,
            session_type: filters.session_type,
            payment_method: filters.payment_method,
            search: filters.search,
            platform_percent: filters.platform_percent
        });

        try {
            window.open(`{{ route("admin.reports.financial.export") }}?${params}`, '_blank');
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
    if (applyBtn) applyBtn.addEventListener('click', updateFilters);
    if (resetBtn) resetBtn.addEventListener('click', resetFilters);
    if (exportBtn) exportBtn.addEventListener('click', exportReport);
    if (filterSearch) {
        filterSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') updateFilters();
        });
    }

    // Initialize
    loadReport();
    renderRevenueTrendChart();
    window.goToPage = goToPage;

    // Re-render charts on sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => { setTimeout(() => renderRevenueTrendChart(), 300); });
    }
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => { setTimeout(() => renderRevenueTrendChart(), 350); });
    }
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => renderRevenueTrendChart(), 250);
    });
</script>
    
    @endpush
@endsection