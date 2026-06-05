{{-- resources/views/specialist/partials/specialists_grid.blade.php --}}
@if($specialists->count() > 0)
    <div class="specialists-grid">
        @foreach($specialists as $specialist)
            @php
                $profileImage = $specialist->getProfileImageUrl();
                $firstLetter = mb_substr($specialist->name, 0, 1, 'UTF-8');
            @endphp
            <div class="specialist-card">
                <div class="card-badge">
                    @if($specialist->specialistProfile->is_verified)
                        <span class="badge-top">{{ __('Verified') }}</span>
                    @endif
                </div>
                
                <div class="specialist-avatar">
                    @if($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $specialist->name }}">
                    @else
                        <div class="avatar-initials">{{ $firstLetter }}</div>
                    @endif
                </div>
                
                <h3 class="specialist-name">{{ $specialist->name }}</h3>
                <div class="specialist-specialty">
                    <i class="fas fa-stethoscope"></i>
                    <span>{{ __($specialist->specialistProfile->specialization) }}</span>
                </div>
                
                <div class="specialist-rating">
                    <div class="stars">
                        @php $rating = $specialist->specialistProfile->rating_avg; @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($rating))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $rating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="rating-value">{{ number_format($rating, 1) }}</span>
                    <span class="reviews-count">({{ $specialist->specialistProfile->total_sessions ?? 0 }})</span>
                </div>
                
                <div class="specialist-info">
                    <div class="info-item">
                        <i class="fas fa-language"></i>
                        <span>{{ __($specialist->specialistProfile->languages ?? 'العربية') }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-dollar-sign"></i>
                        <span>{{ $specialist->specialistProfile->consultation_fee }} {{ __('USD') }}/{{ __('session') }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $specialist->specialistProfile->experience_years ?? 0 }} {{ __('years exp') }}</span>
                    </div>
                </div>
                
                <div class="specialist-bio">
                    {{ Str::limit($specialist->specialistProfile->bio ?? __('No bio available'), 100) }}
                </div>
                
                <div class="card-actions">
                    <a href="{{ route('specialists.show', $specialist) }}" class="btn-view">
                        <i class="fas fa-user-circle"></i> {{ __('View Profile') }}
                    </a>
                    @auth
                        @if(Auth::user()->hasRole('patient'))
                            <a href="{{ route('patient.book', $specialist->id) }}" class="btn-book">
                                <i class="fas fa-calendar-plus"></i> {{ __('Book') }}
                            </a>
                        @elseif(!Auth::user()->hasRole('specialist') && !Auth::user()->hasRole('admin'))
                            <a href="{{ route('login') }}" class="btn-book">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login to Book') }}
                            </a>
                        @else
                            <button class="btn-book" disabled style="opacity:0.6; cursor:not-allowed;">
                                <i class="fas fa-lock"></i> {{ __('Book') }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-book">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Login to Book') }}
                        </a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="no-results">
        <i class="fas fa-user-md"></i>
        <h3>{{ __('No specialists found') }}</h3>
        <p>{{ __('Try adjusting your filters or search criteria') }}</p>
        <button id="resetFiltersBtn" class="btn-reset">{{ __('Reset Filters') }}</button>
    </div>
@endif