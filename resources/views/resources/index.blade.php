{{-- resources/views/resources/index.blade.php --}}
@extends('layouts.guest')

@section('title', __('Mental Health Resources') . ' - ' . __('Tamman'))

@section('content')

<!-- Hero Section -->
<section class="resources-hero">
    <div class="container">
        <div class="resources-hero-content">
            <div class="hero-badge">
                <i class="fas fa-newspaper"></i>
                <span>{{ __('Learn & Grow') }}</span>
            </div>
            <h1>{{ __('Mental Health') }} <span class="gradient-text">{{ __('Resources') }}</span></h1>
            <p>{{ __('Access free educational content to better understand and manage your mental health') }}</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="resources-section">
    <div class="container">
        
        <!-- Search and Filter Bar -->
        <div class="resources-filters">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="{{ __('Search resources...') }}" value="{{ request('search') }}">
            </div>
            
            <div class="filter-tabs">
                <button class="filter-tab  {{ !request('type') || request('type') == 'all' ? 'active' : '' }}" data-type="all">{{ __('All') }} <span class="count">({{ $counts['all'] }})</span></button>
                <button class="filter-tab {{ request('type') == 'article' ? 'active' : '' }}" data-type="article"><i class="fas fa-newspaper"></i> {{ __('Articles') }} <span class="count">({{ $counts['article'] }})</span></button>
                <button class="filter-tab {{ request('type') == 'video' ? 'active' : '' }}" data-type="video"><i class="fas fa-video"></i> {{ __('Videos') }} <span class="count">({{ $counts['video'] }})</span></button>
                <button class="filter-tab {{ request('type') == 'tip' ? 'active' : '' }}" data-type="tip"><i class="fas fa-lightbulb"></i> {{ __('Tips') }} <span class="count">({{ $counts['tip'] }})</span></button>
                <button class="filter-tab {{ request('type') == 'guide' ? 'active' : '' }}" data-type="guide"><i class="fas fa-book"></i> {{ __('Guides') }} <span class="count">({{ $counts['guide'] }})</span></button>
            </div>
        </div>
        
        <!-- Resources Grid -->
        <div id="resourcesGrid">
            @include('resources.partials.resources_grid', ['resources' => $resources])
        </div>
        
        <!-- Pagination -->
        <div id="paginationContainer">
            {{ $resources->links() }}
        </div>
        
    </div>
</section>


@push('styles')
<style>
    .resources-hero {
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
    
    .resources-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .resources-hero p {
        color: #6b7280;
        font-size: 1.1rem;
    }
    
    .resources-section {
        padding: 40px 0 80px;
        background: #f9fafb;
    }
    
    .resources-filters {
        background: white;
        border-radius: 20px;
        padding: 20px 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .search-wrapper {
        position: relative;
        margin-bottom: 20px;
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
    
    .filter-tabs {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .filter-tab {
        padding: 8px 20px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 40px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .filter-tab i {
        font-size: 0.8rem;
    }
    
    .filter-tab .count {
        font-size: 0.7rem;
        color: #9ca3af;
    }
    
    .filter-tab:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }
    
    .filter-tab.active {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-color: #7c3aed;
        color: white;
    }
    
    .filter-tab.active .count {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
    
    .resource-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .resource-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .resource-image {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .resource-image i {
        font-size: 3rem;
        color: white;
        opacity: 0.8;
    }
    
    .resource-type-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        color: white;
    }
    
    .resource-type-badge.article { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .resource-type-badge.video { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .resource-type-badge.tip { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .resource-type-badge.guide { background: linear-gradient(135deg, #10b981, #059669); }
    
    .resource-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .resource-title {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }
    
    .resource-title a {
        color: #1f2937;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .resource-title a:hover {
        color: #7c3aed;
    }
    
    .resource-excerpt {
        font-size: 0.8rem;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }
    
    .resource-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #e5e7eb;
    }
    
    .resource-date {
        font-size: 0.7rem;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .resource-read-more {
        font-size: 0.75rem;
        color: #7c3aed;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .resource-read-more:hover {
        transform: translateX(5px);
    }
    
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
    }
    
    .page-link:hover:not(.disabled) {
        background: #ede9fe;
        border-color: #c4b5fd;
    }
    
    body.rtl .search-wrapper i {
        left: auto;
        right: 15px;
    }
    
    body.rtl .search-input {
        padding: 12px 45px 12px 15px;
    }
    
    body.rtl .resource-type-badge {
        right: auto;
        left: 15px;
    }
    
    body.rtl .resource-read-more:hover {
        transform: translateX(-5px);
    }
    
    @media (max-width: 992px) {
        .resources-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .loading-spinner, .no-results {
            grid-column: span 2;
        }
    }
    
    @media (max-width: 768px) {
        .resources-hero h1 {
            font-size: 1.8rem;
        }
        .resources-grid {
            grid-template-columns: 1fr;
        }
        .loading-spinner, .no-results {
            grid-column: span 1;
        }
        .filter-tabs {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const searchInput = document.getElementById('searchInput');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const resourcesGrid = document.getElementById('resourcesGrid');
    const paginationContainer = document.getElementById('paginationContainer');
    
    // State variables
    let currentType = 'all';
    let currentSearch = '';
    let isLoading = false;
    let searchTimeout = null;
    
    // Function to fetch resources
    function fetchResources(pageUrl = null) {
        if (isLoading) return;
        isLoading = true;
        
        resourcesGrid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> {{ __("Loading...") }}</div>';
        
        let url = pageUrl || '{{ route("resources.index") }}';
        
        // Build params
        const params = new URLSearchParams();
        if (currentSearch) params.append('search', currentSearch);
        if (currentType !== 'all') params.append('type', currentType);
        params.append('ajax', '1');
        
        // Add params to URL
        const separator = url.includes('?') ? '&' : '?';
        url = url + separator + params.toString();
        
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            resourcesGrid.innerHTML = data.html;
            paginationContainer.innerHTML = data.pagination;
            attachPaginationEvents();
            isLoading = false;
        })
        .catch(error => {
            console.error('Error:', error);
            resourcesGrid.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-triangle"></i><h3>{{ __("Error loading resources") }}</h3></div>';
            isLoading = false;
        });
    }
    
    // Attach pagination click events
    function attachPaginationEvents() {
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url && !this.parentElement.classList.contains('disabled')) {
                    fetchResources(url);
                }
            });
        });
    }
    
    // Search input with debounce
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentSearch = searchInput.value;
                fetchResources();
            }, 500);
        });
    }
    
    // Filter tabs click
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentType = this.dataset.type;
            fetchResources();
        });
    });
    
    // Initial pagination events
    attachPaginationEvents();
});
</script>
@endpush
@endsection