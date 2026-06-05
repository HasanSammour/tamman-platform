{{-- resources/views/admin/approvals/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Approvals') . ' - ' . __('Tamman'))

@section('page-title', __('Specialist Applications'))

@section('content')
    <div class="approvals-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card pending-card">
                <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                <div class="stat-data">
                    <h3 id="statPending">{{ $stats['pending'] }}</h3>
                    <p>{{ __('Pending Applications') }}</p>
                </div>
            </div>
            <div class="stat-card approved-card">
                <div class="stat-icon approved"><i class="fas fa-check-circle"></i></div>
                <div class="stat-data">
                    <h3 id="statApproved">{{ $stats['approved'] }}</h3>
                    <p>{{ __('Approved') }}</p>
                </div>
            </div>
            <div class="stat-card rejected-card">
                <div class="stat-icon rejected"><i class="fas fa-times-circle"></i></div>
                <div class="stat-data">
                    <h3 id="statRejected">{{ $stats['rejected'] }}</h3>
                    <p>{{ __('Rejected') }}</p>
                </div>
            </div>
            <div class="stat-card total-card">
                <div class="stat-icon total"><i class="fas fa-users"></i></div>
                <div class="stat-data">
                    <h3 id="statTotal">{{ $stats['total'] }}</h3>
                    <p>{{ __('Total Applications') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs and Search Row -->
        <div class="tabs-search-row">
            <div class="status-tabs">
                <button class="tab-btn {{ $currentStatus == 'pending' ? 'active' : '' }}" data-status="pending">
                    <i class="fas fa-clock"></i>
                    <span>{{ __('Pending') }}</span>
                    <span class="badge">{{ $stats['pending'] }}</span>
                </button>
                <button class="tab-btn {{ $currentStatus == 'approved' ? 'active' : '' }}" data-status="approved">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('Approved') }}</span>
                    <span class="badge">{{ $stats['approved'] }}</span>
                </button>
                <button class="tab-btn {{ $currentStatus == 'rejected' ? 'active' : '' }}" data-status="rejected">
                    <i class="fas fa-times-circle"></i>
                    <span>{{ __('Rejected') }}</span>
                    <span class="badge">{{ $stats['rejected'] }}</span>
                </button>
                <button class="tab-btn {{ $currentStatus == 'all' ? 'active' : '' }}" data-status="all">
                    <i class="fas fa-list"></i>
                    <span>{{ __('All') }}</span>
                    <span class="badge">{{ $stats['total'] }}</span>
                </button>
            </div>

            <div class="search-field">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="{{ __('Search by name, email or phone...') }}">
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="desktop-table-container">
            <div class="table-card">
                <div class="table-header">
                    <h4><i class="fas fa-clipboard-list"></i>
                        <span id="tableTitle">
                            @if($currentStatus == 'pending')
                                {{ __('Pending Applications') }}
                            @elseif($currentStatus == 'approved')
                                {{ __('Approved Applications') }}
                            @elseif($currentStatus == 'rejected')
                                {{ __('Rejected Applications') }}
                            @else
                                {{ __('All Applications') }}
                            @endif
                        </span>
                    </h4>
                    <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
                </div>

                <div class="table-scroll-wrapper">
                    <table class="approvals-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="name" class="sortable">{{ __('Applicant') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="specialization">{{ __('Specialization') }}</th>
                                <th data-sort="email" class="sortable">{{ __('Email') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="phone">{{ __('Phone') }}</th>
                                <th data-sort="experience_years">{{ __('Experience') }}</th>
                                <th data-sort="consultation_fee">{{ __('Fee') }}</th>
                                <th data-sort="applied_at" class="sortable">{{ __('Applied') }} <i class="fas fa-sort"></i>
                                </th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Documents') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="approvalsTableBody">
                            <tr class="loading-row">
                                <td colspan="11">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading applications...') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination-controls" id="paginationControls"></div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-cards-container" id="mobileCardsContainer">
            <!-- Mobile cards will be dynamically rendered here -->
        </div>
    </div>

    @push('styles')
        <style>
            /* Approvals Page Specific Styles - Mobile Optimized */
            .approvals-container {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-card {
                background: white;
                border-radius: 16px;
                padding: 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s;
            }

            .stat-card:hover {
                transform: translateY(-2px);
            }

            .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.3rem;
                color: white;
            }

            .stat-icon.pending {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.approved {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.rejected {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }

            .stat-icon.total {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-data h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 4px 0 0;
            }

            /* Tabs and Search */
            .tabs-search-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 24px;
            }

            .status-tabs {
                display: flex;
                gap: 8px;
                background: white;
                padding: 5px;
                border-radius: 12px;
                flex-wrap: wrap;
            }

            .tab-btn {
                padding: 8px 16px;
                border-radius: 10px;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #f3f4f6;
                color: #6b7280;
                border: none;
                cursor: pointer;
            }

            .tab-btn i {
                font-size: 0.85rem;
            }

            .tab-btn:hover {
                background: #e5e7eb;
            }

            .tab-btn.active {
                background: #7c3aed;
                color: white;
            }

            .tab-btn .badge {
                background: rgba(0, 0, 0, 0.1);
                padding: 2px 6px;
                border-radius: 12px;
                font-size: 0.7rem;
            }

            .tab-btn.active .badge {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .search-field {
                position: relative;
                flex: 1;
                min-width: 250px;
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
                background: white;
            }

            .search-field input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            /* Desktop Table */
            .desktop-table-container {
                display: block;
            }

            .table-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                overflow-x: auto;
            }

            .table-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .table-header h4 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 600;
                color: #1f2937;
            }

            .table-header h4 i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .table-info {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .table-scroll-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .approvals-table {
                width: 100%;
                min-width: 1000px;
                border-collapse: collapse;
            }

            .approvals-table th,
            .approvals-table td {
                padding: 12px 10px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                font-size: 0.75rem;
            }

            .approvals-table th {
                background: #fafafa;
                font-weight: 600;
                color: #374151;
            }

            .approvals-table td {
                color: #4b5563;
            }

            .sortable {
                cursor: pointer;
                user-select: none;
            }

            .sortable:hover {
                color: #7c3aed;
            }

            .sortable i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Avatar */
            .user-avatar-cell {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .avatar-img {
                width: 32px;
                height: 32px;
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

            .avatar-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .user-name {
                font-weight: 600;
                color: #1f2937;
            }

            .user-id {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Status Badges */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 8px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .status-pending {
                background: #fef3c7;
                color: #d97706;
            }

            .status-approved {
                background: #d1fae5;
                color: #065f46;
            }

            .status-rejected {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-document {
                background: #ede9fe;
                color: #7c3aed;
                padding: 4px 8px;
                border-radius: 20px;
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                white-space: nowrap;
            }

            .action-buttons {
                display: flex;
                gap: 6px;
            }

            .action-btn {
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                text-decoration: none;
            }

            .btn-view {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .btn-view:hover {
                background: #c7d2fe;
            }

            /* Loading */
            .loading-spinner {
                width: 35px;
                height: 35px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 12px;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .loading-row td {
                text-align: center;
                padding: 50px 20px !important;
            }

            /* Pagination */
            .table-footer {
                padding: 12px 20px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .pagination-info {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .pagination-controls {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }

            .page-btn {
                min-width: 32px;
                height: 32px;
                padding: 0 8px;
                border: 1px solid #e5e7eb;
                background: white;
                border-radius: 6px;
                cursor: pointer;
                font-size: 0.75rem;
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

            /* Mobile Cards - Hidden on desktop */
            .mobile-cards-container {
                display: none;
            }

            /* ============================================ */
            /* RESPONSIVE BREAKPOINTS */
            /* ============================================ */

            @media (max-width: 992px) {
                .stats-grid {
                    gap: 12px;
                }

                .stat-card {
                    padding: 12px;
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

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                }

                .stat-card {
                    padding: 12px;
                }

                .status-tabs {
                    width: 100%;
                    justify-content: space-between;
                    overflow-x: auto;
                    flex-wrap: nowrap;
                    -webkit-overflow-scrolling: touch;
                }

                .tab-btn {
                    flex: 0 0 auto;
                    padding: 8px 14px;
                    font-size: 0.75rem;
                }

                .search-field {
                    width: 100%;
                    min-width: auto;
                }

                /* Hide desktop table on mobile */
                .desktop-table-container {
                    display: none;
                }

                /* Show mobile cards */
                .mobile-cards-container {
                    display: block;
                    width: 100%;
                    max-width: 100%;
                    overflow-x: hidden;
                }

                /* Mobile Card Styles - Optimized for 375px */
                .mobile-app-card {
                    background: white;
                    border-radius: 16px;
                    margin-bottom: 12px;
                    padding: 14px;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 100%;
                    box-sizing: border-box;
                    overflow-x: hidden;
                }

                .card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 12px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #f0f0f0;
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .card-user {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex: 1;
                    min-width: 0;
                }

                .card-avatar {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #7c3aed, #6d28d9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 700;
                    font-size: 1rem;
                    flex-shrink: 0;
                    overflow: hidden;
                }

                .card-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .card-user-info {
                    flex: 1;
                    min-width: 0;
                }

                .card-user-name {
                    font-weight: 700;
                    font-size: 0.95rem;
                    color: #1f2937;
                    margin-bottom: 4px;
                    word-break: break-word;
                }

                .card-user-id {
                    font-size: 0.6rem;
                    color: #9ca3af;
                }

                .card-details {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    margin-bottom: 12px;
                }

                .detail-row {
                    display: flex;
                    align-items: flex-start;
                    gap: 8px;
                    font-size: 0.75rem;
                    flex-wrap: wrap;
                }

                .detail-icon {
                    width: 24px;
                    color: #7c3aed;
                    text-align: center;
                    font-size: 0.8rem;
                    flex-shrink: 0;
                    margin-top: 2px;
                }

                .detail-label {
                    min-width: 90px;
                    color: #6b7280;
                    font-size: 0.7rem;
                    font-weight: 500;
                    flex-shrink: 0;
                }

                .detail-value {
                    flex: 1;
                    color: #1f2937;
                    font-size: 0.75rem;
                    word-break: break-word;
                    min-width: 0;
                }

                .specialization-badge {
                    background: #ede9fe;
                    color: #7c3aed;
                    padding: 3px 8px;
                    border-radius: 20px;
                    font-size: 0.65rem;
                    display: inline-block;
                    word-break: keep-all;
                }

                .card-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 12px;
                    padding-top: 12px;
                    border-top: 1px solid #f0f0f0;
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .card-docs {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    background: #ede9fe;
                    padding: 4px 10px;
                    border-radius: 20px;
                    font-size: 0.65rem;
                    color: #7c3aed;
                }

                .card-actions {
                    display: flex;
                    gap: 8px;
                }

                .card-action-btn {
                    padding: 5px 14px;
                    border-radius: 20px;
                    font-size: 0.7rem;
                    font-weight: 500;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    transition: all 0.2s;
                }

                .card-action-view {
                    background: #e0e7ff;
                    color: #4f46e5;
                }

                .table-header {
                    flex-direction: column;
                    text-align: center;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                }

                .pagination-controls {
                    justify-content: center;
                }
            }

            /* iPhone SE / Small devices (375px and below) */
            @media (max-width: 480px) {
                .approvals-container {
                    padding: 8px;
                }

                .stats-grid {
                    gap: 8px;
                }

                .stat-card {
                    padding: 10px;
                }

                .stat-icon {
                    width: 36px;
                    height: 36px;
                }

                .stat-icon i {
                    font-size: 0.9rem;
                }

                .stat-data h3 {
                    font-size: 1.1rem;
                }

                .stat-data p {
                    font-size: 0.6rem;
                }

                .tab-btn span:not(.badge) {
                    display: none;
                }

                .tab-btn {
                    padding: 6px 10px;
                }

                .tab-btn i {
                    margin: 0;
                    font-size: 0.9rem;
                }

                .tab-btn .badge {
                    font-size: 0.6rem;
                    padding: 1px 4px;
                }

                /* Mobile card optimizations for 375px */
                .mobile-app-card {
                    padding: 12px;
                }

                .card-user {
                    gap: 8px;
                }

                .card-avatar {
                    width: 38px;
                    height: 38px;
                    font-size: 0.85rem;
                }

                .card-user-name {
                    font-size: 0.85rem;
                }

                .detail-row {
                    gap: 6px;
                }

                .detail-icon {
                    width: 20px;
                    font-size: 0.7rem;
                }

                .detail-label {
                    min-width: 80px;
                    font-size: 0.65rem;
                }

                .detail-value {
                    font-size: 0.7rem;
                }

                .specialization-badge {
                    padding: 2px 6px;
                    font-size: 0.6rem;
                }

                .card-docs {
                    padding: 3px 8px;
                    font-size: 0.6rem;
                }

                .card-action-btn {
                    padding: 4px 12px;
                    font-size: 0.65rem;
                }
            }

            /* Extra small devices (320px) */
            @media (max-width: 380px) {
                .detail-label {
                    min-width: 70px;
                }

                .card-action-btn {
                    padding: 4px 10px;
                }

                .card-action-btn i {
                    display: none;
                }
            }

            @media (max-width: 550px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* RTL Support */
            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .search-field i {
                left: auto;
                right: 12px;
            }

            body.rtl .search-field input {
                padding: 10px 38px 10px 12px;
            }

            body.rtl .table-header h4 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .detail-icon {
                margin-left: 8px;
                margin-right: 0;
            }

            body.rtl .detail-label {
                text-align: right;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let search = '';
            let currentStatus = '{{ $currentStatus }}';
            let isLoading = false;

            const tableBody = document.getElementById('approvalsTableBody');
            const mobileCardsContainer = document.getElementById('mobileCardsContainer');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');

            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (isLoading) return;
                    const status = this.dataset.status;
                    if (status === currentStatus) return;

                    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    currentStatus = status;
                    currentPage = 1;
                    sortField = 'created_at';
                    sortDirection = 'desc';

                    const tableTitle = document.getElementById('tableTitle');
                    if (status === 'pending') tableTitle.innerText = '{{ __("Pending Applications") }}';
                    else if (status === 'approved') tableTitle.innerText = '{{ __("Approved Applications") }}';
                    else if (status === 'rejected') tableTitle.innerText = '{{ __("Rejected Applications") }}';
                    else tableTitle.innerText = '{{ __("All Applications") }}';

                    searchInput.value = '';
                    search = '';

                    loadApplications();

                    const url = new URL(window.location.href);
                    url.searchParams.set('status', status);
                    window.history.pushState({}, '', url);
                });
            });

            // Sort
            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    if (isLoading) return;
                    const field = th.dataset.sort;
                    if (sortField === field) sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    else { sortField = field; sortDirection = 'asc'; }
                    document.querySelectorAll('.sortable i').forEach(icon => icon.className = 'fas fa-sort');
                    th.querySelector('i').className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    currentPage = 1;
                    loadApplications();
                });
            });

            // Search with debounce
            let searchTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    search = searchInput.value;
                    currentPage = 1;
                    loadApplications();
                }, 300);
            });

            async function loadApplications() {
                if (isLoading) return;
                isLoading = true;

                tableBody.innerHTML = `<tr class="loading-row"><td colspan="11"><div class="loading-spinner"></div><p>{{ __('Loading applications...') }}</p></td></tr>`;
                mobileCardsContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading applications...') }}</p></div>`;

                try {
                    const url = `{{ route("admin.approvals.data") }}?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}&status=${currentStatus}`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();

                    if (data.success) {
                        renderTable(data.data);
                        renderMobileCards(data.data);
                        renderPagination(data);
                    } else {
                        showError();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError();
                } finally {
                    isLoading = false;
                }
            }

            function renderMobileCards(applications) {
                if (!applications || !applications.length) {
                    mobileCardsContainer.innerHTML = `<div style="text-align:center;padding:40px;">
                                <i class="fas fa-inbox" style="font-size:2rem;color:#c4b5fd;"></i>
                                <p style="margin-top:12px;color:#6b7280;font-size:0.8rem;">{{ __('No applications found') }}</p>
                            </div>`;
                    return;
                }

                mobileCardsContainer.innerHTML = applications.map(app => {
                    const profileImg = app.profile_image_url;
                    const initial = (app.user_name?.charAt(0) || 'A').toUpperCase();
                    const avatarHtml = profileImg ? `<img src="${profileImg}" alt="${escapeHtml(app.user_name)}" onerror="this.style.display='none';this.parentElement.innerHTML='${initial}'">` : initial;

                    let statusBadge = '';
                    if (app.application_status === 'pending') {
                        statusBadge = '<span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ __("Pending") }}</span>';
                    } else if (app.application_status === 'approved') {
                        statusBadge = '<span class="status-badge status-approved"><i class="fas fa-check-circle"></i> {{ __("Approved") }}</span>';
                    } else {
                        statusBadge = '<span class="status-badge status-rejected"><i class="fas fa-times-circle"></i> {{ __("Rejected") }}</span>';
                    }

                    return `
                                <div class="mobile-app-card">
                                    <div class="card-header">
                                        <div class="card-user">
                                            <div class="card-avatar">${avatarHtml}</div>
                                            <div class="card-user-info">
                                                <div class="card-user-name">${escapeHtml(app.user_name)}</div>
                                                <div class="card-user-id">{{ __('ID') }}: ${app.user_id}</div>
                                            </div>
                                        </div>
                                        <div>${statusBadge}</div>
                                    </div>
                                    <div class="card-details">
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-briefcase"></i></div>
                                            <div class="detail-label">{{ __('Specialization') }}</div>
                                            <div class="detail-value"><span class="specialization-badge">${escapeHtml(app.specialization)}</span></div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                                            <div class="detail-label">{{ __('Email') }}</div>
                                            <div class="detail-value" style="word-break:break-all;">${escapeHtml(app.user_email)}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-phone"></i></div>
                                            <div class="detail-label">{{ __('Phone') }}</div>
                                            <div class="detail-value">${app.user_phone || '—'}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-chart-line"></i></div>
                                            <div class="detail-label">{{ __('Experience') }}</div>
                                            <div class="detail-value">${app.experience_years || 0} ${app.experience_years == 1 ? '{{ __("year") }}' : '{{ __("years") }}'}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-dollar-sign"></i></div>
                                            <div class="detail-label">{{ __('Consultation Fee') }}</div>
                                            <div class="detail-value">$${parseFloat(app.consultation_fee || 0).toFixed(2)}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                                            <div class="detail-label">{{ __('Applied Date') }}</div>
                                            <div class="detail-value">${new Date(app.created_at).toLocaleDateString()}</div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="card-docs"><i class="fas fa-file-alt"></i> 2 {{ __('Documents') }}</div>
                                        <div class="card-actions">
                                            <a href="/admin/approvals/${app.id}" class="card-action-btn card-action-view"><i class="fas fa-eye"></i> {{ __('View') }}</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                }).join('');
            }

            function renderTable(applications) {
                if (!applications || !applications.length) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="11"><div style="text-align:center;padding:40px;"><i class="fas fa-inbox" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:12px;">{{ __('No applications found') }}</p></div></td></tr>`;
                    return;
                }

                tableBody.innerHTML = applications.map(app => {
                    const profileImg = app.profile_image_url;
                    const initial = (app.user_name?.charAt(0) || 'A').toUpperCase();
                    const avatarHtml = profileImg ? `<img src="${profileImg}" alt="${escapeHtml(app.user_name)}" onerror="this.style.display='none';this.parentElement.innerHTML='${initial}'">` : initial;

                    let statusBadge = '';
                    if (app.application_status === 'pending') {
                        statusBadge = '<span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ __("Pending") }}</span>';
                    } else if (app.application_status === 'approved') {
                        statusBadge = '<span class="status-badge status-approved"><i class="fas fa-check-circle"></i> {{ __("Approved") }}</span>';
                    } else {
                        statusBadge = '<span class="status-badge status-rejected"><i class="fas fa-times-circle"></i> {{ __("Rejected") }}</span>';
                    }

                    return `
                                <tr>
                                    <td>#${app.id}</td>
                                    <td>
                                        <div class="user-avatar-cell">
                                            <div class="avatar-img">${avatarHtml}</div>
                                            <div>
                                                <div class="user-name">${escapeHtml(app.user_name)}</div>
                                                <div class="user-id">ID: ${app.user_id}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <td><span class="specialization-badge">${escapeHtml(app.specialization)}</span></div>
                                    <td>${escapeHtml(app.user_email)}</div>
                                    <td>${app.user_phone || '—'}</div>
                                    <td>${app.experience_years || 0} ${app.experience_years == 1 ? '{{ __("year") }}' : '{{ __("years") }}'}</div>
                                    <td>$${parseFloat(app.consultation_fee || 0).toFixed(2)}</div>
                                    <td>${new Date(app.created_at).toLocaleDateString()}</div>
                                    <td>${statusBadge}</div>
                                    <td><span class="badge-document"><i class="fas fa-file-alt"></i> 2</span></div>
                                    <td><div class="action-buttons"><a href="/admin/approvals/${app.id}" class="action-btn btn-view"><i class="fas fa-eye"></i> {{ __('View') }}</a></div></div>
                                </tr>
                            `;
                }).join('');
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);

                if (total > 0) {
                    tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('applications') }}`;
                    paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;
                } else {
                    tableInfo.innerHTML = `{{ __('No applications found') }}`;
                    paginationInfo.innerHTML = '';
                }

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
                if (page === currentPage || isLoading) return;
                currentPage = page;
                loadApplications();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function showError() {
                const errorHtml = `<div style="text-align:center;padding:40px;">
                            <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#ef4444;"></i>
                            <p style="margin-top:12px;color:#6b7280;font-size:0.8rem;">{{ __("Error loading applications") }}</p>
                            <button onclick="loadApplications()" style="margin-top:12px;padding:6px 16px;background:#7c3aed;color:white;border:none;border-radius:8px;cursor:pointer;font-size:0.75rem;">{{ __("Retry") }}</button>
                        </div>`;

                tableBody.innerHTML = `<tr class="empty-row"><td colspan="11">${errorHtml}<\/tr>`;
                mobileCardsContainer.innerHTML = errorHtml;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            // Initial load
            loadApplications();
        </script>
    @endpush
@endsection