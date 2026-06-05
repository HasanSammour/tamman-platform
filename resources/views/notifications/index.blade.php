{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Notifications') . ' - ' . __('Tamman'))

@section('page-title', __('Notifications'))

@section('content')
    <div class="notifications-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3 id="statTotal">0</h3>
                    <p>{{ __('Total Notifications') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3 id="statUnread">0</h3>
                    <p>{{ __('Unread') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3 id="statRead">0</h3>
                    <p>{{ __('Read') }}</p>
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="filters-bar">
            <div class="filters-row">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="{{ __('Search notifications...') }}">
                </div>
                <select id="statusFilter" class="filter-select">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="unread">{{ __('Unread') }}</option>
                    <option value="read">{{ __('Read') }}</option>
                </select>
                <select id="typeFilter" class="filter-select">
                    <option value="all">{{ __('All Types') }}</option>
                    <option value="session_reminder">{{ __('Session Reminders') }}</option>
                    <option value="points_earned">{{ __('Points Earned') }}</option>
                    <option value="payment">{{ __('Payments') }}</option>
                    <option value="application_status">{{ __('Applications') }}</option>
                    <option value="account_status">{{ __('Account Status') }}</option>
                    <option value="donation">{{ __('Donations') }}</option>
                    <option value="credit">{{ __('Credits') }}</option>
                </select>
                <button id="resetFiltersBtn" class="btn-reset"><i class="fas fa-undo-alt"></i> {{ __('Reset') }}</button>
            </div>

            <!-- Bulk Actions Bar -->
            <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none;">
                <div class="bulk-info">
                    <i class="fas fa-check-square"></i>
                    <span id="selectedCount">0</span> {{ __('selected') }}
                </div>
                <div class="bulk-buttons">
                    <button id="bulkMarkReadBtn" class="bulk-btn read">
                        <i class="fas fa-check-double"></i> {{ __('Mark as Read') }}
                    </button>
                    <button id="bulkMarkUnreadBtn" class="bulk-btn unread">
                        <i class="fas fa-undo-alt"></i> {{ __('Mark as Unread') }}
                    </button>
                    <button id="bulkDeleteBtn" class="bulk-btn delete">
                        <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                    </button>
                    <button id="bulkCancelBtn" class="bulk-btn cancel">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button id="markAllReadBtn" class="btn-mark-all">
                    <i class="fas fa-check-double"></i> {{ __('Mark All Read') }}
                </button>
                <button id="clearAllBtn" class="btn-clear-all">
                    <i class="fas fa-trash-alt"></i> {{ __('Clear All') }}
                </button>
            </div>
        </div>

        <!-- Notifications Cards Grid -->
        <div id="notificationsGrid" class="notifications-grid">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>{{ __('Loading notifications...') }}</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="pagination-container" style="display: none;">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    @push('styles')
        <style>
            .notifications-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Stats Cards */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
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
                transition: all 0.3s ease;
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
            }

            .stat-icon i {
                font-size: 1.3rem;
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

            .stat-info h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            /* Filters Bar */
            .filters-bar {
                background: white;
                border-radius: 20px;
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

            .search-wrapper {
                flex: 1;
                min-width: 200px;
                position: relative;
            }

            .search-wrapper i {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
            }

            .search-wrapper input {
                width: 100%;
                padding: 12px 16px 12px 45px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .search-wrapper input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .filter-select {
                padding: 12px 30px 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                background: white;
                cursor: pointer;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
            }

            .btn-reset {
                padding: 12px 24px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                background: #f3f4f6;
                color: #374151;
                transition: all 0.2s;
            }

            .btn-reset:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Bulk Actions Bar */
            .bulk-actions-bar {
                background: #ede9fe;
                border-radius: 16px;
                padding: 12px 20px;
                margin-bottom: 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .bulk-info {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.85rem;
                color: #6d28d9;
                font-weight: 500;
            }

            .bulk-buttons {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .bulk-btn {
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .bulk-btn.read {
                background: #10b981;
                color: white;
            }

            .bulk-btn.read:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            .bulk-btn.unread {
                background: #f59e0b;
                color: white;
            }

            .bulk-btn.unread:hover {
                background: #d97706;
                transform: translateY(-2px);
            }

            .bulk-btn.delete {
                background: #ef4444;
                color: white;
            }

            .bulk-btn.delete:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            .bulk-btn.cancel {
                background: #f3f4f6;
                color: #374151;
            }

            .bulk-btn.cancel:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 12px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-mark-all,
            .btn-clear-all {
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .btn-mark-all {
                background: #10b981;
                color: white;
            }

            .btn-mark-all:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            .btn-clear-all {
                background: #ef4444;
                color: white;
            }

            .btn-clear-all:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            /* Notifications Grid - Card Layout */
            .notifications-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
                gap: 20px;
                margin-bottom: 25px;
            }

            .notification-card {
                background: white;
                border-radius: 20px;
                padding: 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .notification-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            }

            .notification-card.unread {
                background: #f5f3ff;
                border-left: 4px solid #7c3aed;
            }

            .notification-card-header {
                padding: 16px 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
            }

            .notification-checkbox {
                flex-shrink: 0;
            }

            .notification-checkbox input {
                width: 20px;
                height: 20px;
                cursor: pointer;
                accent-color: #7c3aed;
            }

            .notification-icon-wrapper {
                flex-shrink: 0;
            }

            .notification-icon-circle {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-icon-circle i {
                font-size: 1.3rem;
                color: white;
            }

            .notification-content {
                flex: 1;
                min-width: 0;
            }

            .notification-title {
                font-weight: 700;
                font-size: 0.95rem;
                color: #1f2937;
                margin-bottom: 6px;
                word-break: break-word;
            }

            .notification-message {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 8px;
                line-height: 1.4;
                word-break: break-word;
            }

            .notification-time {
                font-size: 0.65rem;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .notification-card-footer {
                padding: 12px 20px;
                background: #f9fafb;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .card-btn-read,
            .card-btn-delete {
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .card-btn-read {
                background: #ede9fe;
                color: #7c3aed;
            }

            .card-btn-read:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            .card-btn-delete {
                background: #fee2e2;
                color: #ef4444;
            }

            .card-btn-delete:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            /* Loading State */
            .loading-container {
                text-align: center;
                padding: 60px 20px;
                grid-column: 1 / -1;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
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

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                grid-column: 1 / -1;
            }

            .empty-state i {
                font-size: 3.5rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-state h4 {
                font-size: 1.2rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .empty-state p {
                color: #6b7280;
                margin-bottom: 20px;
            }

            /* Pagination */
            .pagination-container {
                background: white;
                border-radius: 20px;
                padding: 16px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .pagination-info {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .pagination-controls {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .page-btn {
                min-width: 38px;
                height: 38px;
                padding: 0 10px;
                border: 1px solid #e5e7eb;
                background: white;
                border-radius: 10px;
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
            body.rtl .search-wrapper i {
                left: auto;
                right: 14px;
            }

            body.rtl .search-wrapper input {
                padding: 12px 45px 12px 16px;
            }

            body.rtl .filter-select {
                background-position: left 12px center;
                padding: 12px 16px 12px 30px;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .notifications-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .notifications-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .filters-row {
                    flex-direction: column;
                }

                .search-wrapper,
                .filter-select,
                .btn-reset {
                    width: 100%;
                }

                .bulk-actions-bar {
                    flex-direction: column;
                    text-align: center;
                }

                .bulk-buttons {
                    justify-content: center;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn-mark-all,
                .btn-clear-all {
                    width: 100%;
                    text-align: center;
                }

                .notification-card-header {
                    flex-wrap: wrap;
                }

                .notification-icon-wrapper {
                    order: -1;
                }

                .pagination-container {
                    flex-direction: column;
                    text-align: center;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentPage = 1;
            let perPage = 12;
            let search = '';
            let status = 'all';
            let type = 'all';
            let selectedIds = new Set();

            // Load notifications
            async function loadNotifications() {
                const grid = document.getElementById('notificationsGrid');
                grid.innerHTML = '<div class="loading-container"><div class="loading-spinner"></div><p>{{ __("Loading notifications...") }}</p></div>';

                try {
                    const url = `{{ route("notifications.get") }}?page=${currentPage}&per_page=${perPage}&search=${encodeURIComponent(search)}&status=${status}&type=${type}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();

                    if (data.success) {
                        renderNotifications(data);
                        updateStats(data.filter_counts);
                        renderPagination(data);
                        document.getElementById('paginationContainer').style.display = 'flex';
                    } else {
                        showError();
                    }
                } catch (error) {
                    showError();
                }
            }

            function renderNotifications(data) {
                const grid = document.getElementById('notificationsGrid');

                if (!data.data || data.data.length === 0) {
                    grid.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h4>{{ __("No notifications found") }}</h4>
                            <p>{{ __("You don't have any notifications yet.") }}</p>
                            <button class="btn-reset" onclick="resetAllFilters()">{{ __("Clear Filters") }}</button>
                        </div>
                    `;
                    return;
                }

                grid.innerHTML = data.data.map(notification => `
                    <div class="notification-card ${!notification.is_read ? 'unread' : ''}" data-id="${notification.id}">
                        <div class="notification-card-header">
                            <div class="notification-checkbox">
                                <input type="checkbox" class="notification-check" value="${notification.id}" onchange="toggleSelect(${notification.id})">
                            </div>
                            <div class="notification-icon-wrapper">
                                <div class="notification-icon-circle" style="background: ${notification.color}20;">
                                    <i class="fas ${notification.icon}" style="color: ${notification.color}"></i>
                                </div>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${escapeHtml(notification.title)}</div>
                                <div class="notification-message">${escapeHtml(notification.message)}</div>
                                <div class="notification-time">
                                    <i class="far fa-clock"></i>
                                    <span>${notification.time_ago}</span>
                                </div>
                            </div>
                        </div>
                        <div class="notification-card-footer">
                            ${!notification.is_read ?
                        `<button class="card-btn-read" onclick="markAsRead(${notification.id})">
                                    <i class="fas fa-check"></i> {{ __("Mark as Read") }}
                                </button>` : ''
                    }
                            <button class="card-btn-delete" onclick="deleteNotification(${notification.id})">
                                <i class="fas fa-trash-alt"></i> {{ __("Delete") }}
                            </button>
                        </div>
                    </div>
                `).join('');

                // Restore checkbox selections
                document.querySelectorAll('.notification-check').forEach(cb => {
                    if (selectedIds.has(parseInt(cb.value))) {
                        cb.checked = true;
                    }
                });
                updateBulkActionsBar();
            }

            function updateStats(counts) {
                document.getElementById('statTotal').textContent = counts.all;
                document.getElementById('statUnread').textContent = counts.unread;
                document.getElementById('statRead').textContent = counts.read;

                // Update header badge
                const badge = document.getElementById('notificationsCount');
                if (badge) {
                    badge.textContent = counts.unread;
                    badge.style.display = counts.unread > 0 ? 'inline-block' : 'none';
                }
            }

            function renderPagination(data) {
                const total = data.total;
                const current = data.current_page;
                const last = data.last_page;
                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                document.getElementById('paginationInfo').innerHTML = `{{ __("Showing") }} ${from} - ${to} {{ __("of") }} ${total} {{ __("notifications") }}`;

                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;

                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                }

                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;

                document.getElementById('paginationControls').innerHTML = html;
            }

            function goToPage(page) {
                currentPage = page;
                loadNotifications();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function toggleSelect(id) {
                if (selectedIds.has(id)) {
                    selectedIds.delete(id);
                } else {
                    selectedIds.add(id);
                }
                updateBulkActionsBar();
            }

            function updateBulkActionsBar() {
                const bar = document.getElementById('bulkActionsBar');
                const countSpan = document.getElementById('selectedCount');

                if (selectedIds.size > 0) {
                    bar.style.display = 'flex';
                    countSpan.textContent = selectedIds.size;
                } else {
                    bar.style.display = 'none';
                }
            }

            function resetAllFilters() {
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('typeFilter').value = 'all';
                search = '';
                status = 'all';
                type = 'all';
                currentPage = 1;
                loadNotifications();
            }

            async function markAsRead(id) {
                try {
                    const response = await fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        loadNotifications();
                        updateHeaderBadge(data.unread_count);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }

            async function deleteNotification(id) {
                const result = await Swal.fire({
                    title: '{{ __("Delete Notification") }}',
                    text: '{{ __("Are you sure you want to delete this notification?") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, delete") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        selectedIds.delete(id);
                        loadNotifications();
                        updateHeaderBadge(data.unread_count);
                        Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                }
            }

            async function markAllAsRead() {
                const result = await Swal.fire({
                    title: '{{ __("Mark All as Read") }}',
                    text: '{{ __("Are you sure you want to mark all notifications as read?") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, mark all") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`{{ route("notifications.mark-all-read") }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        selectedIds.clear();
                        loadNotifications();
                        updateHeaderBadge(0);
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                }
            }

            async function clearAll() {
                const result = await Swal.fire({
                    title: '{{ __("Clear All Notifications") }}',
                    html: '{{ __("Are you sure you want to delete <strong>all</strong> notifications? This action cannot be undone.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, clear all") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`{{ route("notifications.destroy-all") }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        selectedIds.clear();
                        loadNotifications();
                        updateHeaderBadge(0);
                        Swal.fire({ icon: 'success', title: '{{ __("Cleared") }}', text: data.message, timer: 1500, showConfirmButton: false });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                }
            }

            async function bulkAction(action) {
                if (selectedIds.size === 0) return;

                let confirmText = '';
                let successText = '';

                if (action === 'mark_read') {
                    confirmText = '{{ __("Mark selected notifications as read?") }}';
                    successText = '{{ __("Selected notifications marked as read") }}';
                } else if (action === 'mark_unread') {
                    confirmText = '{{ __("Mark selected notifications as unread?") }}';
                    successText = '{{ __("Selected notifications marked as unread") }}';
                } else if (action === 'delete') {
                    confirmText = '{{ __("Delete selected notifications? This action cannot be undone.") }}';
                    successText = '{{ __("Selected notifications deleted") }}';
                }

                const result = await Swal.fire({
                    title: '{{ __("Confirm Action") }}',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, proceed") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                Swal.fire({ title: '{{ __("Processing...") }}', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                try {
                    const response = await fetch(`{{ route("notifications.bulk-action") }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            action: action,
                            ids: Array.from(selectedIds)
                        })
                    });
                    const data = await response.json();

                    if (data.success) {
                        selectedIds.clear();
                        loadNotifications();
                        updateHeaderBadge(data.unread_count);
                        Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: successText, timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                }
            }

            function updateHeaderBadge(count) {
                const badge = document.getElementById('notificationsCount');
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                }
            }

            function showError() {
                document.getElementById('notificationsGrid').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>{{ __("Error") }}</h4>
                        <p>{{ __("Failed to load notifications. Please try again.") }}</p>
                        <button class="btn-reset" onclick="loadNotifications()">{{ __("Retry") }}</button>
                    </div>
                `;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            // Event Listeners
            document.getElementById('searchInput')?.addEventListener('input', (e) => {
                search = e.target.value;
                currentPage = 1;
                loadNotifications();
            });

            document.getElementById('statusFilter')?.addEventListener('change', (e) => {
                status = e.target.value;
                currentPage = 1;
                loadNotifications();
            });

            document.getElementById('typeFilter')?.addEventListener('change', (e) => {
                type = e.target.value;
                currentPage = 1;
                loadNotifications();
            });

            document.getElementById('resetFiltersBtn')?.addEventListener('click', resetAllFilters);
            document.getElementById('markAllReadBtn')?.addEventListener('click', markAllAsRead);
            document.getElementById('clearAllBtn')?.addEventListener('click', clearAll);

            document.getElementById('bulkMarkReadBtn')?.addEventListener('click', () => bulkAction('mark_read'));
            document.getElementById('bulkMarkUnreadBtn')?.addEventListener('click', () => bulkAction('mark_unread'));
            document.getElementById('bulkDeleteBtn')?.addEventListener('click', () => bulkAction('delete'));
            document.getElementById('bulkCancelBtn')?.addEventListener('click', () => {
                selectedIds.clear();
                updateBulkActionsBar();
                document.querySelectorAll('.notification-check').forEach(cb => cb.checked = false);
            });

            // Initial load
            loadNotifications();
        </script>
    @endpush

@endsection