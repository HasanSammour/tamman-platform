{{-- resources/views/specialist/index.blade.php --}}
@extends('layouts.guest')

@section('title', __('Find a Specialist - Tamman'))

@section('content')

    <!-- Hero Section -->
    <section class="specialists-hero">
        <div class="container">
            <div class="specialists-hero-content">
                <div class="hero-badge">
                    <i class="fas fa-user-md"></i>
                    <span>{{ __('Find Your Specialist') }}</span>
                </div>
                <h1>{{ __('Find Your') }} <span class="gradient-text">{{ __('Perfect Match') }}</span></h1>
                <p>{{ __('Browse our network of licensed mental health professionals and find the right specialist for you') }}
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="specialists-section">
        <div class="container">

            <!-- Top Filters Bar -->
            <div class="filters-bar">
                <div class="filters-row">
                    <!-- Search Input -->
                    <div class="filter-item search-item">
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="{{ __('Search by name or specialization...') }}"
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Specialization Filter -->
                    <div class="filter-item">
                        <select id="specializationFilter" class="filter-select">
                            <option value="">{{ __('All Specializations') }}</option>
                            @foreach($specializations as $spec)
                                <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                                    {{ __($spec) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Advanced Filters Toggle Button -->
                    <button class="advanced-toggle" id="advancedToggle">
                        <i class="fas fa-sliders-h"></i>
                        <span>{{ __('Advanced Filters') }}</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </button>
                </div>

                <!-- Advanced Filters Panel (Hidden by default) -->
                <div class="advanced-filters" id="advancedFilters">
                    <div class="advanced-filters-grid">
                        <!-- Language Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-language"></i> {{ __('Language') }}</label>
                            <select id="languageFilter" class="filter-select">
                                <option value="">{{ __('All Languages') }}</option>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang }}" {{ request('language') == $lang ? 'selected' : '' }}>
                                        {{ __($lang) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-dollar-sign"></i> {{ __('Price Range') }}</label>
                            <div class="price-range">
                                <input type="number" id="minPrice" class="price-input" placeholder="{{ __('Min') }}"
                                    value="{{ request('min_price') }}">
                                <span>-</span>
                                <input type="number" id="maxPrice" class="price-input" placeholder="{{ __('Max') }}"
                                    value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-star"></i> {{ __('Minimum Rating') }}</label>
                            <select id="ratingFilter" class="filter-select">
                                <option value="">{{ __('Any Rating') }}</option>
                                <option value="4.5" {{ request('rating') == '4.5' ? 'selected' : '' }}>4.5+ ★</option>
                                <option value="4.0" {{ request('rating') == '4.0' ? 'selected' : '' }}>4.0+ ★</option>
                                <option value="3.5" {{ request('rating') == '3.5' ? 'selected' : '' }}>3.5+ ★</option>
                                <option value="3.0" {{ request('rating') == '3.0' ? 'selected' : '' }}>3.0+ ★</option>
                            </select>
                        </div>
                    </div>

                    <div class="advanced-filters-actions">
                        <button id="applyFiltersBtn" class="btn-apply">
                            <i class="fas fa-check"></i> {{ __('Apply Filters') }}
                        </button>
                        <button id="clearFiltersBtn" class="btn-clear">
                            <i class="fas fa-redo"></i> {{ __('Clear All') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="results-header">
                <div class="results-count">
                    <span id="totalCount">{{ $specialists->total() }}</span> {{ __('specialists found') }}
                </div>
            </div>

            <!-- Specialists Grid - 3 columns -->
            <div id="specialistsGrid">
                @include('specialist.partials.specialists_grid', ['specialists' => $specialists])
            </div>

            <!-- Pagination -->
            <div id="paginationContainer">
                {{ $specialists->links() }}
            </div>

        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* Hero Section */
        .specialists-hero {
            background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
            padding: 60px 0 40px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(139, 92, 246, 0.1);
            padding: 8px 20px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            color: #7c3aed;
        }

        .specialists-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .specialists-hero p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        /* Main Section */
        .specialists-section {
            padding: 40px 0 80px;
            background: #f9fafb;
        }

        /* Filters Bar */
        .filters-bar {
            background: white;
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .filters-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-item {
            flex: 2;
            min-width: 250px;
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            background: white;
        }

        .filter-item {
            flex: 1;
            min-width: 180px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .advanced-toggle {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .advanced-toggle:hover {
            background: #e5e7eb;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
        }

        .advanced-toggle.active .toggle-icon {
            transform: rotate(180deg);
        }

        /* Advanced Filters Panel */
        .advanced-filters {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            animation: slideDown 0.3s ease;
        }

        .advanced-filters.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .advanced-filters-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .filter-group label i {
            color: #7c3aed;
            width: 16px;
            font-size: 0.8rem;
        }

        .price-range {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            background: #f9fafb;
        }

        .price-input:focus {
            outline: none;
            border-color: #7c3aed;
        }

        .advanced-filters-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-apply,
        .btn-clear {
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-apply {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        .btn-clear {
            background: #f3f4f6;
            color: #4b5563;
        }

        .btn-clear:hover {
            background: #e5e7eb;
        }

        /* Results Header */
        .results-header {
            margin-bottom: 25px;
        }

        .results-count {
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Specialists Grid - 3 columns */
        .specialists-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        /* Specialist Card */
        .specialist-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: fadeInUp 0.5s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .specialist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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

        .card-badge {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .badge-top {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .specialist-avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
        }

        .specialist-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-placeholder i {
            font-size: 3rem;
            color: #7c3aed;
        }

        .avatar-initials {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 600;
            color: white;
        }

        .specialist-name {
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .specialist-specialty {
            text-align: center;
            font-size: 0.8rem;
            color: #7c3aed;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .specialist-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .stars {
            color: #fbbf24;
            font-size: 0.75rem;
        }

        .rating-value {
            font-weight: 600;
            color: #374151;
            font-size: 0.8rem;
        }

        .reviews-count {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .specialist-info {
            background: #f9fafb;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.7rem;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            width: 18px;
            color: #7c3aed;
            font-size: 0.7rem;
        }

        .specialist-bio {
            font-size: 0.75rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 60px;
            flex: 1;
        }

        .card-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
        }

        .btn-view,
        .btn-book {
            width: 100%;
            text-align: center;
            padding: 10px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-view {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-view:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
            color: #374151;
        }

        .btn-book {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
            color: white;
        }

        /* Loading & No Results */
        .loading-spinner {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            grid-column: span 3;
        }

        .loading-spinner i {
            font-size: 2rem;
            color: #7c3aed;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            grid-column: span 3;
        }

        .no-results i {
            font-size: 3rem;
            color: #c4b5fd;
            margin-bottom: 15px;
        }

        .no-results h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .no-results p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: #7c3aed;
            color: white;
            border-radius: 40px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .btn-reset:hover {
            background: #6d28d9;
            transform: translateY(-2px);
            color: white;
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 5px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-item {
            display: inline-block;
        }

        .page-link {
            padding: 8px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.3s ease;
            background: white;
            cursor: pointer;
            display: inline-block;
            font-size: 0.8rem;
        }

        .page-item.active .page-link {
            background: #7c3aed;
            border-color: #7c3aed;
            color: white;
        }

        .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            color: #9ca3af;
        }

        .page-link:hover:not(.disabled) {
            background: #ede9fe;
            border-color: #c4b5fd;
            color: #5b21b6;
        }

        .page-item.active .page-link:hover {
            background: #7c3aed;
            color: white;
        }

        /* RTL Support */
        body.rtl .search-wrapper i {
            left: auto;
            right: 15px;
        }

        body.rtl .search-input {
            padding: 12px 45px 12px 15px;
        }

        body.rtl .card-badge {
            left: auto;
            right: 20px;
        }

        body.rtl .info-item {
            flex-direction: row-reverse;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .specialists-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .loading-spinner,
            .no-results {
                grid-column: span 2;
            }
        }

        @media (max-width: 900px) {
            .filters-row {
                flex-direction: column;
            }

            .filter-item,
            .search-item {
                width: 100%;
            }

            .advanced-toggle {
                width: 100%;
                justify-content: center;
            }

            .advanced-filters-grid {
                grid-template-columns: 1fr;
            }

            .advanced-filters-actions {
                flex-direction: column;
            }

            .btn-apply,
            .btn-clear {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .specialists-hero h1 {
                font-size: 1.8rem;
            }

            .specialists-grid {
                grid-template-columns: 1fr;
            }

            .loading-spinner,
            .no-results {
                grid-column: span 1;
            }

            .price-range {
                flex-direction: column;
            }

            .price-range span {
                display: none;
            }

            .price-input {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // DOM Elements
            const searchInput = document.getElementById('searchInput');
            const specializationFilter = document.getElementById('specializationFilter');
            const languageFilter = document.getElementById('languageFilter');
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            const ratingFilter = document.getElementById('ratingFilter');
            const applyBtn = document.getElementById('applyFiltersBtn');
            const advancedToggle = document.getElementById('advancedToggle');
            const advancedFilters = document.getElementById('advancedFilters');
            const specialistsGrid = document.getElementById('specialistsGrid');
            const paginationContainer = document.getElementById('paginationContainer');
            const totalCountSpan = document.getElementById('totalCount');

            let isLoading = false;

            // Advanced Filters Toggle
            if (advancedToggle) {
                advancedToggle.addEventListener('click', function () {
                    advancedFilters.classList.toggle('show');
                    this.classList.toggle('active');
                });
            }

            // Validate price range
            function validatePriceRange() {
                const min = parseFloat(minPrice.value);
                const max = parseFloat(maxPrice.value);

                if (minPrice.value && maxPrice.value && min > max) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Invalid Price Range") }}',
                        text: '{{ __("Minimum price cannot be greater than maximum price") }}',
                        confirmButtonColor: '#7c3aed',
                        confirmButtonText: '{{ __("OK") }}'
                    });
                    return false;
                }
                return true;
            }

            // Get all filter values
            function getFilterParams() {
                const params = new URLSearchParams();

                if (searchInput.value) params.append('search', searchInput.value);
                if (specializationFilter.value) params.append('specialization', specializationFilter.value);
                if (languageFilter.value) params.append('language', languageFilter.value);
                if (minPrice.value) params.append('min_price', minPrice.value);
                if (maxPrice.value) params.append('max_price', maxPrice.value);
                if (ratingFilter.value) params.append('rating', ratingFilter.value);

                return params;
            }

            // Fetch specialists
            function fetchSpecialists() {
                if (isLoading) return;

                if (!validatePriceRange()) {
                    return;
                }

                isLoading = true;

                specialistsGrid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> {{ __("Loading...") }}</div>';

                const params = getFilterParams();
                params.append('ajax', '1');

                fetch('{{ route("specialists.index") }}?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        specialistsGrid.innerHTML = data.html;
                        paginationContainer.innerHTML = data.pagination;
                        if (totalCountSpan) totalCountSpan.textContent = data.total;
                        attachPaginationEvents();
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        specialistsGrid.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-triangle"></i><h3>{{ __("Error loading specialists") }}</h3></div>';
                        isLoading = false;
                    });
            }

            // Attach pagination events
            function attachPaginationEvents() {
                document.querySelectorAll('.pagination .page-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url && !this.parentElement.classList.contains('disabled')) {
                            fetchPage(url);
                        }
                    });
                });
            }

            // Fetch specific page
            function fetchPage(url) {
                if (isLoading) return;
                isLoading = true;

                specialistsGrid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> {{ __("Loading...") }}</div>';

                fetch(url + '&ajax=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        specialistsGrid.innerHTML = data.html;
                        paginationContainer.innerHTML = data.pagination;
                        if (totalCountSpan) totalCountSpan.textContent = data.total;
                        attachPaginationEvents();
                        isLoading = false;
                        window.scrollTo({ top: 400, behavior: 'smooth' });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        isLoading = false;
                    });
            }

            // Search on Enter key
            if (searchInput) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        fetchSpecialists();
                    }
                });
            }

            // Apply filters button
            if (applyBtn) {
                applyBtn.addEventListener('click', function () {
                    fetchSpecialists();
                });
            }

            // Use event delegation for both reset buttons (Main Clear All button AND Reset Filters button from no-results section)
            document.addEventListener('click', function (e) {
                // Handle the Reset Filters button (appears in no-results section)
                const resetBtn = e.target.closest('#resetFiltersBtn');
                if (resetBtn) {
                    e.preventDefault();

                    // Clear all filter inputs
                    if (searchInput) searchInput.value = '';
                    if (specializationFilter) specializationFilter.value = '';
                    if (languageFilter) languageFilter.value = '';
                    if (minPrice) minPrice.value = '';
                    if (maxPrice) maxPrice.value = '';
                    if (ratingFilter) ratingFilter.value = '';

                    // Fetch specialists with cleared filters
                    fetchSpecialists();
                }

                // Handle the main Clear All button (from filters bar)
                const clearBtnClicked = e.target.closest('#clearFiltersBtn');
                if (clearBtnClicked) {
                    e.preventDefault();

                    // Clear all filter inputs
                    if (searchInput) searchInput.value = '';
                    if (specializationFilter) specializationFilter.value = '';
                    if (languageFilter) languageFilter.value = '';
                    if (minPrice) minPrice.value = '';
                    if (maxPrice) maxPrice.value = '';
                    if (ratingFilter) ratingFilter.value = '';

                    // Fetch specialists with cleared filters
                    fetchSpecialists();
                }
            });

            // Auto-submit on select changes
            const autoFilters = [specializationFilter, languageFilter, ratingFilter];
            autoFilters.forEach(filter => {
                if (filter) {
                    filter.addEventListener('change', function () {
                        fetchSpecialists();
                    });
                }
            });

            // Initial pagination events
            attachPaginationEvents();
        });
    </script>
@endpush