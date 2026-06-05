{{-- resources/views/vendor/pagination/tamman.blade.php --}}
@if($paginator->hasPages())
    <div class="tamman-pagination-wrapper">
        <div class="tamman-pagination-container">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <span class="tamman-pagination-item prev disabled">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Previous') }}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="tamman-pagination-item prev">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Previous') }}</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="tamman-pagination-numbers">
                @foreach($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if(is_string($element))
                        <span class="tamman-pagination-dots">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $paginator->currentPage())
                                <span class="tamman-pagination-number active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="tamman-pagination-number">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="tamman-pagination-item next">
                    <span>{{ __('Next') }}</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <span class="tamman-pagination-item next disabled">
                    <span>{{ __('Next') }}</span>
                    <i class="fas fa-arrow-right"></i>
                </span>
            @endif
        </div>
        
        <div class="tamman-pagination-info">
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
        /* ==================== TAMMAN PAGINATION STYLES ==================== */
        .tamman-pagination-wrapper {
            margin-top: 45px;
            padding: 25px 0 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .tamman-pagination-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .tamman-pagination-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 40px;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tamman-pagination-item:hover:not(.disabled) {
            background: #f5f3ff;
            border-color: #7c3aed;
            color: #7c3aed;
            transform: translateY(-2px);
        }

        .tamman-pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .tamman-pagination-numbers {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tamman-pagination-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 8px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tamman-pagination-number:hover {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #7c3aed;
            transform: translateY(-2px);
        }

        .tamman-pagination-number.active {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-color: #7c3aed;
            color: white;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        .tamman-pagination-dots {
            color: #9ca3af;
            padding: 0 4px;
        }

        .tamman-pagination-info {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }

        .tamman-pagination-info strong {
            color: #7c3aed;
            font-weight: 600;
        }

        /* RTL Support */
        body.rtl .tamman-pagination-item.prev i,
        body.rtl .tamman-pagination-item.next i {
            transform: rotate(180deg);
        }

        body.rtl .tamman-pagination-numbers {
            flex-direction: row;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .tamman-pagination-wrapper {
                margin-top: 30px;
                padding: 20px 0 10px;
            }

            .tamman-pagination-container {
                gap: 12px;
            }

            .tamman-pagination-item {
                padding: 8px 16px;
                font-size: 0.75rem;
            }

            .tamman-pagination-number {
                min-width: 36px;
                height: 36px;
                font-size: 0.75rem;
                border-radius: 10px;
            }

            .tamman-pagination-info {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .tamman-pagination-container {
                flex-direction: column;
                gap: 12px;
            }

            .tamman-pagination-numbers {
                order: -1;
            }

            .tamman-pagination-item.prev,
            .tamman-pagination-item.next {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endif