{{-- resources/views/terms.blade.php --}}
@extends('layouts.guest')

@section('title', __('Terms and Conditions') . ' - ' . __('Tamman'))

@section('content')

<section class="legal-section">
    <div class="container">
        <div class="legal-header">
            <div class="legal-badge">
                <i class="fas fa-file-alt"></i>
                <span>{{ __('Legal') }}</span>
            </div>
            <h1>{{ __('Terms and Conditions') }}</h1>
            <p>{{ __('Last updated:') }} {{ now()->translatedFormat('j F Y') }}</p>
        </div>

        <div class="legal-content">
            <div class="legal-card">
                <h2><i class="fas fa-check-circle"></i> {{ __('1. Acceptance of Terms') }}</h2>
                <p>{{ __('By accessing or using Tamman platform, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our platform.') }}</p>

                <h2><i class="fas fa-user-md"></i> {{ __('2. User Accounts') }}</h2>
                <p>{{ __('To use our services, you must create an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.') }}</p>

                <h2><i class="fas fa-lock"></i> {{ __('3. Privacy and Data Protection') }}</h2>
                <p>{{ __('Your privacy is important to us. We collect and process personal data in accordance with our Privacy Policy. By using our platform, you consent to such collection and processing.') }}</p>

                <h2><i class="fas fa-stethoscope"></i> {{ __('4. Medical Disclaimer') }}</h2>
                <p>{{ __('Tamman provides mental health support services but is not a substitute for emergency medical care. If you are in crisis, please contact emergency services immediately.') }}</p>

                <h2><i class="fas fa-hand-holding-heart"></i> {{ __('5. User Conduct') }}</h2>
                <p>{{ __('You agree to use the platform respectfully and not to harass, abuse, or harm other users. Violation may result in account termination.') }}</p>

                <h2><i class="fas fa-ban"></i> {{ __('6. Prohibited Activities') }}</h2>
                <p>{{ __('You may not use the platform for any illegal activities, to distribute malware, to impersonate others, or to violate any applicable laws or regulations.') }}</p>

                <h2><i class="fas fa-gavel"></i> {{ __('7. Termination') }}</h2>
                <p>{{ __('We reserve the right to suspend or terminate your account for violation of these terms, providing false information, or professional misconduct.') }}</p>

                <h2><i class="fas fa-phone-alt"></i> {{ __('8. Contact Us') }}</h2>
                <p>{{ __('If you have any questions about these Terms, please contact us at:') }} <strong>support@tamman.ps</strong></p>
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