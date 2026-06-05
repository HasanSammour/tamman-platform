{{-- resources/views/admin/payments/specialist-payouts.blade.php --}}
@extends('layouts.app')

@section('title', __('Specialist Payouts') . ' - ' . __('Tamman'))

@section('page-title', __('Specialist Payouts'))

@section('content')
    <div class="payouts-container">
        <!-- Filters Card -->
        <div class="filters-card">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> {{ __('Generate Payout Report') }}</h3>
                <p>{{ __('Select month and year to generate payouts for specialists') }}</p>
            </div>
            <div class="filters-body">
                <div class="filter-group">
                    <label for="monthSelect">{{ __('Month') }} <span class="required">*</span></label>
                    <select id="monthSelect" class="form-control">
                        <option value="1">{{ __('January') }}</option>
                        <option value="2">{{ __('February') }}</option>
                        <option value="3" selected>{{ __('March') }}</option>
                        <option value="4">{{ __('April') }}</option>
                        <option value="5">{{ __('May') }}</option>
                        <option value="6">{{ __('June') }}</option>
                        <option value="7">{{ __('July') }}</option>
                        <option value="8">{{ __('August') }}</option>
                        <option value="9">{{ __('September') }}</option>
                        <option value="10">{{ __('October') }}</option>
                        <option value="11">{{ __('November') }}</option>
                        <option value="12">{{ __('December') }}</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="yearSelect">{{ __('Year') }} <span class="required">*</span></label>
                    <select id="yearSelect" class="form-control">
                        @php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 1; $i <= $currentYear + 2; $i++) {
                                echo "<option value=\"{$i}\" " . ($i == $currentYear ? 'selected' : '') . ">{$i}</option>";
                            }
                        @endphp
                    </select>
                </div>
                <div class="filter-group">
                    <label for="platformPercent">{{ __('Platform Fee (%)') }}</label>
                    <input type="number" id="platformPercent" class="form-control" step="1" min="0" max="10" value="5"
                        placeholder="5">
                    <small class="form-hint">{{ __('Percentage to deduct as platform fee (e.g., 10 for 10%)') }}</small>
                </div>
                <div class="filter-actions">
                    <button id="generateReportBtn" class="btn-generate">
                        <i class="fas fa-chart-line"></i> {{ __('Generate Report') }}
                    </button>
                    <button id="exportPayoutPdfBtn" class="btn-export" style="display: none;">
                        <i class="fas fa-file-pdf"></i> {{ __('Export PDF') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
            <div class="bulk-actions-left">
                <i class="fas fa-check-circle"></i>
                <span id="selectedCount">0</span> {{ __('specialists selected') }}
            </div>
            <div class="bulk-actions-right">
                <button id="paySelectedBtn" class="btn-pay-selected" disabled>
                    <i class="fas fa-money-bill-wave"></i> {{ __('Pay Selected') }}
                </button>
                <button id="payAllBtn" class="btn-pay-all">
                    <i class="fas fa-check-double"></i> {{ __('Pay All') }}
                </button>
            </div>
        </div>

        <!-- Report Table Card -->
        <div class="report-card">
            <div class="report-header">
                <h3><i class="fas fa-chart-bar"></i> {{ __('Payout Report') }}</h3>
                <div id="reportInfo" class="report-info"></div>
            </div>

            <!-- Mobile Card View (visible only on mobile) -->
            <div id="mobileCardsView" class="mobile-cards-view"></div>

            <!-- Desktop Table View (visible on desktop, hidden on mobile) -->
            <div class="table-wrapper desktop-view">
                <div class="table-scroll-wrapper">
                    <table class="payouts-table" id="payoutsTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox"
                                        class="select-all-checkbox"></th>
                                <th>{{ __('Specialist') }}</th>
                                <th>{{ __('Sessions') }}</th>
                                <th>{{ __('Fee') }}</th>
                                <th>{{ __('Video') }}</th>
                                <th>{{ __('Audio') }}</th>
                                <th>{{ __('Text') }}</th>
                                <th>{{ __('Earnings') }}</th>
                                <th>{{ __('Platform Fee') }}</th>
                                <th>{{ __('Final') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="payoutsTableBody">
                            <tr class="empty-row">
                                <td colspan="11">
                                    <div class="empty-state">
                                        <i class="fas fa-chart-line"></i>
                                        <p>{{ __('Select month and year, then click "Generate Report" to view payouts.') }}
                                        </p>
                                    </div>
                                    <table>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="report-footer" id="reportFooter" style="display: none;">
                <div class="summary-stats">
                    <div class="summary-item">
                        <span class="summary-label">{{ __('Total Earnings') }}:</span>
                        <span class="summary-value" id="totalEarnings">$0.00</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">{{ __('Total Platform Fees') }}:</span>
                        <span class="summary-value" id="totalPlatformFees">$0.00</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">{{ __('Total Final Amount') }}:</span>
                        <span class="summary-value" id="totalFinalAmount">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>{{ __('Generating report...') }}</p>
        </div>
    </div>

    @push('styles')
        <style>
            .payouts-container {
                max-width: 100%;
                margin: 0 auto;
                padding: 20px;
            }

            /* Filters Card */
            .filters-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                margin-bottom: 25px;
            }

            .filters-header {
                padding: 20px 24px;
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-bottom: 1px solid #e5e7eb;
            }

            .filters-header h3 {
                margin: 0 0 5px;
                font-size: 1.1rem;
                color: #1f2937;
            }

            .filters-header h3 i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .filters-header p {
                margin: 0;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .filters-body {
                padding: 20px 24px;
                display: flex;
                flex-wrap: wrap;
                align-items: flex-end;
                gap: 20px;
            }

            .filter-group {
                flex: 1;
                min-width: 150px;
            }

            .filter-group label {
                display: block;
                margin-bottom: 6px;
                font-size: 0.8rem;
                font-weight: 500;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .form-control {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.85rem;
                background: #f9fafb;
                transition: all 0.3s ease;
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

            .filter-actions {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .btn-generate,
            .btn-export {
                padding: 10px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-generate {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .btn-generate:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            }

            .btn-export {
                background: #ef4444;
                color: white;
            }

            .btn-export:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* Bulk Actions Bar */
            .bulk-actions-bar {
                background: white;
                border-radius: 16px;
                padding: 12px 20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                border-left: 4px solid #7c3aed;
            }

            .bulk-actions-left {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.85rem;
                color: #374151;
            }

            .bulk-actions-left i {
                color: #10b981;
                font-size: 1.1rem;
            }

            .bulk-actions-right {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .btn-pay-selected,
            .btn-pay-all {
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-pay-selected {
                background: #10b981;
                color: white;
            }

            .btn-pay-selected:hover:not(:disabled) {
                background: #059669;
                transform: translateY(-2px);
            }

            .btn-pay-all {
                background: #f59e0b;
                color: white;
            }

            .btn-pay-all:hover:not(:disabled) {
                background: #d97706;
                transform: translateY(-2px);
            }

            .btn-pay-selected:disabled,
            .btn-pay-all:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }

            /* Report Card */
            .report-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .report-header {
                padding: 18px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .report-header h3 {
                margin: 0;
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
            }

            .report-header h3 i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .report-info {
                font-size: 0.75rem;
                color: #6b7280;
            }

            /* Desktop Table View */
            .desktop-view {
                display: block;
            }

            .mobile-cards-view {
                display: none;
            }

            .table-wrapper {
                overflow-x: auto;
            }

            .payouts-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 1000px;
            }

            .payouts-table th,
            .payouts-table td {
                padding: 14px 12px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
            }

            .payouts-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.75rem;
                color: #374151;
            }

            .payouts-table td {
                font-size: 0.8rem;
                color: #4b5563;
            }

            /* Specialist Cell */
            .specialist-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .specialist-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
                overflow: hidden;
                flex-shrink: 0;
            }

            .specialist-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .specialist-name {
                font-weight: 600;
                color: #1f2937;
            }

            .specialist-email {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            /* Status Badges */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .status-paid {
                background: #d1fae5;
                color: #065f46;
            }

            .status-pending {
                background: #fef3c7;
                color: #d97706;
            }

            /* Checkbox */
            .select-all-checkbox,
            .row-checkbox {
                width: 18px;
                height: 18px;
                cursor: pointer;
                accent-color: #7c3aed;
            }

            /* Empty State */
            .empty-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            .empty-state {
                text-align: center;
            }

            .empty-state i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
                display: block;
            }

            .empty-state p {
                color: #6b7280;
                margin: 0;
            }

            /* Report Footer */
            .report-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
            }

            .summary-stats {
                display: flex;
                justify-content: flex-end;
                gap: 30px;
                flex-wrap: wrap;
            }

            .summary-item {
                display: flex;
                align-items: baseline;
                gap: 8px;
                flex-wrap: wrap;
            }

            .summary-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: #6b7280;
            }

            .summary-value {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            /* Loading Overlay */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .loading-content {
                background: white;
                border-radius: 20px;
                padding: 30px 40px;
                text-align: center;
                animation: fadeIn 0.3s ease;
            }

            .loading-content .spinner {
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

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            /* ==================== MOBILE RESPONSIVE ==================== */
            @media (max-width: 992px) {
                .filters-body {
                    flex-direction: column;
                    align-items: stretch;
                }

                .filter-group {
                    width: 100%;
                }

                .filter-actions {
                    flex-direction: column;
                }

                .btn-generate,
                .btn-export {
                    width: 100%;
                    justify-content: center;
                }

                .desktop-view {
                    display: none;
                }

                .mobile-cards-view {
                    display: block;
                    padding: 16px;
                }

                .mobile-specialist-card {
                    background: #f9fafb;
                    border-radius: 16px;
                    padding: 16px;
                    margin-bottom: 16px;
                    border: 1px solid #e5e7eb;
                }

                .mobile-card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 12px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #e5e7eb;
                }

                .mobile-specialist-info {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .mobile-specialist-avatar {
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #7c3aed, #6d28d9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 600;
                    font-size: 1.1rem;
                    overflow: hidden;
                }

                .mobile-specialist-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .mobile-specialist-name {
                    font-weight: 600;
                    color: #1f2937;
                    font-size: 0.9rem;
                }

                .mobile-specialist-email {
                    font-size: 0.7rem;
                    color: #9ca3af;
                }

                .mobile-card-checkbox {
                    transform: scale(1.2);
                }

                .mobile-card-details {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                    margin-bottom: 12px;
                }

                .mobile-detail-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 0;
                    border-bottom: 1px solid #f0f0f0;
                }

                .mobile-detail-label {
                    font-size: 0.7rem;
                    color: #6b7280;
                }

                .mobile-detail-value {
                    font-size: 0.8rem;
                    font-weight: 600;
                    color: #1f2937;
                }

                .mobile-card-status {
                    margin-top: 12px;
                    padding-top: 12px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
            }

            @media (max-width: 768px) {
                .payouts-container {
                    padding: 15px;
                }

                .filters-header {
                    padding: 15px 20px;
                }

                .filters-header h3 {
                    font-size: 1rem;
                }

                .filters-header p {
                    font-size: 0.7rem;
                }

                .filters-body {
                    padding: 15px 20px;
                }

                .report-header {
                    padding: 12px 16px;
                }

                .report-header h3 {
                    font-size: 0.9rem;
                }

                .report-info {
                    font-size: 0.7rem;
                }

                .bulk-actions-bar {
                    flex-direction: column;
                    text-align: center;
                }

                .bulk-actions-left {
                    justify-content: center;
                }

                .bulk-actions-right {
                    width: 100%;
                    flex-direction: column;
                }

                .btn-pay-selected,
                .btn-pay-all {
                    width: 100%;
                    justify-content: center;
                }

                .summary-stats {
                    flex-direction: column;
                    gap: 8px;
                    align-items: flex-start;
                }

                .mobile-card-details {
                    grid-template-columns: 1fr;
                    gap: 8px;
                }
            }

            @media (max-width: 480px) {
                .payouts-container {
                    padding: 10px;
                }

                .mobile-specialist-avatar {
                    width: 40px;
                    height: 40px;
                    font-size: 0.9rem;
                }

                .mobile-specialist-name {
                    font-size: 0.8rem;
                }

                .mobile-specialist-email {
                    font-size: 0.6rem;
                }

                .mobile-detail-label,
                .mobile-detail-value {
                    font-size: 0.7rem;
                }

                .status-badge {
                    font-size: 0.6rem;
                    padding: 3px 8px;
                }
            }

            /* RTL Support */
            body.rtl .filters-header h3 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .report-header h3 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .bulk-actions-left {
                flex-direction: row;
            }

            body.rtl .mobile-specialist-info {
                flex-direction: row;
            }

            body.rtl .mobile-detail-item {
                flex-direction: row-reverse;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentReportData = [];
            let currentMonth = 3;
            let currentYear = new Date().getFullYear();
            let currentPlatformPercent = 5;

            // DOM Elements
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            const platformPercentInput = document.getElementById('platformPercent');
            const generateBtn = document.getElementById('generateReportBtn');
            const exportBtn = document.getElementById('exportPayoutPdfBtn');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const paySelectedBtn = document.getElementById('paySelectedBtn');
            const payAllBtn = document.getElementById('payAllBtn');
            const tbody = document.getElementById('payoutsTableBody');
            const mobileCardsView = document.getElementById('mobileCardsView');
            const reportInfo = document.getElementById('reportInfo');
            const reportFooter = document.getElementById('reportFooter');
            const totalEarningsSpan = document.getElementById('totalEarnings');
            const totalPlatformFeesSpan = document.getElementById('totalPlatformFees');
            const totalFinalAmountSpan = document.getElementById('totalFinalAmount');
            const selectedCountSpan = document.getElementById('selectedCount');
            const loadingOverlay = document.getElementById('loadingOverlay');

            function getPlatformPercentValue() {
                let val = platformPercentInput.value;
                if (val === '' || val === null) return 0;
                let num = parseInt(val);
                return isNaN(num) ? 0 : num;
            }

            // Generate Report
            generateBtn.addEventListener('click', async () => {
                currentMonth = parseInt(monthSelect.value);
                currentYear = parseInt(yearSelect.value);
                currentPlatformPercent = getPlatformPercentValue();

                loadingOverlay.style.display = 'flex';

                try {
                    const response = await fetch('/admin/payments/specialist-payouts/generate', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            month: currentMonth,
                            year: currentYear,
                            platform_percent: currentPlatformPercent
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        currentReportData = data.report;
                        renderDesktopTable(currentReportData);
                        renderMobileCards(currentReportData);
                        updateTotals(currentReportData);

                        reportInfo.innerHTML = `<i class="fas fa-calendar-alt"></i> ${data.month_name} | ${currentPlatformPercent}% {{ __('Platform Fee') }}`;
                        reportFooter.style.display = 'block';
                        exportBtn.style.display = 'inline-flex';
                        bulkActionsBar.style.display = currentReportData.length > 0 ? 'flex' : 'none';

                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Report Generated") }}',
                            text: `{{ __("Found") }} ${currentReportData.length} {{ __("specialists with sessions in") }} ${data.month_name}`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    loadingOverlay.style.display = 'none';
                }
            });

            function renderDesktopTable(data) {
                if (!data || data.length === 0) {
                    tbody.innerHTML = `<tr class="empty-row"><td colspan="11"><div class="empty-state"><i class="fas fa-chart-line"></i><p>{{ __("No specialists found") }}</p></div></td></tr>`;
                    selectAllCheckbox.style.display = 'none';
                    return;
                }

                tbody.innerHTML = data.map(specialist => {
                    const avatar = specialist.profile_image_url ? `<img src="${specialist.profile_image_url}" alt="${escapeHtml(specialist.specialist_name)}">` : (specialist.specialist_name?.charAt(0) || 'S');
                    const statusClass = specialist.is_paid ? 'status-paid' : 'status-pending';
                    const statusText = specialist.is_paid ? '{{ __("Paid") }}' : '{{ __("Pending") }}';

                    return `<tr data-specialist-id="${specialist.specialist_id}" data-is-paid="${specialist.is_paid}">
                                <td style="text-align: center;"><input type="checkbox" class="row-checkbox" data-id="${specialist.specialist_id}" ${specialist.is_paid ? 'disabled' : ''}></td>
                                <td><div class="specialist-cell"><div class="specialist-avatar">${avatar}</div><div><div class="specialist-name">${escapeHtml(specialist.specialist_name)}</div><div class="specialist-email">${escapeHtml(specialist.specialist_email)}</div></div></div></td>
                                <td>${specialist.total_sessions || 0}</td>
                                <td>$${parseFloat(specialist.consultation_fee || 0).toFixed(2)}</td>
                                <td>${specialist.video_sessions || 0}</td>
                                <td>${specialist.audio_sessions || 0}</td>
                                <td>${specialist.text_sessions || 0}</td>
                                <td class="font-bold">$${parseFloat(specialist.earnings || 0).toFixed(2)}</td>
                                <td>$${parseFloat(specialist.platform_fee || 0).toFixed(2)}</td>
                                <td class="text-success font-bold">$${parseFloat(specialist.final_amount || 0).toFixed(2)}</td>
                                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            </tr>`;
                }).join('');

                selectAllCheckbox.style.display = 'inline-block';
                selectAllCheckbox.checked = false;
                attachCheckboxEvents();
            }

            function renderMobileCards(data) {
                if (!data || data.length === 0) {
                    mobileCardsView.innerHTML = `<div class="empty-state"><i class="fas fa-chart-line"></i><p>{{ __("No specialists found") }}</p></div>`;
                    return;
                }

                mobileCardsView.innerHTML = data.map(specialist => {
                    const avatar = specialist.profile_image_url ? `<img src="${specialist.profile_image_url}" alt="${escapeHtml(specialist.specialist_name)}">` : (specialist.specialist_name?.charAt(0) || 'S');
                    const statusClass = specialist.is_paid ? 'status-paid' : 'status-pending';
                    const statusText = specialist.is_paid ? '{{ __("Paid") }}' : '{{ __("Pending") }}';

                    return `<div class="mobile-specialist-card" data-specialist-id="${specialist.specialist_id}" data-is-paid="${specialist.is_paid}">
                                <div class="mobile-card-header">
                                    <div class="mobile-specialist-info">
                                        <div class="mobile-specialist-avatar">${avatar}</div>
                                        <div>
                                            <div class="mobile-specialist-name">${escapeHtml(specialist.specialist_name)}</div>
                                            <div class="mobile-specialist-email">${escapeHtml(specialist.specialist_email)}</div>
                                        </div>
                                    </div>
                                    <input type="checkbox" class="mobile-row-checkbox" data-id="${specialist.specialist_id}" ${specialist.is_paid ? 'disabled' : ''}>
                                </div>
                                <div class="mobile-card-details">
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Sessions") }}</span><span class="mobile-detail-value">${specialist.total_sessions || 0}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Fee/Session") }}</span><span class="mobile-detail-value">$${parseFloat(specialist.consultation_fee || 0).toFixed(2)}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Video") }}</span><span class="mobile-detail-value">${specialist.video_sessions || 0}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Audio") }}</span><span class="mobile-detail-value">${specialist.audio_sessions || 0}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Text") }}</span><span class="mobile-detail-value">${specialist.text_sessions || 0}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Earnings") }}</span><span class="mobile-detail-value">$${parseFloat(specialist.earnings || 0).toFixed(2)}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Platform Fee") }}</span><span class="mobile-detail-value">$${parseFloat(specialist.platform_fee || 0).toFixed(2)}</span></div>
                                    <div class="mobile-detail-item"><span class="mobile-detail-label">{{ __("Final Amount") }}</span><span class="mobile-detail-value font-bold text-success">$${parseFloat(specialist.final_amount || 0).toFixed(2)}</span></div>
                                </div>
                                <div class="mobile-card-status">
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                </div>
                            </div>`;
                }).join('');

                attachMobileCheckboxEvents();
            }

            function updateTotals(data) {
                let totalEarnings = 0, totalFees = 0, totalFinal = 0;
                data.forEach(s => {
                    totalEarnings += parseFloat(s.earnings || 0);
                    totalFees += parseFloat(s.platform_fee || 0);
                    totalFinal += parseFloat(s.final_amount || 0);
                });
                totalEarningsSpan.textContent = `$${totalEarnings.toFixed(2)}`;
                totalPlatformFeesSpan.textContent = `$${totalFees.toFixed(2)}`;
                totalFinalAmountSpan.textContent = `$${totalFinal.toFixed(2)}`;
            }

            let selectedSpecialistIds = new Set();

            function attachCheckboxEvents() {
                const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
                checkboxes.forEach(cb => {
                    cb.removeEventListener('change', updateSelectedCount);
                    cb.addEventListener('change', updateSelectedCount);
                });
                updateSelectedCount();
            }

            function attachMobileCheckboxEvents() {
                const checkboxes = document.querySelectorAll('.mobile-row-checkbox:not(:disabled)');
                checkboxes.forEach(cb => {
                    cb.removeEventListener('change', updateSelectedCount);
                    cb.addEventListener('change', updateSelectedCount);
                });
                updateSelectedCount();
            }

            function updateSelectedCount() {
                // Get unique specialist IDs from checked checkboxes (desktop)
                const desktopChecked = document.querySelectorAll('.row-checkbox:checked');
                const mobileChecked = document.querySelectorAll('.mobile-row-checkbox:checked');

                // Use a Set to get unique IDs (avoid duplicates between desktop and mobile)
                const uniqueIds = new Set();
                desktopChecked.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));
                mobileChecked.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));

                const uniqueCount = uniqueIds.size;
                selectedCountSpan.textContent = uniqueCount;
                paySelectedBtn.disabled = uniqueCount === 0;

                if (selectAllCheckbox) {
                    const allDesktop = document.querySelectorAll('.row-checkbox:not(:disabled)');
                    const allMobile = document.querySelectorAll('.mobile-row-checkbox:not(:disabled)');

                    // Get unique total available specialists
                    const allUniqueIds = new Set();
                    allDesktop.forEach(cb => allUniqueIds.add(parseInt(cb.dataset.id)));
                    allMobile.forEach(cb => allUniqueIds.add(parseInt(cb.dataset.id)));

                    const allTotal = allUniqueIds.size;
                    selectAllCheckbox.checked = uniqueCount > 0 && uniqueCount === allTotal;
                    selectAllCheckbox.indeterminate = uniqueCount > 0 && uniqueCount < allTotal;
                }
            }

            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;

                // Get all unique specialist IDs from both views
                const allDesktop = document.querySelectorAll('.row-checkbox:not(:disabled)');
                const allMobile = document.querySelectorAll('.mobile-row-checkbox:not(:disabled)');

                // Create a Set of unique IDs to avoid double-counting
                const uniqueIds = new Set();
                allDesktop.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));
                allMobile.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));

                // For each unique ID, find and check/uncheck ONE of its checkboxes
                // We'll check the desktop ones first (prefer desktop for consistency)
                uniqueIds.forEach(id => {
                    const desktopCb = document.querySelector(`.row-checkbox[data-id="${id}"]`);
                    const mobileCb = document.querySelector(`.mobile-row-checkbox[data-id="${id}"]`);
                    if (desktopCb && !desktopCb.disabled) {
                        desktopCb.checked = isChecked;
                    } else if (mobileCb && !mobileCb.disabled) {
                        mobileCb.checked = isChecked;
                    }
                });

                updateSelectedCount();
            });

            paySelectedBtn.addEventListener('click', async () => {
                // Get unique specialist IDs from checked checkboxes
                const desktopChecked = document.querySelectorAll('.row-checkbox:checked');
                const mobileChecked = document.querySelectorAll('.mobile-row-checkbox:checked');

                const uniqueIds = new Set();
                desktopChecked.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));
                mobileChecked.forEach(cb => uniqueIds.add(parseInt(cb.dataset.id)));

                const specialistIds = Array.from(uniqueIds);

                if (specialistIds.length === 0) return;

                const result = await Swal.fire({
                    title: '{{ __("Confirm Payment") }}',
                    text: `{{ __("Pay") }} ${specialistIds.length} {{ __("specialists?") }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    confirmButtonText: '{{ __("Yes, Pay") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                loadingOverlay.style.display = 'flex';
                try {
                    const response = await fetch('/admin/payments/specialist-payouts/pay-selected', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            specialist_ids: specialistIds,
                            month: currentMonth,
                            year: currentYear,
                            platform_percent: currentPlatformPercent
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        generateBtn.click();
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    loadingOverlay.style.display = 'none';
                }
            });

            payAllBtn.addEventListener('click', async () => {
                const pending = currentReportData.filter(s => !s.is_paid);
                if (pending.length === 0) {
                    Swal.fire({ icon: 'info', title: '{{ __("No Pending") }}', text: '{{ __("All specialists already paid") }}' });
                    return;
                }

                const result = await Swal.fire({
                    title: '{{ __("Confirm All") }}',
                    html: `{{ __("Pay") }} <strong>${pending.length}</strong> {{ __("specialists?") }}<br>{{ __("Total") }}: $${pending.reduce((s, p) => s + parseFloat(p.final_amount), 0).toFixed(2)}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: '{{ __("Yes, Pay All") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                loadingOverlay.style.display = 'flex';
                try {
                    const response = await fetch('/admin/payments/specialist-payouts/pay-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            month: currentMonth,
                            year: currentYear,
                            platform_percent: currentPlatformPercent
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 2000, showConfirmButton: false });
                        generateBtn.click();
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                } finally {
                    loadingOverlay.style.display = 'none';
                }
            });

            exportBtn.addEventListener('click', () => {
                window.open(`/admin/payments/export/payouts-pdf?month=${currentMonth}&year=${currentYear}&platform_percent=${currentPlatformPercent}`, '_blank');
            });

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }
        </script>
    @endpush
@endsection