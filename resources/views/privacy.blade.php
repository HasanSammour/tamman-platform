{{-- resources/views/privacy.blade.php --}}
@extends('layouts.guest')

@section('title', __('Privacy Policy') . ' - ' . __('Tamman'))

@section('content')

<section class="legal-section">
    <div class="container">
        <div class="legal-header">
            <div class="legal-badge">
                <i class="fas fa-shield-alt"></i>
                <span>{{ __('Privacy') }}</span>
            </div>
            <h1>{{ __('Privacy Policy') }}</h1>
            <p>{{ __('Last updated:') }} {{ now()->translatedFormat('j F Y') }}</p>
        </div>

        <div class="legal-content">
            <div class="legal-card">
                <h2><i class="fas fa-database"></i> {{ __('1. Information We Collect') }}</h2>
                <p>{{ __('We collect personal information including name, email address, phone number, date of birth, gender, and professional credentials (for specialists). We also collect usage data, session information, and communication content.') }}</p>

                <h2><i class="fas fa-chart-line"></i> {{ __('2. How We Use Your Information') }}</h2>
                <p>{{ __('Your information is used to provide and improve our services, to communicate with you, to process payments, to ensure platform security, and to comply with legal obligations.') }}</p>

                <h2><i class="fas fa-shield-alt"></i> {{ __('3. Data Security') }}</h2>
                <p>{{ __('We implement industry-standard security measures including encryption, secure servers, access controls, and regular security audits to protect your data from unauthorized access.') }}</p>

                <h2><i class="fas fa-users"></i> {{ __('4. Data Sharing') }}</h2>
                <p>{{ __('We do not sell your personal data. We may share data with your consent, for legal compliance, with service providers, or in aggregated anonymized form for research purposes.') }}</p>

                <h2><i class="fas fa-trash-alt"></i> {{ __('5. Data Retention') }}</h2>
                <p>{{ __('We retain your data as long as your account is active. After account termination, data is retained for legal compliance for up to 5 years. You may request data deletion where legally permissible.') }}</p>

                <h2><i class="fas fa-user-check"></i> {{ __('6. Your Rights') }}</h2>
                <p>{{ __('You have the right to access, correct, delete, or port your personal data. You may also withdraw consent or object to processing. Contact support@tamman.ps to exercise these rights.') }}</p>

                <h2><i class="fas fa-cookie-bite"></i> {{ __('7. Cookies') }}</h2>
                <p>{{ __('We use cookies and similar technologies to enhance your experience, analyze usage, and personalize content. You can control cookie settings through your browser.') }}</p>

                <h2><i class="fas fa-child"></i> {{ __('8. Children\'s Privacy') }}</h2>
                <p>{{ __('Our services are not directed to individuals under 18. We do not knowingly collect data from minors. If you believe a minor has provided us with data, please contact us.') }}</p>

                <h2><i class="fas fa-envelope"></i> {{ __('9. Contact Us') }}</h2>
                <p>{{ __('For privacy-related questions or concerns, please contact us at:') }} <strong>privacy@tamman.ps</strong></p>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .legal-section {
        padding: 60px 0 80px;
        background: #f9fafb;
        min-height: calc(100vh - 200px);
    }
    
    .legal-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .legal-badge {
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
    
    .legal-header h1 {
        font-size: 2.2rem;
        margin-bottom: 10px;
        color: #1f2937;
    }
    
    .legal-header p {
        color: #6b7280;
    }
    
    .legal-card {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .legal-card h2 {
        font-size: 1.25rem;
        margin: 30px 0 15px 0;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .legal-card h2:first-of-type {
        margin-top: 0;
    }
    
    .legal-card h2 i {
        color: #7c3aed;
        font-size: 1.2rem;
    }
    
    .legal-card p {
        color: #4b5563;
        line-height: 1.7;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .legal-section {
            padding: 40px 0 60px;
        }
        
        .legal-header h1 {
            font-size: 1.6rem;
        }
        
        .legal-card {
            padding: 25px;
        }
        
        .legal-card h2 {
            font-size: 1.1rem;
        }
    }
</style>
@endpush
@endsection