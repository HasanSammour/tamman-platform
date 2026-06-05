{{-- resources/views/admin/payments/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Payments Management') . ' - ' . __('Tamman'))

@section('page-title', __('Payments Management'))

@section('content')
    <div class="payments-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-data">
                    <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p>{{ __('Total Revenue') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-hand-holding-heart"></i></div>
                <div class="stat-data">
                    <h3>${{ number_format($stats['total_donated'], 2) }}</h3>
                    <p>{{ __('Total Donations') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['pending_credit_requests']) }}</h3>
                    <p>{{ __('Pending Credit Requests') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal"><i class="fas fa-star"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_points_redeemed']) }}</h3>
                    <p>{{ __('Points Redeemed') }}</p>
                </div>
            </div>
        </div>

        <!-- Mobile Stats Cards -->
        <div class="mobile-stats-grid">
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon purple"><i class="fas fa-dollar-sign"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">${{ number_format($stats['total_revenue'], 2) }}</span>
                    <span class="mobile-stat-label">{{ __('Revenue') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon green"><i class="fas fa-hand-holding-heart"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">${{ number_format($stats['total_donated'], 2) }}</span>
                    <span class="mobile-stat-label">{{ __('Donations') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon orange"><i class="fas fa-clock"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ number_format($stats['pending_credit_requests']) }}</span>
                    <span class="mobile-stat-label">{{ __('Pending') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon teal"><i class="fas fa-star"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ number_format($stats['total_points_redeemed']) }}</span>
                    <span class="mobile-stat-label">{{ __('Points') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="payments-tabs-wrapper">
            <div class="payments-tabs">
                <button class="tab-btn active" data-tab="credit-requests">
                    <i class="fas fa-credit-card"></i>
                    <span class="tab-label">{{ __('Credit Requests') }}</span>
                    @if($stats['pending_credit_requests'] > 0)
                        <span class="tab-badge">{{ $stats['pending_credit_requests'] }}</span>
                    @endif
                </button>
                <button class="tab-btn" data-tab="donations">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span class="tab-label">{{ __('Donations') }}</span>
                    @if($stats['pending_donations'] > 0)
                        <span class="tab-badge">{{ $stats['pending_donations'] }}</span>
                    @endif
                </button>
                <button class="tab-btn" data-tab="redemptions">
                    <i class="fas fa-exchange-alt"></i>
                    <span class="tab-label">{{ __('Points Redemption') }}</span>
                </button>
                <button class="tab-btn" data-tab="specialists">
                    <i class="fas fa-user-md"></i>
                    <span class="tab-label">{{ __('Earnings') }}</span>
                </button>
                <button class="tab-btn" data-tab="payouts">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="tab-label">{{ __('Payouts') }}</span>
                </button>
            </div>
        </div>

        <!-- ==================== TAB 1: CREDIT REQUESTS ==================== -->
        <div class="tab-content active" id="tab-credit-requests">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-credit-card"></i> {{ __('Credit Addition Requests') }}</h4>
                    <div class="table-filters">
                        <select id="creditRequestStatusFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="allocated">{{ __('Allocated') }}</option>
                            <option value="expired">{{ __('Expired') }}</option>
                        </select>
                        <button class="btn-reset-sm" onclick="resetCreditRequestsFilter()"><i class="fas fa-undo-alt"></i></button>
                        <button class="btn-export-sm" onclick="exportCreditRequestsPDF()"><i class="fas fa-file-pdf"></i> {{ __('Export') }}</button>
                    </div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="payments-table" id="creditRequestsTable">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Actions') }}</th>
                            <tr>
                        </thead>
                        <tbody id="creditRequestsTableBody">
                            <tr class="loading-row">
                                <td colspan="6" class="loading-cell">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading...') }}</p>
                                  </td>
                              </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="creditRequestsPaginationInfo"></div>
                    <div class="pagination-controls" id="creditRequestsPagination"></div>
                </div>
            </div>
            <div class="mobile-cards-container" id="creditRequestsMobileCards"></div>
            <div class="mobile-pagination" id="creditRequestsMobilePagination"></div>
        </div>

        <!-- ==================== TAB 2: DONATIONS ==================== -->
        <div class="tab-content" id="tab-donations">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-hand-holding-heart"></i> {{ __('Donation Transactions') }}</h4>
                    <div class="table-filters">
                        <select id="donationStatusFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="allocated">{{ __('Allocated') }}</option>
                            <option value="expired">{{ __('Expired') }}</option>
                        </select>
                        <button class="btn-reset-sm" onclick="resetDonationsFilter()"><i class="fas fa-undo-alt"></i></button>
                        <button class="btn-export-sm" onclick="exportDonationsPDF()"><i class="fas fa-file-pdf"></i> {{ __('Export') }}</button>
                    </div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="payments-table" id="donationsTable">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Donor') }}</th>
                                <th>{{ __('Recipient') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Remaining') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="donationsTableBody">
                            <tr class="loading-row">
                                <td colspan="7" class="loading-cell">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading donations...') }}</p>
                                  </td>
                              <tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="donationsPaginationInfo"></div>
                    <div class="pagination-controls" id="donationsPagination"></div>
                </div>
            </div>
            <div class="mobile-cards-container" id="donationsMobileCards"></div>
            <div class="mobile-pagination" id="donationsMobilePagination"></div>
        </div>

        <!-- ==================== TAB 3: POINTS REDEMPTION ==================== -->
        <div class="tab-content" id="tab-redemptions">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-exchange-alt"></i> {{ __('Points to Credit Redemption') }}</h4>
                    <div class="table-filters">
                        <select id="redemptionStatusFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
                            <option value="failed">{{ __('Failed') }}</option>
                        </select>
                        <button class="btn-reset-sm" onclick="resetRedemptionsFilter()"><i class="fas fa-undo-alt"></i></button>
                        <button class="btn-export-sm" onclick="exportRedemptionsPDF()"><i class="fas fa-file-pdf"></i> {{ __('Export') }}</button>
                    </div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="payments-table" id="redemptionsTable">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Reward') }}</th>
                                <th>{{ __('Points Spent') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody id="redemptionsTableBody">
                            <tr class="loading-row">
                                <td colspan="6" class="loading-cell">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading redemptions...') }}</p>
                                  </td>
                              </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="redemptionsPaginationInfo"></div>
                    <div class="pagination-controls" id="redemptionsPagination"></div>
                </div>
            </div>
            <div class="mobile-cards-container" id="redemptionsMobileCards"></div>
            <div class="mobile-pagination" id="redemptionsMobilePagination"></div>
        </div>

        <!-- ==================== TAB 4: SPECIALISTS ==================== -->
        <div class="tab-content" id="tab-specialists">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-user-md"></i> {{ __('Specialists Earnings Overview') }}</h4>
                    <div class="table-filters">
                        <a href="{{ route('admin.payments.specialist-payouts') }}" class="btn-payout">
                            <i class="fas fa-money-bill-wave"></i> <span>{{ __('Process Payouts') }}</span>
                        </a>
                        <button class="btn-export-sm" onclick="exportSpecialistsPDF()"><i class="fas fa-file-pdf"></i> {{ __('Export') }}</button>
                    </div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="payments-table" id="specialistsTable">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Specialist') }}</th>
                                <th>{{ __('Video') }}</th>
                                <th>{{ __('Audio') }}</th>
                                <th>{{ __('Text') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Fee') }}</th>
                                <th>{{ __('Total Earnings') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Pending') }}</th>
                            </tr>
                        </thead>
                        <tbody id="specialistsTableBody">
                            <tr class="loading-row">
                                <td colspan="10" class="loading-cell">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading specialists...') }}</p>
                                  </td>
                              </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="specialistsPaginationInfo"></div>
                    <div class="pagination-controls" id="specialistsPagination"></div>
                </div>
            </div>
            <div class="mobile-cards-container" id="specialistsMobileCards"></div>
            <div class="mobile-pagination" id="specialistsMobilePagination"></div>
        </div>

        <!-- ==================== TAB 5: PAYOUTS HISTORY ==================== -->
        <div class="tab-content" id="tab-payouts">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-history"></i> {{ __('Payouts History') }}</h4>
                    <div class="table-filters">
                        <select id="payoutStatusFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="paid">{{ __('Paid') }}</option>
                            <option value="failed">{{ __('Failed') }}</option>
                        </select>
                        <button class="btn-reset-sm" onclick="resetPayoutsFilter()"><i class="fas fa-undo-alt"></i></button>
                        <button class="btn-export-sm" onclick="exportPayoutsPDF()"><i class="fas fa-file-pdf"></i> {{ __('Export') }}</button>
                    </div>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="payments-table" id="payoutsTable">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Specialist') }}</th>
                                <th>{{ __('Month') }}</th>
                                <th>{{ __('Total Earnings') }}</th>
                                <th>{{ __('Platform Fee') }}</th>
                                <th>{{ __('Final Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="payoutsTableBody">
                            <tr class="loading-row">
                                <td colspan="7" class="loading-cell">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading payouts...') }}</p>
                                  </td>
                              </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="payoutsPaginationInfo"></div>
                    <div class="pagination-controls" id="payoutsPagination"></div>
                </div>
            </div>
            <div class="mobile-cards-container" id="payoutsMobileCards"></div>
            <div class="mobile-pagination" id="payoutsMobilePagination"></div>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Allocate Donation Modal -->
    <div id="allocateDonationModal" class="custom-modal">
        <div class="custom-modal-content" style="max-width: 500px;">
            <div class="custom-modal-header">
                <h3><i class="fas fa-hand-holding-heart"></i> {{ __('Allocate Donation') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="allocateDonationForm">
                @csrf
                <div class="custom-modal-body">
                    <input type="hidden" id="donationTransactionId">
                    <div class="form-group">
                        <label>{{ __('Donor') }}:</label>
                        <p class="donor-info" id="donorName" style="font-weight: 500; color: #7c3aed;"></p>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Available Amount') }}:</label>
                        <p class="amount-info" id="donationAmountDisplay" style="font-weight: 700; font-size: 1.2rem; color: #10b981;"></p>
                    </div>
                    <div class="form-group">
                        <label for="allocateAmount">{{ __('Amount to Allocate') }} <span class="required">*</span></label>
                        <input type="number" id="allocateAmount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="recipientSelect">{{ __('Select Recipient (Patient)') }} <span class="required">*</span></label>
                        <select id="recipientSelect" class="form-control" required>
                            <option value="">{{ __('Select a patient...') }}</option>
                        </select>
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-allocate" id="allocateDonationBtn">
                        <span class="btn-text"><i class="fas fa-check-circle"></i> {{ __('Allocate') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve Credit Request Modal -->
    <div id="approveCreditModal" class="custom-modal">
        <div class="custom-modal-content" style="max-width: 400px;">
            <div class="custom-modal-header">
                <h3><i class="fas fa-check-circle"></i> {{ __('Approve Credit Request') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <input type="hidden" id="approveCreditId">
                <p>{{ __('Are you sure you want to approve this credit request?') }}</p>
                <p class="warning-text">{{ __('This will add the credits to the user\'s balance immediately.') }}</p>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn-confirm-approve" id="confirmApproveCreditBtn">
                    <span class="btn-text">{{ __('Approve') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Credit Request Modal -->
    <div id="rejectCreditModal" class="custom-modal">
        <div class="custom-modal-content" style="max-width: 400px;">
            <div class="custom-modal-header">
                <h3><i class="fas fa-times-circle"></i> {{ __('Reject Credit Request') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <input type="hidden" id="rejectCreditId">
                <p>{{ __('Are you sure you want to reject this credit request?') }}</p>
                <p class="warning-text">{{ __('This action cannot be undone.') }}</p>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn-confirm-reject" id="confirmRejectCreditBtn">
                    <span class="btn-text">{{ __('Reject') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .payments-container {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
                box-sizing: border-box;
                padding: 20px;
            }

            /* Stats Grid - Desktop */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            /* Mobile Stats Grid - Hidden on desktop */
            .mobile-stats-grid {
                display: none;
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

            .stat-icon.purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
            .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
            .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
            .stat-icon.teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }

            .stat-data h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 0;
            }

            /* Tabs Wrapper */
            .payments-tabs-wrapper {
                margin-bottom: 25px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .payments-tabs {
                display: flex;
                justify-content: center;
                gap: 10px;
                background: white;
                padding: 10px 20px;
                border-radius: 60px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                min-width: min-content;
            }

            .tab-btn {
                padding: 10px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                background: transparent;
                border: none;
                color: #6b7280;
                position: relative;
                white-space: nowrap;
            }

            .tab-btn i {
                margin-right: 8px;
            }

            .tab-btn:hover {
                background: #f3f4f6;
                color: #374151;
            }

            .tab-btn.active {
                background: #7c3aed;
                color: white;
            }

            .tab-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ef4444;
                color: white;
                font-size: 0.6rem;
                padding: 2px 6px;
                border-radius: 20px;
            }

            .tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
            }

            .tab-content.active {
                display: block;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Table Card */
            .table-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .table-header {
                padding: 18px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
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

            .btn-payout {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.75rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-payout:hover {
                transform: translateY(-2px);
                color: white;
            }

            .table-filters {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .filter-select-sm {
                padding: 6px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 0.75rem;
                background: white;
            }

            .btn-reset-sm {
                padding: 6px 12px;
                background: #f3f4f6;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.75rem;
                transition: all 0.2s;
            }

            .btn-reset-sm:hover {
                background: #e5e7eb;
            }

            .btn-export-sm {
                padding: 6px 12px;
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.75rem;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-export-sm:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* Table */
            .table-scroll-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .payments-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 800px;
            }

            .payments-table th,
            .payments-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
            }

            .payments-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
                white-space: nowrap;
            }

            .payments-table td {
                font-size: 0.8rem;
                color: #4b5563;
            }

            /* Fix for status column - keep on same line */
            .payments-table td .badge-status {
                white-space: nowrap;
                display: inline-flex;
            }

            /* Mobile Cards Container */
            .mobile-cards-container {
                display: none;
            }

            .mobile-pagination {
                display: none;
            }

            /* Loading Row */
            .loading-row .loading-cell {
                text-align: center !important;
                vertical-align: middle !important;
                padding: 60px 20px !important;
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
                to { transform: rotate(360deg); }
            }

            /* Badges */
            .badge-status {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .badge-pending { background: #fef3c7; color: #d97706; }
            .badge-allocated, .badge-completed { background: #d1fae5; color: #065f46; }
            .badge-expired, .badge-failed { background: #fee2e2; color: #991b1b; }
            .badge-paid { background: #d1fae5; color: #065f46; }
            .badge-cancelled { background: #fee2e2; color: #991b1b; }

            /* Action Buttons */
            .action-btn {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
                background: #f3f4f6;
                color: #6b7280;
                margin: 0 2px;
            }

            .action-btn:hover {
                transform: translateY(-2px);
            }

            .btn-allocate-action { background: #10b981; color: white; }
            .btn-allocate-action:hover { background: #059669; }
            .btn-approve-action { background: #10b981; color: white; }
            .btn-approve-action:hover { background: #059669; }
            .btn-reject-action { background: #ef4444; color: white; }
            .btn-reject-action:hover { background: #dc2626; }
            .btn-view-action { background: #ede9fe; color: #7c3aed; }
            .btn-view-action:hover { background: #ddd6fe; }

            /* Specialist Avatar */
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

            /* Modal */
            .custom-modal {
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
                opacity: 0;
                visibility: hidden;
                transition: all 0.2s;
            }

            .custom-modal.active {
                opacity: 1;
                visibility: visible;
            }

            .custom-modal-content {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                transform: scale(0.9);
                transition: transform 0.2s;
            }

            .custom-modal.active .custom-modal-content {
                transform: scale(1);
            }

            .custom-modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .custom-modal-header h3 {
                margin: 0;
                font-size: 1.2rem;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                color: #6b7280;
            }

            .custom-modal-body {
                padding: 24px;
            }

            .custom-modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .form-group {
                margin-bottom: 20px;
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
                background: white;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            .warning-text {
                color: #f59e0b;
                font-size: 0.75rem;
                margin-top: 10px;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 8px 20px;
                border-radius: 30px;
                cursor: pointer;
            }

            .btn-allocate,
            .btn-confirm-approve {
                background: #10b981;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 30px;
                cursor: pointer;
            }

            .btn-confirm-reject {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 30px;
                cursor: pointer;
            }

            .btn-allocate:hover,
            .btn-confirm-approve:hover {
                background: #059669;
            }

            .btn-confirm-reject:hover {
                background: #dc2626;
            }

            .btn-spinner {
                display: none;
            }

            .required {
                color: #ef4444;
            }

            .text-muted { color: #9ca3af; }
            .text-warning { color: #f59e0b; }
            .text-success { color: #10b981; }
            .font-bold { font-weight: 700; }

            /* ============================================ */
            /* RESPONSIVE BREAKPOINTS */
            /* ============================================ */

            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .payments-container {
                    padding: 12px;
                }

                .stats-grid {
                    display: none;
                }

                .mobile-stats-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    margin-bottom: 20px;
                }

                .mobile-stat-item {
                    background: white;
                    border-radius: 14px;
                    padding: 12px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }

                .mobile-stat-icon {
                    width: 40px;
                    height: 40px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                }

                .mobile-stat-icon i {
                    font-size: 1.1rem;
                    color: white;
                }

                .mobile-stat-icon.purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
                .mobile-stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
                .mobile-stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
                .mobile-stat-icon.teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }

                .mobile-stat-data {
                    flex: 1;
                }

                .mobile-stat-value {
                    display: block;
                    font-size: 1rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                .mobile-stat-label {
                    font-size: 0.6rem;
                    color: #6b7280;
                }

                .payments-tabs-wrapper {
                    margin-bottom: 15px;
                }

                .payments-tabs {
                    padding: 6px 12px;
                    gap: 6px;
                    border-radius: 40px;
                }

                .tab-btn {
                    padding: 6px 14px;
                    font-size: 0.7rem;
                }

                .tab-btn i {
                    margin-right: 4px;
                    font-size: 0.7rem;
                }

                .table-header {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 12px 16px;
                }

                .table-header h4 {
                    font-size: 0.85rem;
                }

                .table-filters {
                    width: 100%;
                    justify-content: space-between;
                }

                .filter-select-sm,
                .btn-reset-sm,
                .btn-export-sm {
                    font-size: 0.65rem;
                    padding: 5px 10px;
                }

                .table-card .table-scroll-wrapper {
                    display: none;
                }

                .table-footer {
                    display: none;
                }

                .mobile-cards-container {
                    display: block;
                    margin-top: 12px;
                }

                .mobile-pagination {
                    display: flex;
                    justify-content: center;
                    margin-top: 15px;
                    padding: 12px 0;
                }

                .mobile-payment-card {
                    background: white;
                    border-radius: 14px;
                    margin-bottom: 12px;
                    padding: 14px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }

                .mobile-card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 12px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #f0f0f0;
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .mobile-card-user {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex: 1;
                    min-width: 0;
                }

                .mobile-card-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #7c3aed, #6d28d9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 600;
                    font-size: 0.85rem;
                    flex-shrink: 0;
                    overflow: hidden;
                }

                .mobile-card-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .mobile-card-info {
                    flex: 1;
                    min-width: 0;
                }

                .mobile-card-name {
                    font-weight: 600;
                    font-size: 0.85rem;
                    color: #1f2937;
                    margin-bottom: 2px;
                    word-break: break-word;
                }

                .mobile-card-email {
                    font-size: 0.6rem;
                    color: #9ca3af;
                    word-break: break-all;
                }

                .mobile-card-details {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    margin-bottom: 12px;
                }

                .mobile-detail-row {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 0.7rem;
                    flex-wrap: wrap;
                }

                .mobile-detail-label {
                    min-width: 85px;
                    color: #6b7280;
                    font-size: 0.65rem;
                    font-weight: 500;
                }

                .mobile-detail-value {
                    flex: 1;
                    color: #1f2937;
                    font-size: 0.7rem;
                    word-break: break-word;
                }

                .mobile-card-footer {
                    display: flex;
                    justify-content: flex-end;
                    gap: 8px;
                    margin-top: 10px;
                    padding-top: 10px;
                    border-top: 1px solid #f0f0f0;
                }

                .mobile-action-btn {
                    padding: 5px 12px;
                    border-radius: 20px;
                    font-size: 0.65rem;
                    font-weight: 500;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    transition: all 0.2s;
                    background: #f3f4f6;
                    color: #6b7280;
                    border: none;
                    cursor: pointer;
                }

                .mobile-action-approve {
                    background: #10b981;
                    color: white;
                }

                .mobile-action-reject {
                    background: #ef4444;
                    color: white;
                }

                .mobile-action-allocate {
                    background: #f59e0b;
                    color: white;
                }

                .mobile-action-view {
                    background: #ede9fe;
                    color: #7c3aed;
                }

                .mobile-pagination .pagination-controls {
                    justify-content: center;
                }

                .mobile-pagination .page-btn {
                    min-width: 34px;
                    height: 34px;
                    font-size: 0.7rem;
                }

                .mobile-specialist-stats {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                    margin-top: 8px;
                }

                .mobile-stat-badge {
                    text-align: center;
                    padding: 6px;
                    background: #f3f4f6;
                    border-radius: 8px;
                }

                .mobile-stat-badge-label {
                    font-size: 0.55rem;
                    color: #6b7280;
                    display: block;
                }

                .mobile-stat-badge-value {
                    font-size: 0.85rem;
                    font-weight: 700;
                    color: #1f2937;
                }
            }

            @media (max-width: 550px) {
                .mobile-stats-grid {
                    grid-template-columns: 1fr;
                }
                
                .mobile-detail-label {
                    min-width: 75px;
                }
            }

            @media (max-width: 480px) {
                .tab-label {
                    display: none;
                }
                .tab-btn {
                    padding: 6px 10px;
                }
                .tab-btn i {
                    margin: 0;
                    font-size: 0.85rem;
                }
            }

            @media (max-width: 380px) {
                .mobile-detail-label {
                    min-width: 65px;
                }
                
                .mobile-card-avatar {
                    width: 36px;
                    height: 36px;
                }
                
                .mobile-card-name {
                    font-size: 0.8rem;
                }
            }

            /* RTL Support */
            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .payments-table th,
            body.rtl .payments-table td {
                text-align: right;
            }

            body.rtl .specialist-cell {
                flex-direction: row;
            }

            body.rtl .table-header h4 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .mobile-detail-label {
                text-align: right;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const baseUrl = '{{ url("/") }}';
            let perPage = 15;

            let currentCreditRequestsPage = 1, currentDonationsPage = 1, currentRedemptionsPage = 1, currentSpecialistsPage = 1, currentPayoutsPage = 1;
            let creditRequestStatus = 'all', donationStatus = 'all', redemptionStatus = 'all', payoutStatus = 'all';
            let currentDonationId = null, currentCreditId = null;

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            function renderPagination(data, infoId, controlsId, onPageChange, isMobile = false) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);
                const infoElement = document.getElementById(infoId);
                const controlsElement = document.getElementById(controlsId);
                
                if (infoElement) infoElement.innerHTML = `{{ __('Showing') }} ${from}-${to} {{ __('of') }} ${total}`;
                
                let html = `<button class="page-btn" onclick="(${onPageChange})(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>
                           <button class="page-btn" onclick="(${onPageChange})(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;
                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="(${onPageChange})(${i})">${i}</button>`;
                }
                html += `<button class="page-btn" onclick="(${onPageChange})(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>
                        <button class="page-btn" onclick="(${onPageChange})(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;
                
                if (controlsElement) controlsElement.innerHTML = html;
            }

            function renderMobilePagination(data, containerId, onPageChange) {
                const container = document.getElementById(containerId);
                if (!container) return;
                
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);
                
                let html = `<div class="pagination-info" style="text-align:center;margin-bottom:10px;font-size:0.7rem;">{{ __('Showing') }} ${from}-${to} {{ __('of') }} ${total}</div>`;
                html += `<div class="pagination-controls">`;
                html += `<button class="page-btn" onclick="(${onPageChange})(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="(${onPageChange})(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;
                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="(${onPageChange})(${i})">${i}</button>`;
                }
                html += `<button class="page-btn" onclick="(${onPageChange})(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>
                        <button class="page-btn" onclick="(${onPageChange})(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;
                html += `</div>`;
                container.innerHTML = html;
            }

            function showError(containerId, mobileContainerId, message = '{{ __("Error loading data") }}') {
                const errorHtml = `<div style="text-align:center;padding:40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#ef4444;"></i>
                    <p style="margin-top:10px;color:#6b7280;">${message}</p>
                    <button class="btn-reset-sm" onclick="location.reload()">{{ __("Retry") }}</button>
                </div>`;
                const tbody = document.getElementById(containerId);
                if (tbody) tbody.innerHTML = `<tr><td colspan="10" class="loading-cell">${errorHtml}</td></tr>`;
                const mobileContainer = document.getElementById(mobileContainerId);
                if (mobileContainer) mobileContainer.innerHTML = errorHtml;
            }

            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${this.dataset.tab}`).classList.add('active');

                    if (this.dataset.tab === 'credit-requests') loadCreditRequests();
                    else if (this.dataset.tab === 'donations') loadDonations();
                    else if (this.dataset.tab === 'redemptions') loadRedemptions();
                    else if (this.dataset.tab === 'specialists') loadSpecialists();
                    else if (this.dataset.tab === 'payouts') loadPayouts();
                });
            });

            // Export Functions
            function exportCreditRequestsPDF() { window.open(`/admin/payments/export/credit-requests-pdf?status=${creditRequestStatus}`, '_blank'); }
            function exportDonationsPDF() { window.open(`/admin/payments/export/donations-pdf?status=${donationStatus}`, '_blank'); }
            function exportRedemptionsPDF() { window.open(`/admin/payments/export/redemptions-pdf?status=${redemptionStatus}`, '_blank'); }
            function exportSpecialistsPDF() { window.open(`/admin/payments/export/specialists-pdf`, '_blank'); }
            function exportPayoutsPDF() { window.open(`/admin/payments/payouts/export-history-pdf?status=${payoutStatus}`, '_blank'); }

            // ==================== CREDIT REQUESTS ====================
            window.resetCreditRequestsFilter = () => {
                document.getElementById('creditRequestStatusFilter').value = 'all';
                creditRequestStatus = 'all';
                currentCreditRequestsPage = 1;
                loadCreditRequests();
            };

            document.getElementById('creditRequestStatusFilter')?.addEventListener('change', (e) => {
                creditRequestStatus = e.target.value;
                currentCreditRequestsPage = 1;
                loadCreditRequests();
            });

            async function loadCreditRequests() {
                const tbody = document.getElementById('creditRequestsTableBody');
                const mobileContainer = document.getElementById('creditRequestsMobileCards');
                if (tbody) tbody.innerHTML = `<tr class="loading-row"><td colspan="6" class="loading-cell"><div class="loading-spinner"></div><p>{{ __('Loading...') }}</p></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading...') }}</p></div>`;
                
                try {
                    const res = await fetch(`/admin/payments/credit-requests/data?page=${currentCreditRequestsPage}&per_page=${perPage}&status=${creditRequestStatus}`);
                    const data = await res.json();
                    if (data.success) {
                        renderCreditRequestsDesktop(data.data);
                        renderCreditRequestsMobile(data.data);
                        renderPagination(data, 'creditRequestsPaginationInfo', 'creditRequestsPagination', (page) => { currentCreditRequestsPage = page; loadCreditRequests(); });
                        renderMobilePagination(data, 'creditRequestsMobilePagination', (page) => { currentCreditRequestsPage = page; loadCreditRequests(); });
                    } else showError('creditRequestsTableBody', 'creditRequestsMobileCards');
                } catch (error) { showError('creditRequestsTableBody', 'creditRequestsMobileCards'); }
            }

            function renderCreditRequestsDesktop(requests) {
                const tbody = document.getElementById('creditRequestsTableBody');
                if (!requests || !requests.length) {
                    tbody.innerHTML = `<tr><td colspan="6" class="loading-cell" style="text-align:center;padding:40px;"><i class="fas fa-credit-card" style="font-size:2rem;color:#c4b5fd;"></i><p>{{ __('No credit requests found') }}</p></td></tr>`;
                    return;
                }
                tbody.innerHTML = requests.map(r => {
                    const statusClass = r.status === 'pending' ? 'badge-pending' : (r.status === 'allocated' ? 'badge-allocated' : 'badge-expired');
                    const statusText = r.status === 'pending' ? '{{ __("Pending") }}' : (r.status === 'allocated' ? '{{ __("Allocated") }}' : '{{ __("Expired") }}');
                    return `<tr>
                        <td style="white-space: nowrap;">${new Date(r.created_at).toLocaleDateString()}</td>
                        <td><strong>${escapeHtml(r.user_name)}</strong><br><small>${escapeHtml(r.user_email)}</small></td>
                        <td class="font-bold">$${parseFloat(r.amount).toFixed(2)}</div>
                        <td><span class="badge-status ${statusClass}">${statusText}</span></div>
                        <td title="${escapeHtml(r.description)}">${escapeHtml(r.description).substring(0, 50)}${escapeHtml(r.description).length > 50 ? '...' : ''}</div>
                        <td>${r.status === 'pending' ? `<button class="action-btn btn-approve-action" onclick="openApproveCreditModal(${r.id})"><i class="fas fa-check-circle"></i></button><button class="action-btn btn-reject-action" onclick="openRejectCreditModal(${r.id})"><i class="fas fa-times-circle"></i></button>` : '<span class="text-muted">—</span>'}</div>
                    </tr>`;
                }).join('');
            }

            function renderCreditRequestsMobile(requests) {
                const container = document.getElementById('creditRequestsMobileCards');
                if (!container) return;
                if (!requests || !requests.length) {
                    container.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-credit-card" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No credit requests found') }}</p></div>`;
                    return;
                }
                container.innerHTML = requests.map(r => {
                    const statusClass = r.status === 'pending' ? 'badge-pending' : (r.status === 'allocated' ? 'badge-allocated' : 'badge-expired');
                    const statusText = r.status === 'pending' ? '{{ __("Pending") }}' : (r.status === 'allocated' ? '{{ __("Allocated") }}' : '{{ __("Expired") }}');
                    return `
                        <div class="mobile-payment-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-user">
                                    <div class="mobile-card-avatar">${(r.user_name?.charAt(0) || 'U').toUpperCase()}</div>
                                    <div class="mobile-card-info">
                                        <div class="mobile-card-name">${escapeHtml(r.user_name)}</div>
                                        <div class="mobile-card-email">${escapeHtml(r.user_email)}</div>
                                    </div>
                                </div>
                                <span class="badge-status ${statusClass}">${statusText}</span>
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Date') }}:</span>
                                    <span class="mobile-detail-value">${new Date(r.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Amount') }}:</span>
                                    <span class="mobile-detail-value font-bold">$${parseFloat(r.amount).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Description') }}:</span>
                                    <span class="mobile-detail-value">${escapeHtml(r.description).substring(0, 100)}</span>
                                </div>
                            </div>
                            ${r.status === 'pending' ? `
                            <div class="mobile-card-footer">
                                <button class="mobile-action-btn mobile-action-approve" onclick="openApproveCreditModal(${r.id})"><i class="fas fa-check-circle"></i> {{ __('Approve') }}</button>
                                <button class="mobile-action-btn mobile-action-reject" onclick="openRejectCreditModal(${r.id})"><i class="fas fa-times-circle"></i> {{ __('Reject') }}</button>
                            </div>` : ''}
                        </div>
                    `;
                }).join('');
            }

            window.openApproveCreditModal = (id) => {
                document.getElementById('approveCreditId').value = id;
                document.getElementById('approveCreditModal').classList.add('active');
            };

            document.getElementById('confirmApproveCreditBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('approveCreditId').value;
                const btn = document.getElementById('confirmApproveCreditBtn');
                btn.disabled = true; btn.querySelector('.btn-text').style.display = 'none'; btn.querySelector('.btn-spinner').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/payments/credit-requests/${id}/approve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('approveCreditModal').classList.remove('active');
                        loadCreditRequests();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('.btn-text').style.display = 'inline-block'; btn.querySelector('.btn-spinner').style.display = 'none'; }
            });

            window.openRejectCreditModal = (id) => {
                document.getElementById('rejectCreditId').value = id;
                document.getElementById('rejectCreditModal').classList.add('active');
            };

            document.getElementById('confirmRejectCreditBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('rejectCreditId').value;
                const btn = document.getElementById('confirmRejectCreditBtn');
                btn.disabled = true; btn.querySelector('.btn-text').style.display = 'none'; btn.querySelector('.btn-spinner').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/payments/credit-requests/${id}/reject`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Rejected") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('rejectCreditModal').classList.remove('active');
                        loadCreditRequests();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('.btn-text').style.display = 'inline-block'; btn.querySelector('.btn-spinner').style.display = 'none'; }
            });

            // ==================== DONATIONS ====================
            window.resetDonationsFilter = () => {
                document.getElementById('donationStatusFilter').value = 'all';
                donationStatus = 'all';
                currentDonationsPage = 1;
                loadDonations();
            };

            document.getElementById('donationStatusFilter')?.addEventListener('change', (e) => {
                donationStatus = e.target.value;
                currentDonationsPage = 1;
                loadDonations();
            });

            async function loadDonations() {
                const tbody = document.getElementById('donationsTableBody');
                const mobileContainer = document.getElementById('donationsMobileCards');
                if (tbody) tbody.innerHTML = `<tr class="loading-row"><td colspan="7" class="loading-cell"><div class="loading-spinner"></div><p>{{ __('Loading donations...') }}</p></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading donations...') }}</p></div>`;
                try {
                    const res = await fetch(`/admin/payments/donations/data?page=${currentDonationsPage}&per_page=${perPage}&status=${donationStatus}`);
                    const data = await res.json();
                    if (data.success) {
                        renderDonationsDesktop(data.data);
                        renderDonationsMobile(data.data);
                        renderPagination(data, 'donationsPaginationInfo', 'donationsPagination', (page) => { currentDonationsPage = page; loadDonations(); });
                        renderMobilePagination(data, 'donationsMobilePagination', (page) => { currentDonationsPage = page; loadDonations(); });
                    } else showError('donationsTableBody', 'donationsMobileCards');
                } catch (error) { showError('donationsTableBody', 'donationsMobileCards'); }
            }

            function renderDonationsDesktop(donations) {
                const tbody = document.getElementById('donationsTableBody');
                if (!donations || !donations.length) {
                    tbody.innerHTML = `<tr><td colspan="7" class="loading-cell" style="text-align:center;padding:40px;"><i class="fas fa-hand-holding-heart" style="font-size:2rem;color:#c4b5fd;"></i><p>{{ __('No donations found') }}</p></td></tr>`;
                    return;
                }
                tbody.innerHTML = donations.map(d => {
                    const statusClass = d.status === 'pending' ? 'badge-pending' : (d.status === 'allocated' ? 'badge-allocated' : 'badge-expired');
                    const statusText = d.status === 'pending' ? '{{ __("Pending") }}' : (d.status === 'allocated' ? '{{ __("Allocated") }}' : '{{ __("Expired") }}');
                    const canAllocate = d.status === 'allocated' && d.remaining_amount > 0;
                    const canApprove = d.status === 'pending';
                    let remaining = d.remaining_amount || 0;
                    if (remaining < 0) remaining = 0;
                    return `<tr>
                        <td style="white-space: nowrap;">${new Date(d.created_at).toLocaleDateString()}</td>
                        <td><strong>${escapeHtml(d.donor_name)}</strong><br><small>${escapeHtml(d.donor_email)}</small></td>
                        <td>${d.recipient_name !== '{{ __("Not allocated yet") }}' ? '<strong>' + escapeHtml(d.recipient_name) + '</strong>' : '<span class="text-muted">—</span>'}</td>
                        <td class="font-bold">$${parseFloat(d.amount).toFixed(2)}</div>
                        <td class="font-bold text-success">$${parseFloat(remaining).toFixed(2)}</div>
                        <td><span class="badge-status ${statusClass}">${statusText}</span></div>
                        <td>
                            ${canApprove ? `<button class="action-btn btn-approve-action" onclick="approveDonation(${d.id})"><i class="fas fa-check-circle"></i></button><button class="action-btn btn-reject-action" onclick="rejectDonation(${d.id})"><i class="fas fa-times-circle"></i></button>` : ''}
                            ${canAllocate ? `<button class="action-btn btn-allocate-action" onclick="openAllocateDonationModal(${d.id}, '${escapeHtml(d.donor_name)}', ${d.amount}, ${remaining})"><i class="fas fa-hand-holding-heart"></i></button>` : ''}
                        </div>
                    </tr>`;
                }).join('');
            }

            function renderDonationsMobile(donations) {
                const container = document.getElementById('donationsMobileCards');
                if (!container) return;
                if (!donations || !donations.length) {
                    container.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-hand-holding-heart" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No donations found') }}</p></div>`;
                    return;
                }
                container.innerHTML = donations.map(d => {
                    const statusClass = d.status === 'pending' ? 'badge-pending' : (d.status === 'allocated' ? 'badge-allocated' : 'badge-expired');
                    const statusText = d.status === 'pending' ? '{{ __("Pending") }}' : (d.status === 'allocated' ? '{{ __("Allocated") }}' : '{{ __("Expired") }}');
                    const canAllocate = d.status === 'allocated' && d.remaining_amount > 0;
                    const canApprove = d.status === 'pending';
                    let remaining = d.remaining_amount || 0;
                    if (remaining < 0) remaining = 0;
                    return `
                        <div class="mobile-payment-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-user">
                                    <div class="mobile-card-avatar">${(d.donor_name?.charAt(0) || 'D').toUpperCase()}</div>
                                    <div class="mobile-card-info">
                                        <div class="mobile-card-name">${escapeHtml(d.donor_name)}</div>
                                        <div class="mobile-card-email">${escapeHtml(d.donor_email)}</div>
                                    </div>
                                </div>
                                <span class="badge-status ${statusClass}">${statusText}</span>
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Date') }}:</span>
                                    <span class="mobile-detail-value">${new Date(d.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Amount') }}:</span>
                                    <span class="mobile-detail-value font-bold">$${parseFloat(d.amount).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Remaining') }}:</span>
                                    <span class="mobile-detail-value text-success">$${parseFloat(remaining).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Recipient') }}:</span>
                                    <span class="mobile-detail-value">${d.recipient_name !== '{{ __("Not allocated yet") }}' ? escapeHtml(d.recipient_name) : '—'}</span>
                                </div>
                            </div>
                            ${(canApprove || canAllocate) ? `
                            <div class="mobile-card-footer">
                                ${canApprove ? `<button class="mobile-action-btn mobile-action-approve" onclick="approveDonation(${d.id})"><i class="fas fa-check-circle"></i> {{ __('Approve') }}</button>
                                                <button class="mobile-action-btn mobile-action-reject" onclick="rejectDonation(${d.id})"><i class="fas fa-times-circle"></i> {{ __('Reject') }}</button>` : ''}
                                ${canAllocate ? `<button class="mobile-action-btn mobile-action-allocate" onclick="openAllocateDonationModal(${d.id}, '${escapeHtml(d.donor_name)}', ${d.amount}, ${remaining})"><i class="fas fa-hand-holding-heart"></i> {{ __('Allocate') }}</button>` : ''}
                            </div>` : ''}
                        </div>
                    `;
                }).join('');
            }

            window.approveDonation = async (id) => {
                const result = await Swal.fire({ title: '{{ __("Approve Donation") }}', text: '{{ __("Are you sure you want to approve this donation?") }}', icon: 'question', showCancelButton: true, confirmButtonColor: '#10b981', confirmButtonText: '{{ __("Approve") }}', cancelButtonText: '{{ __("Cancel") }}' });
                if (!result.isConfirmed) return;
                try {
                    const res = await fetch(`/admin/payments/donations/${id}/approve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false }); loadDonations(); }
                    else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
            };

            window.rejectDonation = async (id) => {
                const result = await Swal.fire({ title: '{{ __("Reject Donation") }}', text: '{{ __("Are you sure you want to reject this donation?") }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: '{{ __("Reject") }}', cancelButtonText: '{{ __("Cancel") }}' });
                if (!result.isConfirmed) return;
                Swal.fire({ title: '{{ __("Processing...") }}', text: '{{ __("Please wait") }}', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                try {
                    const response = await fetch(`/admin/payments/donations/${id}/reject`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' } });
                    const data = await response.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Rejected") }}', text: data.message, timer: 1500, showConfirmButton: false }); loadDonations(); }
                    else { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message, confirmButtonColor: '#7c3aed' }); }
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}', confirmButtonColor: '#7c3aed' }); }
            };

            window.openAllocateDonationModal = (id, donorName, amount, available) => {
                currentDonationId = id;
                document.getElementById('donationTransactionId').value = id;
                document.getElementById('donorName').innerHTML = donorName;
                document.getElementById('donationAmountDisplay').innerHTML = `$${parseFloat(available).toFixed(2)}`;
                document.getElementById('allocateAmount').value = '';
                fetchPatientsList();
                document.getElementById('allocateDonationModal').classList.add('active');
            };

            async function fetchPatientsList() {
                try {
                    const res = await fetch('/admin/users/data?per_page=1000');
                    const data = await res.json();
                    const select = document.getElementById('recipientSelect');
                    select.innerHTML = '<option value="">{{ __("Select a patient...") }}</option>';
                    if (data.success && data.data) {
                        data.data.filter(u => u.is_active).forEach(patient => {
                            select.innerHTML += `<option value="${patient.id}">${escapeHtml(patient.name)} (${escapeHtml(patient.email)})</option>`;
                        });
                    }
                } catch (error) { console.error('Error loading patients:', error); }
            }

            document.getElementById('allocateDonationForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const recipientId = document.getElementById('recipientSelect').value;
                const amount = parseFloat(document.getElementById('allocateAmount').value);
                if (!recipientId) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Please select a recipient") }}' }); return; }
                if (!amount || amount <= 0) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Please enter a valid amount") }}' }); return; }
                const btn = document.getElementById('allocateDonationBtn');
                btn.disabled = true; btn.querySelector('.btn-text').style.display = 'none'; btn.querySelector('.btn-spinner').style.display = 'inline-block';
                try {
                    const res = await fetch('/admin/payments/donations/allocate', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({ transaction_id: currentDonationId, recipient_id: recipientId, amount: amount }) });
                    const data = await res.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false }); document.getElementById('allocateDonationModal').classList.remove('active'); loadDonations(); }
                    else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('.btn-text').style.display = 'inline-block'; btn.querySelector('.btn-spinner').style.display = 'none'; }
            });

            // ==================== POINTS REDEMPTION ====================
            window.resetRedemptionsFilter = () => {
                document.getElementById('redemptionStatusFilter').value = 'all';
                redemptionStatus = 'all';
                currentRedemptionsPage = 1;
                loadRedemptions();
            };

            document.getElementById('redemptionStatusFilter')?.addEventListener('change', (e) => {
                redemptionStatus = e.target.value;
                currentRedemptionsPage = 1;
                loadRedemptions();
            });

            async function loadRedemptions() {
                const tbody = document.getElementById('redemptionsTableBody');
                const mobileContainer = document.getElementById('redemptionsMobileCards');
                if (tbody) tbody.innerHTML = `<tr class="loading-row"><td colspan="6" class="loading-cell"><div class="loading-spinner"></div><p>{{ __('Loading redemptions...') }}</p></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading redemptions...') }}</p></div>`;
                try {
                    const res = await fetch(`/admin/payments/redemptions/data?page=${currentRedemptionsPage}&per_page=${perPage}&status=${redemptionStatus}`);
                    const data = await res.json();
                    if (data.success) {
                        renderRedemptionsDesktop(data.data);
                        renderRedemptionsMobile(data.data);
                        renderPagination(data, 'redemptionsPaginationInfo', 'redemptionsPagination', (page) => { currentRedemptionsPage = page; loadRedemptions(); });
                        renderMobilePagination(data, 'redemptionsMobilePagination', (page) => { currentRedemptionsPage = page; loadRedemptions(); });
                    } else showError('redemptionsTableBody', 'redemptionsMobileCards');
                } catch (error) { showError('redemptionsTableBody', 'redemptionsMobileCards'); }
            }

            function renderRedemptionsDesktop(redemptions) {
                const tbody = document.getElementById('redemptionsTableBody');
                if (!redemptions || !redemptions.length) {
                    tbody.innerHTML = `<tr><td colspan="6" class="loading-cell" style="text-align:center;padding:40px;"><i class="fas fa-exchange-alt" style="font-size:2rem;color:#c4b5fd;"></i><p>{{ __('No redemptions found') }}</p></td></tr>`;
                    return;
                }
                tbody.innerHTML = redemptions.map(r => {
                    const statusClass = r.status === 'pending' ? 'badge-pending' : (r.status === 'completed' ? 'badge-completed' : (r.status === 'cancelled' ? 'badge-cancelled' : 'badge-failed'));
                    const statusText = r.status === 'pending' ? '{{ __("Pending") }}' : (r.status === 'completed' ? '{{ __("Completed") }}' : (r.status === 'cancelled' ? '{{ __("Cancelled") }}' : '{{ __("Failed") }}'));
                    let detailsText = '';
                    if (r.reward_type === 'credit') detailsText = '{{ __("Added to balance") }}';
                    else if (r.reward_type === 'free_session') detailsText = '{{ __("Free session") }}';
                    else if (r.reward_type === 'donate') detailsText = '{{ __("Donated") }}';
                    return `<tr>
                        <td style="white-space: nowrap;">${new Date(r.created_at).toLocaleDateString()}</div>
                        <td><strong>${escapeHtml(r.user_name)}</strong><br><small>${escapeHtml(r.user_email)}</small></div>
                        <td>${escapeHtml(r.reward_name)}</div>
                        <td class="font-bold">${r.points_spent.toLocaleString()} {{ __('points') }}</div>
                        <td><span class="badge-status ${statusClass}">${statusText}</span></div>
                        <td class="text-muted">${detailsText}</div>
                    </tr>`;
                }).join('');
            }

            function renderRedemptionsMobile(redemptions) {
                const container = document.getElementById('redemptionsMobileCards');
                if (!container) return;
                if (!redemptions || !redemptions.length) {
                    container.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-exchange-alt" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No redemptions found') }}</p></div>`;
                    return;
                }
                container.innerHTML = redemptions.map(r => {
                    const statusClass = r.status === 'pending' ? 'badge-pending' : (r.status === 'completed' ? 'badge-completed' : (r.status === 'cancelled' ? 'badge-cancelled' : 'badge-failed'));
                    const statusText = r.status === 'pending' ? '{{ __("Pending") }}' : (r.status === 'completed' ? '{{ __("Completed") }}' : (r.status === 'cancelled' ? '{{ __("Cancelled") }}' : '{{ __("Failed") }}'));
                    let detailsText = '';
                    if (r.reward_type === 'credit') detailsText = '{{ __("Added to balance") }}';
                    else if (r.reward_type === 'free_session') detailsText = '{{ __("Free session") }}';
                    else if (r.reward_type === 'donate') detailsText = '{{ __("Donated") }}';
                    return `
                        <div class="mobile-payment-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-user">
                                    <div class="mobile-card-avatar">${(r.user_name?.charAt(0) || 'U').toUpperCase()}</div>
                                    <div class="mobile-card-info">
                                        <div class="mobile-card-name">${escapeHtml(r.user_name)}</div>
                                        <div class="mobile-card-email">${escapeHtml(r.user_email)}</div>
                                    </div>
                                </div>
                                <span class="badge-status ${statusClass}">${statusText}</span>
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Date') }}:</span>
                                    <span class="mobile-detail-value">${new Date(r.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Reward') }}:</span>
                                    <span class="mobile-detail-value">${escapeHtml(r.reward_name)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Points Spent') }}:</span>
                                    <span class="mobile-detail-value font-bold">${r.points_spent.toLocaleString()} {{ __('points') }}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Details') }}:</span>
                                    <span class="mobile-detail-value">${detailsText}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // ==================== SPECIALISTS ====================
            async function loadSpecialists() {
                const tbody = document.getElementById('specialistsTableBody');
                const mobileContainer = document.getElementById('specialistsMobileCards');
                if (tbody) tbody.innerHTML = `<tr class="loading-row"><td colspan="10" class="loading-cell"><div class="loading-spinner"></div><p>{{ __('Loading specialists...') }}</p></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading specialists...') }}</p></div>`;
                try {
                    const res = await fetch(`/admin/payments/specialists/data?page=${currentSpecialistsPage}&per_page=${perPage}`);
                    const data = await res.json();
                    if (data.success) {
                        renderSpecialistsDesktop(data.data);
                        renderSpecialistsMobile(data.data);
                        renderPagination(data, 'specialistsPaginationInfo', 'specialistsPagination', (page) => { currentSpecialistsPage = page; loadSpecialists(); });
                        renderMobilePagination(data, 'specialistsMobilePagination', (page) => { currentSpecialistsPage = page; loadSpecialists(); });
                    } else showError('specialistsTableBody', 'specialistsMobileCards');
                } catch (error) { showError('specialistsTableBody', 'specialistsMobileCards'); }
            }

            function renderSpecialistsDesktop(specialists) {
                const tbody = document.getElementById('specialistsTableBody');
                if (!specialists || !specialists.length) {
                    tbody.innerHTML = `<td><td colspan="10" class="loading-cell" style="text-align:center;padding:40px;"><i class="fas fa-user-md" style="font-size:2rem;color:#c4b5fd;"></i><p>{{ __('No specialists found') }}</p></td></tr>`;
                    return;
                }
                tbody.innerHTML = specialists.map(s => {
                    const avatar = s.profile_image_url ? `<img src="${s.profile_image_url}" alt="${escapeHtml(s.name)}">` : (s.name?.charAt(0) || 'S');
                    return `<tr>
                        <td>#${s.id}</div>
                        <td><div class="specialist-cell"><div class="specialist-avatar">${avatar}</div><div><div class="specialist-name">${escapeHtml(s.name)}</div><div class="specialist-email">${escapeHtml(s.email)}</div></div></div></div>
                        <td>${s.video_sessions || 0}</div>
                        <td>${s.audio_sessions || 0}</div>
                        <td>${s.text_sessions || 0}</div>
                        <td class="font-bold">${s.total_sessions || 0}</div>
                        <td>$${parseFloat(s.consultation_fee).toFixed(2)}</div>
                        <td class="font-bold">$${parseFloat(s.total_earnings).toFixed(2)}</div>
                        <td>$${parseFloat(s.total_paid).toFixed(2)}</div>
                        <td class="text-warning">$${parseFloat(s.pending_payment).toFixed(2)}</div>
                    </tr>`;
                }).join('');
            }

            function renderSpecialistsMobile(specialists) {
                const container = document.getElementById('specialistsMobileCards');
                if (!container) return;
                if (!specialists || !specialists.length) {
                    container.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-user-md" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No specialists found') }}</p></div>`;
                    return;
                }
                container.innerHTML = specialists.map(s => {
                    const avatar = s.profile_image_url ? `<img src="${s.profile_image_url}" alt="${escapeHtml(s.name)}">` : (s.name?.charAt(0) || 'S');
                    return `
                        <div class="mobile-payment-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-user">
                                    <div class="mobile-card-avatar">${avatar}</div>
                                    <div class="mobile-card-info">
                                        <div class="mobile-card-name">${escapeHtml(s.name)}</div>
                                        <div class="mobile-card-email">${escapeHtml(s.email)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mobile-specialist-stats">
                                <div class="mobile-stat-badge">
                                    <span class="mobile-stat-badge-label">{{ __('Video') }}</span>
                                    <span class="mobile-stat-badge-value">${s.video_sessions || 0}</span>
                                </div>
                                <div class="mobile-stat-badge">
                                    <span class="mobile-stat-badge-label">{{ __('Audio') }}</span>
                                    <span class="mobile-stat-badge-value">${s.audio_sessions || 0}</span>
                                </div>
                                <div class="mobile-stat-badge">
                                    <span class="mobile-stat-badge-label">{{ __('Text') }}</span>
                                    <span class="mobile-stat-badge-value">${s.text_sessions || 0}</span>
                                </div>
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Total Sessions') }}:</span>
                                    <span class="mobile-detail-value font-bold">${s.total_sessions || 0}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Fee per Session') }}:</span>
                                    <span class="mobile-detail-value">$${parseFloat(s.consultation_fee).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Total Earnings') }}:</span>
                                    <span class="mobile-detail-value font-bold">$${parseFloat(s.total_earnings).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Paid') }}:</span>
                                    <span class="mobile-detail-value">$${parseFloat(s.total_paid).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Pending') }}:</span>
                                    <span class="mobile-detail-value text-warning">$${parseFloat(s.pending_payment).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // ==================== PAYOUTS HISTORY ====================
            window.resetPayoutsFilter = () => {
                document.getElementById('payoutStatusFilter').value = 'all';
                payoutStatus = 'all';
                currentPayoutsPage = 1;
                loadPayouts();
            };

            document.getElementById('payoutStatusFilter')?.addEventListener('change', (e) => {
                payoutStatus = e.target.value;
                currentPayoutsPage = 1;
                loadPayouts();
            });

            async function loadPayouts() {
                const tbody = document.getElementById('payoutsTableBody');
                const mobileContainer = document.getElementById('payoutsMobileCards');
                if (tbody) tbody.innerHTML = `<tr class="loading-row"><td colspan="7" class="loading-cell"><div class="loading-spinner"></div><p>{{ __('Loading payouts...') }}</p></tr>`;
                if (mobileContainer) mobileContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading payouts...') }}</p></div>`;
                try {
                    const res = await fetch(`/admin/payments/payouts/data?page=${currentPayoutsPage}&per_page=${perPage}&status=${payoutStatus}`);
                    const data = await res.json();
                    if (data.success) {
                        renderPayoutsDesktop(data.data);
                        renderPayoutsMobile(data.data);
                        renderPagination(data, 'payoutsPaginationInfo', 'payoutsPagination', (page) => { currentPayoutsPage = page; loadPayouts(); });
                        renderMobilePagination(data, 'payoutsMobilePagination', (page) => { currentPayoutsPage = page; loadPayouts(); });
                    } else showError('payoutsTableBody', 'payoutsMobileCards');
                } catch (error) { showError('payoutsTableBody', 'payoutsMobileCards'); }
            }

            function renderPayoutsDesktop(payouts) {
                const tbody = document.getElementById('payoutsTableBody');
                if (!payouts || !payouts.length) {
                    tbody.innerHTML = `<tr><td colspan="7" class="loading-cell" style="text-align:center;padding:40px;"><i class="fas fa-money-bill-wave" style="font-size:2rem;color:#c4b5fd;"></i><p>{{ __('No payouts found') }}</p></td></tr>`;
                    return;
                }
                tbody.innerHTML = payouts.map(p => {
                    const statusClass = p.status === 'pending' ? 'badge-pending' : (p.status === 'paid' ? 'badge-paid' : 'badge-failed');
                    const statusText = p.status === 'pending' ? '{{ __("Pending") }}' : (p.status === 'paid' ? '{{ __("Paid") }}' : '{{ __("Failed") }}');
                    const avatar = p.profile_image_url ? `<img src="${p.profile_image_url}" alt="${escapeHtml(p.specialist_name)}">` : (p.specialist_name?.charAt(0) || 'S');
                    return `<tr>
                        <td style="white-space: nowrap;">${new Date(p.created_at).toLocaleDateString()}</td>
                        <td><div class="specialist-cell"><div class="specialist-avatar">${avatar}</div><div><div class="specialist-name">${escapeHtml(p.specialist_name)}</div><div class="specialist-email">${escapeHtml(p.specialist_email)}</div></div></div></div>
                        <td>${p.month_year}</div>
                        <td class="font-bold">$${parseFloat(p.amount).toFixed(2)}</div>
                        <td>$${parseFloat(p.platform_fee).toFixed(2)}</div>
                        <td class="font-bold text-success">$${parseFloat(p.final_amount).toFixed(2)}</div>
                        <td><span class="badge-status ${statusClass}">${statusText}</span></div>
                    </tr>`;
                }).join('');
            }

            function renderPayoutsMobile(payouts) {
                const container = document.getElementById('payoutsMobileCards');
                if (!container) return;
                if (!payouts || !payouts.length) {
                    container.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-money-bill-wave" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No payouts found') }}</p></div>`;
                    return;
                }
                container.innerHTML = payouts.map(p => {
                    const statusClass = p.status === 'pending' ? 'badge-pending' : (p.status === 'paid' ? 'badge-paid' : 'badge-failed');
                    const statusText = p.status === 'pending' ? '{{ __("Pending") }}' : (p.status === 'paid' ? '{{ __("Paid") }}' : '{{ __("Failed") }}');
                    const avatar = p.profile_image_url ? `<img src="${p.profile_image_url}" alt="${escapeHtml(p.specialist_name)}">` : (p.specialist_name?.charAt(0) || 'S');
                    return `
                        <div class="mobile-payment-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-user">
                                    <div class="mobile-card-avatar">${avatar}</div>
                                    <div class="mobile-card-info">
                                        <div class="mobile-card-name">${escapeHtml(p.specialist_name)}</div>
                                        <div class="mobile-card-email">${escapeHtml(p.specialist_email)}</div>
                                    </div>
                                </div>
                                <span class="badge-status ${statusClass}">${statusText}</span>
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Date') }}:</span>
                                    <span class="mobile-detail-value">${new Date(p.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Month') }}:</span>
                                    <span class="mobile-detail-value">${p.month_year}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Total Earnings') }}:</span>
                                    <span class="mobile-detail-value">$${parseFloat(p.amount).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Platform Fee') }}:</span>
                                    <span class="mobile-detail-value">$${parseFloat(p.platform_fee).toFixed(2)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Final Amount') }}:</span>
                                    <span class="mobile-detail-value font-bold text-success">$${parseFloat(p.final_amount).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .custom-modal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('active')));
            });
            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
            });

            // Initial load
            loadCreditRequests();
        </script>
    @endpush
@endsection