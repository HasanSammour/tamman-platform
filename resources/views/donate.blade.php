@extends('layouts.app')

@section('title', __('Become a Donor') . ' - ' . __('Tamman'))

@section('page-title', __('Support Those in Need'))

@section('content')
    <div class="donate-container">
        <div class="donate-header animate-fade-in">
            <h1><i class="fas fa-hand-holding-heart"></i> {{ __('Make a Difference') }}</h1>
            <p>{{ __('Your donation helps provide mental health support to those who cannot afford it.') }}</p>
        </div>

        <div class="donate-grid">
            <!-- Donation Form -->
            <div class="donate-card animate-slide-in-left">
                <div class="card-header">
                    <h3><i class="fas fa-gift"></i> {{ __('Make a Donation') }}</h3>
                    <p>{{ __('Choose your donation amount and payment method') }}</p>
                </div>

                <form id="donationForm" class="donation-form">
                    @csrf

                    <div class="amount-presets">
                        <button type="button" class="amount-preset" data-amount="50">$50</button>
                        <button type="button" class="amount-preset" data-amount="100">$100</button>
                        <button type="button" class="amount-preset" data-amount="250">$250</button>
                        <button type="button" class="amount-preset" data-amount="500">$500</button>
                        <button type="button" class="amount-preset custom" data-amount="custom">
                            <i class="fas fa-pen"></i> {{ __('Custom') }}
                        </button>
                    </div>

                    <div class="form-group custom-amount-group" style="display: none;">
                        <label for="custom_amount">{{ __('Enter Amount (USD)') }}</label>
                        <input type="number" name="amount" id="custom_amount" class="form-control" min="10" max="10000"
                            step="10" placeholder="50">
                    </div>
                    <input type="hidden" name="amount" id="selected_amount">

                    <div class="form-group">
                        <label for="payment_method">{{ __('Payment Method') }}</label>
                        <div class="payment-methods">
                            <div class="payment-method" data-method="credit_card">
                                <i class="fas fa-credit-card"></i>
                                <span>{{ __('Credit Card') }}</span>
                                <div class="method-check"><i class="far fa-circle"></i></div>
                            </div>
                            <div class="payment-method" data-method="bank_transfer">
                                <i class="fas fa-university"></i>
                                <span>{{ __('Bank Transfer') }}</span>
                                <div class="method-check"><i class="far fa-circle"></i></div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="selected_payment_method">
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>{{ __('After submitting, our team will contact you within 24 hours to complete the donation process.') }}
                        </p>
                    </div>

                    <button type="submit" class="btn-donate" id="submitDonationBtn">
                        <span class="btn-text">{{ __('Proceed to Donation') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </form>
            </div>

            <!-- Impact & Stats -->
            <div class="impact-card animate-slide-in-right">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Your Impact') }}</h3>
                </div>

                <div class="impact-stats">
                    <div class="impact-stat">
                        <div class="stat-number">$50</div>
                        <div class="stat-label">{{ __('Supports 1 therapy session') }}</div>
                    </div>
                    <div class="impact-stat">
                        <div class="stat-number">$200</div>
                        <div class="stat-label">{{ __('Supports 1 month of care') }}</div>
                    </div>
                    <div class="impact-stat">
                        <div class="stat-number">$500</div>
                        <div class="stat-label">{{ __('Supports 5 patients') }}</div>
                    </div>
                </div>

                @if($isDonor)
                    <div class="donor-stats">
                        <h4><i class="fas fa-user-check"></i> {{ __('Your Donation History') }}</h4>
                        <div class="stat-row">
                            <span>{{ __('Total Donated') }}</span>
                            <strong>${{ number_format($stats['total_donated'], 2) }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>{{ __('Users Supported') }}</span>
                            <strong>{{ number_format($stats['users_supported']) }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>{{ __('Transactions') }}</span>
                            <strong>{{ number_format($stats['total_transactions']) }}</strong>
                        </div>
                    </div>
                @endif

                <div class="testimonial">
                    <i class="fas fa-quote-right"></i>
                    <p>{{ __('Being a donor on Tamman has been incredibly rewarding. Knowing that my contribution helps someone get the mental health support they need is priceless.') }}
                    </p>
                    <div class="testimonial-author">
                        <strong>{{ __('Ahmed K.') }}</strong>
                        <span>{{ __('Monthly Donor') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation History Section -->
        @if($isDonor || ($donationHistory['received'] && $donationHistory['received']->count() > 0))
            <div class="donation-history-section animate-fade-in">
                <div class="history-header">
                    <h3><i class="fas fa-history"></i> {{ __('Donation History') }}</h3>
                    <div class="history-tabs">
                        @if($isDonor && $donationHistory['given']->count() > 0)
                            <button class="history-tab active" data-tab="given">{{ __('Donations Given') }}</button>
                        @endif
                        @if($donationHistory['received'] && $donationHistory['received']->count() > 0)
                            <button class="history-tab {{ $isDonor ? '' : 'active' }}"
                                data-tab="received">{{ __('Donations Received') }}</button>
                        @endif
                    </div>
                </div>

                <!-- Donations Given Tab (للمتبرع فقط) -->
                @if($isDonor && $donationHistory['given']->count() > 0)
                    <div class="history-tab-content active" id="tab-given">
                        <div class="history-cards">
                            @foreach($donationHistory['given'] as $donation)
                                <div class="history-card">
                                    <div class="card-header-history">
                                        <div class="donation-amount">${{ number_format($donation['amount'], 2) }}</div>
                                        <div class="donation-status">
                                            <span class="status-badge status-{{ $donation['status'] }}">
                                                @if($donation['status'] == 'allocated' && $donation['total_allocated'] == 0)
                                                    {{ __('Approved - Awaiting Distribution') }}
                                                @elseif($donation['status'] == 'allocated' && $donation['total_allocated'] > 0)
                                                    {{ __('Partially Distributed') }}
                                                @elseif($donation['status'] == 'allocated' && $donation['remaining'] == 0)
                                                    {{ __('Fully Distributed') }}
                                                @else
                                                    {{ $donation['status_text'] }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body-history">
                                        <div class="donation-date">
                                            <i class="fas fa-calendar-alt"></i> {{ $donation['date'] }}
                                        </div>

                                        <div class="donation-summary">
                                            <div class="summary-row">
                                                <span>{{ __('Total Donated') }}:</span>
                                                <strong>${{ number_format($donation['amount'], 2) }}</strong>
                                            </div>
                                            <div class="summary-row">
                                                <span>{{ __('Total Distributed') }}:</span>
                                                <strong>${{ number_format($donation['total_allocated'], 2) }}</strong>
                                            </div>
                                            <div class="summary-row">
                                                <span>{{ __('Patients Helped') }}:</span>
                                                <strong>{{ $donation['recipients_count'] }} {{ __('patients') }}</strong>
                                            </div>
                                            @if($donation['remaining'] > 0)
                                                <div class="summary-row remaining">
                                                    <span>{{ __('Remaining Balance') }}:</span>
                                                    <strong class="text-warning">${{ number_format($donation['remaining'], 2) }}</strong>
                                                </div>
                                            @endif
                                        </div>

                                        @if($donation['status'] == 'pending')
                                            <div class="pending-badge">
                                                <i class="fas fa-hourglass-half"></i>
                                                {{ __('Your donation is pending approval. Our team will contact you soon.') }}
                                            </div>
                                        @elseif($donation['remaining'] == 0 && $donation['total_allocated'] > 0)
                                            <div class="fully-allocated-badge">
                                                <i class="fas fa-check-circle"></i>
                                                {{ __('Thank you! Your donation has been fully distributed to patients in need.') }}
                                            </div>
                                        @elseif($donation['total_allocated'] == 0 && $donation['status'] == 'allocated')
                                            <div class="pending-distribution-badge">
                                                <i class="fas fa-clock"></i>
                                                {{ __('Your donation is approved and awaiting distribution to patients.') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Donations Received Tab (للمريض) -->
                @if($donationHistory['received'] && $donationHistory['received']->count() > 0)
                    <div class="history-tab-content {{ $isDonor ? '' : 'active' }}" id="tab-received">
                        <div class="history-cards">
                            @foreach($donationHistory['received'] as $donation)
                                <div class="history-card received">
                                    <div class="card-header-history">
                                        <div class="donation-amount">${{ number_format($donation['amount'], 2) }}</div>
                                        <div class="donation-badge">
                                            <i class="fas fa-gift"></i> {{ __('Received') }}
                                        </div>
                                    </div>
                                    <div class="card-body-history">
                                        <div class="donation-date">
                                            <i class="fas fa-calendar-alt"></i> {{ $donation['date'] }}
                                        </div>
                                        <div class="donor-info">
                                            <i class="fas fa-hand-holding-heart"></i>
                                            <span>{{ __('From') }}: <strong>{{ $donation['donor_name'] }}</strong></span>
                                        </div>
                                        <div class="donation-message">
                                            <i class="fas fa-heart" style="color: #ef4444;"></i>
                                            <p>{{ __('This donation has been added to your credit balance.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if((!$isDonor || $donationHistory['given']->count() == 0) && (!$donationHistory['received'] || $donationHistory['received']->count() == 0))
                    <div class="empty-history">
                        <i class="fas fa-hand-holding-heart"></i>
                        <p>{{ __('No donation history yet.') }}</p>
                        @if(!$isDonor)
                            <button class="btn-donate-small"
                                onclick="document.querySelector('.donate-card').scrollIntoView({behavior: 'smooth'})">
                                {{ __('Make Your First Donation') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .donate-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            .donate-header {
                text-align: center;
                margin-bottom: 40px;
            }

            .donate-header h1 {
                font-size: 2rem;
                color: #1f2937;
                margin-bottom: 10px;
            }

            .donate-header p {
                color: #6b7280;
            }

            .donate-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 30px;
                margin-bottom: 40px;
            }

            .donate-card,
            .impact-card {
                background: white;
                border-radius: 24px;
                padding: 30px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .card-header {
                margin-bottom: 25px;
            }

            .card-header h3 {
                font-size: 1.3rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .card-header p {
                color: #6b7280;
                font-size: 0.85rem;
            }

            .amount-presets {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 20px;
            }

            .amount-preset {
                padding: 10px 20px;
                background: #f3f4f6;
                border: 2px solid #e5e7eb;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .amount-preset:hover {
                border-color: #c4b5fd;
                transform: translateY(-2px);
            }

            .amount-preset.selected {
                background: #7c3aed;
                border-color: #7c3aed;
                color: white;
            }

            .amount-preset.custom {
                background: #ede9fe;
                border-color: #ede9fe;
                color: #7c3aed;
            }

            .custom-amount-group {
                margin-bottom: 20px;
            }

            .payment-methods {
                display: flex;
                gap: 15px;
                margin-top: 10px;
            }

            .payment-method {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 12px;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
            }

            .payment-method:hover {
                border-color: #c4b5fd;
            }

            .payment-method.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .method-check {
                position: absolute;
                top: 8px;
                right: 12px;
            }

            .method-check i {
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

            .info-box {
                background: #fef3c7;
                padding: 15px;
                border-radius: 12px;
                display: flex;
                gap: 12px;
                margin: 20px 0;
            }

            .info-box i {
                color: #f59e0b;
                font-size: 1.2rem;
            }

            .info-box p {
                margin: 0;
                font-size: 0.8rem;
                color: #92400e;
            }

            .btn-donate {
                width: 100%;
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                border: none;
                padding: 14px;
                border-radius: 40px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-donate:hover {
                background: linear-gradient(135deg, #059669, #047857);
                transform: translateY(-2px);
            }

            .impact-stats {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-bottom: 25px;
                flex-wrap: wrap;
            }

            .impact-stat {
                text-align: center;
                flex: 1;
                padding: 15px;
                background: #f9fafb;
                border-radius: 16px;
            }

            .impact-stat .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                color: #7c3aed;
            }

            .impact-stat .stat-label {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .donor-stats {
                background: #f5f3ff;
                padding: 20px;
                border-radius: 16px;
                margin: 20px 0;
            }

            .donor-stats h4 {
                font-size: 0.9rem;
                margin-bottom: 15px;
                color: #1f2937;
            }

            .stat-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .stat-row:last-child {
                border-bottom: none;
            }

            .testimonial {
                background: #f9fafb;
                padding: 20px;
                border-radius: 16px;
                text-align: center;
            }

            .testimonial i {
                font-size: 1.5rem;
                color: #c4b5fd;
                margin-bottom: 10px;
            }

            .testimonial p {
                font-size: 0.85rem;
                color: #374151;
                line-height: 1.5;
                margin-bottom: 15px;
            }

            .testimonial-author strong {
                display: block;
                font-size: 0.8rem;
            }

            .testimonial-author span {
                font-size: 0.7rem;
                color: #6b7280;
            }

            /* Donation History Section */
            .donation-history-section {
                background: white;
                border-radius: 24px;
                padding: 30px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                margin-top: 30px;
            }

            .history-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid #e5e7eb;
            }

            .history-header h3 {
                font-size: 1.2rem;
                margin: 0;
                color: #1f2937;
            }

            .history-header h3 i {
                color: #7c3aed;
                margin-right: 8px;
            }

            .history-tabs {
                display: flex;
                gap: 10px;
            }

            .history-tab {
                padding: 8px 20px;
                background: #f3f4f6;
                border: none;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.8rem;
                font-weight: 500;
            }

            .history-tab.active {
                background: #7c3aed;
                color: white;
            }

            .history-tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
            }

            .history-tab-content.active {
                display: block;
            }

            .history-cards {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .history-card {
                background: #f9fafb;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                transition: all 0.3s ease;
            }

            .history-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .history-card.received {
                background: #f0fdf4;
                border-color: #d1fae5;
            }

            .card-header-history {
                padding: 16px 20px;
                background: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                border-bottom: 1px solid #e5e7eb;
            }

            .donation-amount {
                font-size: 1.3rem;
                font-weight: 700;
                color: #1f2937;
            }

            .status-badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .status-pending {
                background: #fef3c7;
                color: #d97706;
            }

            .status-allocated {
                background: #d1fae5;
                color: #065f46;
            }

            .donation-badge {
                background: #10b981;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .card-body-history {
                padding: 16px 20px;
            }

            .donation-date {
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 12px;
            }

            .donation-date i {
                margin-right: 5px;
            }

            .donation-summary {
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid #e5e7eb;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                font-size: 0.8rem;
                padding: 6px 0;
            }

            .summary-row.remaining {
                margin-top: 4px;
                padding-top: 6px;
                border-top: 1px dashed #e5e7eb;
            }

            .text-warning {
                color: #f59e0b;
            }

            .pending-badge,
            .fully-allocated-badge,
            .pending-distribution-badge {
                margin-top: 12px;
                padding: 8px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                width: 100%;
                justify-content: center;
            }

            .pending-badge {
                background: #fef3c7;
                color: #d97706;
            }

            .fully-allocated-badge {
                background: #d1fae5;
                color: #065f46;
            }

            .pending-distribution-badge {
                background: #e0e7ff;
                color: #3730a3;
            }

            .donor-info {
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 10px 0;
                font-size: 0.8rem;
            }

            .donor-info i {
                color: #7c3aed;
            }

            .donation-message {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 12px;
                padding: 10px;
                background: #fef2f2;
                border-radius: 12px;
            }

            .donation-message p {
                margin: 0;
                font-size: 0.7rem;
                color: #991b1b;
            }

            .empty-history {
                text-align: center;
                padding: 40px;
            }

            .empty-history i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-history p {
                color: #6b7280;
                margin-bottom: 15px;
            }

            .btn-donate-small {
                background: #10b981;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-donate-small:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.5s ease;
            }

            .animate-slide-in-left {
                animation: slideInLeft 0.5s ease;
            }

            .animate-slide-in-right {
                animation: slideInRight 0.5s ease;
            }

            @media (max-width: 768px) {
                .donate-grid {
                    grid-template-columns: 1fr;
                }

                .impact-stats {
                    flex-direction: column;
                }

                .amount-presets {
                    justify-content: center;
                }

                .history-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .card-header-history {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            body.rtl .method-check {
                right: auto;
                left: 12px;
            }

            body.rtl .history-header h3 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .donation-date i {
                margin-right: 0;
                margin-left: 5px;
            }

            body.rtl .donor-info i {
                margin-right: 0;
                margin-left: 8px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Amount Preset Selection
            let selectedAmount = null;

            document.querySelectorAll('.amount-preset').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');

                    const amount = this.dataset.amount;
                    const customGroup = document.querySelector('.custom-amount-group');

                    if (amount === 'custom') {
                        customGroup.style.display = 'block';
                        document.getElementById('selected_amount').value = '';
                        selectedAmount = null;
                    } else {
                        customGroup.style.display = 'none';
                        document.getElementById('selected_amount').value = amount;
                        selectedAmount = amount;
                    }
                });
            });

            // Custom amount input
            document.getElementById('custom_amount')?.addEventListener('input', function () {
                const value = parseFloat(this.value);
                if (!isNaN(value) && value >= 10) {
                    document.getElementById('selected_amount').value = value;
                    selectedAmount = value;
                }
            });

            // Payment Method Selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function () {
                    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('selected_payment_method').value = this.dataset.method;
                });
            });

            // Tab Switching in History
            document.querySelectorAll('.history-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    const tabId = this.dataset.tab;
                    document.querySelectorAll('.history-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.history-tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // Form Submit
            const donationForm = document.getElementById('donationForm');
            if (donationForm) {
                donationForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const amount = document.getElementById('selected_amount').value;
                    const paymentMethod = document.getElementById('selected_payment_method').value;

                    if (!amount || amount < 10) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Invalid Amount") }}',
                            text: '{{ __("Please select or enter a valid donation amount (minimum $10).") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    if (!paymentMethod) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Missing Information") }}',
                            text: '{{ __("Please select a payment method.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    const submitBtn = document.getElementById('submitDonationBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(donationForm);

                    try {
                        const response = await fetch('{{ route("donate.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Thank You!") }}',
                                text: data.message,
                                confirmButtonColor: '#10b981',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            donationForm.reset();
                            document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('selected'));
                            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                            document.querySelector('.custom-amount-group').style.display = 'none';

                            setTimeout(() => location.reload(), 3000);
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
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }
        </script>
    @endpush
@endsection