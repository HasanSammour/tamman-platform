{{-- resources/views/admin/content/show.blade.php --}}
@extends('layouts.app')

@section('title', $content->title . ' - ' . __('Tamman'))

@section('page-title', __('Content Details'))

@section('content')
    <div class="content-show-wrapper">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav animate-fade-in">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">
                <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <a href="{{ route('admin.content') }}" class="breadcrumb-link">
                <i class="fas fa-newspaper"></i> {{ __('Content Management') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <span class="breadcrumb-current">{{ $content->title }}</span>
        </div>

        <!-- Action Bar -->
        <div class="action-bar animate-slide-up">
            <a href="{{ route('admin.content') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            <div class="action-buttons">
                <a href="{{ route('admin.content.edit', $content->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> {{ __('Edit Content') }}
                </a>
                @if($content->is_published)
                    <button class="btn-unpublish" onclick="toggleStatus('unpublish')">
                        <i class="fas fa-eye-slash"></i> {{ __('Unpublish') }}
                    </button>
                @else
                    <button class="btn-publish" onclick="toggleStatus('publish')">
                        <i class="fas fa-eye"></i> {{ __('Publish') }}
                    </button>
                @endif
                <button class="btn-delete" onclick="deleteContent()">
                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                </button>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="content-card animate-slide-up" style="animation-delay: 0.1s">
            <!-- Header Section -->
            <div class="content-header">
                <div class="content-type-badge {{ $content->type }}">
                    @if($content->type == 'article')
                        <i class="fas fa-newspaper"></i> {{ __('Article') }}
                    @elseif($content->type == 'video')
                        <i class="fas fa-video"></i> {{ __('Video') }}
                    @elseif($content->type == 'tip')
                        <i class="fas fa-lightbulb"></i> {{ __('Tip') }}
                    @else
                        <i class="fas fa-book"></i> {{ __('Guide') }}
                    @endif
                </div>
                <div class="content-status-badge {{ $content->is_published ? 'published' : 'draft' }}">
                    @if($content->is_published)
                        <i class="fas fa-check-circle"></i> {{ __('Published') }}
                    @else
                        <i class="fas fa-clock"></i> {{ __('Draft') }}
                    @endif
                </div>
            </div>

            <!-- Title -->
            <h1 class="content-title">{{ $content->title }}</h1>

            <!-- Meta Info -->
            <div class="content-meta">
                <div class="meta-item">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ __('Created by') }}: <strong>{{ $content->creator?->name ?? __('System') }}</strong></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ __('Created') }}:
                        <strong>{{ $content->created_at->translatedFormat('l, F d, Y') . ' ' . __('at') . ' ' . $content->created_at->translatedFormat('h:i A') }}</strong>
                    </span>
                </div>
                @if($content->updated_at != $content->created_at)
                    <div class="meta-item">
                        <i class="fas fa-edit"></i>
                        <span>{{ __('Last updated') }}:
                            <strong>{{ $content->updated_at->translatedFormat('l, F d, Y') . ' ' . __('at') . ' ' . $content->updated_at->translatedFormat('h:i A') }}</strong>
                        </span>
                    </div>
                @endif
                @if($content->published_at)
                    <div class="meta-item">
                        <i class="fas fa-globe"></i>
                        <span>{{ __('Published on') }}:
                            <strong>{{ $content->published_at->translatedFormat('l, F d, Y') . ' ' . __('at') . ' ' . $content->published_at->translatedFormat('h:i A') }}</strong>
                        </span>
                    </div>
                @endif
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span>{{ __('Views') }}: <strong>{{ number_format($content->views ?? 0) }}</strong></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-id-card"></i>
                    <span>{{ __('Content ID') }}: <strong>#{{ $content->id }}</strong></span>
                </div>
            </div>

            <!-- Media Section (for video type) -->
            @if($content->type == 'video' && $content->media_url)
                <div class="media-section">
                    <h3><i class="fas fa-video"></i> {{ __('Video') }}</h3>
                    <div class="video-container">
                        @if($youtubeId)
                            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        @else
                            <div class="video-link">
                                <i class="fab fa-youtube"></i>
                                <a href="{{ $content->media_url }}" target="_blank">{{ $content->media_url }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Media URL (if video and has URL) -->
            @if($content->media_url && $content->type == 'video')
                <div class="info-section">
                    <h3><i class="fas fa-link"></i> {{ __('Media URL') }}</h3>
                    <div class="url-box">
                        <input type="text" id="mediaUrl" value="{{ $content->media_url }}" readonly>
                        <button onclick="copyToClipboard('mediaUrl')" class="btn-copy">
                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                        </button>
                        <a href="{{ $content->media_url }}" target="_blank" class="btn-visit">
                            <i class="fas fa-external-link-alt"></i> {{ __('Visit') }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- Body Content -->
            <div class="body-section">
                <h3><i class="fas fa-align-left"></i> {{ __('Content Body') }}</h3>
                <div class="body-content">
                    {!! $content->body !!}
                </div>
            </div>

            <!-- Preview for Tips (quick view) -->
            @if($content->type == 'tip')
                <div class="tip-preview">
                    <h3><i class="fas fa-lightbulb"></i> {{ __('Tip Preview') }}</h3>
                    <div class="tip-card">
                        <div class="tip-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="tip-text">
                            {{ strip_tags($content->body) }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Metadata Card -->
        <div class="metadata-card animate-slide-up" style="animation-delay: 0.2s">
            <h3><i class="fas fa-chart-line"></i> {{ __('Content Statistics') }}</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon purple"><i class="fas fa-eye"></i></div>
                    <div class="stat-data">
                        <h3>{{ number_format($content->views ?? 0) }}</h3>
                        <p>{{ __('Total Views') }}</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon green"><i class="fas fa-calendar-week"></i></div>
                    <div class="stat-data">
                        <h3>{{ $content->created_at->diffForHumans() }}</h3>
                        <p>{{ __('Age') }}</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon orange"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-data">
                        <h3>{{ number_format(strlen(strip_tags($content->body))) }}</h3>
                        <p>{{ __('Characters') }}</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
                    <div class="stat-data">
                        @php
                            // For Arabic text, use a different approach
                            $text = strip_tags($content->body);

                            // Method 1: Count words by splitting on spaces (works for Arabic)
                            $wordCount = count(preg_split('/\s+/u', trim($text)));

                            // Method 2: Fallback - count characters and estimate (more accurate for Arabic)
                            if ($wordCount == 0) {
                                $charCount = mb_strlen($text);
                                $wordCount = round($charCount / 5); // Average Arabic word is ~5 characters
                            }

                            $totalSeconds = round(($wordCount / 200) * 60);
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            $seconds = $totalSeconds % 60;

                            $timeString = '';
                            if ($hours > 0) {
                                $timeString .= $hours . ' ' . __('hours') . ' ';
                            }
                            if ($minutes > 0 || $hours > 0) {
                                $timeString .= $minutes . ' ' . __('minutes') . ' ';
                            }
                            if ($seconds > 0 || ($hours == 0 && $minutes == 0)) {
                                $timeString .= $seconds . ' ' . __('seconds');
                            }

                            // For very short content
                            if ($totalSeconds < 30) {
                                $timeString = __('Less than 30 seconds');
                            }
                        @endphp

                        <h3>{{ $timeString }}</h3>
                        <p>{{ __('Reading time') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3><i class="fas fa-trash-alt"></i> {{ __('Delete Content') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p>{{ __('Are you sure you want to delete this content?') }}</p>
                <p class="warning-text">{{ __('This action cannot be undone.') }}</p>
                <p><strong>{{ $content->title }}</strong></p>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button class="btn-confirm-delete" id="confirmDeleteBtn">
                    <span>{{ __('Delete Permanently') }}</span>
                    <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Status Toggle Confirmation Modal -->
    <div id="statusModal" class="custom-modal">
        <div class="custom-modal-content small">
            <div class="custom-modal-header">
                <h3><i class="fas fa-question-circle"></i> <span id="statusModalTitle">{{ __('Confirm Action') }}</span>
                </h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p id="statusModalMessage">{{ __('Are you sure you want to change the status of this content?') }}</p>
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
            .content-show-wrapper {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Breadcrumb */
            .breadcrumb-nav {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 25px;
                padding: 12px 0;
            }

            .breadcrumb-link {
                color: #6b7280;
                text-decoration: none;
                font-size: 0.8rem;
                transition: color 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .breadcrumb-link:hover {
                color: #7c3aed;
            }

            .breadcrumb-sep {
                font-size: 0.7rem;
                color: #cbd5e1;
            }

            .breadcrumb-current {
                color: #7c3aed;
                font-size: 0.8rem;
                font-weight: 500;
            }

            /* Action Bar */
            .action-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: #f3f4f6;
                border-radius: 40px;
                color: #374151;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateX(-3px);
            }

            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            .action-buttons {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .btn-edit,
            .btn-publish,
            .btn-unpublish,
            .btn-delete {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                text-decoration: none;
            }

            .btn-edit {
                background: #dbeafe;
                color: #2563eb;
            }

            .btn-edit:hover {
                background: #bfdbfe;
                transform: translateY(-2px);
            }

            .btn-publish {
                background: #d1fae5;
                color: #059669;
            }

            .btn-publish:hover {
                background: #a7f3d0;
                transform: translateY(-2px);
            }

            .btn-unpublish {
                background: #fef3c7;
                color: #d97706;
            }

            .btn-unpublish:hover {
                background: #fde68a;
                transform: translateY(-2px);
            }

            .btn-delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .btn-delete:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            /* Content Card */
            .content-card {
                background: white;
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                margin-bottom: 25px;
            }

            .content-header {
                padding: 30px 35px 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .content-type-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .content-type-badge.article {
                background: #ede9fe;
                color: #7c3aed;
            }

            .content-type-badge.video {
                background: #fee2e2;
                color: #dc2626;
            }

            .content-type-badge.tip {
                background: #fef3c7;
                color: #d97706;
            }

            .content-type-badge.guide {
                background: #d1fae5;
                color: #059669;
            }

            .content-status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .content-status-badge.published {
                background: #d1fae5;
                color: #065f46;
            }

            .content-status-badge.draft {
                background: #fef3c7;
                color: #92400e;
            }

            .content-title {
                font-size: 2rem;
                font-weight: 700;
                color: #1f2937;
                margin: 20px 35px 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #f0f0f0;
                line-height: 1.3;
            }

            /* Content Meta */
            .content-meta {
                padding: 20px 35px;
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                border-bottom: 1px solid #f0f0f0;
                background: #fafafa;
            }

            .meta-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .meta-item i {
                color: #7c3aed;
                width: 18px;
            }

            .meta-item strong {
                color: #1f2937;
                font-weight: 600;
            }

            /* Media Section */
            .media-section {
                padding: 25px 35px;
                border-bottom: 1px solid #f0f0f0;
            }

            .media-section h3 {
                font-size: 1rem;
                margin-bottom: 15px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .media-section h3 i {
                color: #7c3aed;
            }

            .video-container {
                position: relative;
                padding-bottom: 56.25%;
                height: 0;
                overflow: hidden;
                border-radius: 16px;
                background: #1f2937;
            }

            .video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: none;
            }

            .video-link {
                background: #f9fafb;
                padding: 20px;
                border-radius: 16px;
                text-align: center;
            }

            .video-link i {
                font-size: 2rem;
                color: #ef4444;
                margin-bottom: 10px;
                display: block;
            }

            .video-link a {
                color: #2563eb;
                text-decoration: none;
                word-break: break-all;
            }

            /* Info Section */
            .info-section {
                padding: 25px 35px;
                border-bottom: 1px solid #f0f0f0;
            }

            .info-section h3 {
                font-size: 1rem;
                margin-bottom: 15px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .info-section h3 i {
                color: #7c3aed;
            }

            .url-box {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .url-box input {
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.8rem;
                background: #f9fafb;
                color: #1f2937;
            }

            .btn-copy,
            .btn-visit {
                padding: 12px 20px;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-copy {
                background: #7c3aed;
                color: white;
            }

            .btn-copy:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-visit {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-visit:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            /* Body Section */
            .body-section {
                padding: 25px 35px;
                border-bottom: 1px solid #f0f0f0;
            }

            .body-section h3 {
                font-size: 1rem;
                margin-bottom: 15px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .body-section h3 i {
                color: #7c3aed;
            }

            .body-content {
                line-height: 1.8;
                color: #374151;
            }

            .body-content img {
                max-width: 100%;
                height: auto;
                border-radius: 12px;
                margin: 15px 0;
            }

            .body-content h1,
            .body-content h2,
            .body-content h3,
            .body-content h4,
            .body-content h5,
            .body-content h6 {
                margin-top: 1.5em;
                margin-bottom: 0.5em;
                color: #1f2937;
            }

            .body-content h1 {
                font-size: 2rem;
            }

            .body-content h2 {
                font-size: 1.5rem;
            }

            .body-content h3 {
                font-size: 1.25rem;
            }

            .body-content p {
                margin-bottom: 1em;
            }

            .body-content ul,
            .body-content ol {
                margin: 1em 0;
                padding-left: 1.5em;
            }

            .body-content li {
                margin: 0.5em 0;
            }

            .body-content a {
                display: inline-block;
                padding: 8px 16px 8px 16px;
                background: #f9fafb;
                border-left: 4px solid #7c3aed;
                color: #1f2937;
                text-decoration: none;
                border-radius: 0 12px 12px 0;
                margin: 8px 0;
                transition: all 0.3s ease;
            }

            .body-content a:hover {
                background: #ede9fe;
                border-left-width: 6px;
                transform: translateX(4px);
                color: #9159f3;
            }

            .body-content blockquote {
                border-left: 4px solid #7c3aed;
                padding: 10px 20px;
                margin: 20px 0;
                background: #f5f3ff;
                border-radius: 12px;
                font-style: italic;
            }

            .body-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }

            .body-content th,
            .body-content td {
                border: 1px solid #e5e7eb;
                padding: 8px 12px;
                text-align: left;
            }

            .body-content th {
                background: #f9fafb;
                font-weight: 600;
            }

            /* Tip Preview */
            .tip-preview {
                padding: 25px 35px;
            }

            .tip-preview h3 {
                font-size: 1rem;
                margin-bottom: 15px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .tip-card {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 20px;
                padding: 25px;
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .tip-icon {
                width: 60px;
                height: 60px;
                background: #d97706;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .tip-icon i {
                font-size: 1.8rem;
                color: white;
            }

            .tip-text {
                flex: 1;
                font-size: 1rem;
                line-height: 1.6;
                color: #92400e;
            }

            /* Metadata Card */
            .metadata-card {
                background: white;
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .metadata-card h3 {
                padding: 25px 30px 0;
                font-size: 1rem;
                margin-bottom: 0;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .metadata-card h3 i {
                color: #7c3aed;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                padding: 20px 30px 30px;
            }

            .stat-item {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon i {
                font-size: 1.2rem;
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
                padding: 0;
                font-size: 1.2rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 5px 0 0;
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
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
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

            .animate-fade-in {
                animation: fadeIn 0.5s ease;
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease forwards;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .content-show-wrapper {
                    padding: 15px;
                }

                .content-header {
                    padding: 20px 20px 0;
                }

                .content-title {
                    font-size: 1.4rem;
                    margin: 15px 20px 0;
                }

                .content-meta {
                    padding: 15px 20px;
                    flex-direction: column;
                    gap: 10px;
                }

                .media-section,
                .info-section,
                .body-section,
                .tip-preview {
                    padding: 20px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .action-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .action-buttons {
                    justify-content: center;
                }

                .btn-back,
                .btn-edit,
                .btn-publish,
                .btn-unpublish,
                .btn-delete {
                    justify-content: center;
                }

                .url-box {
                    flex-direction: column;
                }

                .tip-card {
                    flex-direction: column;
                    text-align: center;
                }

                .metadata-card h3 {
                    padding: 20px 20px 0;
                }

                .stats-grid {
                    padding: 15px 20px 20px;
                }
            }

            /* RTL Support */
            body.rtl .breadcrumb-sep {
                transform: rotate(180deg);
            }

            body.rtl .body-content ul,
            body.rtl .body-content ol {
                padding-left: 0;
                padding-right: 1.5em;
            }

            body.rtl .body-content blockquote {
                border-left: none;
                border-right: 4px solid #7c3aed;
            }

            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let statusAction = null;

            function copyToClipboard(elementId) {
                const input = document.getElementById(elementId);
                input.select();
                document.execCommand('copy');

                Swal.fire({
                    icon: 'success',
                    title: '{{ __("Copied!") }}',
                    text: '{{ __("URL copied to clipboard") }}',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#fff',
                    color: '#1f2937'
                });
            }

            function toggleStatus(action) {
                statusAction = action;
                const title = action === 'publish' ? '{{ __("Publish Content") }}' : '{{ __("Unpublish Content") }}';
                const message = action === 'publish'
                    ? '{{ __("Are you sure you want to publish this content? It will be visible to all users.") }}'
                    : '{{ __("Are you sure you want to unpublish this content? It will be hidden from users.") }}';

                document.getElementById('statusModalTitle').innerHTML = title;
                document.getElementById('statusModalMessage').innerHTML = message;
                document.getElementById('statusModal').classList.add('active');
            }

            function deleteContent() {
                document.getElementById('deleteModal').classList.add('active');
            }

            // Confirm Delete
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('confirmDeleteBtn');
                btn.disabled = true;
                btn.querySelector('span:first-child').style.display = 'none';
                btn.querySelector('.btn-loader').style.display = 'inline-block';

                try {
                    const response = await fetch(`/admin/content/{{ $content->id }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Deleted!") }}',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.content") }}';
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    btn.disabled = false;
                    btn.querySelector('span:first-child').style.display = 'inline-block';
                    btn.querySelector('.btn-loader').style.display = 'none';
                    document.getElementById('deleteModal').classList.remove('active');
                }
            });

            // Confirm Status Change
            document.getElementById('confirmStatusBtn')?.addEventListener('click', async () => {
                const btn = document.getElementById('confirmStatusBtn');
                btn.disabled = true;
                btn.querySelector('span:first-child').style.display = 'none';
                btn.querySelector('.btn-loader').style.display = 'inline-block';

                const url = statusAction === 'publish'
                    ? '/admin/content/{{ $content->id }}/publish'
                    : '/admin/content/{{ $content->id }}/unpublish';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Success!") }}',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    btn.disabled = false;
                    btn.querySelector('span:first-child').style.display = 'inline-block';
                    btn.querySelector('.btn-loader').style.display = 'none';
                    document.getElementById('statusModal').classList.remove('active');
                }
            });

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.custom-modal').forEach(modal => {
                        modal.classList.remove('active');
                    });
                });
            });

            document.querySelectorAll('.custom-modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            });
        </script>
    @endpush
@endsection