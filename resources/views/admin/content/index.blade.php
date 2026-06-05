{{-- resources/views/admin/content/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Content Management') . ' - ' . __('Tamman'))

@section('page-title', __('Content Management'))

@section('content')
    <div class="content-manager-wrapper">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple"><i class="fas fa-newspaper"></i></div>
                <div class="stat-data">
                    <h3 id="statTotal">{{ $stats['total'] }}</h3>
                    <p>{{ __('Total Content') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-data">
                    <h3 id="statPublished">{{ $stats['published'] }}</h3>
                    <p>{{ __('Published') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon orange"><i class="fas fa-pen-fancy"></i></div>
                <div class="stat-data">
                    <h3 id="statDraft">{{ $stats['draft'] }}</h3>
                    <p>{{ __('Draft') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon blue"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-data">
                    <h3>{{ $stats['articles'] + $stats['videos'] + $stats['tips'] + $stats['guides'] }}</h3>
                    <p>{{ __('Total Items') }}</p>
                    <small style="font-size: 0.5rem;">
                        {{ __('Articles') }}: {{ $stats['articles'] }} | 
                        {{ __('Videos') }}: {{ $stats['videos'] }} |
                        {{ __('Tips') }}: {{ $stats['tips'] }} | 
                        {{ __('Guides') }}:{{ $stats['guides'] }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Mobile Stats Cards -->
        <div class="mobile-stats-grid">
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon purple"><i class="fas fa-newspaper"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ $stats['total'] }}</span>
                    <span class="mobile-stat-label">{{ __('Total') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ $stats['published'] }}</span>
                    <span class="mobile-stat-label">{{ __('Published') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon orange"><i class="fas fa-pen-fancy"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ $stats['draft'] }}</span>
                    <span class="mobile-stat-label">{{ __('Draft') }}</span>
                </div>
            </div>
            <div class="mobile-stat-item">
                <div class="mobile-stat-icon blue"><i class="fas fa-chart-pie"></i></div>
                <div class="mobile-stat-data">
                    <span class="mobile-stat-value">{{ $stats['articles'] + $stats['videos'] + $stats['tips'] + $stats['guides'] }}</span>
                    <span class="mobile-stat-label">{{ __('Items') }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="action-bar animate-slide-up">
            <a href="{{ route('admin.content.create') }}" class="btn-create">
                <i class="fas fa-plus-circle"></i> {{ __('Create New Content') }}
            </a>
            <div class="action-buttons">
                <button id="refreshBtn" class="btn-refresh" title="{{ __('Refresh') }}">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container animate-slide-up" style="animation-delay: 0.1s">
            <div class="filters-row">
                <div class="filter-item search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="{{ __('Search by title or content...') }}">
                </div>
                <select id="typeFilter" class="filter-select">
                    <option value="all">{{ __('All Types') }}</option>
                    <option value="article">{{ __('Articles') }}</option>
                    <option value="video">{{ __('Videos') }}</option>
                    <option value="tip">{{ __('Tips') }}</option>
                    <option value="guide">{{ __('Guides') }}</option>
                </select>
                <select id="statusFilter" class="filter-select">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="published">{{ __('Published') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                </select>
                <button id="resetFiltersBtn" class="btn-reset">
                    <i class="fas fa-undo-alt"></i> {{ __('Reset') }}
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card animate-slide-up" style="animation-delay: 0.2s">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> {{ __('Content Library') }}</h4>
                <div id="tableInfo" class="table-info">{{ __('Loading...') }}</div>
            </div>

            <!-- Desktop Table View -->
            <div class="desktop-table-container">
                <div class="table-scroll-wrapper">
                    <div class="table-responsive-inner">
                        <table class="content-table">
                            <thead>
                                <tr>
                                    <th data-sort="id" class="sortable">{{ __('ID') }} <i class="fas fa-sort"></i></th>
                                    <th data-sort="title" class="sortable">{{ __('Title') }} <i class="fas fa-sort"></i></th>
                                    <th data-sort="type" class="sortable">{{ __('Type') }} <i class="fas fa-sort"></i></th>
                                    <th data-sort="is_published">{{ __('Status') }}</th>
                                    <th data-sort="views">{{ __('Views') }}</th>
                                    <th data-sort="created_by">{{ __('Created By') }}</th>
                                    <th data-sort="created_at" class="sortable">{{ __('Created') }} <i class="fas fa-sort"></i></th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="contentTableBody">
                                <tr class="loading-row">
                                    <td colspan="8">
                                        <div class="loading-spinner"></div>
                                        <p>{{ __('Loading content...') }}</p>
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

            <!-- Mobile Cards View -->
            <div class="mobile-cards-container" id="mobileCardsContainer"></div>
            <div class="mobile-pagination" id="mobilePagination"></div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-trash-alt"></i> {{ __('Delete Content') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p>{{ __('Are you sure you want to delete this content?') }}</p>
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

    <!-- Publish/Unpublish Confirmation Modal -->
    <div id="statusModal" class="custom-modal">
        <div class="custom-modal-content small">
            <div class="custom-modal-header">
                <h3><i class="fas fa-question-circle"></i> <span id="statusModalTitle">{{ __('Confirm Action') }}</span>
                </h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p id="statusModalMessage">{{ __('Are you sure you want to change the status of this content?') }}</p>
                <input type="hidden" id="statusContentId">
                <input type="hidden" id="statusAction">
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button class="btn-confirm-status" id="confirmStatusBtn">
                    <span>{{ __('Confirm') }}</span>
                    <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .content-manager-wrapper {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 20px;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            /* Stats Grid - Desktop */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            /* Mobile Stats Grid */
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
                transition: all 0.3s ease;
                min-width: 0; /* Prevents overflow */
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
            .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }

            .stat-data {
                flex: 1;
                min-width: 0; /* Prevents text overflow */
            }

            .stat-data h3 {
                font-size: 1.6rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
                line-height: 1.2;
            }
            
            .stat-data p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 0;
                line-height: 1.3;
            }
            
            .stat-data small {
                font-size: 0.65rem;
                color: #9ca3af;
                display: block;
                margin-top: 5px;
                line-height: 1.2;
            }

            /* Action Bar */
            .action-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .btn-create {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .btn-create:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
                color: white;
            }

            .btn-refresh {
                background: #f3f4f6;
                border: none;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #6b7280;
            }

            .btn-refresh:hover {
                background: #e5e7eb;
                transform: rotate(180deg);
            }

            /* Filters */
            .filters-container {
                background: white;
                border-radius: 16px;
                padding: 15px 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .filters-row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 12px;
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
            }

            .search-field input {
                width: 100%;
                padding: 10px 12px 10px 38px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                transition: all 0.3s ease;
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
                transition: all 0.3s ease;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 10px center;
            }

            .filter-select:focus {
                outline: none;
                border-color: #7c3aed;
            }

            .btn-reset {
                padding: 10px 18px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                background: #f3f4f6;
                color: #374151;
                transition: all 0.3s ease;
            }

            .btn-reset:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Desktop Table */
            .desktop-table-container {
                display: block;
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
                min-width: 900px;
            }

            .content-table {
                width: 100%;
                border-collapse: collapse;
            }

            .content-table th,
            .content-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
            }

            .content-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .content-table td {
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
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .badge-success {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-warning {
                background: #fef3c7;
                color: #d97706;
            }

            .badge-primary {
                background: #ede9fe;
                color: #7c3aed;
            }

            .badge-danger {
                background: #fee2e2;
                color: #dc2626;
            }

            .badge-info {
                background: #dbeafe;
                color: #2563eb;
            }

            /* Content Preview */
            .content-preview {
                max-width: 250px;
                white-space: normal;
                word-break: break-word;
            }

            .content-title {
                font-weight: 500;
                color: #1f2937;
                margin-bottom: 4px;
            }

            .content-excerpt {
                font-size: 0.7rem;
                color: #9ca3af;
                line-height: 1.4;
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
                text-decoration: none;
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

            .btn-publish {
                background: #d1fae5;
                color: #059669;
            }

            .btn-publish:hover {
                background: #a7f3d0;
            }

            .btn-unpublish {
                background: #fef3c7;
                color: #d97706;
            }

            .btn-unpublish:hover {
                background: #fde68a;
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

            /* Mobile Cards Container */
            .mobile-cards-container {
                display: none;
            }

            .mobile-pagination {
                display: none;
            }

            /* Loading */
            .loading-row td {
                text-align: center;
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

            /* Empty State */
            .empty-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            .empty-icon {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-text {
                color: #6b7280;
                margin-bottom: 15px;
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

            .custom-modal-content.small {
                max-width: 400px;
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

            .btn-confirm-status {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-loader {
                display: none;
            }

            /* Animations */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease forwards;
            }

            /* ============================================ */
            /* RESPONSIVE BREAKPOINTS */
            /* ============================================ */

            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                }
            }

            @media (max-width: 768px) {
                .content-manager-wrapper {
                    padding: 12px;
                }

                /* Hide desktop stats, show mobile stats */
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
                .mobile-stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }

                .mobile-stat-data {
                    flex: 1;
                }

                .mobile-stat-value {
                    display: block;
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                .mobile-stat-label {
                    font-size: 0.6rem;
                    color: #6b7280;
                }

                .action-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .btn-create {
                    justify-content: center;
                }

                .btn-refresh {
                    align-self: flex-end;
                }

                .filters-row {
                    flex-direction: column;
                    align-items: stretch;
                }

                .search-field {
                    width: 100%;
                }

                .filter-select {
                    width: 100%;
                }

                .btn-reset {
                    width: 100%;
                }

                .table-header {
                    padding: 12px 16px;
                }

                /* Hide desktop table on mobile */
                .desktop-table-container {
                    display: none;
                }

                /* Show mobile cards */
                .mobile-cards-container {
                    display: block;
                    padding: 12px;
                }

                .mobile-pagination {
                    display: flex;
                    justify-content: center;
                    margin-top: 15px;
                    padding: 12px;
                }

                /* Mobile Card Styles */
                .mobile-content-card {
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

                .mobile-card-title {
                    font-weight: 700;
                    font-size: 0.9rem;
                    color: #1f2937;
                    margin-bottom: 5px;
                    word-break: break-word;
                }

                .mobile-card-excerpt {
                    font-size: 0.7rem;
                    color: #9ca3af;
                    line-height: 1.4;
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
                    border: none;
                    cursor: pointer;
                }

                .mobile-action-view {
                    background: #ede9fe;
                    color: #7c3aed;
                }

                .mobile-action-edit {
                    background: #dbeafe;
                    color: #2563eb;
                }

                .mobile-action-publish {
                    background: #d1fae5;
                    color: #059669;
                }

                .mobile-action-unpublish {
                    background: #fef3c7;
                    color: #d97706;
                }

                .mobile-action-delete {
                    background: #fee2e2;
                    color: #dc2626;
                }

                .mobile-pagination .pagination-controls {
                    justify-content: center;
                }

                .mobile-pagination .page-btn {
                    min-width: 34px;
                    height: 34px;
                    font-size: 0.7rem;
                }

                .table-footer {
                    display: none;
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
                .mobile-detail-label {
                    min-width: 70px;
                }
                
                .mobile-card-title {
                    font-size: 0.85rem;
                }
            }

            /* ============================================ */
            /* RTL SUPPORT */
            /* ============================================ */
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

            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .table-header h4 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .content-table th,
            body.rtl .content-table td {
                text-align: right;
            }

            body.rtl .action-buttons {
                flex-direction: row-reverse;
            }

            body.rtl .mobile-detail-label {
                text-align: right;
            }

            body.rtl .custom-modal-footer {
                justify-content: flex-start;
            }

            body.rtl .stat-card {
                flex-direction: row-reverse;
            }

            body.rtl .mobile-stat-item {
                flex-direction: row-reverse;
            }

            /* RTL for badge icons */
            body.rtl .badge i,
            body.rtl .action-btn i,
            body.rtl .mobile-action-btn i {
                margin-left: 4px;
                margin-right: 0;
            }

            /* RTL for pagination icons */
            body.rtl .page-btn i.fa-angle-double-left {
                transform: rotate(180deg);
                display: inline-block;
            }

            body.rtl .page-btn i.fa-angle-left {
                transform: rotate(180deg);
                display: inline-block;
            }

            body.rtl .page-btn i.fa-angle-right {
                transform: rotate(180deg);
                display: inline-block;
            }

            body.rtl .page-btn i.fa-angle-double-right {
                transform: rotate(180deg);
                display: inline-block;
            }

            /* RTL for sort icons */
            body.rtl .sortable i.fa-sort-up,
            body.rtl .sortable i.fa-sort-down {
                margin-right: 5px;
                margin-left: 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let search = '', type = 'all', status = 'all';

            const tableBody = document.getElementById('contentTableBody');
            const mobileCardsContainer = document.getElementById('mobileCardsContainer');
            const tableInfo = document.getElementById('tableInfo');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const statusFilter = document.getElementById('statusFilter');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const refreshBtn = document.getElementById('refreshBtn');
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
                    loadContent();
                });
            });

            // Filters
            searchInput.addEventListener('input', () => { search = searchInput.value; currentPage = 1; loadContent(); });
            typeFilter.addEventListener('change', () => { type = typeFilter.value; currentPage = 1; loadContent(); });
            statusFilter.addEventListener('change', () => { status = statusFilter.value; currentPage = 1; loadContent(); });
            resetBtn.addEventListener('click', () => {
                searchInput.value = ''; typeFilter.value = 'all'; statusFilter.value = 'all';
                search = ''; type = 'all'; status = 'all';
                currentPage = 1;
                loadContent();
            });
            refreshBtn.addEventListener('click', () => loadContent());

            async function loadContent() {
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="8"><div class="loading-spinner"></div><p>{{ __('Loading content...') }}</p>研</tr>`;
                mobileCardsContainer.innerHTML = `<div style="text-align:center;padding:40px;"><div class="loading-spinner"></div><p>{{ __('Loading content...') }}</p></div>`;
                
                try {
                    const url = `{{ route("admin.content.data") }}?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}&type=${type}&status=${status}`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    if (data.success) { 
                        renderDesktopTable(data.data); 
                        renderMobileCards(data.data);
                        renderPagination(data); 
                    } else showError();
                } catch (error) { showError(); }
            }

            function renderDesktopTable(contents) {
                if (!contents || !contents.length) {
                    tableBody.innerHTML = `<tr class="empty-row"><td colspan="8"><div class="empty-icon"><i class="fas fa-newspaper"></i></div><p class="empty-text">{{ __('No content found') }}</p><button class="btn-reset" onclick="resetAllFilters()">{{ __('Clear Filters') }}</button> </tr>`;
                    return;
                }

                tableBody.innerHTML = contents.map(content => {
                    const statusBadge = content.is_published
                        ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> {{ __("Published") }}</span>'
                        : '<span class="badge badge-warning"><i class="fas fa-clock"></i> {{ __("Draft") }}</span>';

                    return `<tr data-id="${content.id}">
                        <td>#${content.id}</td>
                        <td>
                            <div class="content-preview">
                                <div class="content-title">${escapeHtml(content.title)}</div>
                                <div class="content-excerpt">${escapeHtml(content.short_body)}</div>
                            </div>
                        </td>
                        <td>${content.type_badge}</td>
                        <td>${statusBadge}</td>
                        <td>${content.views || 0}</td>
                        <td>${escapeHtml(content.creator_name)}</td>
                        <td>${new Date(content.created_at).toLocaleDateString()}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="${baseUrl}/admin/content/${content.id}" class="action-btn btn-view"><i class="fas fa-eye"></i></a>
                                <a href="${baseUrl}/admin/content/${content.id}/edit" class="action-btn btn-edit"><i class="fas fa-edit"></i></a>
                                ${content.is_published
                                    ? `<button class="action-btn btn-unpublish" onclick="toggleStatus(${content.id}, 'unpublish')"><i class="fas fa-eye-slash"></i></button>`
                                    : `<button class="action-btn btn-publish" onclick="toggleStatus(${content.id}, 'publish')"><i class="fas fa-eye"></i></button>`}
                                <button class="action-btn btn-delete" onclick="deleteContent(${content.id})"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
            }

            function renderMobileCards(contents) {
                if (!contents || !contents.length) {
                    mobileCardsContainer.innerHTML = `<div style="text-align:center;padding:30px;"><i class="fas fa-newspaper" style="font-size:2rem;color:#c4b5fd;"></i><p style="margin-top:10px;">{{ __('No content found') }}</p><button class="btn-reset" onclick="resetAllFilters()">{{ __('Clear Filters') }}</button></div>`;
                    return;
                }

                mobileCardsContainer.innerHTML = contents.map(content => {
                    const statusBadge = content.is_published
                        ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> {{ __("Published") }}</span>'
                        : '<span class="badge badge-warning"><i class="fas fa-clock"></i> {{ __("Draft") }}</span>';

                    return `
                        <div class="mobile-content-card">
                            <div class="mobile-card-header">
                                <div>
                                    <div class="mobile-card-title">${escapeHtml(content.title)}</div>
                                    <div class="mobile-card-excerpt">${escapeHtml(content.short_body)}</div>
                                </div>
                                ${statusBadge}
                            </div>
                            <div class="mobile-card-details">
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('ID') }}:</span>
                                    <span class="mobile-detail-value">#${content.id}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Type') }}:</span>
                                    <span class="mobile-detail-value">${content.type_badge}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Views') }}:</span>
                                    <span class="mobile-detail-value">${content.views || 0}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Created By') }}:</span>
                                    <span class="mobile-detail-value">${escapeHtml(content.creator_name)}</span>
                                </div>
                                <div class="mobile-detail-row">
                                    <span class="mobile-detail-label">{{ __('Created') }}:</span>
                                    <span class="mobile-detail-value">${new Date(content.created_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                            <div class="mobile-card-footer">
                                <a href="${baseUrl}/admin/content/${content.id}" class="mobile-action-btn mobile-action-view"><i class="fas fa-eye"></i> {{ __('View') }}</a>
                                <a href="${baseUrl}/admin/content/${content.id}/edit" class="mobile-action-btn mobile-action-edit"><i class="fas fa-edit"></i> {{ __('Edit') }}</a>
                                ${content.is_published
                                    ? `<button class="mobile-action-btn mobile-action-unpublish" onclick="toggleStatus(${content.id}, 'unpublish')"><i class="fas fa-eye-slash"></i> {{ __('Unpublish') }}</button>`
                                    : `<button class="mobile-action-btn mobile-action-publish" onclick="toggleStatus(${content.id}, 'publish')"><i class="fas fa-eye"></i> {{ __('Publish') }}</button>`}
                                <button class="mobile-action-btn mobile-action-delete" onclick="deleteContent(${content.id})"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1, to = Math.min(current * perPage, total);
                tableInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('items') }}`;
                paginationInfo.innerHTML = `{{ __('Page') }} ${current} {{ __('of') }} ${last}`;
                
                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;
                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;
                
                paginationControls.innerHTML = html;
                
                // Mobile pagination
                const mobilePaginationDiv = document.getElementById('mobilePagination');
                if (mobilePaginationDiv) {
                    mobilePaginationDiv.innerHTML = `
                        <div class="pagination-info" style="text-align:center;margin-bottom:10px;font-size:0.7rem;">{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total}</div>
                        <div class="pagination-controls">${html}</div>
                    `;
                }
            }

            function goToPage(page) { currentPage = page; loadContent(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
            function resetAllFilters() { searchInput.value = ''; typeFilter.value = 'all'; statusFilter.value = 'all'; search = ''; type = 'all'; status = 'all'; currentPage = 1; loadContent(); }
            function showError() { 
                const errorHtml = `<div style="text-align:center;padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#ef4444;"></i><p style="margin-top:10px;">{{ __("Error loading content") }}</p><button class="btn-reset" onclick="loadContent()">{{ __("Retry") }}</button></div>`;
                tableBody.innerHTML = `<tr class="empty-row"><td colspan="8">${errorHtml}</tr>`;
                mobileCardsContainer.innerHTML = errorHtml;
            }
            function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); }

            // Delete Content
            window.deleteContent = (id) => {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteModal').classList.add('active');
            };

            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('deleteId').value;
                const btn = document.getElementById('confirmDeleteBtn');
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const res = await fetch(`/admin/content/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('deleteModal').classList.remove('active');
                        loadContent();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            // Toggle Publish Status
            window.toggleStatus = (id, action) => {
                document.getElementById('statusModalTitle').innerHTML = action === 'publish' ? '<i class="fas fa-eye"></i> {{ __("Publish Content") }}' : '<i class="fas fa-eye-slash"></i> {{ __("Unpublish Content") }}';
                document.getElementById('statusModalMessage').innerHTML = action === 'publish'
                    ? '{{ __("Are you sure you want to publish this content? It will be visible to all users.") }}'
                    : '{{ __("Are you sure you want to unpublish this content? It will be hidden from users.") }}';
                document.getElementById('statusContentId').value = id;
                document.getElementById('statusAction').value = action;
                document.getElementById('statusModal').classList.add('active');
            };

            document.getElementById('confirmStatusBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('statusContentId').value;
                const action = document.getElementById('statusAction').value;
                const btn = document.getElementById('confirmStatusBtn');
                btn.disabled = true; btn.querySelector('span:first-child').style.display = 'none'; btn.querySelector('.btn-loader').style.display = 'inline-block';
                try {
                    const url = action === 'publish' ? `/admin/content/${id}/publish` : `/admin/content/${id}/unpublish`;
                    const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                        document.getElementById('statusModal').classList.remove('active');
                        loadContent();
                    } else Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                } catch (error) { Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' }); }
                finally { btn.disabled = false; btn.querySelector('span:first-child').style.display = 'inline-block'; btn.querySelector('.btn-loader').style.display = 'none'; }
            });

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .custom-modal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => document.querySelectorAll('.custom-modal').forEach(m => m.classList.remove('active')));
            });
            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
            });

            loadContent();
        </script>
    @endpush
@endsection
