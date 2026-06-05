{{-- resources/views/resources/show.blade.php --}}
@extends('layouts.guest')

@section('title', $resource->title . ' - ' . __('Tamman'))

@section('content')

    <!-- Hero Section -->
    <section class="resource-hero">
        <div class="container">
            <div class="resource-hero-content">
                <div class="resource-type">
                    @php
                        $typeIcons = [
                            'article' => 'fa-newspaper',
                            'video' => 'fa-video',
                            'tip' => 'fa-lightbulb',
                            'guide' => 'fa-book'
                        ];
                        $typeColors = [
                            'article' => '#3b82f6',
                            'video' => '#ef4444',
                            'tip' => '#f59e0b',
                            'guide' => '#10b981'
                        ];
                        $typeIcon = $typeIcons[$resource->type] ?? 'fa-newspaper';
                        $typeColor = $typeColors[$resource->type] ?? '#7c3aed';
                    @endphp
                    <i class="fas {{ $typeIcon }}" style="color: {{ $typeColor }};"></i>
                    <span style="color: {{ $typeColor }};">{{ __(ucfirst($resource->type)) }}</span>
                </div>
                <h1>{{ $resource->title }}</h1>
                <div class="resource-meta">
                    <span><i class="fas fa-calendar-alt"></i>
                        {{ $resource->published_at ? $resource->published_at->translatedFormat('j F Y') : '' }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="resource-section">
        <div class="container">
            <div class="resource-layout">

                <!-- Main Content -->
                <div class="resource-main">
                    <div class="resource-body">
                        @if($resource->type == 'video')
                            <!-- Video Content -->
                            <div class="video-container">
                                @if($resource->media_url)
                                    @if(strpos($resource->media_url, 'youtube.com') !== false || strpos($resource->media_url, 'youtu.be') !== false)
                                        @php
                                            // Extract YouTube video ID
                                            if (preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\s]{11})/', $resource->media_url, $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        @if(isset($videoId))
                                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0"
                                                allowfullscreen></iframe>
                                        @else
                                            <div class="video-placeholder">
                                                <i class="fas fa-video"></i>
                                                <p>{{ __('Video URL not recognized') }}</p>
                                                <a href="{{ $resource->media_url }}" target="_blank"
                                                    class="btn-link">{{ __('Watch on external site') }}</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="video-placeholder">
                                            <i class="fas fa-video"></i>
                                            <p>{{ __('Video content') }}</p>
                                            <a href="{{ $resource->media_url }}" target="_blank"
                                                class="btn-link">{{ __('Watch video') }}</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="video-placeholder">
                                        <i class="fas fa-video"></i>
                                        <p>{{ __('No video URL provided') }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Article/Tip/Guide Content -->
                        <div class="content-body">
                            {!! $resource->body !!}
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="resource-sidebar">
                    @if($relatedResources->count() > 0)
                        <div class="related-card">
                            <h3><i class="fas fa-link"></i> {{ __('Related Resources') }}</h3>
                            <div class="related-list">
                                @foreach($relatedResources as $related)
                                    @php
                                        $relIcon = $typeIcons[$related->type] ?? 'fa-newspaper';
                                    @endphp
                                    <a href="{{ route('resources.show', $related->id) }}" class="related-item">
                                        <div class="related-icon">
                                            <i class="fas {{ $relIcon }}"></i>
                                        </div>
                                        <div class="related-info">
                                            <h4>{{ $related->title }}</h4>
                                            <span>{{ $related->published_at ? $related->published_at->translatedFormat('M d, Y') : '' }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="share-card">
                        <h3><i class="fas fa-share-alt"></i> {{ __('Share This') }}</h3>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank" class="share-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($resource->title) }}"
                                target="_blank" class="share-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($resource->title . ' - ' . url()->current()) }}"
                                target="_blank" class="share-btn whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject={{ urlencode($resource->title) }}&body={{ urlencode(url()->current()) }}"
                                class="share-btn email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .resource-hero {
                background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
                padding: 60px 0 40px;
                text-align: center;
            }

            .resource-type {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: rgba(139, 92, 246, 0.1);
                padding: 8px 20px;
                border-radius: 50px;
                margin-bottom: 20px;
                font-size: 0.875rem;
            }

            .resource-hero h1 {
                font-size: 2.2rem;
                margin-bottom: 15px;
                max-width: 800px;
                margin-left: auto;
                margin-right: auto;
            }

            .resource-meta {
                color: #6b7280;
                font-size: 0.875rem;
            }

            .resource-section {
                padding: 40px 0 80px;
                background: #f9fafb;
            }

            .resource-layout {
                display: flex;
                gap: 40px;
            }

            .resource-main {
                flex: 2;
            }

            .resource-sidebar {
                flex: 1;
            }

            .resource-body {
                background: white;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            /* Video Container */
            .video-container {
                margin-bottom: 30px;
                border-radius: 16px;
                overflow: hidden;
            }

            .video-container iframe {
                width: 100%;
                height: 400px;
                border: none;
            }

            .video-placeholder {
                background: linear-gradient(135deg, #1f2937, #374151);
                height: 300px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: white;
                text-align: center;
                border-radius: 16px;
            }

            .video-placeholder i {
                font-size: 4rem;
                margin-bottom: 15px;
                opacity: 0.5;
            }

            .video-placeholder p {
                margin-bottom: 15px;
                color: #9ca3af;
            }

            .btn-link {
                color: #7c3aed;
                text-decoration: none;
                padding: 8px 20px;
                background: white;
                border-radius: 40px;
                transition: all 0.3s ease;
            }

            .btn-link:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            /* Content Body */
            .content-body {
                line-height: 1.8;
            }

            .content-body h1,
            .content-body h2,
            .content-body h3 {
                margin-top: 1.5em;
                margin-bottom: 0.5em;
                color: #1f2937;
            }

            .content-body p {
                color: #4b5563;
                margin-bottom: 1.2em;
            }

            .content-body ul,
            .content-body ol {
                margin-bottom: 1.2em;
                padding-left: 1.5em;
            }

            .content-body li {
                margin-bottom: 0.5em;
                color: #4b5563;
            }

            .content-body img {
                max-width: 100%;
                border-radius: 12px;
                margin: 20px 0;
            }

            .content-body a {
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

            .content-body a:hover {
                background: #ede9fe;
                border-left-width: 6px;
                transform: translateX(4px);
                color: #9159f3;
            }

            .content-body blockquote {
                border-left: 4px solid #7c3aed;
                padding-left: 20px;
                margin: 20px 0;
                font-style: italic;
                color: #6b7280;
            }

            /* Sidebar Cards */
            .related-card,
            .share-card {
                background: white;
                border-radius: 20px;
                padding: 25px;
                margin-bottom: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .related-card h3,
            .share-card h3 {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e5e7eb;
                font-size: 1.1rem;
            }

            .related-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .related-item {
                display: flex;
                gap: 12px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .related-item:hover {
                transform: translateX(5px);
            }

            .related-icon {
                width: 40px;
                height: 40px;
                background: #f3f4f6;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7c3aed;
            }

            .related-info h4 {
                font-size: 0.9rem;
                margin: 0 0 5px 0;
                color: #1f2937;
            }

            .related-info span {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .share-buttons {
                display: flex;
                gap: 12px;
                justify-content: center;
            }

            .share-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .share-btn.facebook {
                background: #1877f2;
                color: white;
            }

            .share-btn.twitter {
                background: #1da1f2;
                color: white;
            }

            .share-btn.whatsapp {
                background: #25d366;
                color: white;
            }

            .share-btn.email {
                background: #6b7280;
                color: white;
            }

            .share-btn:hover {
                transform: translateY(-3px);
            }

            /* RTL Support */
            body.rtl .related-item:hover {
                transform: translateX(-5px);
            }

            body.rtl .content-body ul,
            body.rtl .content-body ol {
                padding-left: 0;
                padding-right: 1.5em;
            }

            body.rtl .content-body blockquote {
                border-left: none;
                border-right: 4px solid #7c3aed;
                padding-left: 0;
                padding-right: 20px;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .resource-layout {
                    flex-direction: column;
                }

                .resource-hero h1 {
                    font-size: 1.8rem;
                }

                .resource-body {
                    padding: 25px;
                }

                .video-container iframe {
                    height: 250px;
                }

                .video-placeholder {
                    height: 200px;
                }
            }

            @media (max-width: 768px) {
                .video-container iframe {
                    height: 200px;
                }

                .video-placeholder {
                    height: 180px;
                }

                .video-placeholder i {
                    font-size: 2.5rem;
                }
            }
        </style>
    @endpush
@endsection