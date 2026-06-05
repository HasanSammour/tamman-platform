{{-- resources/views/patient/bookings/book.blade.php --}}
@extends('layouts.app')

@section('title', __('Book Session') . ' - ' . __('Tamman'))

@section('page-title', __('Book Session with') . ' ' . $specialist->name)

@section('content')
    <div class="booking-container">
        <div class="booking-grid">
            <!-- Left Column - Specialist Info -->
            <div class="specialist-card animate-slide-in-left">
                <div class="specialist-header">
                    <div class="specialist-avatar">
                        @php
                            $specialistImage = $specialist->getProfileImageUrl();
                            $specialistInitial = mb_substr($specialist->name, 0, 1, 'UTF-8');
                        @endphp
                        @if($specialistImage)
                            <img src="{{ $specialistImage }}" alt="{{ $specialist->name }}">
                        @else
                            <div class="avatar-placeholder">{{ $specialistInitial }}</div>
                        @endif
                    </div>
                    <div class="specialist-info">
                        <h2>{{ $specialist->name }}</h2>
                        <p class="specialization">
                            <i class="fas fa-stethoscope"></i> {{ $specialist->specialistProfile->specialization }}
                        </p>
                        <div class="rating">
                            @php $rating = $specialist->specialistProfile->rating_avg ?? 0; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($rating))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span>({{ number_format($rating, 1) }})</span>
                        </div>
                    </div>
                </div>

                <div class="specialist-details">
                    <div class="detail-item">
                        <i class="fas fa-graduation-cap"></i>
                        <div>
                            <strong>{{ __('Experience') }}</strong>
                            <p>{{ $specialist->specialistProfile->experience_years ?? 0 }} {{ __('years') }}</p>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-language"></i>
                        <div>
                            <strong>{{ __('Languages') }}</strong>
                            <p>{{ $specialist->specialistProfile->languages ?? __('Not specified') }}</p>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <strong>{{ __('Sessions Completed') }}</strong>
                            <p>{{ number_format($specialist->specialistProfile->total_sessions ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="specialist-bio">
                    <h4><i class="fas fa-user-md"></i> {{ __('About') }}</h4>
                    <p>{{ $specialist->specialistProfile->bio ?? __('This specialist has not added a bio yet.') }}</p>
                </div>
            </div>

            <!-- Right Column - Booking Form -->
            <div class="booking-card animate-slide-in-right">
                <div class="booking-header">
                    <h3><i class="fas fa-calendar-plus"></i> {{ __('Book a Session') }}</h3>
                    <p>{{ __('Select session type, date, time and payment method') }}</p>
                </div>

                <form id="bookingForm" class="booking-form">
                    @csrf
                    <input type="hidden" name="specialist_id" value="{{ $specialist->id }}">
                    <input type="hidden" name="session_datetime" id="selectedDatetime">
                    <input type="hidden" name="session_type" id="selectedSessionType">
                    <input type="hidden" name="payment_method" id="selectedPaymentMethod">
                    <input type="hidden" name="free_redemption_id" id="selectedFreeRedemptionId"value="">

                    <!-- Step 1: Session Type - Vertical/Stacked Layout -->
                    <div class="form-step active" id="step1">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h4>{{ __('Choose Session Type') }}</h4>
                        </div>
                        <div class="session-types-vertical">
                            @foreach($sessionTypes as $type => $data)
                                <div class="session-card-vertical" {{ $preSelectedType == $type ? 'selected' : '' }}" 
                                     data-type="{{ $type }}" 
                                     data-price="{{ $data['price'] }}" 
                                     data-has-free="{{ $data['has_free'] ? 'true' : 'false' }}" 
                                     data-free-id="{{ $data['free_redemption_id'] }}">
                                    <div class="session-card-icon" style="background: {{ $data['color'] }}20; color: {{ $data['color'] }}">
                                        <i class="fas {{ $data['icon'] }}"></i>
                                    </div>
                                    <div class="session-card-details">
                                        <div class="session-card-header">
                                            <h4>{{ $data['name'] }}</h4>
                                            @if($data['has_free'])
                                                <span class="free-badge">
                                                    <i class="fas fa-gift"></i> {{ __('Free Available!') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="session-card-desc">{{ $data['description'] }}</p>
                                        <div class="session-card-price">
                                            @if($data['has_free'])
                                                <span class="original-price">${{ number_format($data['price'], 2) }}</span>
                                                <span class="free-price">{{ __('FREE') }}</span>
                                            @else
                                                <span class="price">${{ number_format($data['price'], 2) }}</span>
                                            @endif
                                            @if($type != 'video')
                                                <span class="discount-badge">-{{ $type == 'audio' ? '10' : '20' }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="session-card-select">
                                        <div class="radio-custom">
                                            <i class="far fa-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="step-actions">
                            <button type="button" class="btn-next-step" disabled>{{ __('Next') }} <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Date & Time -->
                    <div class="form-step" id="step2">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h4>{{ __('Choose Date & Time') }}</h4>
                        </div>

                        <div class="date-picker-container">
                            <div class="date-slots" id="dateSlots">
                                <div class="loading-slots">
                                    <div class="spinner"></div>
                                    <p>{{ __('Loading available dates...') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="time-slots-container" id="timeSlotsContainer" style="display: none;">
                            <h5><i class="fas fa-clock"></i> {{ __('Available Time Slots') }}</h5>
                            <div class="time-slots" id="timeSlots"></div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev-step"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</button>
                            <button type="button" class="btn-next-step" id="nextToPaymentBtn" disabled>{{ __('Next') }} <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: Payment -->
                    <div class="form-step" id="step3">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h4>{{ __('Payment Method') }}</h4>
                        </div>

                        <div class="payment-summary" id="paymentSummary"></div>

                        <div class="payment-methods" id="paymentMethods">
                            <!-- Credit Payment Option -->
                            <div class="payment-method credit-method" data-method="credit">
                                <div class="method-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="method-info">
                                    <h4>{{ __('Credit Balance') }}</h4>
                                    <p>{{ __('Pay using your credit balance') }}</p>
                                    <div class="method-balance">
                                        {{ __('Available') }}: <strong>${{ number_format($creditBalance, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="method-check">
                                    <i class="far fa-circle"></i>
                                </div>
                            </div>

                            <!-- Free Session Option (shown only if available for selected type) -->
                            <div class="payment-method free-method" data-method="free" style="display: none;">
                                <div class="method-icon">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <div class="method-info">
                                    <h4>{{ __('Free Session') }}</h4>
                                    <p>{{ __('Use your redeemed free session') }}</p>
                                    <div class="method-note">
                                        <i class="fas fa-info-circle"></i> {{ __('You have a free session available!') }}
                                    </div>
                                </div>
                                <div class="method-check">
                                    <i class="far fa-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label for="notes"><i class="fas fa-pen"></i> {{ __('Additional Notes (Optional)') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="{{ __('Any specific concerns or questions for the specialist?') }}"></textarea>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev-step"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</button>
                            <button type="submit" class="btn-submit-booking" id="submitBookingBtn" disabled>
                                <span class="btn-text">{{ __('Confirm Booking') }}</span>
                                <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .booking-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .booking-grid {
                display: grid;
                grid-template-columns: 1fr 1.5fr;
                gap: 30px;
            }

            /* Specialist Card */
            .specialist-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                position: sticky;
                top: 100px;
            }

            .specialist-header {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                padding: 30px;
                text-align: center;
            }

            .specialist-avatar img,
            .avatar-placeholder {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid white;
                margin: 0 auto 15px;
            }

            .avatar-placeholder {
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                font-weight: 600;
                color: white;
                margin: 0 auto 15px;
            }

            .specialist-header h2 {
                color: white;
                font-size: 1.3rem;
                margin-bottom: 5px;
            }

            .specialization {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.85rem;
                margin-bottom: 10px;
            }

            .rating {
                color: #fbbf24;
                font-size: 0.8rem;
            }

            .rating span {
                color: rgba(255, 255, 255, 0.8);
                margin-left: 5px;
            }

            .specialist-details {
                padding: 20px;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                border-bottom: 1px solid #e5e7eb;
            }

            .detail-item {
                text-align: center;
            }

            .detail-item i {
                font-size: 1.2rem;
                color: #7c3aed;
                margin-bottom: 8px;
            }

            .detail-item strong {
                display: block;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .detail-item p {
                font-size: 0.8rem;
                color: #1f2937;
                margin: 0;
            }

            .specialist-bio {
                padding: 20px;
            }

            .specialist-bio h4 {
                margin-bottom: 10px;
                color: #1f2937;
            }

            .specialist-bio p {
                font-size: 0.85rem;
                color: #6b7280;
                line-height: 1.5;
            }

            /* Booking Card */
            .booking-card {
                background: white;
                border-radius: 24px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .booking-header {
                padding: 25px 30px;
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-bottom: 1px solid #e5e7eb;
            }

            .booking-header h3 {
                margin: 0 0 5px;
                color: #1f2937;
            }

            .booking-header p {
                margin: 0;
                color: #6b7280;
                font-size: 0.85rem;
            }

            .booking-form {
                padding: 30px;
            }

            .form-step {
                display: none;
                animation: fadeInStep 0.4s ease;
            }

            .form-step.active {
                display: block;
            }

            @keyframes fadeInStep {
                from { opacity: 0; transform: translateX(20px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .step-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 25px;
            }

            .step-number {
                width: 32px;
                height: 32px;
                background: #7c3aed;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .step-header h4 {
                margin: 0;
                color: #1f2937;
            }

            /* Vertical Session Types */
            .session-types-vertical {
                display: flex;
                flex-direction: column;
                gap: 16px;
                margin-bottom: 30px;
            }

            .session-card-vertical {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 20px;
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 20px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .session-card-vertical:hover {
                border-color: #c4b5fd;
                transform: translateX(5px);
                background: white;
            }

            .session-card-vertical.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .session-card-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .session-card-icon i {
                font-size: 1.8rem;
            }

            .session-card-details {
                flex: 1;
            }

            .session-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 6px;
            }

            .session-card-header h4 {
                font-size: 1.1rem;
                font-weight: 600;
                margin: 0;
                color: #1f2937;
            }

            .free-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #10b981;
                color: white;
                padding: 4px 12px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .session-card-desc {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .session-card-price {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .price {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .original-price {
                font-size: 0.85rem;
                color: #9ca3af;
                text-decoration: line-through;
            }

            .free-price {
                font-size: 1rem;
                font-weight: 700;
                color: #10b981;
            }

            .discount-badge {
                display: inline-block;
                background: #10b981;
                color: white;
                font-size: 0.6rem;
                padding: 2px 8px;
                border-radius: 20px;
            }

            .session-card-select .radio-custom i {
                font-size: 1.3rem;
                color: #9ca3af;
            }

            .session-card-vertical.selected .radio-custom i {
                color: #7c3aed;
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
            }

            .session-card-vertical.selected .radio-custom i::before {
                content: "\f058";
            }

            /* Date Picker */
            .date-picker-container {
                margin-bottom: 25px;
            }

            .date-slots {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                padding-bottom: 10px;
                scrollbar-width: thin;
            }

            .date-slot {
                min-width: 100px;
                padding: 12px;
                text-align: center;
                background: #f9fafb;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .date-slot:hover {
                background: #f3f4f6;
                transform: translateY(-2px);
            }

            .date-slot.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .date-slot .day {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .date-slot .date {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .loading-slots {
                text-align: center;
                padding: 30px;
            }

            .spinner {
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

            .time-slots-container h5 {
                margin-bottom: 15px;
                color: #1f2937;
            }

            .time-slots {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .time-slot {
                padding: 10px 18px;
                background: #f9fafb;
                border-radius: 30px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
                font-size: 0.8rem;
            }

            .time-slot:hover {
                background: #f3f4f6;
                transform: translateY(-2px);
            }

            .time-slot.selected {
                background: #7c3aed;
                color: white;
                border-color: #7c3aed;
            }

            /* Payment Methods */
            .payment-summary {
                background: #f5f3ff;
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
                text-align: center;
            }

            .payment-summary .total {
                font-size: 1.5rem;
                font-weight: 700;
                color: #7c3aed;
            }

            .payment-methods {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 20px;
            }

            .payment-method {
                border: 2px solid #e5e7eb;
                border-radius: 16px;
                padding: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 15px;
                position: relative;
            }

            .payment-method:hover {
                border-color: #c4b5fd;
            }

            .payment-method.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .method-icon {
                width: 48px;
                height: 48px;
                background: #f3f4f6;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .method-icon i {
                font-size: 1.3rem;
                color: #7c3aed;
            }

            .method-info {
                flex: 1;
            }

            .method-info h4 {
                font-size: 0.9rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .method-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 4px;
            }

            .method-balance {
                font-size: 0.7rem;
                color: #10b981;
            }

            .method-note {
                font-size: 0.65rem;
                color: #f59e0b;
                margin-top: 4px;
            }

            .method-check {
                position: absolute;
                top: 16px;
                right: 16px;
            }

            .method-check i {
                font-size: 1.1rem;
                color: #9ca3af;
            }

            .payment-method.selected .method-check i {
                color: #7c3aed;
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
            }

            .payment-method.selected .method-check i::before {
                content: "\f058";
            }

            /* Step Actions */
            .step-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-next-step, .btn-prev-step, .btn-submit-booking {
                padding: 10px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-next-step {
                background: #7c3aed;
                color: white;
                border: none;
            }

            .btn-next-step:hover:not(:disabled) {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-next-step:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .btn-prev-step {
                background: #f3f4f6;
                color: #374151;
                border: none;
            }

            .btn-prev-step:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-submit-booking {
                background: #10b981;
                color: white;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-submit-booking:hover:not(:disabled) {
                background: #059669;
                transform: translateY(-2px);
            }

            .btn-submit-booking:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .empty-slots {
                text-align: center;
                padding: 40px;
            }

            .empty-slots i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            /* Animations */
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .animate-slide-in-left {
                animation: slideInLeft 0.5s ease;
            }

            .animate-slide-in-right {
                animation: slideInRight 0.5s ease;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .booking-grid {
                    grid-template-columns: 1fr;
                }

                .specialist-card {
                    position: static;
                    margin-bottom: 20px;
                }
            }

            @media (max-width: 768px) {
                .booking-container {
                    padding: 15px;
                }

                .booking-form {
                    padding: 20px;
                }

                .session-card-vertical {
                    flex-wrap: wrap;
                }

                .session-card-select {
                    width: 100%;
                    display: flex;
                    justify-content: flex-end;
                }

                .date-slots {
                    gap: 8px;
                }

                .date-slot {
                    min-width: 80px;
                    padding: 8px;
                }

                .step-actions {
                    flex-direction: column-reverse;
                    gap: 10px;
                }

                .btn-next-step, .btn-prev-step, .btn-submit-booking {
                    width: 100%;
                    justify-content: center;
                }
            }

            /* RTL Support */
            body.rtl .step-actions {
                flex-direction: row-reverse;
            }

            body.rtl .session-card-vertical {
                flex-direction: row;
            }

            body.rtl .method-check {
                right: auto;
                left: 16px;
            }

            body.rtl .session-card-select .radio-custom {
                text-align: left;
            }

            @media (max-width: 768px) {
                body.rtl .step-actions {
                    flex-direction: column-reverse;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Variables
            let selectedType = null;
            let selectedPrice = 0;
            let selectedDate = null;
            let selectedTime = null;
            let selectedDatetime = null;
            let selectedHasFree = false;
            let selectedFreeId = null;
            let availableSlots = [];
            let currentStep = 1;

            // Auto-select session type from URL parameter (using PHP preSelectedType)
            document.addEventListener('DOMContentLoaded', function() {
                // Check if PHP already set a selected card
                const preSelectedCard = document.querySelector('.session-card-vertical.selected');

                if (preSelectedCard) {
                    // Trigger the selection logic
                    selectedType = preSelectedCard.dataset.type;
                    selectedPrice = parseFloat(preSelectedCard.dataset.price);
                    selectedHasFree = preSelectedCard.dataset.hasFree === 'true';
                    selectedFreeId = preSelectedCard.dataset.freeId;

                    // Set hidden inputs
                    document.getElementById('selectedSessionType').value = selectedType;
                    document.getElementById('selectedFreeRedemptionId').value = selectedFreeId || '';

                    // Update payment methods
                    updatePaymentMethods();

                    // Enable next button
                    const nextBtn = document.querySelector('.btn-next-step');
                    if (nextBtn) nextBtn.disabled = false;

                    console.log('Pre-selected via PHP:', selectedType);
                }
            });

            // Session Type Selection - Vertical Cards
            document.querySelectorAll('.session-card-vertical').forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    document.querySelectorAll('.session-card-vertical').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    // Get data from selected card
                    selectedType = this.dataset.type;
                    selectedPrice = parseFloat(this.dataset.price);
                    selectedHasFree = this.dataset.hasFree === 'true';
                    selectedFreeId = this.dataset.freeId;

                    // Debug - log to console
                    console.log('Selected Type:', selectedType);
                    console.log('Has Free:', selectedHasFree);
                    console.log('Free ID:', selectedFreeId);

                    // Set hidden inputs
                    document.getElementById('selectedSessionType').value = selectedType;
                    document.getElementById('selectedFreeRedemptionId').value = selectedFreeId || '';

                    // Update payment methods visibility
                    updatePaymentMethods();

                    // Enable next button
                    const nextBtn = document.querySelector('.btn-next-step');
                    if (nextBtn) nextBtn.disabled = false;
                });
            });

            // Update Payment Methods based on selected session type
            function updatePaymentMethods() {
                const freeMethod = document.querySelector('.free-method');
                const creditMethod = document.querySelector('.credit-method');

                if (selectedHasFree && selectedFreeId && selectedFreeId !== 'null' && selectedFreeId !== '') {
                    // Show free method option
                    if (freeMethod) freeMethod.style.display = 'flex';
                    // Update free method message
                    const freeMethodNote = freeMethod?.querySelector('.method-note');
                    if (freeMethodNote) {
                        freeMethodNote.innerHTML = '<i class="fas fa-info-circle"></i> {{ __("You have a free session available for this type!") }}';
                    }
                } else {
                    // Hide free method
                    if (freeMethod) freeMethod.style.display = 'none';
                    // If free method was selected, reset to credit
                    if (document.getElementById('selectedPaymentMethod').value === 'free') {
                        document.getElementById('selectedPaymentMethod').value = '';
                        document.querySelector('.payment-method.selected')?.classList.remove('selected');
                        document.getElementById('submitBookingBtn').disabled = true;
                    }
                }
            }

            // Next Step Function
            function goToStep(step) {
                document.querySelectorAll('.form-step').forEach((el, index) => {
                    el.classList.remove('active');
                    if (index + 1 === step) {
                        el.classList.add('active');
                    }
                });
                currentStep = step;

                if (step === 2 && availableSlots.length === 0) {
                    loadAvailableSlots();
                }

                if (step === 3) {
                    updatePaymentSummary();
                }
            }

            // Prev Step Function
            function goPrevStep() {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            }

            // Load Available Slots via AJAX
            async function loadAvailableSlots() {
                const dateSlotsContainer = document.getElementById('dateSlots');
                dateSlotsContainer.innerHTML = `
                    <div class="loading-slots">
                        <div class="spinner"></div>
                        <p>{{ __('Loading available dates...') }}</p>
                    </div>
                `;

                try {
                    const specialistId = {{ $specialist->id }};
                    const response = await fetch(`{{ route("patient.booking.slots") }}?specialist_id=${specialistId}&days=14`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.slots && data.slots.length > 0) {
                        availableSlots = data.slots;
                        renderDateSlots();
                    } else {
                        dateSlotsContainer.innerHTML = `
                            <div class="empty-slots">
                                <i class="fas fa-calendar-times"></i>
                                <p>{{ __('No available slots found for the next 14 days.') }}</p>
                            </div>
                        `;
                    }
                } catch (error) {
                    console.error('Error loading slots:', error);
                    dateSlotsContainer.innerHTML = `
                        <div class="empty-slots">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>{{ __('Error loading slots. Please try again.') }}</p>
                        </div>
                    `;
                }
            }

            // Render Date Slots
            function renderDateSlots() {
                const dateSlotsContainer = document.getElementById('dateSlots');
                dateSlotsContainer.innerHTML = '';

                if (!availableSlots || availableSlots.length === 0) {
                    dateSlotsContainer.innerHTML = `
                        <div class="empty-slots">
                            <i class="fas fa-calendar-times"></i>
                            <p>{{ __('No available slots found for the next 14 days.') }}</p>
                        </div>
                    `;
                    return;
                }

                availableSlots.forEach(slotGroup => {
                    const dateSlot = document.createElement('div');
                    dateSlot.className = 'date-slot';
                    dateSlot.dataset.date = slotGroup.date;
                    dateSlot.dataset.slots = JSON.stringify(slotGroup.slots);
                    dateSlot.innerHTML = `
                        <div class="day">${new Date(slotGroup.date).toLocaleDateString('en', { weekday: 'short' })}</div>
                        <div class="date">${slotGroup.date_formatted}</div>
                    `;
                    dateSlot.addEventListener('click', () => selectDate(slotGroup));
                    dateSlotsContainer.appendChild(dateSlot);
                });
            }

            // Select Date
            function selectDate(slotGroup) {
                document.querySelectorAll('.date-slot').forEach(el => el.classList.remove('selected'));
                event.target.closest('.date-slot').classList.add('selected');

                selectedDate = slotGroup;
                renderTimeSlots(slotGroup.slots);
            }

            // Render Time Slots
            function renderTimeSlots(times) {
                const timeSlotsContainer = document.getElementById('timeSlots');
                const timeContainer = document.getElementById('timeSlotsContainer');

                timeSlotsContainer.innerHTML = '';

                if (!times || times.length === 0) {
                    timeSlotsContainer.innerHTML = '<div class="empty-slots"><p>{{ __("No time slots available for this date.") }}</p></div>';
                    timeContainer.style.display = 'block';
                    return;
                }

                times.forEach(time => {
                    const timeSlot = document.createElement('div');
                    timeSlot.className = 'time-slot';
                    timeSlot.dataset.datetime = time.datetime;
                    timeSlot.innerHTML = time.time;
                    timeSlot.addEventListener('click', () => selectTime(time));
                    timeSlotsContainer.appendChild(timeSlot);
                });

                timeContainer.style.display = 'block';
            }

            // Select Time
            function selectTime(time) {
                document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
                event.target.closest('.time-slot').classList.add('selected');

                selectedTime = time;
                selectedDatetime = time.datetime;

                document.getElementById('nextToPaymentBtn').disabled = false;
            }

            // Update Payment Summary
            function updatePaymentSummary() {
                const summary = document.getElementById('paymentSummary');
                const sessionCard = document.querySelector('.session-card-vertical.selected');
                const sessionTypeName = sessionCard?.querySelector('h4')?.innerText || selectedType;
                const isFree = selectedHasFree && selectedFreeId && selectedFreeId !== 'null' && selectedFreeId !== '';
                const displayPrice = isFree ? 0 : selectedPrice;

                summary.innerHTML = `
                    <div>
                        <strong>{{ __('Session Summary') }}</strong>
                        <div style="margin-top: 10px;">
                            <p>{{ __('Session Type') }}: <strong>${sessionTypeName}</strong></p>
                            <p>{{ __('Date & Time') }}: <strong>${selectedTime?.date_formatted || ''} at ${selectedTime?.time || ''}</strong></p>
                            <p>{{ __('Duration') }}: <strong>60 {{ __('minutes') }}</strong></p>
                            <hr style="margin: 10px 0;">
                            <p class="total">{{ __('Total Amount') }}: <strong>${isFree ? '{{ __("FREE") }}' : '$' + displayPrice.toFixed(2)}</strong></p>
                            ${isFree ? '<p class="free-note"><i class="fas fa-gift"></i> {{ __("You are using a free session reward!") }}</p>' : ''}
                        </div>
                    </div>
                `;
            }

            // Payment Method Selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');

                    const methodValue = this.dataset.method;
                    document.getElementById('selectedPaymentMethod').value = methodValue;
                    document.getElementById('submitBookingBtn').disabled = false;
                });
            });

            // Next to Payment Step
            document.getElementById('nextToPaymentBtn')?.addEventListener('click', () => {
                if (selectedDate && selectedTime) {
                    // Auto-select payment method based on availability
                    if (selectedHasFree && selectedFreeId && selectedFreeId !== 'null' && selectedFreeId !== '') {
                        // Auto-select free method if available
                        const freeMethod = document.querySelector('.free-method');
                        if (freeMethod) {
                            freeMethod.click();
                        }
                    } else {
                        // Auto-select credit method
                        const creditMethod = document.querySelector('.credit-method');
                        if (creditMethod) {
                            creditMethod.click();
                        }
                    }
                    goToStep(3);
                }
            });

            // Form Submit
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    // Get the selected card directly
                    const selectedCard = document.querySelector('.session-card-vertical.selected');
                    if (!selectedCard) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please select a session type.'
                        });
                        return;
                    }

                    // Get values from the selected card
                    const sessionType = selectedCard.dataset.type;
                    const freeRedemptionId = selectedCard.dataset.freeId;
                    const hasFree = selectedCard.dataset.hasFree === 'true';

                    // Get payment method
                    let paymentMethod = document.getElementById('selectedPaymentMethod').value;

                    // If has free and free ID exists, force free payment method
                    if (hasFree && freeRedemptionId && freeRedemptionId !== 'null' && freeRedemptionId !== '') {
                        paymentMethod = 'free';
                        document.getElementById('selectedPaymentMethod').value = 'free';
                    } else if (!paymentMethod) {
                        paymentMethod = 'credit';
                        document.getElementById('selectedPaymentMethod').value = 'credit';
                    }

                    // Set the hidden inputs
                    document.getElementById('selectedSessionType').value = sessionType;
                    document.getElementById('selectedFreeRedemptionId').value = freeRedemptionId || '';
                    document.getElementById('selectedDatetime').value = selectedDatetime;

                    // Validate
                    if (!selectedDatetime) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please select a date and time.'
                        });
                        return;
                    }

                    // Log what we're sending
                    console.log('=== SUBMITTING BOOKING ===');
                    console.log('Session Type:', sessionType);
                    console.log('Payment Method:', paymentMethod);
                    console.log('Free Redemption ID:', freeRedemptionId);
                    console.log('Selected Datetime:', selectedDatetime);
                    console.log('===========================');

                    // Show loading spinner
                    const submitBtn = document.getElementById('submitBookingBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    // Create FormData and manually append all values
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('specialist_id', {{ $specialist->id }});
                    formData.append('session_datetime', selectedDatetime);
                    formData.append('session_type', sessionType);
                    formData.append('payment_method', paymentMethod);
                    if (freeRedemptionId && freeRedemptionId !== 'null' && freeRedemptionId !== '') {
                        formData.append('free_redemption_id', freeRedemptionId);
                    }

                    const notes = document.getElementById('notes')?.value || '';
                    if (notes) {
                        formData.append('notes', notes);
                    }

                    try {
                        const response = await fetch('{{ route("patient.bookings.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Booking Confirmed!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            window.location.href = data.redirect_url;
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Booking Failed',
                                text: data.message
                            });
                            btnText.style.display = 'inline-block';
                            btnSpinner.style.display = 'none';
                            submitBtn.disabled = false;
                        }
                    } catch (error) {
                        console.error('Booking error:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error. Please try again.'
                        });
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Navigation Buttons
            document.querySelectorAll('.btn-next-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep === 1 && selectedType) {
                        goToStep(2);
                    }
                });
            });

            document.querySelectorAll('.btn-prev-step').forEach(btn => {
                btn.addEventListener('click', goPrevStep);
            });

            // Auto-select session type from URL parameter
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const preSelectedType = urlParams.get('type');

                if (preSelectedType) {
                    const sessionCard = document.querySelector(`.session-card-vertical[data-type="${preSelectedType}"]`);
                    if (sessionCard) {
                        // Simulate click on the pre-selected session type
                        sessionCard.click();
                    }
                }
            });
        </script>
    @endpush

@endsection