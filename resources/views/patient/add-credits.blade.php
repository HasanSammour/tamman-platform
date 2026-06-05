@extends('layouts.app')

@section('title', __('Add Credits') . ' - ' . __('Tamman'))

@section('page-title', __('Add Credits to Your Account'))

@section('content')
    <div class="credits-container">
        <div class="credits-header animate-fade-in">
            <h1><i class="fas fa-plus-circle"></i> {{ __('Add Credits') }}</h1>
            <p>{{ __('Add funds to your account to book therapy sessions') }}</p>
        </div>

        <div class="credits-grid">
            <!-- Add Credits Form -->
            <div class="credits-card animate-slide-in-left">
                <div class="card-header">
                    <h3><i class="fas fa-wallet"></i> {{ __('Add Funds') }}</h3>
                    <p>{{ __('Your current balance:') }} <strong
                            class="balance-amount">${{ number_format($stats['total_credits'], 2) }}</strong></p>
                </div>

                <form id="creditsForm" class="credits-form">
                    @csrf

                    <div class="amount-presets">
                        <button type="button" class="amount-preset" data-amount="20">$20</button>
                        <button type="button" class="amount-preset" data-amount="50">$50</button>
                        <button type="button" class="amount-preset" data-amount="100">$100</button>
                        <button type="button" class="amount-preset" data-amount="200">$200</button>
                        <button type="button" class="amount-preset custom" data-amount="custom">
                            <i class="fas fa-pen"></i> {{ __('Custom') }}
                        </button>
                    </div>

                    <div class="form-group custom-amount-group" style="display: none;">
                        <label for="custom_amount">{{ __('Enter Amount (USD)') }}</label>
                        <input type="number" name="amount" id="custom_amount" class="form-control" min="10" max="5000"
                            step="10" placeholder="50">
                        <small class="form-text">{{ __('Minimum: $10, Maximum: $5,000') }}</small>
                    </div>
                    <input type="hidden" name="amount" id="selected_amount">

                    <div class="form-group">
                        <label for="payment_method">{{ __('Payment Method') }}</label>
                        <div class="payment-methods">
                            <div class="payment-method" data-method="bank_transfer">
                                <i class="fas fa-university"></i>
                                <span>{{ __('Bank Transfer') }}</span>
                                <div class="method-check"><i class="far fa-circle"></i></div>
                            </div>
                            <div class="payment-method" data-method="cash">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>{{ __('Cash') }}</span>
                                <div class="method-check"><i class="far fa-circle"></i></div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="selected_payment_method">
                    </div>

                    <div class="form-group">
                        <label for="notes">{{ __('Additional Notes (Optional)') }}</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"
                            placeholder="{{ __('Any special instructions or notes for the admin...') }}"></textarea>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <p>{{ __('After submitting your request:') }}</p>
                            <ul>
                                <li>{{ __('Our team will contact you within 24 hours') }}</li>
                                <li>{{ __('Complete the payment using your chosen method') }}</li>
                                <li>{{ __('Credits will be added to your account after payment confirmation') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bank-info" style="display: none;">
                        <h4><i class="fas fa-university"></i> {{ __('Bank Account Information') }}</h4>
                        <div class="bank-details">
                            <div class="bank-row">
                                <span class="bank-label">{{ __('Bank Name') }}:</span>
                                <span class="bank-value">{{ __('Palestine Islamic Bank') }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">{{ __('Account Name') }}:</span>
                                <span class="bank-value">{{ __('Tamman Platform') }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">{{ __('Account Number') }}:</span>
                                <span class="bank-value">{{ __('1234-5678-9012-3456') }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">{{ __('IBAN') }}:</span>
                                <span class="bank-value">{{ __('PS60PALS123456789012345678901') }}</span>
                            </div>
                        </div>
                        <div class="copy-bank-info">
                            <button type="button" class="btn-copy-bank" onclick="copyBankInfo()">
                                <i class="fas fa-copy"></i> {{ __('Copy Bank Info') }}
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-request" id="submitCreditsBtn">
                        <span class="btn-text">{{ __('Submit Request') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </form>
            </div>

            <!-- Transaction History -->
            <div class="history-card animate-slide-in-right">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Transaction History') }}</h3>
                    <p>{{ __('Your credit requests and transactions') }}</p>
                </div>

                <div class="stats-row-summary">
                    <div class="stat-summary">
                        <span class="stat-label">{{ __('Current Balance') }}</span>
                        <span class="stat-value positive">${{ number_format($stats['total_credits'], 2) }}</span>
                    </div>
                    <div class="stat-summary">
                        <span class="stat-label">{{ __('Total Received') }}</span>
                        <span class="stat-value">${{ number_format($stats['total_received'], 2) }}</span>
                    </div>
                    <div class="stat-summary">
                        <span class="stat-label">{{ __('Total Used') }}</span>
                        <span class="stat-value">${{ number_format($stats['total_used'], 2) }}</span>
                    </div>
                </div>

                <div class="transactions-list" id="transactionsList">
                    @if($transactions->count() > 0)
                        @foreach($transactions as $transaction)
                            <div class="transaction-item">
                                <div class="transaction-icon">
                                    @if($transaction->status == 'allocated')
                                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                    @elseif($transaction->status == 'pending')
                                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                    @else
                                        <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                                    @endif
                                </div>
                                <div class="transaction-details">
                                    <div class="transaction-amount">+${{ number_format($transaction->amount, 2) }}</div>
                                    <div class="transaction-status {{ $transaction->status }}">
                                        {{ __(ucfirst($transaction->status)) }}
                                    </div>
                                    <div class="transaction-date">{{ $transaction->created_at->translatedFormat('M d, Y h:i A') }}
                                    </div>
                                </div>
                                <div class="transaction-meta">
                                    <span class="transaction-method">
                                        <i class="fas {{ $transaction->description ? 'fa-comment' : 'fa-credit-card' }}"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-transactions">
                            <i class="fas fa-receipt"></i>
                            <p>{{ __('No transactions yet') }}</p>
                            <p class="text-muted">{{ __('Your first credit request will appear here') }}</p>
                        </div>
                    @endif
                </div>

                @if($transactions->hasPages())
                    <div class="pagination-wrapper">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .credits-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            .credits-header {
                text-align: center;
                margin-bottom: 40px;
            }

            .credits-header h1 {
                font-size: 2rem;
                color: #1f2937;
                margin-bottom: 10px;
            }

            .credits-header p {
                color: #6b7280;
            }

            .credits-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }

            .credits-card,
            .history-card {
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

            .balance-amount {
                color: #7c3aed;
                font-size: 1.1rem;
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
                margin: 20px 0;
            }

            .info-box i {
                color: #f59e0b;
                font-size: 1.2rem;
                float: left;
                margin-right: 12px;
            }

            .info-box ul {
                margin: 8px 0 0 30px;
                font-size: 0.8rem;
                color: #92400e;
            }

            .info-box li {
                margin: 4px 0;
            }

            .bank-info {
                background: #f0fdf4;
                padding: 20px;
                border-radius: 16px;
                margin: 20px 0;
                border: 1px solid #bbf7d0;
            }

            .bank-info h4 {
                font-size: 0.9rem;
                margin-bottom: 15px;
                color: #166534;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .bank-details {
                margin-bottom: 15px;
            }

            .bank-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #dcfce7;
            }

            .bank-label {
                font-size: 0.75rem;
                color: #166534;
            }

            .bank-value {
                font-size: 0.75rem;
                font-weight: 500;
                color: #1f2937;
            }

            .btn-copy-bank {
                width: 100%;
                background: #166534;
                color: white;
                border: none;
                padding: 8px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-copy-bank:hover {
                background: #14532d;
                transform: translateY(-2px);
            }

            .btn-submit-request {
                width: 100%;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
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

            .btn-submit-request:hover {
                background: linear-gradient(135deg, #6d28d9, #5b21b6);
                transform: translateY(-2px);
            }

            /* Stats Summary */
            .stats-row-summary {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e5e7eb;
            }

            .stat-summary {
                flex: 1;
                text-align: center;
                padding: 10px;
                background: #f9fafb;
                border-radius: 12px;
            }

            .stat-summary .stat-label {
                display: block;
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .stat-summary .stat-value {
                display: block;
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .stat-summary .stat-value.positive {
                color: #10b981;
            }

            /* Transactions List */
            .transactions-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .transaction-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .transaction-icon {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .transaction-icon i {
                font-size: 1.2rem;
            }

            .transaction-details {
                flex: 1;
            }

            .transaction-amount {
                font-size: 0.9rem;
                font-weight: 700;
                color: #10b981;
            }

            .transaction-status {
                font-size: 0.65rem;
                padding: 2px 8px;
                border-radius: 20px;
                display: inline-block;
                margin: 4px 0;
            }

            .transaction-status.pending {
                background: #fef3c7;
                color: #d97706;
            }

            .transaction-status.allocated {
                background: #d1fae5;
                color: #065f46;
            }

            .transaction-status.used {
                background: #e5e7eb;
                color: #6b7280;
            }

            .transaction-status.expired {
                background: #fee2e2;
                color: #991b1b;
            }

            .transaction-date {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .transaction-meta {
                text-align: right;
            }

            .transaction-method i {
                color: #7c3aed;
            }

            .empty-transactions {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-transactions i {
                font-size: 2.5rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-transactions p {
                color: #6b7280;
                margin-bottom: 5px;
            }

            .text-muted {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .pagination-wrapper {
                margin-top: 20px;
                text-align: center;
            }

            .form-text {
                font-size: 0.7rem;
                color: #6b7280;
                margin-top: 5px;
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
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

            /* Responsive */
            @media (max-width: 992px) {
                .credits-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .credits-container {
                    padding: 15px;
                }

                .credits-card,
                .history-card {
                    padding: 20px;
                }

                .amount-presets {
                    justify-content: center;
                }

                .payment-methods {
                    flex-direction: column;
                }

                .stats-row-summary {
                    flex-direction: column;
                    gap: 10px;
                }

                .bank-row {
                    flex-direction: column;
                    gap: 4px;
                }
            }

            /* RTL Support */
            body.rtl .method-check {
                right: auto;
                left: 12px;
            }

            body.rtl .info-box i {
                float: right;
                margin-right: 0;
                margin-left: 12px;
            }

            body.rtl .transaction-meta {
                text-align: left;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let selectedAmount = null;

            // Amount Preset Selection
            document.querySelectorAll('.amount-preset').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');

                    const amount = this.dataset.amount;
                    const customGroup = document.querySelector('.custom-amount-group');
                    const bankInfo = document.querySelector('.bank-info');

                    if (amount === 'custom') {
                        customGroup.style.display = 'block';
                        bankInfo.style.display = 'none';
                        document.getElementById('selected_amount').value = '';
                        selectedAmount = null;
                    } else {
                        customGroup.style.display = 'none';
                        document.getElementById('selected_amount').value = amount;
                        selectedAmount = amount;
                        bankInfo.style.display = 'none';
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

                    const methodValue = this.dataset.method;
                    const bankInfo = document.querySelector('.bank-info');

                    document.getElementById('selected_payment_method').value = methodValue;

                    // Show bank info if bank transfer is selected
                    if (methodValue === 'bank_transfer') {
                        bankInfo.style.display = 'block';
                    } else {
                        bankInfo.style.display = 'none';
                    }
                });
            });

            // Copy Bank Info
            window.copyBankInfo = function () {
                const bankDetails = document.querySelector('.bank-details');
                if (bankDetails) {
                    const text = bankDetails.innerText;
                    navigator.clipboard.writeText(text);

                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Copied!") }}',
                        text: '{{ __("Bank information copied to clipboard") }}',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            };

            // Form Submit
            const creditsForm = document.getElementById('creditsForm');
            if (creditsForm) {
                creditsForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const amount = document.getElementById('selected_amount').value;
                    const paymentMethod = document.getElementById('selected_payment_method').value;
                    const notes = document.getElementById('notes')?.value || '';

                    if (!amount || amount < 10) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Invalid Amount") }}',
                            text: '{{ __("Please select or enter a valid amount (minimum $10).") }}',
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

                    const submitBtn = document.getElementById('submitCreditsBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(creditsForm);

                    try {
                        const response = await fetch('{{ route("patient.add-credits.request") }}', {
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
                                title: '{{ __("Request Submitted!") }}',
                                text: data.message,
                                confirmButtonColor: '#10b981',
                                timer: 3000,
                                showConfirmButton: false
                            });

                            // Reset form
                            creditsForm.reset();
                            document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('selected'));
                            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                            document.querySelector('.custom-amount-group').style.display = 'none';
                            document.querySelector('.bank-info').style.display = 'none';

                            // Reload page after 2 seconds
                            setTimeout(() => location.reload(), 2000);
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