{{-- resources/views/vendor/pagination/simple-custom.blade.php --}}
@if($paginator->hasPages())
    <div class="simple-custom-pagination-wrapper">
        <div class="simple-custom-pagination-container">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <span class="simple-custom-pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                    <span>{{ __('Previous') }}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="simple-custom-pagination-item">
                    <i class="fas fa-chevron-left"></i>
                    <span>{{ __('Previous') }}</span>
                </a>
            @endif

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="simple-custom-pagination-item">
                    <span>{{ __('Next') }}</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="simple-custom-pagination-item disabled">
                    <span>{{ __('Next') }}</span>
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>

    <style>
        /* ==================== SIMPLE CUSTOM PAGINATION STYLES ==================== */
        .simple-custom-pagination-wrapper {
            margin-top: 30px;
            padding: 15px 0;
            display: flex;
            justify-content: center;
        }

        .simple-custom-pagination-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .simple-custom-pagination-item {
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

        .simple-custom-pagination-item:hover:not(.disabled) {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: #7c3aed;
            transform: translateY(-2px);
        }

        .simple-custom-pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .simple-custom-pagination-item i {
            font-size: 0.7rem;
        }

        /* RTL Support */
        body.rtl .simple-custom-pagination-item i.fa-chevron-left {
            transform: rotate(180deg);
        }

        body.rtl .simple-custom-pagination-item i.fa-chevron-right {
            transform: rotate(180deg);
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .simple-custom-pagination-item {
                padding: 8px 16px;
                font-size: 0.75rem;
            }
            
            .simple-custom-pagination-item span {
                display: none;
            }
            
            .simple-custom-pagination-item i {
                margin: 0;
            }
        }
    </style>
@endif