{{-- resources/views/admin/logs/index.blade.php --}}
@extends('layouts.app')

@section('title', __('System Logs') . ' - ' . __('Tamman'))

@section('page-title', __('System Logs'))

@section('content')
    <div class="system-logs-wrapper">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple"><i class="fas fa-history"></i></div>
                <div class="stat-data">
                    <h3 id="statTotal">{{ number_format($stats['total']) }}</h3>
                    <p>{{ __('Total Logs') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon blue"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-data">
                    <h3 id="statToday">{{ number_format($stats['today']) }}</h3>
                    <p>{{ __('Today') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon green"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-data">
                    <h3 id="statWeek">{{ number_format($stats['this_week']) }}</h3>
                    <p>{{ __('This Week') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon orange"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-data">
                    <h3 id="statMonth">{{ number_format($stats['this_month']) }}</h3>
                    <p>{{ __('This Month') }}</p>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="filters-container animate-slide-up">
            <div class="filters-row">
                <div class="filter-item date-range-item">
                    <div class="date-range-wrapper">
                        <div class="date-input"><i class="fas fa-calendar-alt"></i><input type="date" id="filterDateFrom"
                                placeholder="{{ __('From Date') }}"></div>
                        <span class="date-separator">—</span>
                        <div class="date-input"><i class="fas fa-calendar-alt"></i><input type="date" id="filterDateTo"
                                placeholder="{{ __('To Date') }}"></div>
                    </div>
                </div>
                <select id="filterAction" class="filter-select">
                    <option value="all">{{ __('All Actions') }}</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">
                            {{ __('Approve Specialist') == $action ? __('Approve Specialist') : (__('Reject Specialist') == $action ? __('Reject Specialist') : ucfirst(str_replace('_', ' ', $action))) }}
                        </option>
                    @endforeach
                </select>
                <button id="clearByDateBtn" class="btn-clear-date"><i class="fas fa-trash-alt"></i>
                    {{ __('Clear by Date') }}
                </button>
            </div>
            <div class="filters-row">
                <div class="search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filterSearch" placeholder="{{ __('Search by action, details or admin...') }}">
                    <button id="clearSearchBtn" class="clear-search-btn" style="display: none;"><i
                            class="fas fa-times-circle"></i></button>
                </div>
                <button id="applyFiltersBtn" class="btn-apply"><i class="fas fa-check-circle"></i>
                    {{ __('Apply Filters') }}</button>
                <button id="resetFiltersBtn" class="btn-reset"><i class="fas fa-undo-alt"></i> {{ __('Reset') }}</button>
                <button id="exportCsvBtn" class="btn-export"><i class="fas fa-file-csv"></i> {{ __('Export CSV') }}</button>
            </div>
        </div>

        <!-- Bulk Actions Bar - Fixed Styling -->
        <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none;">
            <div class="bulk-selected-info">
                <i class="fas fa-check-circle"></i>
                <span id="selectedCount">0</span> {{ __('logs selected') }}
            </div>
            <div class="bulk-buttons">
                <button id="bulkDeleteBtn" class="btn-bulk-delete"><i class="fas fa-trash-alt"></i>
                    {{ __('Delete Selected') }}</button>
                <button id="cancelBulkBtn" class="btn-cancel-bulk"><i class="fas fa-times"></i> {{ __('Cancel') }}</button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card animate-scale-in">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> {{ __('System Logs') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <div class="table-scroll-wrapper">
                <div class="table-responsive-inner">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th class="checkbox-col"><input type="checkbox" id="selectAllCheckbox"></th>
                                <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                <th data-sort="action" class="sortable">{{ __('Action') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="admin_id" class="sortable">{{ __('Admin') }} <i class="fas fa-sort"></i></th>
                                <th data-sort="details">{{ __('Details') }}</th>
                                <th data-sort="created_at" class="sortable">{{ __('Date & Time') }} <i
                                        class="fas fa-sort"></i></th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <tr class="loading-row">
                                <td colspan="7">
                                    <div class="loading-container">
                                        <div class="loading-spinner"></div>
                                        <p>{{ __('Loading logs...') }}</p>
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

    <!-- Delete Single Log Modal -->
    <div id="deleteLogModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-trash-alt"></i> {{ __('Delete Log Entry') }}</h3><button
                    class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p>{{ __('Are you sure you want to delete this log entry?') }}</p>
                <p class="warning-text">{{ __('This action cannot be undone.') }}</p><input type="hidden" id="deleteLogId">
            </div>
            <div class="custom-modal-footer"><button class="btn-cancel-modal">{{ __('Cancel') }}</button><button
                    class="btn-confirm-delete" id="confirmDeleteBtn"><span>{{ __('Delete') }}</span><span
                        class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span></button></div>
        </div>
    </div>

    <!-- Clear by Date Modal -->
    <div id="clearByDateModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-calendar-alt"></i> {{ __('Clear Logs by Date Range') }}</h3><button
                    class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p>{{ __('This will delete all logs between the selected dates.') }}</p>
                <p class="warning-text">{{ __('This action cannot be undone!') }}</p>
                <div class="form-group"><label for="clearDateFrom">{{ __('From Date') }} <span
                            class="required">*</span></label><input type="date" id="clearDateFrom" class="form-control">
                </div>
                <div class="form-group" style="margin-top: 15px;"><label for="clearDateTo">{{ __('To Date') }} <span
                            class="required">*</span></label><input type="date" id="clearDateTo" class="form-control"></div>
            </div>
            <div class="custom-modal-footer"><button class="btn-cancel-modal">{{ __('Cancel') }}</button><button
                    class="btn-confirm-clear" id="confirmClearBtn"><span>{{ __('Clear Logs') }}</span><span
                        class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span></button></div>
        </div>
    </div>

    @push('styles')
        <style>
            .system-logs-wrapper {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                box-sizing: border-box;
            }

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

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
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

            .filters-container {
                background: white;
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .filters-row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 12px;
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
            .btn-export,
            .btn-clear-date {
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
                background: #10b981;
                color: white;
            }

            .btn-export:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            .btn-clear-date {
                background: #ef4444;
                color: white;
            }

            .btn-clear-date:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            .bulk-actions-bar {
                background: #ede9fe;
                border-radius: 12px;
                padding: 12px 20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                animation: slideUp 0.3s ease;
            }

            .bulk-selected-info {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #7c3aed;
                font-weight: 500;
                font-size: 0.85rem;
            }

            .bulk-buttons {
                display: flex;
                gap: 10px;
            }

            .btn-bulk-delete {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .btn-bulk-delete:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            .btn-cancel-bulk {
                background: #f3f4f6;
                color: #374151;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .btn-cancel-bulk:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

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
            }

            .logs-table {
                width: 100%;
                border-collapse: collapse;
            }

            .logs-table th,
            .logs-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
            }

            .logs-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .logs-table td {
                font-size: 0.8rem;
                color: #4b5563;
            }

            .checkbox-col {
                width: 40px;
                text-align: center;
            }

            .checkbox-col input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
                accent-color: #7c3aed;
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

            .action-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .action-badge.success {
                background: #d1fae5;
                color: #065f46;
            }

            .action-badge.danger {
                background: #fee2e2;
                color: #991b1b;
            }

            .action-badge.warning {
                background: #fef3c7;
                color: #d97706;
            }

            .action-badge.info {
                background: #dbeafe;
                color: #1e40af;
            }

            .action-badge.secondary {
                background: #f3f4f6;
                color: #4b5563;
            }

            .admin-name {
                font-weight: 500;
                color: #1f2937;
            }

            .admin-email {
                font-size: 0.65rem;
                color: #9ca3af;
                display: block;
            }

            .details-cell {
                max-width: 350px;
            }

            .details-preview {
                cursor: pointer;
                color: #7c3aed;
                font-size: 0.75rem;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .details-preview:hover {
                text-decoration: underline;
            }

            .log-details-list {
                font-size: 0.75rem;
            }

            .detail-row {
                display: flex;
                padding: 6px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .detail-row:last-child {
                border-bottom: none;
            }

            .detail-key {
                width: 140px;
                font-weight: 600;
                color: #374151;
                flex-shrink: 0;
            }

            .detail-value {
                flex: 1;
                color: #4b5563;
                word-break: break-word;
            }

            .action-btn-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #fee2e2;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
                color: #dc2626;
            }

            .action-btn-icon:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

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

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease forwards;
            }

            .animate-scale-in {
                animation: scaleIn 0.3s ease forwards;
            }

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
            }

            .required {
                color: #ef4444;
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

            .btn-confirm-delete,
            .btn-confirm-clear {
                background: #ef4444;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-loader {
                display: none;
            }

            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .date-range-item {
                    flex: 1;
                    min-width: 250px;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
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

                .filter-select,
                .search-field,
                .btn-apply,
                .btn-reset,
                .btn-export,
                .btn-clear-date {
                    width: 100%;
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

                .bulk-actions-bar {
                    flex-direction: column;
                    text-align: center;
                }

                .bulk-buttons {
                    width: 100%;
                    justify-content: center;
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

                .detail-key {
                    width: 100px;
                    font-size: 0.7rem;
                }
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

            body.rtl .logs-table th,
            body.rtl .logs-table td {
                text-align: right;
            }

            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .clear-search-btn {
                right: auto;
                left: 10px;
            }

            body.rtl .checkbox-col {
                text-align: center;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let filters = { date_from: '', date_to: '', action: 'all', search: '' };
            let selectedLogs = new Set();

            const tableBody = document.getElementById('logsTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCountSpan = document.getElementById('selectedCount');

            const filterDateFrom = document.getElementById('filterDateFrom');
            const filterDateTo = document.getElementById('filterDateTo');
            const filterAction = document.getElementById('filterAction');
            const filterSearch = document.getElementById('filterSearch');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const applyBtn = document.getElementById('applyFiltersBtn');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const exportBtn = document.getElementById('exportCsvBtn');
            const clearByDateBtn = document.getElementById('clearByDateBtn');

            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    const field = th.dataset.sort;
                    if (sortField === field) sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    else { sortField = field; sortDirection = 'asc'; }
                    document.querySelectorAll('.sortable i').forEach(icon => icon.className = 'fas fa-sort');
                    th.querySelector('i').className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    currentPage = 1;
                    loadLogs();
                });
            });

            filterSearch.addEventListener('input', function () { clearSearchBtn.style.display = this.value ? 'block' : 'none'; });
            clearSearchBtn.addEventListener('click', function () { filterSearch.value = ''; clearSearchBtn.style.display = 'none'; updateFilters(); });
            filterSearch.addEventListener('keypress', (e) => { if (e.key === 'Enter') updateFilters(); });

            function updateFilters() {
                filters.date_from = filterDateFrom.value;
                filters.date_to = filterDateTo.value;
                filters.action = filterAction.value;
                filters.search = filterSearch.value;
                currentPage = 1;
                selectedLogs.clear();
                updateBulkActionsBar();
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                loadLogs();
            }

            function resetFilters() {
                filterDateFrom.value = '';
                filterDateTo.value = '';
                filterAction.value = 'all';
                filterSearch.value = '';
                clearSearchBtn.style.display = 'none';
                updateFilters();
            }

            async function loadLogs() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="7"><div class="loading-container"><div class="loading-spinner"></div><p>{{ __('Loading logs...') }}</p></div></td></tr>`;
                try {
                    const params = new URLSearchParams({ page: currentPage, per_page: perPage, sort_field: sortField, sort_direction: sortDirection, date_from: filters.date_from, date_to: filters.date_to, action: filters.action, search: filters.search });
                    const response = await fetch(`{{ route("admin.logs.data") }}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    if (data.success) { renderTable(data.data); renderPagination(data); if (data.total !== undefined && statTotal) statTotal.textContent = data.total.toLocaleString(); }
                    else showError();
                } catch (error) { console.error(error); showError(); }
            }

            function renderTable(logs) {
                if (!logs || logs.length === 0) { tableBody.innerHTML = `<tr class="empty-row"><td colspan="7"><div style="text-align:center;padding:40px;"><i class="fas fa-history" style="font-size:3rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No logs found') }}</p></div></td></tr>`; return; }
                tableBody.innerHTML = logs.map(log => {
                    const isSelected = selectedLogs.has(log.id);
                    const actionClass = log.action_color || 'secondary';
                    const actionIcon = log.action_icon || 'fa-history';
                    return `<tr data-log-id="${log.id}">
                            <td class="checkbox-col"><input type="checkbox" class="log-checkbox" value="${log.id}" ${isSelected ? 'checked' : ''}></td>
                            <td>${log.id}</td>
                            <td><span class="action-badge ${actionClass}"><i class="fas ${actionIcon}"></i> ${escapeHtml(log.action_display)}</span></td>
                            <td><span class="admin-name">${log.admin ? escapeHtml(log.admin.name) : 'System'}</span>${log.admin ? `<span class="admin-email">${escapeHtml(log.admin.email)}</span>` : ''}</td>
                            <td class="details-cell"><div class="details-preview" onclick="showDetailsModal(${JSON.stringify(log.details).replace(/"/g, '&quot;')}, '${escapeHtml(log.action_display)}')"><i class="fas fa-info-circle"></i> ${escapeHtml(log.details_short || '{{ __("View details") }}')}</div></td>
                            <td>${new Date(log.created_at).toLocaleString()}</td>
                            <td><button class="action-btn-icon" onclick="deleteLog(${log.id})"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>`;
                }).join('');
                attachCheckboxEvents();
            }

            function attachCheckboxEvents() {
                document.querySelectorAll('.log-checkbox').forEach(cb => {
                    cb.addEventListener('change', function () {
                        const logId = parseInt(this.value);
                        if (this.checked) selectedLogs.add(logId);
                        else selectedLogs.delete(logId);
                        updateBulkActionsBar();
                        if (selectAllCheckbox) selectAllCheckbox.checked = selectedLogs.size === document.querySelectorAll('.log-checkbox').length;
                    });
                });
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function () {
                        if (this.checked) { document.querySelectorAll('.log-checkbox').forEach(cb => { cb.checked = true; selectedLogs.add(parseInt(cb.value)); }); }
                        else { document.querySelectorAll('.log-checkbox').forEach(cb => { cb.checked = false; selectedLogs.clear(); }); }
                        updateBulkActionsBar();
                    });
                }
            }

            function updateBulkActionsBar() {
                const count = selectedLogs.size;
                if (bulkActionsBar) {
                    if (count > 0) { bulkActionsBar.style.display = 'flex'; if (selectedCountSpan) selectedCountSpan.textContent = count; }
                    else bulkActionsBar.style.display = 'none';
                }
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                if (total === 0) { tableInfo.innerHTML = '{{ __("No logs found") }}'; paginationInfo.innerHTML = ''; paginationControls.innerHTML = ''; return; }
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);
                tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total.toLocaleString()} {{ __('logs') }}`;
                paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;
                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;
                let startPage = Math.max(1, current - 2), endPage = Math.min(last, current + 2);
                for (let i = startPage; i <= endPage; i++) html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;
                paginationControls.innerHTML = html;
            }

            function goToPage(page) { currentPage = page; loadLogs(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
            function showError() { tableBody.innerHTML = `<tr class="empty-row"><td colspan="7"><div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:3rem;color:#ef4444;"></i><p>{{ __("Error loading logs") }}</p><button onclick="loadLogs()" style="margin-top:15px;background:#7c3aed;color:white;border:none;padding:8px 20px;border-radius:30px;cursor:pointer;">{{ __("Try Again") }}</button></div></td></tr>`; }
            function escapeHtml(str) { if (!str) return ''; return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); }

            window.showDetailsModal = function (details, actionName) {
                let detailsHtml = '';
                if (details && typeof details === 'object') {
                    detailsHtml = '<div class="log-details-list">';
                    for (const [key, value] of Object.entries(details)) {
                        if (value === null || value === '') continue;
                        const keyName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        let displayValue = value;
                        if (typeof value === 'object') displayValue = JSON.stringify(value, null, 2);
                        if (typeof value === 'number' && (key.includes('amount') || key.includes('fee') || key.includes('price'))) displayValue = '$' + value.toFixed(2);
                        detailsHtml += `<div class="detail-row"><span class="detail-key">${escapeHtml(keyName)}:</span><span class="detail-value">${escapeHtml(String(displayValue))}</span></div>`;
                    }
                    detailsHtml += '</div>';
                } else { detailsHtml = `<div class="log-details-list"><div class="detail-row"><span class="detail-value">${escapeHtml(String(details))}</span></div></div>`; }
                Swal.fire({ title: actionName, html: detailsHtml, icon: 'info', confirmButtonColor: '#7c3aed', confirmButtonText: '{{ __("Close") }}', width: '600px' });
            };

            window.deleteLog = (logId) => { document.getElementById('deleteLogId').value = logId; document.getElementById('deleteLogModal').classList.add('active'); };
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('confirmDeleteBtn'), logId = document.getElementById('deleteLogId').value;
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/logs/${logId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } });
                    const data = await res.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false }); document.getElementById('deleteLogModal').classList.remove('active'); loadLogs(); }
                    else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            document.getElementById('bulkDeleteBtn')?.addEventListener('click', async () => {
                if (selectedLogs.size === 0) return;
                const result = await Swal.fire({ title: '{{ __("Delete Selected Logs") }}', text: `{{ __("Are you sure you want to delete") }} ${selectedLogs.size} {{ __("log entries?") }}`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: '{{ __("Yes, delete") }}', cancelButtonText: '{{ __("Cancel") }}' });
                if (!result.isConfirmed) return;
                const btn = document.getElementById('bulkDeleteBtn');
                btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Deleting...") }}';
                try {
                    const res = await fetch(`/admin/logs/bulk/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({ log_ids: Array.from(selectedLogs) }) });
                    const data = await res.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false }); selectedLogs.clear(); updateBulkActionsBar(); if (selectAllCheckbox) selectAllCheckbox.checked = false; loadLogs(); }
                    else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash-alt"></i> {{ __("Delete Selected") }}'; }
            });

            document.getElementById('cancelBulkBtn')?.addEventListener('click', () => { selectedLogs.clear(); updateBulkActionsBar(); if (selectAllCheckbox) selectAllCheckbox.checked = false; });

            clearByDateBtn?.addEventListener('click', () => document.getElementById('clearByDateModal').classList.add('active'));
            document.getElementById('confirmClearBtn')?.addEventListener('click', async () => {
                const dateFrom = document.getElementById('clearDateFrom').value, dateTo = document.getElementById('clearDateTo').value;
                if (!dateFrom || !dateTo) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Please select both dates") }}' }); return; }
                const btn = document.getElementById('confirmClearBtn');
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/logs/clear-by-date`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({ date_from: dateFrom, date_to: dateTo }) });
                    const data = await res.json();
                    if (data.success) { Swal.fire({ icon: 'success', title: '{{ __("Cleared") }}', text: data.message, timer: 1500, showConfirmButton: false }); document.getElementById('clearByDateModal').classList.remove('active'); loadLogs(); }
                    else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            exportBtn?.addEventListener('click', async () => {
                exportBtn.disabled = true; const originalHtml = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Exporting...") }}';
                const params = new URLSearchParams({ date_from: filters.date_from, date_to: filters.date_to, action: filters.action, search: filters.search });
                window.open(`{{ route("admin.logs.export-csv") }}?${params}`, '_blank');
                setTimeout(() => { exportBtn.disabled = false; exportBtn.innerHTML = originalHtml; }, 2000);
            });

            document.querySelectorAll('.modal-close, .custom-modal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('active')));
            });
            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
            });

            applyBtn.addEventListener('click', updateFilters);
            resetBtn.addEventListener('click', resetFilters);

            loadLogs();
            window.goToPage = goToPage;
        </script>
    @endpush
@endsection