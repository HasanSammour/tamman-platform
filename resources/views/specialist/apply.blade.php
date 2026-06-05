{{-- resources/views/specialist/apply.blade.php --}}
@extends('layouts.guest')

@section('title', __('Become a Specialist - Tamman'))

@section('content')

    <!-- Hero Section with Animation -->
    <section class="apply-hero">
        <div class="container">
            <div class="apply-hero-content animate-fade-in">
                <div class="hero-badge">
                    <i class="fas fa-user-md"></i>
                    <span>{{ __('Join Our Team') }}</span>
                </div>
                <h1>{{ __('Become a') }} <span class="gradient-text">{{ __('Specialist') }}</span></h1>
                <p>{{ __('Join our network of licensed mental health professionals and help make a difference') }}</p>
            </div>
        </div>
    </section>

    <!-- Multi-Step Form -->
    <section class="apply-section">
        <div class="container">
            <div class="apply-card animate-slide-up">

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="progress-step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-name">{{ __('Personal') }}</div>
                        <div class="step-icon"><i class="fas fa-user"></i></div>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-name">{{ __('Professional') }}</div>
                        <div class="step-icon"><i class="fas fa-briefcase"></i></div>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-name">{{ __('Documents') }}</div>
                        <div class="step-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    </div>
                    <div class="progress-step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-name">{{ __('Review') }}</div>
                        <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="progress-line">
                    <div class="progress-line-fill"></div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('specialist.apply.store') }}" enctype="multipart/form-data"
                    id="multiStepForm">
                    @csrf

                    <!-- STEP 1: Personal Information -->
                    <div class="form-step active" id="step1">
                        <div class="step-header">
                            <div class="step-header-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h2>{{ __('Personal Information') }}</h2>
                            <p>{{ __('Tell us about yourself') }}</p>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Full Name') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="{{ __('e.g., Dr. Ahmed Mohamed') }}" value="{{ old('name') }}">
                            </div>
                            <div class="error-message" id="nameError"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('Email Address') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="{{ __('doctor@example.com') }}" value="{{ old('email') }}">
                                </div>
                                <div class="error-message" id="emailError"></div>
                                @if($errors->has('email'))
                                    <div class="error-message show">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>{{ __('Phone Number') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="tel" name="phone" id="phone" class="form-control"
                                        placeholder="{{ __('+970 5X XXX XXXX') }}" value="{{ old('phone') }}">
                                </div>
                                <div class="error-message" id="phoneError"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('Password') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="{{ __('At least 8 characters') }}">
                                    <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;"
                                        onclick="togglePassword(this)"></i>
                                </div>
                                <div class="error-message" id="passwordError"></div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Confirm Password') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password_confirmation" id="passwordConfirmation"
                                        class="form-control" placeholder="{{ __('Repeat your password') }}">
                                    <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;"
                                        onclick="togglePassword(this)"></i>
                                </div>
                                <div class="error-message" id="passwordConfirmationError"></div>
                            </div>
                        </div>

                        <div class="step-buttons">
                            <button type="button" class="btn-next" data-next="2" id="continueToStep2">
                                {{ __('Continue') }} <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Professional Information -->
                    <div class="form-step" id="step2">
                        <div class="step-header">
                            <div class="step-header-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h2>{{ __('Professional Information') }}</h2>
                            <p>{{ __('Your professional background and qualifications') }}</p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('Specialization') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-stethoscope input-icon"></i>
                                    <select name="specialization" id="specialization" class="form-control">
                                        <option value="">{{ __('Select specialization') }}</option>
                                        <option value="علم النفس السريري" {{ old('specialization') == 'علم النفس السريري' ? 'selected' : '' }}>{{ __('Clinical Psychology') }}</option>
                                        <option value="علم النفس الإرشادي" {{ old('specialization') == 'علم النفس الإرشادي' ? 'selected' : '' }}>{{ __('Counseling Psychology') }}</option>
                                        <option value="الطب النفسي" {{ old('specialization') == 'الطب النفسي' ? 'selected' : '' }}>{{ __('Psychiatry') }}</option>
                                        <option value="العلاج السلوكي المعرفي" {{ old('specialization') == 'العلاج السلوكي المعرفي' ? 'selected' : '' }}>{{ __('CBT Therapy') }}</option>
                                        <option value="علاج الصدمات" {{ old('specialization') == 'علاج الصدمات' ? 'selected' : '' }}>{{ __('Trauma Therapy') }}</option>
                                        <option value="العلاج الأسري" {{ old('specialization') == 'العلاج الأسري' ? 'selected' : '' }}>{{ __('Family Therapy') }}</option>
                                        <option value="علم نفس الطفل" {{ old('specialization') == 'علم نفس الطفل' ? 'selected' : '' }}>{{ __('Child Psychology') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <div class="error-message" id="specializationError"></div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('License Number') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="license_number" id="licenseNumber" class="form-control"
                                        placeholder="{{ __('e.g., PSY-12345') }}" value="{{ old('license_number') }}">
                                </div>
                                <div class="error-message" id="licenseNumberError"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('Years of Experience') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <select name="experience_years" id="experienceYears" class="form-control">
                                        <option value="">{{ __('Select years') }}</option>
                                        @for($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}" {{ old('experience_years') == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? __('year') : __('years') }}
                                            </option>
                                        @endfor
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <div class="error-message" id="experienceYearsError"></div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Fee per Session (USD)') }} <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-dollar-sign input-icon"></i>
                                    <input type="number" name="consultation_fee" id="consultationFee" class="form-control"
                                        placeholder="{{ __('e.g., 50') }}" value="{{ old('consultation_fee') }}">
                                </div>
                                <div class="error-message" id="consultationFeeError"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Languages You Speak') }} <span class="required">*</span></label>
                            <div class="language-group" id="languageGroup">
                                @php
                                    $oldLanguages = old('languages', []);
                                    if (!is_array($oldLanguages) && $oldLanguages) {
                                        $oldLanguages = explode(', ', $oldLanguages);
                                    }
                                    if (!is_array($oldLanguages)) {
                                        $oldLanguages = [];
                                    }
                                @endphp
                                <label class="lang-option {{ in_array('العربية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="العربية" {{ in_array('العربية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('Arabic') }}</span>
                                </label>
                                <label class="lang-option {{ in_array('الإنجليزية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="الإنجليزية" {{ in_array('الإنجليزية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('English') }}</span>
                                </label>
                                <label class="lang-option {{ in_array('الفرنسية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="الفرنسية" {{ in_array('الفرنسية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('French') }}</span>
                                </label>
                                <label class="lang-option {{ in_array('الألمانية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="الألمانية" {{ in_array('الألمانية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('German') }}</span>
                                </label>
                                <label class="lang-option {{ in_array('الإسبانية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="الإسبانية" {{ in_array('الإسبانية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('Spanish') }}</span>
                                </label>
                                <label class="lang-option {{ in_array('العبرية', $oldLanguages) ? 'selected' : '' }}">
                                    <input type="checkbox" value="العبرية" {{ in_array('العبرية', $oldLanguages) ? 'checked' : '' }}> <span>{{ __('Hebrew') }}</span>
                                </label>
                            </div>
                            <input type="hidden" name="languages" id="languagesHidden" value="{{ old('languages') }}">
                            <div class="error-message" id="languagesError"></div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Qualifications & Education') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-graduation-cap input-icon textarea-icon"></i>
                                <textarea name="qualifications" id="qualifications" class="form-control" rows="3"
                                    placeholder="{{ __('e.g., PhD in Clinical Psychology - Cairo University, 2015') }}">{{ old('qualifications') }}</textarea>
                            </div>
                            <div class="error-message" id="qualificationsError"></div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Professional Bio') }} <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-file-alt input-icon textarea-icon"></i>
                                <textarea name="bio" id="bio" class="form-control" rows="4"
                                    placeholder="{{ __('Tell us about your experience, approach to therapy, and what makes you unique...') }}">{{ old('bio') }}</textarea>
                            </div>
                            <div class="error-message" id="bioError"></div>
                        </div>

                        <div class="step-buttons">
                            <button type="button" class="btn-prev" data-prev="1">
                                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                            </button>
                            <button type="button" class="btn-next" data-next="3">
                                {{ __('Continue') }} <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Upload Documents -->
                    <div class="form-step" id="step3">
                        <div class="step-header">
                            <div class="step-header-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h2>{{ __('Upload Documents') }}</h2>
                            <p>{{ __('Upload your professional documents for verification') }}</p>
                        </div>

                        <div class="upload-box" id="licenseBox">
                            <div class="upload-preview">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <h4>{{ __('Professional License') }} <span class="required">*</span></h4>
                            <p class="upload-hint">{{ __('PDF, JPG, or PNG (Max 5MB)') }}</p>
                            <div class="upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ __('Click to upload') }}</span>
                            </div>
                            <input type="file" name="license_file" id="licenseFile" class="file-input"
                                accept=".pdf,.jpg,.jpeg,.png" hidden>
                            <div class="uploaded-name" id="licenseFileName"></div>
                            <div class="error-message" id="licenseFileError"></div>
                        </div>

                        <div class="upload-box" id="certificateBox">
                            <div class="upload-preview">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h4>{{ __('Professional Certificate') }} <span class="required">*</span></h4>
                            <p class="upload-hint">{{ __('PDF, JPG, or PNG (Max 5MB)') }}</p>
                            <div class="upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ __('Click to upload') }}</span>
                            </div>
                            <input type="file" name="certificate_file" id="certificateFile" class="file-input"
                                accept=".pdf,.jpg,.jpeg,.png" hidden>
                            <div class="uploaded-name" id="certificateFileName"></div>
                            <div class="error-message" id="certificateFileError"></div>
                        </div>

                        <div class="step-buttons">
                            <button type="button" class="btn-prev" data-prev="2">
                                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                            </button>
                            <button type="button" class="btn-next" data-next="4">
                                {{ __('Review') }} <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: Review & Submit -->
                    <div class="form-step" id="step4">
                        <div class="step-header">
                            <div class="step-header-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>{{ __('Review Your Application') }}</h2>
                            <p>{{ __('Please review your information before submitting') }}</p>
                        </div>

                        <div class="review-summary" id="reviewSummary">
                            <!-- Dynamic summary -->
                        </div>

                        <div class="terms-box">
                            <label class="terms-checkbox">
                                <input type="checkbox" id="termsCheckbox" name="terms" value="1">
                                <span>{{ __('I confirm that all information provided is accurate and I agree to the') }}
                                    <a href="{{ route('terms') }}" target="_blank">{{ __('Terms and Conditions') }}</a>
                                    {{ __('and') }}
                                    <a href="{{ route('privacy') }}" target="_blank">{{ __('Privacy Policy') }}</a>
                                </span>
                            </label>
                            <div class="error-message" id="termsError"></div>
                        </div>

                        <div class="step-buttons">
                            <button type="button" class="btn-prev" data-prev="3">
                                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                            </button>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <span class="btn-text">{{ __('Submit Application') }}</span>
                                <span class="btn-spinner" style="display: none;"><i
                                        class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .apply-hero {
                text-align: center;
                padding: 60px 0 30px;
                background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
            }

            .hero-badge {
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

            .apply-hero h1 {
                font-size: 2.5rem;
                margin-bottom: 15px;
            }

            .apply-hero p {
                color: #6b7280;
                font-size: 1.1rem;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.05);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.6s ease;
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease;
            }

            .apply-section {
                padding: 0 0 80px;
            }

            .apply-card {
                max-width: 750px;
                margin: 0 auto;
                background: white;
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .progress-steps {
                display: flex;
                padding: 35px 35px 0;
                background: white;
            }

            .progress-step {
                flex: 1;
                text-align: center;
                position: relative;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .step-number {
                width: 45px;
                height: 45px;
                background: #f3f4f6;
                border: 2px solid #e5e7eb;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 10px;
                font-weight: 700;
                color: #9ca3af;
                transition: all 0.3s ease;
            }

            .progress-step.active .step-number {
                background: #7c3aed;
                border-color: #7c3aed;
                color: white;
                box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.2);
                animation: pulse 0.5s ease;
            }

            .progress-step.completed .step-number {
                background: #10b981;
                border-color: #10b981;
                color: white;
            }

            .step-name {
                font-size: 0.8rem;
                font-weight: 500;
                color: #9ca3af;
                transition: all 0.3s ease;
            }

            .step-icon {
                display: none;
            }

            .progress-step.active .step-name {
                color: #7c3aed;
            }

            .progress-step.completed .step-name {
                color: #10b981;
            }

            .progress-line {
                margin: 25px 35px 0;
                height: 4px;
                background: #f3f4f6;
                border-radius: 2px;
                overflow: hidden;
            }

            .progress-line-fill {
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
                border-radius: 2px;
                transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .form-step {
                display: none;
                padding: 35px;
                animation: fadeIn 0.4s ease;
            }

            .form-step.active {
                display: block;
            }

            .step-header {
                text-align: center;
                margin-bottom: 35px;
            }

            .step-header-icon {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px;
            }

            .step-header-icon i {
                font-size: 2rem;
                color: #7c3aed;
            }

            .step-header h2 {
                font-size: 1.5rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .step-header p {
                color: #6b7280;
                font-size: 0.875rem;
            }

            .form-group {
                margin-bottom: 22px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #374151;
                font-size: 0.875rem;
            }

            .required {
                color: #ef4444;
            }

            .input-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 1rem;
                transition: all 0.3s ease;
            }

            body.rtl .input-icon {
                left: auto;
                right: 15px;
            }

            body.rtl .form-control {
                padding-left: 16px;
                padding-right: 45px;
            }

            .textarea-icon {
                top: 18px;
                transform: none;
            }

            .select-icon {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
            }

            body.rtl .select-icon {
                right: auto;
                left: 15px;
            }

            .password-toggle {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #9ca3af;
                transition: all 0.3s ease;
                z-index: 2;
            }

            body.rtl .password-toggle {
                right: auto;
                left: 15px;
            }

            .password-toggle:hover {
                color: #7c3aed;
            }

            .form-control {
                width: 100%;
                padding: 12px 45px 12px 45px;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                font-size: 0.875rem;
                transition: all 0.3s ease;
                background: #f9fafb;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control.error {
                border-color: #ef4444;
                background: #fef2f2;
            }

            select.form-control {
                cursor: pointer;
                appearance: none;
                padding-right: 45px;
            }

            body.rtl select.form-control {
                padding-right: 15px;
                padding-left: 45px;
            }

            textarea.form-control {
                padding-top: 15px;
                resize: vertical;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.7rem;
                margin-top: 5px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            .language-group {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                padding: 10px 0;
            }

            .lang-option {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: #f3f4f6;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .lang-option:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .lang-option input {
                display: none;
            }

            .lang-option.selected {
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                color: #7c3aed;
                border: 1px solid #c4b5fd;
            }

            .upload-box {
                background: #f9fafb;
                border-radius: 20px;
                padding: 30px;
                text-align: center;
                margin-bottom: 25px;
                border: 2px dashed #e5e7eb;
                transition: all 0.3s ease;
            }

            .upload-box:hover {
                border-color: #c4b5fd;
                background: #f5f3ff;
            }

            .upload-box.has-file {
                border-color: #10b981;
                background: #f0fdf4;
            }

            .upload-preview i {
                font-size: 2.5rem;
                color: #7c3aed;
                margin-bottom: 15px;
            }

            .upload-box h4 {
                margin: 0 0 5px;
            }

            .upload-hint {
                font-size: 0.7rem;
                color: #9ca3af;
                margin-bottom: 20px;
            }

            .upload-area {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 12px 25px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 50px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.875rem;
                font-weight: 500;
            }

            .upload-area:hover {
                border-color: #7c3aed;
                background: #ede9fe;
                transform: translateY(-2px);
            }

            .uploaded-name {
                margin-top: 12px;
                font-size: 0.75rem;
                color: #10b981;
                display: none;
            }

            .uploaded-name.show {
                display: block;
            }

            .step-buttons {
                display: flex;
                justify-content: space-between;
                margin-top: 35px;
                padding-top: 25px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-next,
            .btn-prev,
            .btn-submit {
                padding: 14px 32px;
                border-radius: 50px;
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
            }

            .btn-next,
            .btn-submit {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .btn-next:hover,
            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
            }

            .btn-prev {
                background: transparent;
                border: 1px solid #e5e7eb;
                color: #4b5563;
            }

            .btn-prev:hover {
                border-color: #7c3aed;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            .btn-submit {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .btn-submit:hover {
                box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            }

            .btn-loading {
                position: relative;
                pointer-events: none;
                opacity: 0.7;
            }

            .btn-loading .btn-text {
                visibility: hidden;
            }

            .btn-loading .btn-spinner {
                display: inline-block !important;
            }

            .review-summary {
                background: #f9fafb;
                border-radius: 20px;
                padding: 25px;
                margin-bottom: 25px;
                max-height: 450px;
                overflow-y: auto;
            }

            .review-section {
                margin-bottom: 25px;
            }

            .review-section:last-child {
                margin-bottom: 0;
            }

            .review-section h4 {
                color: #374151;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #e5e7eb;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .review-section h4 i {
                color: #7c3aed;
            }

            .review-item {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .review-item:last-child {
                border-bottom: none;
            }

            .review-label {
                font-weight: 500;
                color: #6b7280;
            }

            .review-value {
                color: #374151;
                text-align: right;
                max-width: 60%;
                word-break: break-word;
            }

            body.rtl .review-value {
                text-align: left;
            }

            .terms-box {
                background: #fef3c7;
                border-radius: 16px;
                padding: 18px 22px;
                margin-bottom: 25px;
            }

            .terms-checkbox {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                cursor: pointer;
            }

            .terms-checkbox input {
                width: 18px;
                height: 18px;
                margin-top: 2px;
                cursor: pointer;
                accent-color: #7c3aed;
            }

            .terms-checkbox span {
                font-size: 0.875rem;
                color: #374151;
                line-height: 1.5;
            }

            .terms-checkbox a {
                color: #7c3aed;
                text-decoration: none;
                font-weight: 500;
            }

            .terms-checkbox a:hover {
                text-decoration: underline;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            @media (max-width: 768px) {
                .apply-hero h1 {
                    font-size: 1.8rem;
                }

                .progress-steps {
                    padding: 25px 20px 0;
                }

                .step-number {
                    width: 35px;
                    height: 35px;
                    font-size: 0.75rem;
                }

                .step-name {
                    font-size: 0.65rem;
                }

                .progress-line {
                    margin: 20px 20px 0;
                }

                .form-step {
                    padding: 25px 20px;
                }

                .form-row {
                    grid-template-columns: 1fr;
                    gap: 0;
                }

                .step-buttons {
                    flex-direction: column;
                    gap: 12px;
                }

                .step-buttons button {
                    width: 100%;
                }

                .language-group {
                    gap: 8px;
                }

                .lang-option {
                    padding: 8px 16px;
                    font-size: 0.75rem;
                }

                .review-item {
                    flex-direction: column;
                    gap: 5px;
                }

                .review-value {
                    text-align: left;
                    max-width: 100%;
                }

                .upload-box {
                    padding: 20px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Password toggle function
            function togglePassword(element) {
                var input = element.previousElementSibling;
                if (input && input.type === 'password') {
                    input.type = 'text';
                    element.classList.remove('fa-eye-slash');
                    element.classList.add('fa-eye');
                } else if (input) {
                    input.type = 'password';
                    element.classList.remove('fa-eye');
                    element.classList.add('fa-eye-slash');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                let currentStep = 1;
                let totalSteps = 4;
                let isCheckingEmail = false;

                const stepContainers = document.querySelectorAll('.form-step');
                const progressSteps = document.querySelectorAll('.progress-step');
                const progressFill = document.querySelector('.progress-line-fill');

                // ==================== FILE SIZE VALIDATION ====================
                const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

                function validateFileSize(file, fieldName) {
                    if (file && file.size > MAX_FILE_SIZE) {
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        showError(fieldName, `{{ __("File size exceeds 5MB. Current size:") }} ${sizeMB}MB`);
                        return false;
                    }
                    return true;
                }

                function validateStep3Files() {
                    let isValid = true;
                    clearError('licenseFile');
                    clearError('certificateFile');

                    const licenseFile = document.getElementById('licenseFile')?.files[0];
                    const certFile = document.getElementById('certificateFile')?.files[0];

                    if (!licenseFile) {
                        showError('licenseFile', '{{ __("Please upload your professional license") }}');
                        isValid = false;
                    } else if (!validateFileSize(licenseFile, 'licenseFile')) {
                        isValid = false;
                    }

                    if (!certFile) {
                        showError('certificateFile', '{{ __("Please upload your professional certificate") }}');
                        isValid = false;
                    } else if (!validateFileSize(certFile, 'certificateFile')) {
                        isValid = false;
                    }

                    return isValid;
                }

                // ==================== LANGUAGE SELECTION ====================
                const langOptions = document.querySelectorAll('.lang-option');
                const languagesHidden = document.getElementById('languagesHidden');

                function updateLanguagesValue() {
                    const selected = [];
                    document.querySelectorAll('.lang-option input:checked').forEach(cb => {
                        selected.push(cb.value);
                    });
                    if (languagesHidden) {
                        languagesHidden.value = selected.join(', ');
                    }
                }

                langOptions.forEach(opt => {
                    const checkbox = opt.querySelector('input');
                    if (checkbox) {
                        if (checkbox.checked) {
                            opt.classList.add('selected');
                        }
                        checkbox.addEventListener('change', function () {
                            if (this.checked) {
                                opt.classList.add('selected');
                            } else {
                                opt.classList.remove('selected');
                            }
                            updateLanguagesValue();
                        });
                    }
                });

                updateLanguagesValue();

                // ==================== FILE UPLOADS WITH SIZE VALIDATION ====================
                function setupFileUpload(boxId, inputId, nameId, fieldName) {
                    const box = document.getElementById(boxId);
                    if (!box) return;

                    const uploadArea = box.querySelector('.upload-area');
                    const fileInput = document.getElementById(inputId);
                    const fileNameSpan = document.getElementById(nameId);

                    if (uploadArea && fileInput) {
                        const newUploadArea = uploadArea.cloneNode(true);
                        uploadArea.parentNode.replaceChild(newUploadArea, uploadArea);

                        newUploadArea.addEventListener('click', function (e) {
                            e.preventDefault();
                            fileInput.click();
                        });

                        fileInput.addEventListener('change', function () {
                            // Clear previous error for this field
                            clearError(fieldName);

                            if (this.files && this.files[0]) {
                                const file = this.files[0];

                                // Validate file size
                                if (file.size > MAX_FILE_SIZE) {
                                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                                    showError(fieldName, `{{ __("File size exceeds 5MB. Current size:") }} ${sizeMB}MB`);
                                    this.value = ''; // Clear the file input
                                    fileNameSpan.classList.remove('show');
                                    box.classList.remove('has-file');
                                    return;
                                }

                                fileNameSpan.textContent = '✓ ' + file.name;
                                fileNameSpan.classList.add('show');
                                box.classList.add('has-file');
                            } else {
                                fileNameSpan.classList.remove('show');
                                box.classList.remove('has-file');
                            }
                        });
                    }
                }

                setupFileUpload('licenseBox', 'licenseFile', 'licenseFileName', 'licenseFile');
                setupFileUpload('certificateBox', 'certificateFile', 'certificateFileName', 'certificateFile');

                // ==================== ERROR HANDLING ====================
                function showError(fieldId, message) {
                    const errorDiv = document.getElementById(fieldId + 'Error');
                    if (errorDiv) {
                        errorDiv.textContent = message;
                        errorDiv.classList.add('show');
                    }
                    const input = document.getElementById(fieldId);
                    if (input) {
                        input.classList.add('error');
                    }
                }

                function clearError(fieldId) {
                    const errorDiv = document.getElementById(fieldId + 'Error');
                    if (errorDiv) {
                        errorDiv.classList.remove('show');
                    }
                    const input = document.getElementById(fieldId);
                    if (input) {
                        input.classList.remove('error');
                    }
                }

                function clearAllErrors() {
                    const errorFields = ['name', 'email', 'phone', 'password', 'passwordConfirmation',
                        'specialization', 'licenseNumber', 'experienceYears', 'consultationFee', 'qualifications', 'bio', 'languages',
                        'licenseFile', 'certificateFile'];
                    errorFields.forEach(f => clearError(f));
                }

                // ==================== EMAIL DUPLICATION CHECK ====================
                async function checkEmailDuplicate(email) {
                    if (!email) return false;

                    try {
                        const response = await fetch('/specialist/check-email', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ email: email })
                        });

                        const data = await response.json();

                        if (data.exists) {
                            showError('email', '{{ __("This email is already registered. Please use a different email or login.") }}');
                            return false;
                        }
                        return true;
                    } catch (error) {
                        console.error('Email check error:', error);
                        return true;
                    }
                }

                // ==================== VALIDATION ====================
                function validateStep1() {
                    let isValid = true;
                    clearError('name');
                    clearError('email');
                    clearError('phone');
                    clearError('password');
                    clearError('passwordConfirmation');

                    const name = document.getElementById('name')?.value.trim() || '';
                    const email = document.getElementById('email')?.value.trim() || '';
                    const phone = document.getElementById('phone')?.value.trim() || '';
                    const password = document.getElementById('password')?.value || '';
                    const passwordConfirmation = document.getElementById('passwordConfirmation')?.value || '';

                    if (!name) {
                        showError('name', '{{ __("Please enter your full name") }}');
                        isValid = false;
                    }
                    if (!email) {
                        showError('email', '{{ __("Please enter your email address") }}');
                        isValid = false;
                    } else if (!/^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(email)) {
                        showError('email', '{{ __("Please enter a valid email address") }}');
                        isValid = false;
                    }
                    if (!phone) {
                        showError('phone', '{{ __("Please enter your phone number") }}');
                        isValid = false;
                    }
                    if (!password) {
                        showError('password', '{{ __("Please enter a password") }}');
                        isValid = false;
                    } else if (password.length < 8) {
                        showError('password', '{{ __("Password must be at least 8 characters") }}');
                        isValid = false;
                    }
                    if (password !== passwordConfirmation) {
                        showError('passwordConfirmation', '{{ __("Passwords do not match") }}');
                        isValid = false;
                    }
                    return isValid;
                }

                async function validateAndProceedToStep2() {
                    if (!validateStep1()) return false;

                    const email = document.getElementById('email')?.value.trim();
                    if (email) {
                        const btnNext = document.getElementById('continueToStep2');
                        const originalText = btnNext.innerHTML;

                        btnNext.classList.add('btn-loading');
                        btnNext.disabled = true;

                        const isEmailUnique = await checkEmailDuplicate(email);

                        btnNext.classList.remove('btn-loading');
                        btnNext.innerHTML = originalText;
                        btnNext.disabled = false;

                        if (!isEmailUnique) {
                            return false;
                        }
                    }
                    return true;
                }

                function validateStep2() {
                    let isValid = true;
                    const fields = ['specialization', 'licenseNumber', 'experienceYears', 'consultationFee', 'qualifications', 'bio'];
                    fields.forEach(f => clearError(f));
                    clearError('languages');

                    const specialization = document.getElementById('specialization')?.value || '';
                    const licenseNumber = document.getElementById('licenseNumber')?.value.trim() || '';
                    const experienceYears = document.getElementById('experienceYears')?.value || '';
                    const consultationFee = document.getElementById('consultationFee')?.value || '';
                    const languages = document.getElementById('languagesHidden')?.value || '';
                    const qualifications = document.getElementById('qualifications')?.value.trim() || '';
                    const bio = document.getElementById('bio')?.value.trim() || '';

                    if (!specialization) {
                        showError('specialization', '{{ __("Please select your specialization") }}');
                        isValid = false;
                    }
                    if (!licenseNumber) {
                        showError('licenseNumber', '{{ __("Please enter your license number") }}');
                        isValid = false;
                    }
                    if (!experienceYears) {
                        showError('experienceYears', '{{ __("Please select your years of experience") }}');
                        isValid = false;
                    }
                    if (!consultationFee) {
                        showError('consultationFee', '{{ __("Please enter your consultation fee") }}');
                        isValid = false;
                    }
                    if (!languages) {
                        showError('languages', '{{ __("Please select at least one language") }}');
                        isValid = false;
                    }
                    if (!qualifications) {
                        showError('qualifications', '{{ __("Please enter your qualifications") }}');
                        isValid = false;
                    }
                    if (!bio) {
                        showError('bio', '{{ __("Please enter your professional bio") }}');
                        isValid = false;
                    }
                    return isValid;
                }

                function validateStep3() {
                    return validateStep3Files();
                }

                // ==================== REVIEW SUMMARY ====================
                function updateReviewSummary() {
                    const reviewDiv = document.getElementById('reviewSummary');
                    if (!reviewDiv) return;

                    const name = document.getElementById('name')?.value || '-';
                    const email = document.getElementById('email')?.value || '-';
                    const phone = document.getElementById('phone')?.value || '-';
                    const specialization = document.getElementById('specialization')?.value || '-';
                    const licenseNumber = document.getElementById('licenseNumber')?.value || '-';
                    const experienceYears = document.getElementById('experienceYears')?.value || '-';
                    const consultationFee = document.getElementById('consultationFee')?.value || '0';
                    const languages = document.getElementById('languagesHidden')?.value || '-';
                    const qualifications = document.getElementById('qualifications')?.value || '-';
                    const bio = document.getElementById('bio')?.value || '-';
                    const licenseFile = document.getElementById('licenseFile')?.files[0];
                    const certFile = document.getElementById('certificateFile')?.files[0];

                    function escapeHtml(str) {
                        if (!str) return str;
                        return str.replace(/[&<>]/g, function (m) {
                            if (m === '&') return '&amp;';
                            if (m === '<') return '&lt;';
                            if (m === '>') return '&gt;';
                            return m;
                        });
                    }

                    reviewDiv.innerHTML = `
                            <div class="review-section">
                                <h4><i class="fas fa-user"></i> {{ __("Personal Information") }}</h4>
                                <div class="review-item"><span class="review-label">{{ __("Full Name") }}:</span><span class="review-value">${escapeHtml(name)}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Email") }}:</span><span class="review-value">${escapeHtml(email)}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Phone") }}:</span><span class="review-value">${escapeHtml(phone)}</span></div>
                            </div>
                            <div class="review-section">
                                <h4><i class="fas fa-briefcase"></i> {{ __("Professional Information") }}</h4>
                                <div class="review-item"><span class="review-label">{{ __("Specialization") }}:</span><span class="review-value">${escapeHtml(specialization)}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("License Number") }}:</span><span class="review-value">${escapeHtml(licenseNumber)}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Experience") }}:</span><span class="review-value">${escapeHtml(experienceYears)} years</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Fee per Session") }}:</span><span class="review-value">$${escapeHtml(consultationFee)}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Languages") }}:</span><span class="review-value">${escapeHtml(languages)}</span></div>
                            </div>
                            <div class="review-section">
                                <h4><i class="fas fa-cloud-upload-alt"></i> {{ __("Documents") }}</h4>
                                <div class="review-item"><span class="review-label">{{ __("License File") }}:</span><span class="review-value">${licenseFile ? escapeHtml(licenseFile.name) : '{{ __("Not uploaded") }}'}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Certificate File") }}:</span><span class="review-value">${certFile ? escapeHtml(certFile.name) : '{{ __("Not uploaded") }}'}</span></div>
                            </div>
                            <div class="review-section">
                                <h4><i class="fas fa-graduation-cap"></i> {{ __("Qualifications & Bio") }}</h4>
                                <div class="review-item"><span class="review-label">{{ __("Qualifications") }}:</span><span class="review-value">${escapeHtml(qualifications.substring(0, 100))}${qualifications.length > 100 ? '...' : ''}</span></div>
                                <div class="review-item"><span class="review-label">{{ __("Bio") }}:</span><span class="review-value">${escapeHtml(bio.substring(0, 100))}${bio.length > 100 ? '...' : ''}</span></div>
                            </div>
                        `;
                }

                // ==================== STEP NAVIGATION ====================
                function updateProgress() {
                    const percent = ((currentStep - 1) / (totalSteps - 1)) * 100;
                    if (progressFill) progressFill.style.width = percent + '%';

                    progressSteps.forEach((step, index) => {
                        const stepNum = index + 1;
                        step.classList.remove('active', 'completed');
                        if (stepNum < currentStep) {
                            step.classList.add('completed');
                        } else if (stepNum === currentStep) {
                            step.classList.add('active');
                        }
                    });

                    stepContainers.forEach(container => {
                        container.classList.remove('active');
                    });
                    const activeStep = document.getElementById(`step${currentStep}`);
                    if (activeStep) activeStep.classList.add('active');

                    if (currentStep === 4) {
                        updateReviewSummary();
                    }
                }

                async function goToStep(stepNumber) {
                    if (stepNumber < 1 || stepNumber > totalSteps) return;

                    if (stepNumber > currentStep) {
                        if (currentStep === 1) {
                            const canProceed = await validateAndProceedToStep2();
                            if (!canProceed) return;
                        }
                        if (currentStep === 2 && !validateStep2()) return;
                        if (currentStep === 3 && !validateStep3()) return;
                    }

                    currentStep = stepNumber;
                    updateProgress();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                document.querySelectorAll('.btn-next').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const nextStep = parseInt(this.getAttribute('data-next'));
                        if (nextStep && nextStep > currentStep) {
                            if (currentStep === 1) {
                                const canProceed = await validateAndProceedToStep2();
                                if (!canProceed) return;
                                goToStep(nextStep);
                            } else if (currentStep === 2 && validateStep2()) {
                                goToStep(nextStep);
                            } else if (currentStep === 3 && validateStep3()) {
                                goToStep(nextStep);
                            }
                        }
                    });
                });

                document.querySelectorAll('.btn-prev').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const prevStep = parseInt(this.getAttribute('data-prev'));
                        if (prevStep && prevStep < currentStep) {
                            goToStep(prevStep);
                        }
                    });
                });

                progressSteps.forEach((step, index) => {
                    step.addEventListener('click', async function () {
                        const targetStep = index + 1;
                        if (targetStep < currentStep) {
                            goToStep(targetStep);
                        } else if (targetStep > currentStep) {
                            if (currentStep === 1) {
                                const canProceed = await validateAndProceedToStep2();
                                if (!canProceed) return;
                                goToStep(targetStep);
                            } else if (currentStep === 2 && validateStep2()) {
                                goToStep(targetStep);
                            } else if (currentStep === 3 && validateStep3()) {
                                goToStep(targetStep);
                            }
                        }
                    });
                });

                // ==================== FORM SUBMIT ====================
                const form = document.getElementById('multiStepForm');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        const termsChecked = document.getElementById('termsCheckbox');
                        if (!termsChecked || !termsChecked.checked) {
                            e.preventDefault();
                            showError('terms', '{{ __("Please agree to the Terms and Conditions and Privacy Policy") }}');
                        } else {
                            clearError('terms');
                            const submitBtn = document.getElementById('submitBtn');
                            if (submitBtn) {
                                submitBtn.classList.add('btn-loading');
                                submitBtn.disabled = true;
                            }
                        }
                    });
                }

                updateProgress();
            });
        </script>
    @endpush

@endsection