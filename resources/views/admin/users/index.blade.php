{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', __('User Management') . ' - ' . __('Tamman'))

@section('page-title', __('User Management'))

@section('content')
    <div class="admin-users-wrapper">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                <div class="stat-data">
                    <h3 id="statTotal">{{ $stats['total'] }}</h3>
                    <p>{{ __('Total Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                <div class="stat-data">
                    <h3 id="statActive">{{ $stats['active'] }}</h3>
                    <p>{{ __('Active Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-user-slash"></i></div>
                <div class="stat-data">
                    <h3 id="statSuspended">{{ $stats['suspended'] }}</h3>
                    <p>{{ __('Suspended Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-hand-holding-heart"></i></div>
                <div class="stat-data">
                    <h3 id="statDonors">{{ $stats['donors'] }}</h3>
                    <p>{{ __('Donors') }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container">
            <div class="filters-row">
                <div class="filter-item search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="{{ __('Search by name, email or phone...') }}">
                </div>
                <select id="statusFilter" class="filter-select">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="suspended">{{ __('Suspended') }}</option>
                </select>
                <select id="donorFilter" class="filter-select">
                    <option value="all">{{ __('All Users') }}</option>
                    <option value="yes">{{ __('Donors Only') }}</option>
                    <option value="no">{{ __('Non-Donors') }}</option>
                </select>
                <select id="genderFilter" class="filter-select">
                    <option value="all">{{ __('All Genders') }}</option>
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
                <button id="resetFiltersBtn" class="btn-reset"><i class="fas fa-undo-alt"></i> {{ __('Reset') }}</button>
                <button id="exportPdfBtn" class="btn-export"><i class="fas fa-file-pdf"></i> {{ __('Export PDF') }}</button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> {{ __('Users List') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <!-- IMPORTANT: This wrapper enables horizontal scroll -->
            <div class="table-scroll-wrapper">
                <div class="table-responsive-inner">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="name" class="sortable">{{ __('User') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="email" class="sortable">{{ __('Email') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="phone" class="sortable">{{ __('Phone') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="total_sessions">{{ __('Sessions') }}</th>
                                <th data-sort="total_points">{{ __('Points') }}</th>
                                <th data-sort="credit_balance">{{ __('Credit') }}</th>
                                <th data-sort="is_donor">{{ __('Donor') }}</th>
                                <th data-sort="is_active">{{ __('Status') }}</th>
                                <th data-sort="last_login_at">{{ __('Last Active') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr class="loading-row">
                                <td colspan="11">
                                    <div class="loading-spinner"></div>
                                    <p>{{ __('Loading users...') }}</p>
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

    <!-- Modals (same as before) -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-trash-alt"></i> {{ __('Delete User') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p>{{ __('Are you sure you want to delete this user?') }}</p>
                <p class="warning-text">{{ __('This action cannot be undone.') }}</p>
                <input type="hidden" id="deleteId">
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button class="btn-confirm-delete" id="confirmDeleteBtn">
                    <span>{{ __('Delete') }}</span>
                    <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <div id="suspendModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-ban"></i> <span id="suspendTitle">{{ __('Suspend User') }}</span></h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p id="suspendMessage">{{ __('Are you sure you want to suspend this user?') }}</p>
                <input type="hidden" id="suspendId">
                <input type="hidden" id="suspendActionType">
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button class="btn-confirm-suspend" id="confirmSuspendBtn">
                    <span>{{ __('Confirm') }}</span>
                    <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <div id="impersonateBanner" class="impersonate-banner" style="display: none;">
        <i class="fas fa-user-secret"></i>
        <span>{{ __('You are impersonating a user') }}</span>
        <a href="{{ route('admin.users.stop-impersonate') }}" class="btn-stop-impersonate">
            <i class="fas fa-sign-out-alt"></i> {{ __('Stop') }}
        </a>
    </div>

    @push('styles')
        <style>
            /* these are ensure the main app layout doesn't overflow */
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

            /* Mobile responsive */
            @media (max-width: 768px) {
                .app-main {
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }
            }

            /* MAIN WRAPPER - Ensures everything stays inside borders */
            .admin-users-wrapper {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 20px;
                overflow-x: hidden;
                box-sizing: border-box;
                position: relative;
            }

            /* Stats Grid - Responsive */
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
                /* Prevents overflow in flex/grid items */
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

            .stat-icon.red {
                background: linear-gradient(135deg, #ef4444, #dc2626);
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

            /* Filters Container */
            .filters-container {
                background: white;
                border-radius: 16px;
                padding: 15px 20px;
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
            }

            .search-field {
                flex: 1;
                min-width: 200px;
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
            }

            .search-field input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            .filter-select {
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
            }

            .btn-reset,
            .btn-export {
                padding: 10px 18px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
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

            /* Table Card */
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

            .table-responsive-inner {
                min-width: 1100px;
                width: 100%;
            }

            .users-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 100%;
            }

            .users-table th,
            .users-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
                white-space: nowrap;
                max-width: 300px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .users-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .users-table td {
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

            /* User Avatar */
            .user-avatar-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .avatar-img {
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
                font-size: 0.65rem;
                color: #9ca3af;
            }

            /* Badges */
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

            .badge-active {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-suspended {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-donor {
                background: #fef3c7;
                color: #d97706;
            }

            .badge-non-donor {
                background: #f3f4f6;
                color: #6b7280;
            }

            .online-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #10b981;
                display: inline-block;
                margin-right: 6px;
                animation: pulse 1.5s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.4;
                }
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 6px;
                flex-wrap: nowrap;
            }

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
            }

            .action-btn:hover {
                transform: translateY(-2px);
            }

            .btn-view {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-view:hover {
                background: #ddd6fe;
            }

            .btn-edit {
                background: #dbeafe;
                color: #2563eb;
            }

            .btn-edit:hover {
                background: #bfdbfe;
            }

            .btn-suspend {
                background: #fef3c7;
                color: #d97706;
            }

            .btn-suspend:hover {
                background: #fde68a;
            }

            .btn-activate {
                background: #d1fae5;
                color: #059669;
            }

            .btn-activate:hover {
                background: #a7f3d0;
            }

            .btn-impersonate {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .btn-impersonate:hover {
                background: #c7d2fe;
            }

            .btn-delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .btn-delete:hover {
                background: #fecaca;
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

            /* Loading */
            .loading-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            .loading-row td > div {
                text-align: center;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
                display: block;
            }
            
            .loading-row p {
                text-align: center;
                margin: 0;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Empty State */
            .empty-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            .empty-row td > div {
                text-align: center;
                width: 100%;
            }

            .empty-icon {
                font-size: 4rem;
                color: #c4b5fd;
                margin-bottom: 15px;
                display: block;
                text-align: center;
                width: 100%;
            }

            .empty-icon i {
                display: inline-block;
            }

            .empty-text {
                color: #6b7280;
                margin-bottom: 15px;
                text-align: center;
                font-size: 1rem;
            }

            /* Modals */
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
                max-width: 450px;
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

            .warning-text {
                color: #f59e0b;
                font-size: 0.8rem;
                margin-top: 10px;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-confirm-delete {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-confirm-suspend {
                background: #f59e0b;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-loader {
                display: none;
            }

            /* Impersonate Banner */
            .impersonate-banner {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #1e1b4b;
                color: white;
                padding: 12px 20px;
                border-radius: 50px;
                display: flex;
                align-items: center;
                gap: 12px;
                z-index: 9999;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .btn-stop-impersonate {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: none;
                padding: 6px 14px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.75rem;
            }

            .btn-stop-impersonate:hover {
                background: rgba(255, 255, 255, 0.3);
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

            body.rtl .users-table th,
            body.rtl .users-table td {
                text-align: right;
            }

            body.rtl .online-dot {
                margin-right: 0;
                margin-left: 6px;
            }

            body.rtl .impersonate-banner {
                right: auto;
                left: 20px;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                }
            }

            /* Allow email and name columns to wrap on very small screens if needed */
            @media (max-width: 1400px) {
                .users-table th:nth-child(3),
                .users-table td:nth-child(3) {
                    max-width: 200px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
            }

            @media (max-width: 768px) {
                .admin-users-wrapper {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .filters-row {
                    flex-direction: column;
                    align-items: stretch;
                }

                .search-field {
                    width: 100%;
                    min-width: auto;
                }

                .filter-select {
                    width: 100%;
                }

                .btn-reset,
                .btn-export {
                    width: 100%;
                    text-align: center;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                    padding: 16px;
                }

                .table-header {
                    padding: 15px;
                }

                .pagination-controls {
                    justify-content: center;
                    flex-wrap: wrap;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if(session()->has('impersonate_admin'))
                document.getElementById('impersonateBanner').style.display = 'flex';
            @endif

            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let search = '', status = 'all', donor = 'all', gender = 'all';

            const tableBody = document.getElementById('usersTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const donorFilter = document.getElementById('donorFilter');
            const genderFilter = document.getElementById('genderFilter');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const exportBtn = document.getElementById('exportPdfBtn');
            const baseUrl = '{{ url("/") }}';

            // Sort
            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    const field = th.dataset.sort;
                    if (sortField === field) sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    else { sortField = field; sortDirection = 'asc'; }
                    document.querySelectorAll('.sortable i').forEach(icon => icon.className = 'fas fa-sort');
                    th.querySelector('i').className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    currentPage = 1;
                    loadUsers();
                });
            });

            // Filters
            searchInput.addEventListener('input', () => { search = searchInput.value; currentPage = 1; loadUsers(); });
            statusFilter.addEventListener('change', () => { status = statusFilter.value; currentPage = 1; loadUsers(); });
            donorFilter.addEventListener('change', () => { donor = donorFilter.value; currentPage = 1; loadUsers(); });
            genderFilter.addEventListener('change', () => { gender = genderFilter.value; currentPage = 1; loadUsers(); });

            resetBtn.addEventListener('click', () => {
                searchInput.value = ''; statusFilter.value = 'all'; donorFilter.value = 'all'; genderFilter.value = 'all';
                search = ''; status = 'all'; donor = 'all'; gender = 'all';
                currentPage = 1;
                loadUsers();
            });

            exportBtn.addEventListener('click', () => {
                window.open(`{{ route("admin.users.export-pdf") }}?search=${encodeURIComponent(search)}&status=${status}&donor=${donor}&gender=${gender}`, '_blank');
            });

            async function loadUsers() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="11"><div class="loading-spinner"></div><p>{{ __('Loading users...') }}</p></td></tr>`;
                try {
                    const url = `{{ route("admin.users.data") }}?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}&status=${status}&donor=${donor}&gender=${gender}`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    if (data.success) { renderTable(data.data); renderPagination(data); }
                    else showError();
                } catch (error) { showError(); }
            }

            function renderTable(users) {
                if (!users || !users.length) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="11"><div class="empty-icon"><i class="fas fa-users"></i></div><p class="empty-text">{{ __('No users found') }}</p></td></tr>`;
                    return;
                }
                tableBody.innerHTML = users.map(user => {
                    const profileImg = user.profile_image_url;
                    const initial = (user.name?.charAt(0) || 'U').toUpperCase();
                    const avatarHtml = profileImg ? `<img src="${profileImg}" alt="${escapeHtml(user.name)}" onerror="this.style.display='none';this.parentElement.innerHTML='${initial}'">` : initial;
                    const points = parseInt(user.total_points) || 0;
                    const credit = parseFloat(user.credit_balance) || 0;
                    return `<tr>
                                <td>#${user.id}</td>
                                <td><div class="user-avatar-cell"><div class="avatar-img">${avatarHtml}</div><div><div class="user-name">${escapeHtml(user.name)}</div><div class="user-id">ID: ${user.id}</div></div></div></td>
                                <td>${escapeHtml(user.email)}</td>
                                <td>${user.phone || '—'}</td>
                                <td>${user.total_sessions || 0}</td>
                                <td>${points.toLocaleString()}</td>
                                <td>$${credit.toFixed(2)}</td>
                                <td>${user.is_donor ? '<span class="badge badge-donor"><i class="fas fa-hand-holding-heart"></i> {{ __("Donor") }}</span>' : '<span class="badge badge-non-donor">{{ __("No") }}</span>'}</td>
                                <td>${user.is_active ? '<span class="badge badge-active"><span class="online-dot"></span> {{ __("Active") }}</span>' : '<span class="badge badge-suspended"><i class="fas fa-ban"></i> {{ __("Suspended") }}</span>'}</td>
                                <td>${getLastActiveHtml(user)}</td>
                                <td><div class="action-buttons">
                                    <a href="${baseUrl}/admin/users/${user.id}" class="action-btn btn-view"><i class="fas fa-eye"></i></a>
                                    <a href="${baseUrl}/admin/users/${user.id}/edit" class="action-btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <button class="action-btn ${user.is_active ? 'btn-suspend' : 'btn-activate'}" onclick="toggleSuspend(${user.id}, ${user.is_active})"><i class="fas ${user.is_active ? 'fa-ban' : 'fa-check-circle'}"></i></button>
                                    <button class="action-btn btn-impersonate" onclick="impersonateUser(${user.id})"><i class="fas fa-user-secret"></i></button>
                                    <button class="action-btn btn-delete" onclick="deleteUser(${user.id})"><i class="fas fa-trash-alt"></i></button>
                                </div></td>
                            </tr>`;
                }).join('');
            }

            function getLastActiveHtml(user) {
                if (user.is_online) return '<span class="badge badge-active"><span class="online-dot"></span> {{ __("Online") }}</span>';
                if (user.last_login_at) {
                    const lastSeen = new Date(user.last_login_at);
                    const diffHours = (new Date() - lastSeen) / (1000 * 60 * 60);
                    if (diffHours < 24) return `<span class="badge badge-non-donor"><i class="fas fa-clock"></i> {{ __("Last seen") }} ${Math.floor(diffHours)} {{ __("hours ago") }}</span>`;
                    return `<span class="badge badge-non-donor"><i class="fas fa-calendar"></i> ${lastSeen.toLocaleDateString()}</span>`;
                }
                return '<span class="badge badge-non-donor"><i class="fas fa-user-slash"></i> {{ __("Never") }}</span>';
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);
                tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('users') }}`;
                paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;
                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;
                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;
                paginationControls.innerHTML = html;
            }

            function goToPage(page) { currentPage = page; loadUsers(); }
            function showError() { tableBody.innerHTML = `<tr class="empty-row"><td colspan="11"><div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div><p class="empty-text">{{ __("Error loading users") }}</p><button class="btn-reset" onclick="loadUsers()">{{ __("Retry") }}</button></td></tr>`; }
            function escapeHtml(str) { if (!str) return str; return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); }

            // Suspend
            window.toggleSuspend = (userId, isActive) => {
                document.getElementById('suspendTitle').innerHTML = isActive ? '<i class="fas fa-ban"></i> {{ __("Suspend User") }}' : '<i class="fas fa-check-circle"></i> {{ __("Activate User") }}';
                document.getElementById('suspendMessage').innerHTML = isActive ? '{{ __("Are you sure you want to suspend this user?") }}' : '{{ __("Are you sure you want to activate this user?") }}';
                document.getElementById('suspendId').value = userId;
                document.getElementById('suspendActionType').value = isActive ? 'suspend' : 'activate';
                document.getElementById('suspendModal').classList.add('active');
            };

            document.getElementById('confirmSuspendBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('confirmSuspendBtn');
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/users/${document.getElementById('suspendId').value}/suspend`, {
                        method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('suspendModal').classList.remove('active');
                        loadUsers();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            // Delete
            window.deleteUser = (userId) => { document.getElementById('deleteId').value = userId; document.getElementById('deleteModal').classList.add('active'); };
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('confirmDeleteBtn');
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/users/${document.getElementById('deleteId').value}`, {
                        method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('deleteModal').classList.remove('active');
                        loadUsers();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            // Impersonate
            window.impersonateUser = async (userId) => {
                const result = await Swal.fire({ title: '{{ __("Impersonate User") }}', text: '{{ __("You will be logged in as this user.") }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#7c3aed', confirmButtonText: '{{ __("Yes") }}', cancelButtonText: '{{ __("Cancel") }}' });
                if (result.isConfirmed) window.location.href = `/admin/users/${userId}/impersonate`;
            };

            document.querySelectorAll('.modal-close, .custom-modal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('active')));
            });
            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
            });

            loadUsers();
        </script>
    @endpush
@endsection