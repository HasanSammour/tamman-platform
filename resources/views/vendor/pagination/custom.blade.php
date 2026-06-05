{{-- resources/views/vendor/pagination/custom.blade.php --}}
@if($paginator->hasPages())
    <div class="custom-pagination-wrapper">
        <div class="custom-pagination-container">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <span class="custom-pagination-item disabled" aria-disabled="true">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="custom-pagination-item" rel="prev">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if(is_string($element))
                    <span class="custom-pagination-item dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $paginator->currentPage())
                            <span class="custom-pagination-item active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="custom-pagination-item">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="custom-pagination-item" rel="next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="custom-pagination-item disabled" aria-disabled="true">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
        
        <div class="custom-pagination-info">
            {{ __('Showing') }} 
            <strong>{{ $paginator->firstItem() }}</strong> 
            {{ __('to') }} 
            <strong>{{ $paginator->lastItem() }}</strong> 
            {{ __('of') }} 
            <strong>{{ $paginator->total() }}</strong> 
            {{ __('results') }}
        </div>
    </div>

    <style>
        /* ==================== CUSTOM PAGINATION STYLES ==================== */
        .custom-pagination-wrapper {
            margin-top: 40px;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .custom-pagination-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .custom-pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .custom-pagination-item i {
            font-size: 0.75rem;
        }

        .custom-pagination-item:hover:not(.active):not(.disabled) {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #7c3aed;
            transform: translateY(-2px);
        }

        .custom-pagination-item.active {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-color: #7c3aed;
            color: white;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        .custom-pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .custom-pagination-item.dots {
            background: transparent;
            border: none;
            cursor: default;
            color: #9ca3af;
        }

        .custom-pagination-info {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }

        .custom-pagination-info strong {
            color: #7c3aed;
            font-weight: 600;
        }

        /* RTL Support */
        body.rtl .custom-pagination-item i.fa-chevron-left {
            transform: rotate(180deg);
        }

        body.rtl .custom-pagination-item i.fa-chevron-right {
            transform: rotate(180deg);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .custom-pagination-wrapper {
                margin-top: 30px;
                padding: 15px 0;
            }
            
            .custom-pagination-item {
                min-width: 36px;
                height: 36px;
                padding: 0 10px;
                font-size: 0.75rem;
                border-radius: 10px;
            }
            
            .custom-pagination-info {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .custom-pagination-container {
                gap: 6px;
            }
            
            .custom-pagination-item {
                min-width: 32px;
                height: 32px;
                padding: 0 8px;
                font-size: 0.7rem;
                border-radius: 8px;
            }
        }
    </style>
@endif