{{-- resources/views/patient/mood-tracker.blade.php --}}
@extends('layouts.app')

@php
use App\Helpers\MoodHelper;
@endphp

@section('title', __('Mood Tracker') . ' - ' . __('Tamman'))

@section('page-title', __('Mood Tracker'))

@section('content')
<div class="mood-tracker-container">
    <!-- Encouragement Banner with Animated Waves -->
    <div class="encouragement-banner">
        <div class="animated-waves">
            <svg class="waves-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path class="wave wave1" fill="rgba(255,255,255,0.15)" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,176C672,171,768,181,864,192C960,203,1056,213,1152,208C1248,203,1344,181,1392,170.7L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
                <path class="wave wave2" fill="rgba(255,255,255,0.1)" d="M0,224L48,218.7C96,213,192,203,288,208C384,213,480,235,576,234.7C672,235,768,213,864,197.3C960,181,1056,171,1152,176C1248,181,1344,203,1392,213.3L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
                <path class="wave wave3" fill="rgba(255,255,255,0.05)" d="M0,256L48,250.7C96,245,192,235,288,229.3C384,224,480,224,576,224C672,224,768,224,864,224C960,224,1056,224,1152,224C1248,224,1344,224,1392,224L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
            </svg>
        </div>
        <div class="encouragement-content">
            <div class="encouragement-emoji">🌟</div>
            <div class="encouragement-text">
                <h3 id="encouragementTitle">{{ __('Your feelings matter') }}</h3>
                <p id="encouragementMessage">{{ __('Taking a moment to check in with yourself is a powerful act of self-care. How are you really feeling today?') }}</p>
            </div>
            <div class="encouragement-tips">
                <div class="tip-bubble">💭 {{ __('Be honest with yourself') }}</div>
                <div class="tip-bubble">🌱 {{ __('Every feeling is valid') }}</div>
                <div class="tip-bubble">💪 {{ __('You are not alone') }}</div>
            </div>
        </div>
    </div>

    <!-- Today's Mood Card -->
    <div class="today-mood-card">
        <div class="today-mood-header">
            <h2><i class="fas fa-smile-wink"></i> {{ __('How are you feeling today?') }}</h2>
            <p>{{ __('Tracking your mood daily helps you understand your emotional patterns and earn Tamman Points!') }}</p>
        </div>
        
        <div id="todayMoodContainer">
            @if($todayLog)
                <div class="already-logged" id="alreadyLoggedBox">
                    <div class="logged-mood">
                        <div class="mood-emoji-large">{{ MoodHelper::getEmoji($todayLog->mood_value) }}</div>
                        <div class="mood-info">
                            <h3>{{ __('You already logged your mood today') }}</h3>
                            <p>{{ __('Mood') }}: <strong>{{ app()->getLocale() === 'ar' ? MoodHelper::getLabelAr($todayLog->mood_value) : MoodHelper::getLabel($todayLog->mood_value) }}</strong> ({{ $todayLog->mood_value }}/10)</p>
                            <p>{{ __('You can edit or delete your entry below') }}</p>
                        </div>
                    </div>
                    <button class="btn-edit-mood" data-id="{{ $todayLog->id }}" data-value="{{ $todayLog->mood_value }}" data-label="{{ $todayLog->mood_label }}" data-notes="{{ $todayLog->notes }}">
                        <i class="fas fa-edit"></i> {{ __('Edit Entry') }}
                    </button>
                </div>
            @else
                <form id="moodForm" class="mood-form">
                    @csrf
                    <div class="mood-options">
                        <div class="mood-option" data-value="10" data-label="absolutely_amazing">
                            <div class="mood-emoji">😍</div>
                            <span>{{ __('Absolutely Amazing') }}</span>
                        </div>
                        <div class="mood-option" data-value="9" data-label="great">
                            <div class="mood-emoji">😊</div>
                            <span>{{ __('Great') }}</span>
                        </div>
                        <div class="mood-option" data-value="8" data-label="very_happy">
                            <div class="mood-emoji">😄</div>
                            <span>{{ __('Very Happy') }}</span>
                        </div>
                        <div class="mood-option" data-value="7" data-label="happy">
                            <div class="mood-emoji">🙂</div>
                            <span>{{ __('Happy') }}</span>
                        </div>
                        <div class="mood-option" data-value="6" data-label="pretty_good">
                            <div class="mood-emoji">😐</div>
                            <span>{{ __('Pretty Good') }}</span>
                        </div>
                        <div class="mood-option" data-value="5" data-label="neutral">
                            <div class="mood-emoji">😶</div>
                            <span>{{ __('Neutral') }}</span>
                        </div>
                        <div class="mood-option" data-value="4" data-label="slightly_sad">
                            <div class="mood-emoji">😕</div>
                            <span>{{ __('Slightly Sad') }}</span>
                        </div>
                        <div class="mood-option" data-value="3" data-label="sad">
                            <div class="mood-emoji">😔</div>
                            <span>{{ __('Sad') }}</span>
                        </div>
                        <div class="mood-option" data-value="2" data-label="very_sad">
                            <div class="mood-emoji">😢</div>
                            <span>{{ __('Very Sad') }}</span>
                        </div>
                        <div class="mood-option" data-value="1" data-label="terrible">
                            <div class="mood-emoji">😫</div>
                            <span>{{ __('Terrible') }}</span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="mood_value" id="selectedMoodValue" required>
                    <input type="hidden" name="mood_label" id="selectedMoodLabel" required>
                    
                    <div class="mood-notes">
                        <label for="mood_notes"><i class="fas fa-pen"></i> {{ __('Add a note (optional)') }}</label>
                        <textarea name="notes" id="mood_notes" rows="3" placeholder="{{ __('What made you feel this way? This helps you reflect later...') }}"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit-mood" id="submitMoodBtn" disabled>
                            <i class="fas fa-check-circle"></i> {{ __('Save My Mood') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid-mood" id="statsContainer">
        <div class="stat-card-mood">
            <div class="stat-icon-mood"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info-mood">
                <h3 id="currentStreak">{{ $currentStreak }}</h3>
                <p>{{ __('Current Streak') }}</p>
                <small>{{ __('Days in a row') }}</small>
            </div>
        </div>
        <div class="stat-card-mood">
            <div class="stat-icon-mood"><i class="fas fa-trophy"></i></div>
            <div class="stat-info-mood">
                <h3 id="longestStreak">{{ $longestStreak }}</h3>
                <p>{{ __('Longest Streak') }}</p>
                <small>{{ __('Your best record') }}</small>
            </div>
        </div>
        <div class="stat-card-mood">
            <div class="stat-icon-mood"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info-mood">
                <h3 id="avgMood">{{ $stats['average'] }}/10</h3>
                <p>{{ __('Average Mood') }}</p>
                <small>{{ __('Overall average') }}</small>
            </div>
        </div>
        <div class="stat-card-mood">
            <div class="stat-icon-mood"><i class="fas fa-star"></i></div>
            <div class="stat-info-mood">
                <h3 id="totalEntries">{{ $stats['total'] }}</h3>
                <p>{{ __('Total Entries') }}</p>
                <small>{{ __('Moods logged') }}</small>
            </div>
        </div>
    </div>

    <!-- Mood Chart -->
    <div class="dashboard-card full-width">
        <div class="card-header">
            <h3><i class="fas fa-chart-line"></i> {{ __('Mood Trends (Last 30 Days)') }}</h3>
            <button class="view-all" id="refreshChartBtn"><i class="fas fa-sync-alt"></i> {{ __('Refresh') }}</button>
        </div>
        <div class="card-body chart-wrapper">
            <div id="moodChart" class="apex-chart"></div>
        </div>
    </div>

    <!-- Weekly & Monthly Insights -->
    <div class="insights-grid">
        <div class="dashboard-card">
            <div class="card-header"><h3><i class="fas fa-calendar-week"></i> {{ __('Weekly Mood Average') }}</h3></div>
            <div class="card-body">
                <div class="weekly-averages" id="weeklyAveragesContainer">
                    @foreach($weeklyAverages as $day)
                        <div class="weekly-item">
                            <div class="weekday-name">{{ $day['day'] }}</div>
                            <div class="weekday-bar">
                                <div class="weekday-fill" style="width: {{ ($day['average'] / 10) * 100 }}%; background: {{ $day['color'] }}"></div>
                            </div>
                            <div class="weekday-value">{{ $day['average'] }}/10</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-header"><h3><i class="fas fa-chart-bar"></i> {{ __('Monthly Average') }}</h3></div>
            <div class="card-body"><div id="monthlyChart" class="mini-chart"></div></div>
        </div>
    </div>

    <!-- Recent Mood History -->
    <div class="dashboard-card full-width">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> {{ __('Recent Mood History') }}</h3>
            <button class="view-all" id="refreshHistoryBtn"><i class="fas fa-sync-alt"></i> {{ __('Refresh') }}</button>
        </div>
        <div class="card-body">
            <div id="historyTableContainer">
                @include('patient.partials.mood-history-table', ['moodLogs' => $moodLogs])
            </div>
        </div>
    </div>
</div>

<!-- Edit Mood Modal -->
<div id="editMoodModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> {{ __('Edit Mood Entry') }}</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="mood-options-small" id="editMoodOptions"></div>
            <input type="hidden" id="editMoodId">
            <input type="hidden" id="editMoodValue">
            <input type="hidden" id="editMoodLabel">
            <div class="form-group">
                <label for="edit_notes"><i class="fas fa-pen"></i> {{ __('Notes') }}</label>
                <textarea id="edit_notes" rows="4" placeholder="{{ __('How were you feeling? What happened?') }}"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="closeEditModal">{{ __('Cancel') }}</button>
            <button type="button" class="btn-save" id="saveEditMood">{{ __('Save Changes') }}</button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .mood-tracker-container { max-width: 100%; margin: 0 auto; }
    .encouragement-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        padding: 30px;
        color: white;
    }
    .animated-waves { position: absolute; bottom: 0; left: 0; width: 100%; height: 100px; overflow: hidden; opacity: 0.4; }
    .waves-svg { position: relative; width: 100%; height: 100%; animation: waveFloat 8s ease-in-out infinite; }
    .wave { animation: waveMove 6s ease-in-out infinite alternate; }
    .wave1 { animation: waveMove 8s ease-in-out infinite alternate; }
    .wave2 { animation: waveMove 6s ease-in-out infinite alternate reverse; }
    .wave3 { animation: waveMove 10s ease-in-out infinite alternate; }
    @keyframes waveMove { 0% { transform: translateX(0) translateY(0); } 100% { transform: translateX(-30px) translateY(5px); } }
    @keyframes waveFloat { 0% { transform: translateY(0); } 50% { transform: translateY(-5px); } 100% { transform: translateY(0); } }
    .encouragement-content { position: relative; z-index: 2; display: flex; align-items: center; gap: 25px; flex-wrap: wrap; }
    .encouragement-emoji { font-size: 4rem; animation: bounce 2s ease-in-out infinite; }
    @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .encouragement-text { flex: 1; }
    .encouragement-text h3 { color: white; font-size: 1.3rem; margin-bottom: 8px; transition: opacity 0.3s ease; }
    .encouragement-text p { color: rgba(255,255,255,0.9); margin: 0; transition: opacity 0.3s ease; }
    .encouragement-tips { display: flex; gap: 12px; flex-wrap: wrap; }
    .tip-bubble { background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 40px; font-size: 0.8rem; backdrop-filter: blur(10px); }
    .today-mood-card { background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 24px; padding: 30px; margin-bottom: 30px; color: white; }
    .today-mood-header h2 { color: white; font-size: 1.5rem; margin-bottom: 10px; }
    .today-mood-header p { color: rgba(255,255,255,0.8); margin-bottom: 20px; }
    .already-logged { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; background: rgba(255,255,255,0.1); border-radius: 16px; padding: 20px; margin-top: 10px; }
    .logged-mood { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
    .mood-emoji-large { font-size: 3rem; }
    .logged-mood h3 { color: white; margin-bottom: 5px; }
    .logged-mood p { color: rgba(255,255,255,0.8); margin: 0; }
    .btn-edit-mood { background: rgba(255,255,255,0.2); border: none; padding: 10px 20px; border-radius: 40px; color: white; cursor: pointer; transition: all 0.3s ease; }
    .btn-edit-mood:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
    .mood-options { display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-bottom: 25px; }
    .mood-option { flex: 1; min-width: 70px; text-align: center; padding: 12px 8px; background: rgba(255,255,255,0.15); border-radius: 16px; cursor: pointer; transition: all 0.3s ease; border: 2px solid transparent; }
    .mood-option:hover { transform: translateY(-5px); background: rgba(255,255,255,0.25); }
    .mood-option.selected { background: rgba(255,255,255,0.3); border-color: white; transform: scale(1.02); }
    .mood-emoji { font-size: 1.8rem; margin-bottom: 5px; }
    .mood-option span { font-size: 0.7rem; display: block; }
    .mood-notes { margin-bottom: 20px; }
    .mood-notes label { display: block; margin-bottom: 10px; font-weight: 500; }
    .mood-notes textarea { width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; background: rgba(255,255,255,0.1); color: white; resize: vertical; font-family: inherit; }
    .mood-notes textarea::placeholder { color: rgba(255,255,255,0.6); }
    .mood-notes textarea:focus { outline: none; border-color: white; }
    .btn-submit-mood { background: white; color: #7c3aed; border: none; padding: 12px 30px; border-radius: 40px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; width: 100%; }
    .btn-submit-mood:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .btn-submit-mood:disabled { opacity: 0.5; cursor: not-allowed; }
    .stats-grid-mood { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-card-mood { background: white; border-radius: 20px; padding: 20px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .stat-card-mood:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
    .stat-icon-mood { width: 50px; height: 50px; background: linear-gradient(135deg, #ede9fe, #ddd6fe); border-radius: 15px; display: flex; align-items: center; justify-content: center; }
    .stat-icon-mood i { font-size: 1.5rem; color: #7c3aed; }
    .stat-info-mood h3 { font-size: 1.5rem; font-weight: 700; margin: 0; color: #1f2937; }
    .stat-info-mood p { font-size: 0.75rem; color: #6b7280; margin: 0; }
    .stat-info-mood small { font-size: 0.65rem; color: #9ca3af; }
    .dashboard-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; }
    .dashboard-card.full-width { width: 100%; }
    .card-header { padding: 20px 25px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .card-header h3 { font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 10px; color: #1f2937; }
    .view-all { font-size: 0.75rem; color: #7c3aed; text-decoration: none; background: none; border: none; cursor: pointer; }
    .card-body { padding: 20px 25px; }
    .chart-wrapper { position: relative; width: 100%; padding: 0; }
    .apex-chart { width: 100%; min-height: 350px; }
    .insights-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 25px; }
    .mini-chart { width: 100%; min-height: 250px; }
    .weekly-averages { display: flex; flex-direction: column; gap: 12px; }
    .weekly-item { display: flex; align-items: center; gap: 12px; }
    .weekday-name { width: 100px; font-size: 0.8rem; font-weight: 500; color: #374151; }
    .weekday-bar { flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
    .weekday-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
    .weekday-value { width: 45px; font-size: 0.7rem; color: #6b7280; text-align: right; }
    .history-table-container { overflow-x: auto; }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th, .history-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f3f4f6; }
    .history-table th { font-weight: 600; color: #374151; background: #f9fafb; }
    .mood-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
    .mood-value-indicator { display: flex; align-items: center; gap: 10px; }
    .mood-value-indicator span { min-width: 35px; font-size: 0.75rem; }
    .mini-bar { flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden; }
    .mini-fill { height: 100%; border-radius: 2px; }
    .btn-icon-small { background: none; border: none; padding: 6px 8px; cursor: pointer; border-radius: 8px; transition: all 0.3s ease; color: #6b7280; }
    .btn-icon-small:hover { background: #f3f4f6; color: #7c3aed; }
    .btn-icon-small.delete-mood:hover { color: #ef4444; }
    .pagination-wrapper { margin-top: 25px; text-align: center; }
    .pagination { display: inline-flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap; padding: 0; margin: 0; list-style: none; }
    .pagination .page-item { list-style: none; }
    .pagination .page-link { display: flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 12px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; color: #4b5563; text-decoration: none; font-size: 0.85rem; transition: all 0.3s ease; }
    .pagination .page-link:hover { background: #f3f4f6; border-color: #c4b5fd; color: #7c3aed; }
    .pagination .active .page-link { background: linear-gradient(135deg, #7c3aed, #6d28d9); border-color: #7c3aed; color: white; }
    .pagination .disabled .page-link { opacity: 0.5; cursor: not-allowed; background: #f9fafb; }
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-container { background: white; border-radius: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; transform: scale(0.9); transition: transform 0.3s ease; }
    .modal-overlay.active .modal-container { transform: scale(1); }
    .modal-header { padding: 20px 25px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 1.2rem; color: #1f2937; }
    .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; }
    .modal-body { padding: 20px 25px; }
    .modal-footer { padding: 20px 25px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; }
    .form-group label i { margin-right: 8px; color: #7c3aed; }
    .form-group textarea { width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 0.9rem; font-family: inherit; resize: vertical; transition: all 0.3s ease; background: #f9fafb; }
    .form-group textarea:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: white; }
    .btn-cancel { background: #f3f4f6; border: none; padding: 10px 24px; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; }
    .btn-cancel:hover { background: #e5e7eb; }
    .btn-save { background: #7c3aed; color: white; border: none; padding: 10px 24px; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; }
    .btn-save:hover { background: #6d28d9; transform: translateY(-2px); }
    .mood-options-small { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 20px; }
    .mood-option-small { text-align: center; padding: 10px; background: #f3f4f6; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; border: 2px solid transparent; }
    .mood-option-small:hover { background: #e5e7eb; transform: translateY(-2px); }
    .mood-option-small.selected { background: #ede9fe; border-color: #7c3aed; }
    .mood-option-small .mood-emoji { font-size: 1.3rem; margin-bottom: 4px; }
    .mood-option-small span { font-size: 0.65rem; }
    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 3rem; color: #c4b5fd; margin-bottom: 15px; }
    .empty-state p { color: #6b7280; margin-bottom: 15px; }
    @media (max-width: 1200px) { .stats-grid-mood { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 992px) { 
        .insights-grid { grid-template-columns: 1fr; }
        .apex-chart { min-height: 300px; }
        .encouragement-content { flex-direction: column; text-align: center; }
        .encouragement-tips { justify-content: center; }
        .mood-options-small { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 768px) {
        .today-mood-card { padding: 20px; }
        .mood-options { gap: 6px; }
        .mood-option { min-width: 55px; padding: 8px 4px; }
        .mood-emoji { font-size: 1.2rem; }
        .mood-option span { font-size: 0.55rem; }
        .stats-grid-mood { grid-template-columns: 1fr; }
        .weekly-item { flex-wrap: wrap; }
        .weekday-name { width: 100%; }
        .history-table th, .history-table td { padding: 8px 10px; }
        .mood-options-small { grid-template-columns: repeat(3, 1fr); }
        .encouragement-emoji { font-size: 3rem; }
        .encouragement-text h3 { font-size: 1.1rem; }
        .tip-bubble { font-size: 0.7rem; padding: 6px 12px; }
    }
    @media (max-width: 480px) {
        .mood-option { min-width: 65px; }
        .mood-options-small { grid-template-columns: repeat(2, 1fr); }
        .logged-mood { flex-direction: column; text-align: center; }
        .already-logged { flex-direction: column; text-align: center; }
        .pagination .page-link { min-width: 32px; height: 32px; padding: 0 8px; font-size: 0.75rem; }
        .modal-footer { flex-direction: column; }
        .btn-cancel, .btn-save { width: 100%; }
    }
    body.rtl .weekday-value { text-align: left; }
    body.rtl .history-table th, body.rtl .history-table td { text-align: right; }
    body.rtl .form-group label i { margin-right: 0; margin-left: 8px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const currentLocale = '{{ app()->getLocale() }}';
    let moodChart = null;
    let monthlyChart = null;
    
    // Helper function for translations in JavaScript
    function __(key) {
        const translations = {
            'Saving...': '{{ __("Saving...") }}',
            'Error': '{{ __("Error") }}',
            'Something went wrong. Please try again.': '{{ __("Something went wrong. Please try again.") }}'
        };
        return translations[key] || key;
    }
    
    // ==================== MOOD SELECTION ====================
    function initMoodSelection() {
        const moodOptions = document.querySelectorAll('.mood-option');
        const selectedMoodValue = document.getElementById('selectedMoodValue');
        const selectedMoodLabel = document.getElementById('selectedMoodLabel');
        const submitBtn = document.getElementById('submitMoodBtn');
        
        if (moodOptions.length === 0) return;
        
        function handleMoodClick(e) {
            const option = e.currentTarget;
            
            if (option.classList.contains('selected')) {
                option.classList.remove('selected');
                if (selectedMoodValue) selectedMoodValue.value = '';
                if (selectedMoodLabel) selectedMoodLabel.value = '';
                if (submitBtn) submitBtn.disabled = true;
            } else {
                moodOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                if (selectedMoodValue) selectedMoodValue.value = option.dataset.value;
                if (selectedMoodLabel) selectedMoodLabel.value = option.dataset.label;
                if (submitBtn) submitBtn.disabled = false;
            }
        }
        
        moodOptions.forEach(option => {
            option.removeEventListener('click', handleMoodClick);
            option.addEventListener('click', handleMoodClick);
        });
    }
    
    // ==================== EDIT MODAL FUNCTIONS ====================
    function openEditModal(id, value, label, notes) {
        const modal = document.getElementById('editMoodModal');
        const moodOptionsList = [
            { value: 10, label: 'absolutely_amazing', emoji: '😍', name: currentLocale === 'ar' ? '{{ __("Absolutely Amazing") }}' : '{{ __("Absolutely Amazing") }}' },
            { value: 9, label: 'great', emoji: '😊', name: currentLocale === 'ar' ? '{{ __("Great") }}' : '{{ __("Great") }}' },
            { value: 8, label: 'very_happy', emoji: '😄', name: currentLocale === 'ar' ? '{{ __("Very Happy") }}' : '{{ __("Very Happy") }}' },
            { value: 7, label: 'happy', emoji: '🙂', name: currentLocale === 'ar' ? '{{ __("Happy") }}' : '{{ __("Happy") }}' },
            { value: 6, label: 'pretty_good', emoji: '😐', name: currentLocale === 'ar' ? '{{ __("Pretty Good") }}' : '{{ __("Pretty Good") }}' },
            { value: 5, label: 'neutral', emoji: '😶', name: currentLocale === 'ar' ? '{{ __("Neutral") }}' : '{{ __("Neutral") }}' },
            { value: 4, label: 'slightly_sad', emoji: '😕', name: currentLocale === 'ar' ? '{{ __("Slightly Sad") }}' : '{{ __("Slightly Sad") }}' },
            { value: 3, label: 'sad', emoji: '😔', name: currentLocale === 'ar' ? '{{ __("Sad") }}' : '{{ __("Sad") }}' },
            { value: 2, label: 'very_sad', emoji: '😢', name: currentLocale === 'ar' ? '{{ __("Very Sad") }}' : '{{ __("Very Sad") }}' },
            { value: 1, label: 'terrible', emoji: '😫', name: currentLocale === 'ar' ? '{{ __("Terrible") }}' : '{{ __("Terrible") }}' }
        ];
        
        const optionsContainer = document.getElementById('editMoodOptions');
        const moodValueInput = document.getElementById('editMoodValue');
        const moodLabelInput = document.getElementById('editMoodLabel');
        const notesTextarea = document.getElementById('edit_notes');
        const moodIdInput = document.getElementById('editMoodId');
        
        moodIdInput.value = id;
        
        optionsContainer.innerHTML = '';
        moodOptionsList.forEach(opt => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'mood-option-small' + (opt.value == value ? ' selected' : '');
            optionDiv.dataset.value = opt.value;
            optionDiv.dataset.label = opt.label;
            optionDiv.innerHTML = `<div class="mood-emoji">${opt.emoji}</div><span>${opt.name}</span>`;
            optionDiv.addEventListener('click', function() {
                document.querySelectorAll('#editMoodOptions .mood-option-small').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                moodValueInput.value = this.dataset.value;
                moodLabelInput.value = this.dataset.label;
            });
            optionsContainer.appendChild(optionDiv);
        });
        
        moodValueInput.value = value;
        moodLabelInput.value = label;
        notesTextarea.value = notes || '';
        
        modal.classList.add('active');
    }
    
    // Make function global for onclick attributes
    window.openEditModal = openEditModal;
    
    // ==================== SUBMIT MOOD FORM ====================
    const moodForm = document.getElementById('moodForm');
    if (moodForm) {
        moodForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.btn-submit-mood');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Saving...") }}';
            
            try {
                const response = await fetch('{{ route("patient.mood-tracker.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '{{ __("Mood Saved!") }}', html: data.message, timer: 3000, showConfirmButton: false });
                    setTimeout(() => location.reload(), 500);
                } else {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Something went wrong. Please try again.") }}' });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // ==================== SAVE EDIT MOOD ====================
    document.getElementById('saveEditMood')?.addEventListener('click', async function() {
        const id = document.getElementById('editMoodId').value;
        const moodValue = document.getElementById('editMoodValue').value;
        const moodLabel = document.getElementById('editMoodLabel').value;
        const notes = document.getElementById('edit_notes').value;
        
        const saveBtn = this;
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Saving...") }}';
        
        try {
            const response = await fetch(`/patient/mood-tracker/${id}`, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ mood_value: moodValue, mood_label: moodLabel, notes: notes })
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({ icon: 'success', title: '{{ __("Updated!") }}', text: data.message, timer: 2000, showConfirmButton: false });
                document.getElementById('editMoodModal').classList.remove('active');
                setTimeout(() => location.reload(), 500);
            } else {
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Something went wrong. Please try again.") }}' });
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
    
    // ==================== DELETE MOOD ====================
    async function deleteMood(id) {
        const result = await Swal.fire({
            title: '{{ __("Delete Mood Entry") }}',
            text: '{{ __("Are you sure you want to delete this mood entry? This action cannot be undone.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        });
        
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/patient/mood-tracker/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '{{ __("Deleted!") }}', text: data.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => location.reload(), 500);
                } else {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Something went wrong. Please try again.") }}' });
            }
        }
    }
    
    // ==================== ATTACH EVENT HANDLERS ====================
    function attachEventHandlers() {
        document.querySelectorAll('.edit-mood').forEach(btn => {
            btn.removeEventListener('click', editClickHandler);
            btn.addEventListener('click', editClickHandler);
        });
        
        document.querySelectorAll('.delete-mood').forEach(btn => {
            btn.removeEventListener('click', deleteClickHandler);
            btn.addEventListener('click', deleteClickHandler);
        });
        
        const todayEditBtn = document.querySelector('.already-logged .btn-edit-mood');
        if (todayEditBtn && !todayEditBtn.hasAttribute('data-listener')) {
            todayEditBtn.addEventListener('click', editClickHandler);
            todayEditBtn.setAttribute('data-listener', 'true');
        }
    }
    
    function editClickHandler(e) {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        const value = btn.dataset.value;
        const label = btn.dataset.label;
        const notes = btn.dataset.notes || '';
        openEditModal(id, value, label, notes);
    }
    
    function deleteClickHandler(e) {
        const id = e.currentTarget.dataset.id;
        deleteMood(id);
    }
    
    // ==================== CHART FUNCTIONS ====================
    function renderMoodChart(labels, values) {
        const markerColors = values.map(value => {
            if (!value) return '#c4b5fd';
            if (value <= 2) return '#ef4444';
            if (value <= 4) return '#f59e0b';
            if (value <= 6) return '#eab308';
            if (value <= 8) return '#10b981';
            return '#7c3aed';
        });
        
        const options = {
            series: [{ name: currentLocale === 'ar' ? '{{ __("Mood Level") }}' : '{{ __("Mood Level") }}', data: values }],
            chart: { type: 'line', height: 350, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter' },
            stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
            markers: { size: 6, hover: { size: 10 }, colors: markerColors, strokeColors: '#fff', strokeWidth: 2, radius: 6 },
            tooltip: { enabled: true, shared: true, intersect: false, theme: 'dark', y: { formatter: (value) => value ? value + '/10' : '{{ __("No data") }}', title: { formatter: () => currentLocale === 'ar' ? '{{ __("Mood") }}: ' : '{{ __("Mood") }}: ' } } },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            xaxis: { categories: labels, title: { text: currentLocale === 'ar' ? '{{ __("Date") }}' : '{{ __("Date") }}' }, labels: { rotate: -35, style: { fontSize: '10px' } } },
            yaxis: { min: 0, max: 10, title: { text: currentLocale === 'ar' ? '{{ __("Mood Level (1-10)") }}' : '{{ __("Mood Level (1-10)") }}' }, labels: { formatter: (value) => Math.round(value) } },
            fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 100] } },
            legend: { show: true, position: 'top' },
            responsive: [{ breakpoint: 768, options: { chart: { height: 280 }, markers: { size: 4 } } }]
        };
        
        const element = document.querySelector("#moodChart");
        if (element && typeof ApexCharts !== 'undefined') {
            if (moodChart) moodChart.destroy();
            moodChart = new ApexCharts(element, options);
            moodChart.render();
        }
    }
    
    function renderMonthlyChart(months, values) {
        const options = {
            series: [{ name: currentLocale === 'ar' ? '{{ __("Average Mood") }}' : '{{ __("Average Mood") }}', data: values }],
            chart: { type: 'bar', height: 250, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter' },
            plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
            colors: ['#7c3aed'],
            tooltip: { y: { formatter: (val) => val + '/10', title: { formatter: () => currentLocale === 'ar' ? '{{ __("Average Mood") }}: ' : '{{ __("Average Mood") }}: ' } } },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            xaxis: { categories: months, labels: { rotate: -35, style: { fontSize: '10px' } } },
            yaxis: { min: 0, max: 10, title: { text: currentLocale === 'ar' ? '{{ __("Mood Level") }}' : '{{ __("Mood Level") }}' }, labels: { formatter: (val) => val.toFixed(1) } }
        };
        
        const element = document.querySelector("#monthlyChart");
        if (element && typeof ApexCharts !== 'undefined') {
            if (monthlyChart) monthlyChart.destroy();
            monthlyChart = new ApexCharts(element, options);
            monthlyChart.render();
        }
    }
    
    // ==================== INITIALIZATION ====================
    function initCharts() {
        @if(!empty($chartData['labels']))
        renderMoodChart({!! json_encode($chartData['labels']) !!}, {!! json_encode($chartData['values']) !!});
        @endif
        @if(!empty($monthlyAverages['months']))
        renderMonthlyChart({!! json_encode($monthlyAverages['months']) !!}, {!! json_encode($monthlyAverages['averages']) !!});
        @endif
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initMoodSelection();
        attachEventHandlers();
        setTimeout(initCharts, 400);
        
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                setTimeout(() => {
                    if (moodChart) moodChart.resize();
                    if (monthlyChart) monthlyChart.resize();
                }, 350);
            });
        }
        
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                setTimeout(() => {
                    if (moodChart) moodChart.resize();
                    if (monthlyChart) monthlyChart.resize();
                }, 400);
            });
        }
        
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (moodChart) moodChart.resize();
                if (monthlyChart) monthlyChart.resize();
            }, 250);
        });
    });
    
    // Refresh buttons
    document.getElementById('refreshChartBtn')?.addEventListener('click', () => location.reload());
    document.getElementById('refreshHistoryBtn')?.addEventListener('click', () => location.reload());
    
    // Modal close handlers
    document.querySelectorAll('.modal-close, #closeEditModal').forEach(btn => {
        btn.addEventListener('click', () => document.getElementById('editMoodModal').classList.remove('active'));
    });
    
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
    });
    
    // Encouragement messages rotator
    const encouragementMessages = [
        { title: '{{ __("Your feelings matter") }}', message: '{{ __("Taking a moment to check in with yourself is a powerful act of self-care. How are you really feeling today?") }}' },
        { title: '{{ __("You are stronger than you think") }}', message: '{{ __("Every emotion you feel is valid. Recognizing your feelings is the first step toward healing.") }}' },
        { title: '{{ __("Small steps, big changes") }}', message: '{{ __("Tracking your mood daily helps you understand your patterns and celebrate your progress. You\'ve got this!") }}' },
        { title: '{{ __("You are not alone") }}', message: '{{ __("Many people are on this journey with you. Every mood entry brings you closer to better mental health.") }}' },
        { title: '{{ __("Progress over perfection") }}', message: '{{ __("Some days are harder than others, and that\'s perfectly normal. What matters is that you keep caring for yourself.") }}' }
    ];
    
    let messageIndex = 0;
    const titleElement = document.getElementById('encouragementTitle');
    const messageElement = document.getElementById('encouragementMessage');
    
    if (titleElement && messageElement) {
        setInterval(() => {
            messageIndex = (messageIndex + 1) % encouragementMessages.length;
            titleElement.style.opacity = '0';
            messageElement.style.opacity = '0';
            setTimeout(() => {
                titleElement.textContent = encouragementMessages[messageIndex].title;
                messageElement.textContent = encouragementMessages[messageIndex].message;
                titleElement.style.opacity = '1';
                messageElement.style.opacity = '1';
            }, 300);
        }, 8000);
    }
</script>
@endpush

@endsection