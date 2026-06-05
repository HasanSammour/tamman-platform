{{-- resources/views/contact.blade.php --}}
@extends('layouts.guest')

@section('title', __('Contact Us') . ' - ' . __('Tamman'))

@section('content')
    <div class="contact-page">
        <!-- Hero Section -->
        <div class="contact-hero">
            <div class="hero-badge animate-fade-in-down">
                <i class="fas fa-headset"></i>
                <span>{{ __('Get in Touch') }}</span>
            </div>
            <h1 class="animate-fade-in-up">{{ __('We\'d Love to Hear From You') }}</h1>
            <p class="animate-fade-in-up" style="animation-delay: 0.1s">
                {{ __('Whether you have a question about our services, need support, or want to give feedback, we\'re here to help.') }}
            </p>
        </div>

        <div class="contact-wrapper">
            <!-- Contact Cards Row -->
            <div class="contact-cards-row">
                <div class="contact-card animate-scale-in" style="animation-delay: 0.1s">
                    <div class="card-icon email">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>{{ __('Email Us') }}</h3>
                    <p>{{ __('Send us an email anytime') }}</p>
                    <div class="card-details">
                        <a href="mailto:support@tamman.ps" class="contact-email">support@tamman.ps</a>
                        <a href="mailto:info@tamman.ps" class="contact-email secondary">info@tamman.ps</a>
                    </div>
                    <div class="card-footer">
                        <span>{{ __('Response within 24h') }}</span>
                    </div>
                </div>

                <div class="contact-card animate-scale-in" style="animation-delay: 0.2s">
                    <div class="card-icon phone">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>{{ __('Call Us') }}</h3>
                    <p>{{ __('Talk to our support team') }}</p>
                    <div class="card-details">
                        <a href="tel:+97081234567" class="contact-phone ltr-text" dir="ltr">+970 8 123 4567</a>
                    </div>
                    <div class="card-footer">
                        <span>{{ __('Sat - Thu, 9 AM - 5 PM') }}</span>
                    </div>
                </div>

                <div class="contact-card animate-scale-in" style="animation-delay: 0.3s">
                    <div class="card-icon location">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>{{ __('Visit Us') }}</h3>
                    <p>{{ __('Come say hello at our office') }}</p>
                    <div class="card-details">
                        <p class="address">{{ __('Al-Rimal District, Gaza, Palestine') }}</p>
                    </div>
                    <div class="card-footer">
                        <button class="map-btn"
                            onclick="window.open('https://maps.google.com/?q=Gaza+Palestine', '_blank')">
                            <i class="fas fa-map-pin"></i> {{ __('View on Map') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="form-section">
                <div class="form-container">
                    <div class="form-header">
                        <h2>{{ __('Send us a message') }}</h2>
                        <p>{{ __('Fill out the form below and we\'ll get back to you shortly.') }}</p>
                    </div>

                    <form id="contactForm" class="contact-form">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">{{ __('Full Name') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="name" id="name" class="form-control" placeholder=" ">
                                    <div class="error-message" id="name-error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" id="email" class="form-control" placeholder=" ">
                                    <div class="error-message" id="email-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">{{ __('Subject') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder=" ">
                                <div class="error-message" id="subject-error"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">{{ __('Message') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-comment input-icon"></i>
                                <textarea name="message" id="message" class="form-control" rows="5"
                                    placeholder=" "></textarea>
                                <div class="error-message" id="message-error"></div>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">
                            <div class="btn-content btn-text">
                                <i class="fas fa-paper-plane"></i>
                                <span>{{ __('Send Message') }}</span>
                            </div>
                            <div class="btn-loader btn-spinner" style="display: none;">
                                <div class="loader-spinner"></div>
                                <span>{{ __('Sending...') }}</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Emergency Section with Lavender/Purple Theme -->
            <div class="emergency-section animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="emergency-toggle" id="emergencyToggle">
                    <div class="emergency-header">
                        <div class="emergency-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="emergency-title">
                            <h3>{{ __('Mental Health Emergency?') }}</h3>
                            <p>{{ __('24/7 Crisis Support Available') }}</p>
                        </div>
                        <div class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="emergency-content" id="emergencyContent">
                        <div class="emergency-message">
                            <i class="fas fa-shield-alt"></i>
                            <div class="message-text">
                                <strong>{{ __('If you are in crisis or thinking about harming yourself:') }}</strong>
                                <p>{{ __('Please seek immediate help. You are not alone.') }}</p>
                            </div>
                        </div>
                        <div class="emergency-helpline">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <span class="helpline-label">{{ __('24/7 Helpline') }}</span>
                                <span class="helpline-number ltr-text" dir="ltr">101</span>
                            </div>
                        </div>
                        <div class="emergency-resources">
                            <button class="resource-link" onclick="openModal('mentalHealthServices')">
                                <i class="fas fa-hospital-user"></i> {{ __('Find Mental Health Services') }}
                            </button>
                            <button class="resource-link" onclick="openModal('copingStrategies')">
                                <i class="fas fa-heart"></i> {{ __('Coping Strategies') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Mental Health Services -->
    <div id="modalMentalHealthServices" class="custom-modal">
        <div class="modal-overlay-custom" onclick="closeModal('mentalHealthServices')"></div>
        <div class="modal-container-custom">
            <div class="modal-header-custom">
                <h3><i class="fas fa-hospital-user"></i> {{ __('Mental Health Services in Gaza') }}</h3>
                <button class="modal-close-custom" onclick="closeModal('mentalHealthServices')">&times;</button>
            </div>
            <div class="modal-body-custom">
                <div class="service-category">
                    <h4><i class="fas fa-ambulance"></i> {{ __('Emergency & Crisis Services') }}</h4>
                    <ul>
                        <li><strong>{{ __('Emergency Hotline') }}:</strong> 101 ({{ __('24/7 Ambulance & Emergency') }})
                        </li>
                        <li><strong>{{ __('Palestinian Red Crescent') }}:</strong> 104 ({{ __('Medical Emergencies') }})
                        </li>
                        <li><strong>{{ __('Mental Health Helpline') }}:</strong> 161 ({{ __('Psychological Support') }})
                        </li>
                    </ul>
                </div>

                <div class="service-category">
                    <h4><i class="fas fa-hospital"></i> {{ __('Hospitals with Psychiatric Departments') }}</h4>
                    <ul>
                        <li><strong>{{ __('Al-Shifa Medical Complex') }}</strong> - {{ __('Psychiatric Department') }}<br>
                            <small>{{ __('Tel') }}: 08 283 4444 | {{ __('Location') }}:
                                {{ __('Al-Rimal District, Gaza City') }}</small>
                        </li>
                        <li><strong>{{ __('Al-Ahli Arab Hospital') }}</strong> - {{ __('Mental Health Unit') }}<br>
                            <small>{{ __('Tel') }}: 08 286 4242 | {{ __('Location') }}:
                                {{ __('Al-Zaytoun, Gaza City') }}</small>
                        </li>
                        <li><strong>{{ __('Indonesian Hospital') }}</strong> - {{ __('Psychiatric Services') }}<br>
                            <small>{{ __('Tel') }}: 08 267 8000 | {{ __('Location') }}:
                                {{ __('Beit Lahia, North Gaza') }}</small>
                        </li>
                        <li><strong>{{ __('European Gaza Hospital') }}</strong> - {{ __('Mental Health Department') }}<br>
                            <small>{{ __('Tel') }}: 08 268 7000 | {{ __('Location') }}: {{ __('Khan Younis') }}</small>
                        </li>
                    </ul>
                </div>

                <div class="service-category">
                    <h4><i class="fas fa-hand-holding-heart"></i> {{ __('Mental Health NGOs & Support Centers') }}</h4>
                    <ul>
                        <li><strong>{{ __('Gaza Community Mental Health Programme (GCMHP)') }}</strong><br>
                            <small>{{ __('Tel') }}: 08 282 3131 | {{ __('Location') }}:
                                {{ __('Al-Rimal, Gaza City') }}</small>
                        </li>
                        <li><strong>{{ __('Palestinian Counseling Center (PCC)') }}</strong><br>
                            <small>{{ __('Tel') }}: 08 283 7007 | {{ __('Location') }}:
                                {{ __('Al-Rimal, Gaza City') }}</small>
                        </li>
                        <li><strong>{{ __('Health Work Committees (HWC)') }}</strong><br>
                            <small>{{ __('Tel') }}: 08 282 4193 | {{ __('Location') }}:
                                {{ __('Nasr Area, Gaza City') }}</small>
                        </li>
                        <li><strong>{{ __('Mental Health Support - UNRWA') }}</strong><br>
                            <small>{{ __('Tel') }}: 08 288 2222 | {{ __('Location') }}:
                                {{ __('UNRWA Headquarters, Gaza City') }}</small>
                        </li>
                    </ul>
                </div>

                <div class="service-category">
                    <h4><i class="fas fa-chalkboard-teacher"></i> {{ __('Online & Telehealth Services') }}</h4>
                    <ul>
                        <li><strong>{{ __('Tamman Platform') }}</strong> - {{ __('Online Therapy Sessions') }}<br>
                            <small>{{ __('Available 24/7 via our website') }}</small>
                        </li>
                        <li><strong>{{ __('Sawa\'leh for Psychological Consultation') }}</strong><br>
                            <small>{{ __('Tel') }}: 08 284 4000 | {{ __('WhatsApp') }}: 059 123 4567</small>
                        </li>
                    </ul>
                </div>

                <div class="service-note">
                    <i class="fas fa-info-circle"></i>
                    <p>{{ __('Note: All services listed are available in Gaza. Please call ahead to confirm operating hours due to current conditions.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Coping Strategies -->
    <div id="modalCopingStrategies" class="custom-modal">
        <div class="modal-overlay-custom" onclick="closeModal('copingStrategies')"></div>
        <div class="modal-container-custom">
            <div class="modal-header-custom">
                <h3><i class="fas fa-heart"></i> {{ __('Coping Strategies for Mental Health') }}</h3>
                <button class="modal-close-custom" onclick="closeModal('copingStrategies')">&times;</button>
            </div>
            <div class="modal-body-custom">
                <div class="strategy-category">
                    <h4><i class="fas fa-lungs"></i> {{ __('Breathing Exercises') }}</h4>
                    <div class="strategy-content">
                        <p><strong>{{ __('4-7-8 Breathing Technique') }}:</strong></p>
                        <ul>
                            <li>{{ __('Inhale through your nose for 4 seconds') }}</li>
                            <li>{{ __('Hold your breath for 7 seconds') }}</li>
                            <li>{{ __('Exhale slowly through your mouth for 8 seconds') }}</li>
                            <li>{{ __('Repeat 4-5 times') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="strategy-category">
                    <h4><i class="fas fa-brain"></i> {{ __('Grounding Techniques (5-4-3-2-1 Method)') }}</h4>
                    <div class="strategy-content">
                        <ul>
                            <li><strong>5</strong> - {{ __('Things you can SEE around you') }}</li>
                            <li><strong>4</strong> - {{ __('Things you can TOUCH around you') }}</li>
                            <li><strong>3</strong> - {{ __('Things you can HEAR around you') }}</li>
                            <li><strong>2</strong> - {{ __('Things you can SMELL around you') }}</li>
                            <li><strong>1</strong> - {{ __('Thing you can TASTE') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="strategy-category">
                    <h4><i class="fas fa-journal-whills"></i> {{ __('Journaling & Reflection') }}</h4>
                    <div class="strategy-content">
                        <p>{{ __('Writing down your thoughts and feelings can help process emotions. Try these prompts:') }}
                        </p>
                        <ul>
                            <li>{{ __('"Today I am feeling..."') }}</li>
                            <li>{{ __('"Three things I am grateful for today are..."') }}</li>
                            <li>{{ __('"Something that made me feel calm today was..."') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="strategy-category">
                    <h4><i class="fas fa-spa"></i> {{ __('Physical & Relaxation Activities') }}</h4>
                    <div class="strategy-content">
                        <ul>
                            <li>{{ __('Take a 10-minute walk outdoors') }}</li>
                            <li>{{ __('Listen to calming music or nature sounds') }}</li>
                            <li>{{ __('Gentle stretching or light exercise') }}</li>
                            <li>{{ __('Drink a cup of herbal tea') }}</li>
                            <li>{{ __('Progressive muscle relaxation') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="strategy-category">
                    <h4><i class="fas fa-users"></i> {{ __('When to Seek Professional Help') }}</h4>
                    <div class="strategy-content">
                        <ul>
                            <li>{{ __('Feeling overwhelmed for more than 2 weeks') }}</li>
                            <li>{{ __('Difficulty sleeping or eating') }}</li>
                            <li>{{ __('Loss of interest in activities you once enjoyed') }}</li>
                            <li>{{ __('Thoughts of self-harm or suicide - Seek help IMMEDIATELY') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="emergency-reminder">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><strong>{{ __('Emergency') }}:</strong>
                        {{ __('If you are in immediate danger, call 101 or go to the nearest emergency room.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .contact-page {
                min-height: 100vh;
                background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            }

            /* Hero Section */
            .contact-hero {
                text-align: center;
                padding: 50px 20px 40px;
                max-width: 800px;
                margin: 0 auto;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: rgba(124, 58, 237, 0.1);
                padding: 6px 16px;
                border-radius: 40px;
                margin-bottom: 20px;
            }

            .hero-badge i {
                color: #7c3aed;
                font-size: 0.8rem;
            }

            .hero-badge span {
                color: #7c3aed;
                font-weight: 500;
                font-size: 0.8rem;
            }

            .contact-hero h1 {
                font-size: 2.5rem;
                font-weight: 700;
                background: linear-gradient(135deg, #1f2937, #4c1d95);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                margin-bottom: 15px;
            }

            .contact-hero p {
                font-size: 1rem;
                color: #6b7280;
                line-height: 1.5;
            }

            /* Wrapper */
            .contact-wrapper {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Contact Cards Row */
            .contact-cards-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
                margin-bottom: 50px;
            }

            .contact-card {
                background: white;
                border-radius: 20px;
                padding: 25px;
                text-align: center;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                position: relative;
                overflow: hidden;
            }

            .contact-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }

            .contact-card::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .contact-card:hover::after {
                transform: scaleX(1);
            }

            .card-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 18px;
            }

            .card-icon i {
                font-size: 1.6rem;
            }

            .card-icon.email {
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            }

            .card-icon.email i {
                color: #7c3aed;
            }

            .card-icon.phone {
                background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            }

            .card-icon.phone i {
                color: #10b981;
            }

            .card-icon.location {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
            }

            .card-icon.location i {
                color: #f59e0b;
            }

            .contact-card h3 {
                font-size: 1.2rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 6px;
            }

            .contact-card>p {
                font-size: 0.8rem;
                color: #9ca3af;
                margin-bottom: 15px;
            }

            .card-details {
                margin-bottom: 15px;
            }

            .contact-email,
            .contact-phone,
            .address {
                display: block;
                font-size: 0.85rem;
                margin: 5px 0;
            }

            .contact-email {
                color: #7c3aed;
                text-decoration: none;
                font-weight: 500;
            }

            .contact-email.secondary {
                color: #6b7280;
                font-weight: normal;
            }

            .ltr-text {
                direction: ltr;
                display: inline-block;
                unicode-bidi: embed;
            }

            .contact-phone {
                color: #10b981;
                text-decoration: none;
                font-weight: 500;
                font-size: 1rem;
            }

            .address {
                color: #6b7280;
                font-size: 0.8rem;
            }

            .card-footer {
                border-top: 1px solid #f3f4f6;
                padding-top: 12px;
                margin-top: 5px;
            }

            .card-footer span {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .map-btn {
                background: none;
                border: none;
                color: #7c3aed;
                font-size: 0.75rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .map-btn:hover {
                text-decoration: underline;
            }

            /* Form Section */
            .form-section {
                max-width: 700px;
                margin: 0 auto 50px;
            }

            .form-container {
                background: white;
                border-radius: 28px;
                padding: 40px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            }

            .form-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .form-header h2 {
                font-size: 1.6rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 8px;
            }

            .form-header p {
                color: #6b7280;
                font-size: 0.85rem;
            }

            .contact-form {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .form-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .form-group label {
                font-size: 0.8rem;
                font-weight: 500;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .input-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.9rem;
                pointer-events: none;
            }

            textarea~.input-icon {
                top: 18px;
                transform: none;
            }

            .form-control {
                width: 100%;
                padding: 12px 16px 12px 42px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            textarea.form-control {
                padding-top: 14px;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.08);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.7rem;
                margin-top: 4px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            /* Submit Button */
            .submit-btn {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border: none;
                border-radius: 50px;
                padding: 14px 28px;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 10px;
                width: 100%;
            }

            .submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
            }

            .btn-content,
            .btn-loader {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .loader-spinner {
                width: 18px;
                height: 18px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Emergency Section - Lavender/Purple Theme */
            .emergency-section {
                max-width: 700px;
                margin: 0 auto;
            }

            .emergency-toggle {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 20px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(124, 58, 237, 0.2);
            }

            .emergency-header {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 20px 25px;
            }

            .emergency-icon i {
                font-size: 1.8rem;
                color: #fbbf24;
            }

            .emergency-title {
                flex: 1;
            }

            .emergency-title h3 {
                color: white;
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 3px;
            }

            .emergency-title p {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.7rem;
            }

            .toggle-icon i {
                color: rgba(255, 255, 255, 0.8);
                font-size: 1rem;
                transition: transform 0.3s ease;
            }

            .emergency-toggle.open .toggle-icon i {
                transform: rotate(180deg);
            }

            .emergency-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease;
                padding: 0 25px;
            }

            .emergency-toggle.open .emergency-content {
                max-height: 500px;
                padding: 0 25px 20px 25px;
            }

            .emergency-message {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                background: rgba(255, 255, 255, 0.15);
                padding: 15px;
                border-radius: 14px;
                margin-bottom: 15px;
            }

            .emergency-message i {
                color: #fbbf24;
                font-size: 1.2rem;
                margin-top: 2px;
            }

            .message-text strong {
                color: white;
                font-size: 0.85rem;
                display: block;
                margin-bottom: 4px;
            }

            .message-text p {
                color: rgba(255, 255, 255, 0.85);
                font-size: 0.75rem;
            }

            .emergency-helpline {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 14px;
                padding: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 15px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .emergency-helpline i {
                color: #fbbf24;
                font-size: 1.5rem;
            }

            .emergency-helpline div {
                flex: 1;
            }

            .helpline-label {
                display: block;
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.7rem;
            }

            .helpline-number {
                display: block;
                color: white;
                font-size: 1.3rem;
                font-weight: 700;
            }

            .emergency-resources {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .resource-link {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: rgba(255, 255, 255, 0.1);
                padding: 10px;
                border-radius: 12px;
                color: rgba(255, 255, 255, 0.9);
                text-decoration: none;
                font-size: 0.75rem;
                transition: all 0.3s ease;
                cursor: pointer;
                border: none;
                font-family: inherit;
            }

            .resource-link:hover {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                transform: translateY(-2px);
            }

            /* Custom Modal Styles */
            .custom-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
            }

            .custom-modal.active {
                display: block;
            }

            .modal-overlay-custom {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
            }

            .modal-container-custom {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 700px;
                max-height: 85vh;
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
                animation: modalFadeIn 0.3s ease;
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -48%);
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }

            .modal-header-custom {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 25px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .modal-header-custom h3 {
                margin: 0;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .modal-close-custom {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                transition: transform 0.3s ease;
            }

            .modal-close-custom:hover {
                transform: scale(1.1);
            }

            .modal-body-custom {
                padding: 25px;
                max-height: calc(85vh - 70px);
                overflow-y: auto;
            }

            /* Service Categories in Modal */
            .service-category,
            .strategy-category {
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 1px solid #e5e7eb;
            }

            .service-category:last-child,
            .strategy-category:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .service-category h4,
            .strategy-category h4 {
                color: #7c3aed;
                font-size: 1rem;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .service-category ul,
            .strategy-category ul {
                margin: 0;
                padding-left: 20px;
            }

            .service-category li,
            .strategy-category li {
                margin-bottom: 10px;
                color: #4b5563;
                font-size: 0.85rem;
                line-height: 1.5;
            }

            .service-category li strong {
                color: #1f2937;
            }

            .service-category small {
                color: #9ca3af;
                font-size: 0.7rem;
            }

            .service-note {
                background: #fef3c7;
                padding: 12px 15px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 20px;
            }

            .service-note i {
                color: #f59e0b;
                font-size: 1.1rem;
            }

            .service-note p {
                margin: 0;
                font-size: 0.75rem;
                color: #92400e;
            }

            .strategy-content {
                padding-left: 10px;
            }

            .strategy-content p {
                margin-bottom: 10px;
                color: #374151;
                font-size: 0.85rem;
            }

            .emergency-reminder {
                background: #fee2e2;
                padding: 15px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 12px;
                margin-top: 20px;
            }

            .emergency-reminder i {
                color: #ef4444;
                font-size: 1.2rem;
            }

            .emergency-reminder p {
                margin: 0;
                font-size: 0.8rem;
                color: #991b1b;
            }

            /* RTL Fix */
            body.rtl .ltr-text {
                direction: ltr;
                display: inline-block;
            }

            body.rtl .service-category ul,
            body.rtl .strategy-category ul {
                padding-left: 0;
                padding-right: 20px;
            }

            /* Animations */
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .animate-fade-in-down {
                animation: fadeInDown 0.6s ease forwards;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease forwards;
            }

            .animate-scale-in {
                animation: scaleIn 0.5s ease forwards;
                opacity: 0;
            }

            /* Responsive */
            @media (max-width: 900px) {
                .contact-cards-row {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 15px;
                }

                .contact-card {
                    padding: 20px 15px;
                }

                .card-icon {
                    width: 50px;
                    height: 50px;
                }

                .card-icon i {
                    font-size: 1.3rem;
                }

                .contact-card h3 {
                    font-size: 1rem;
                }
            }

            @media (max-width: 768px) {
                .contact-hero {
                    padding: 40px 20px 30px;
                }

                .contact-hero h1 {
                    font-size: 1.8rem;
                }

                .contact-cards-row {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .contact-card {
                    display: flex;
                    align-items: center;
                    text-align: left;
                    gap: 20px;
                    padding: 20px;
                }

                .contact-card>p {
                    display: none;
                }

                .card-icon {
                    margin: 0;
                }

                .card-details {
                    flex: 1;
                    margin-bottom: 0;
                }

                .card-footer {
                    border-top: none;
                    padding-top: 0;
                    margin-top: 0;
                }

                .form-row {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .form-container {
                    padding: 30px 25px;
                }

                .form-header h2 {
                    font-size: 1.3rem;
                }

                .emergency-header {
                    padding: 15px 20px;
                }

                .emergency-icon i {
                    font-size: 1.5rem;
                }

                .emergency-title h3 {
                    font-size: 0.9rem;
                }

                .modal-container-custom {
                    width: 95%;
                }

                .modal-header-custom h3 {
                    font-size: 1rem;
                }
            }

            @media (max-width: 480px) {
                .contact-card {
                    flex-wrap: wrap;
                    text-align: center;
                    justify-content: center;
                }

                .card-icon {
                    margin: 0 auto;
                }

                .card-details {
                    text-align: center;
                    width: 100%;
                }

                .emergency-resources {
                    flex-direction: column;
                }

                .modal-body-custom {
                    padding: 15px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Emergency Toggle
            const emergencyToggle = document.getElementById('emergencyToggle');

            emergencyToggle.addEventListener('click', () => {
                emergencyToggle.classList.toggle('open');
            });

            // Modal Functions
            function openModal(modalId) {
                const modal = document.getElementById(`modal${modalId.charAt(0).toUpperCase() + modalId.slice(1)}`);
                if (modal) {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(`modal${modalId.charAt(0).toUpperCase() + modalId.slice(1)}`);
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.custom-modal.active').forEach(modal => {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }
            });

            // Form Submission
            const contactForm = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnContent = submitBtn.querySelector('.btn-content');
            const btnLoader = submitBtn.querySelector('.btn-loader');

            // Clear error on input
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function () {
                    this.classList.remove('is-invalid');
                    const errorDiv = document.getElementById(this.id + '-error');
                    if (errorDiv) {
                        errorDiv.classList.remove('show');
                        errorDiv.textContent = '';
                    }
                });
            });

            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => {
                    el.classList.remove('show');
                    el.textContent = '';
                });
                document.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Show loading state
                btnContent.style.display = 'none';
                btnLoader.style.display = 'flex';
                submitBtn.disabled = true;

                const formData = new FormData(contactForm);

                try {
                    const response = await fetch('{{ route("contact.send") }}', {
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
                            title: '{{ __("Message Sent!") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937',
                            iconColor: '#10b981',
                            confirmButtonText: '{{ __("Great!") }}'
                        });
                        contactForm.reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937'
                        });
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        for (const [field, messages] of Object.entries(errors)) {
                            const errorDiv = document.getElementById(`${field}-error`);
                            const input = document.getElementById(field);
                            if (errorDiv) {
                                errorDiv.textContent = messages[0];
                                errorDiv.classList.add('show');
                            }
                            if (input) {
                                input.classList.add('is-invalid');
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Validation Error") }}',
                            text: '{{ __("Please check the form for errors.") }}',
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed',
                            background: '#fff',
                            color: '#1f2937'
                        });
                    }
                } finally {
                    btnContent.style.display = 'flex';
                    btnLoader.style.display = 'none';
                    submitBtn.disabled = false;
                }
            });
        </script>
    @endpush

@endsection