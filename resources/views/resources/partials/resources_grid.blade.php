@if($resources->count() > 0)
    <div class="resources-grid">
        @foreach($resources as $resource)
            @php
                $typeClasses = [
                    'article' => 'article',
                    'video' => 'video',
                    'tip' => 'tip',
                    'guide' => 'guide'
                ];
                $typeIcons = [
                    'article' => 'fa-newspaper',
                    'video' => 'fa-video',
                    'tip' => 'fa-lightbulb',
                    'guide' => 'fa-book'
                ];
                $bgColors = [
                    'article' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'video' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    'tip' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                    'guide' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'
                ];
                $typeClass = $typeClasses[$resource->type] ?? 'article';
                $typeIcon = $typeIcons[$resource->type] ?? 'fa-newspaper';
                $bgColor = $bgColors[$resource->type] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            @endphp
            <div class="resource-card">
                <div class="resource-image" style="background: {{ $bgColor }}; display: flex; align-items: center; justify-content: center;">
                    <i class="fas {{ $typeIcon }}" style="font-size: 3rem; color: white; opacity: 0.8;"></i>
                    <span class="resource-type-badge {{ $typeClass }}">{{ __(ucfirst($resource->type)) }}</span>
                </div>
                <div class="resource-content">
                    <h3 class="resource-title">
                        <a href="{{ route('resources.show', $resource->id) }}">{{ $resource->title }}</a>
                    </h3>
                    <p class="resource-excerpt">{{ Str::limit(strip_tags($resource->body), 120) }}</p>
                    <div class="resource-meta">
                        <span class="resource-date">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $resource->published_at ? $resource->published_at->translatedFormat('M d, Y') : '' }}
                        </span>
                        <a href="{{ route('resources.show', $resource->id) }}" class="resource-read-more">
                            {{ __('Read More') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="no-results">
        <i class="fas fa-search"></i>
        <h3>{{ __('No resources found') }}</h3>
        <p>{{ __('Try adjusting your search or filter criteria') }}</p>
    </div>
@endif