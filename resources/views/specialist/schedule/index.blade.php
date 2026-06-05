{{-- resources/views/specialist/schedule/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Manage Schedule') . ' - ' . __('Tamman'))

@section('page-title', __('Manage Schedule'))

@section('content')
    <div class="schedule-container">
        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $recurringSlots->count() }}</h3>
                    <p>{{ __('Total Time Slots') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $daysWithSlots }}</h3>
                    <p>{{ __('Days Covered') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ count($blockedDates) }}</h3>
                    <p>{{ __('Blocked Dates') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon {{ $scheduleStatus === 'active' ? 'green' : 'gray' }}">
                    <i class="fas {{ $scheduleStatus === 'active' ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $scheduleStatus === 'active' ? __('Active') : __('Inactive') }}</h3>
                    <p>{{ __('Schedule Status') }}</p>
                </div>
            </div>
        </div>

        <!-- How It Works Guide - No Arrow -->
        <div class="guide-card animate-fade-in">
            <div class="guide-header" id="guideHeader">
                <i class="fas fa-lightbulb"></i>
                <h3>{{ __('How Availability Works') }}</h3>
            </div>
            <div class="guide-content" id="guideContent">
                <div class="guide-steps">
                    <div class="guide-step">
                        <div class="step-icon">1</div>
                        <div class="step-text">
                            <strong>{{ __('Add Recurring Slots') }}</strong>
                            <p>{{ __('Add weekly repeating time slots for each day') }}</p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">2</div>
                        <div class="step-text">
                            <strong>{{ __('Add One-Time Slots') }}</strong>
                            <p>{{ __('Add extra availability on specific dates') }}</p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">3</div>
                        <div class="step-text">
                            <strong>{{ __('Block Dates') }}</strong>
                            <p>{{ __('Mark dates as unavailable for vacation or personal days') }}</p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">4</div>
                        <div class="step-text">
                            <strong>{{ __('Patients Book Automatically') }}</strong>
                            <p>{{ __('Patients can only book during your available time slots') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt"></i> {{ __('Schedule Calendar') }}</h3>
                <div class="header-actions">
                    <button class="btn-block-date" id="blockDateBtn">
                        <i class="fas fa-ban"></i> {{ __('Block Date') }}
                    </button>
                    <button class="btn-refresh" id="refreshCalendar" title="{{ __('Refresh') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body calendar-wrapper">
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Weekly Schedule Grid -->
        <div class="weekly-schedule-grid">
            <div class="weekly-header">
                <h3><i class="fas fa-clock"></i> {{ __('Weekly Schedule') }}</h3>
                <div class="header-buttons">
                    <button class="btn-add-slot-global" id="addRecurringBtn">
                        <i class="fas fa-plus"></i> {{ __('Add Recurring Slot') }}
                    </button>
                    <button class="btn-add-onetime" id="addOneTimeBtn">
                        <i class="fas fa-calendar-plus"></i> {{ __('Add One-Time Slot') }}
                    </button>
                </div>
            </div>

            <div class="weekly-days-grid">
                @foreach($weeklySchedule as $dayIndex => $day)
                    <div class="schedule-day-card" data-day="{{ $dayIndex }}">
                        <div class="day-card-header">
                            <span class="day-name">{{ __($day['day']) }}</span>
                            <button class="btn-add-slot-day" data-day="{{ $dayIndex }}" title="{{ __('Add slot') }}">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                        <div class="day-slots-list" id="daySlots{{ $dayIndex }}">
                            @forelse($day['slots'] as $slot)
                                <div class="time-slot-item" data-id="{{ $slot->id }}"
                                    data-start="{{ substr($slot->start_time, 0, 5) }}"
                                    data-end="{{ substr($slot->end_time, 0, 5) }}" data-day="{{ $dayIndex }}">
                                    <span class="slot-time-range">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') . ' ' . (\Carbon\Carbon::parse($slot->start_time)->format('A') === 'AM' ? __('AM') : __('PM')) }}
                                        -
                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') . ' ' . (\Carbon\Carbon::parse($slot->end_time)->format('A') === 'AM' ? __('AM') : __('PM')) }}
                                    </span>
                                    <div class="slot-actions-group">
                                        <button class="edit-slot-btn" data-id="{{ $slot->id }}" data-day="{{ $dayIndex }}"
                                            data-start="{{ substr($slot->start_time, 0, 5) }}"
                                            data-end="{{ substr($slot->end_time, 0, 5) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-slot-btn" data-id="{{ $slot->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-slots-message">
                                    <i class="far fa-clock"></i>
                                    <span>{{ __('No time slots') }}</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- One-Time Slots Section -->
            @if($oneTimeSlots->count() > 0)
                <div class="onetime-slots-section">
                    <div class="onetime-header">
                        <h4><i class="fas fa-calendar-day"></i> {{ __('One-Time Availability') }}</h4>
                    </div>
                    <div class="onetime-slots-list">
                        @foreach($oneTimeSlots as $slot)
                            <div class="onetime-slot-item" data-id="{{ $slot->id }}">
                                <div class="onetime-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($slot->specific_date)->translatedFormat('l, M d, Y') }}
                                </div>
                                <div class="onetime-time">
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') . ' ' . (\Carbon\Carbon::parse($slot->start_time)->format('A') === 'AM' ? __('AM') : __('PM')) }}
                                    -
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') . ' ' . (\Carbon\Carbon::parse($slot->end_time)->format('A') === 'AM' ? __('AM') : __('PM')) }}
                                </div>
                                <div class="onetime-actions">
                                    <button class="delete-onetime-btn" data-id="{{ $slot->id }}">
                                        <i class="fas fa-trash-alt"></i> {{ __('Remove') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Copy Schedule Tool -->
            <div class="copy-schedule-tool">
                <div class="copy-tool-header">
                    <i class="fas fa-copy"></i>
                    <h4>{{ __('Copy Schedule to Another Day') }}</h4>
                </div>
                <div class="copy-tool-body">
                    <div class="copy-field">
                        <label>{{ __('Source Day') }}</label>
                        <select id="copySourceDay" class="form-control">
                            <option value="">{{ __('Select source day') }}</option>
                            <option value="0">{{ __('Sunday') }}</option>
                            <option value="1">{{ __('Monday') }}</option>
                            <option value="2">{{ __('Tuesday') }}</option>
                            <option value="3">{{ __('Wednesday') }}</option>
                            <option value="4">{{ __('Thursday') }}</option>
                            <option value="5">{{ __('Friday') }}</option>
                            <option value="6">{{ __('Saturday') }}</option>
                        </select>
                    </div>
                    <div class="copy-field">
                        <label>{{ __('Target Days') }}</label>
                        <select id="copyTargetDays" class="form-control" multiple size="4">
                            <option value="0">{{ __('Sunday') }}</option>
                            <option value="1">{{ __('Monday') }}</option>
                            <option value="2">{{ __('Tuesday') }}</option>
                            <option value="3">{{ __('Wednesday') }}</option>
                            <option value="4">{{ __('Thursday') }}</option>
                            <option value="5">{{ __('Friday') }}</option>
                            <option value="6">{{ __('Saturday') }}</option>
                        </select>
                        <small
                            class="form-hint">{{ __('Hold Ctrl (Windows) or Command (Mac) to select multiple days') }}</small>
                    </div>
                    <div class="copy-field copy-action">
                        <button id="copyWeekBtn" class="btn-copy-schedule">
                            <i class="fas fa-copy"></i> {{ __('Copy to Selected Days') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Recurring Slot Modal -->
    <div id="slotModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-plus"></i> {{ __('Add Recurring Time Slot') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="slotForm">
                @csrf
                <input type="hidden" id="slotId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('Day of Week') }} <span class="required">*</span></label>
                        <select id="slotDaySelect" class="form-control" required>
                            <option value="0">{{ __('Sunday') }}</option>
                            <option value="1">{{ __('Monday') }}</option>
                            <option value="2">{{ __('Tuesday') }}</option>
                            <option value="3">{{ __('Wednesday') }}</option>
                            <option value="4">{{ __('Thursday') }}</option>
                            <option value="5">{{ __('Friday') }}</option>
                            <option value="6">{{ __('Saturday') }}</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ __('Start Time') }} <span class="required">*</span></label>
                            <input type="time" id="startTime" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('End Time') }} <span class="required">*</span></label>
                            <input type="time" id="endTime" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-save">
                        <span class="btn-text">{{ __('Save Slot') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add One-Time Slot Modal -->
    <div id="oneTimeModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-plus"></i> {{ __('Add One-Time Availability') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="oneTimeForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('Select Date') }} <span class="required">*</span></label>
                        <input type="date" id="oneTimeDate" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ __('Start Time') }} <span class="required">*</span></label>
                            <input type="time" id="oneTimeStart" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('End Time') }} <span class="required">*</span></label>
                            <input type="time" id="oneTimeEnd" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-save">
                        <span class="btn-text">{{ __('Add Availability') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Block Date Modal -->
    <div id="blockModal" class="modal-overlay">
        <div class="modal-container small">
            <div class="modal-header">
                <h3><i class="fas fa-ban"></i> {{ __('Block Date') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="blockForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('Select Date') }} <span class="required">*</span></label>
                        <input type="date" id="blockDate" class="form-control"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Reason (Optional)') }}</label>
                        <textarea id="blockReason" class="form-control" rows="3"
                            placeholder="{{ __('Vacation, Conference, Personal day, etc...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-danger">
                        <span class="btn-text">{{ __('Block Date') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link href="{{ asset('vendor/fullcalendar/main.min.css') }}" rel="stylesheet">
        <style>
            /* CRITICAL FIXES - NO OVERFLOW */
            html,
            body {
                max-width: 100% !important;
                width: 100% !important;
                overflow-x: hidden !important;
                position: relative !important;
            }

            .schedule-container {
                max-width: 1400px !important;
                width: 100% !important;
                margin: 0 auto !important;
                padding: 20px !important;
                overflow-x: hidden !important;
                box-sizing: border-box !important;
            }

            /* ALL CONTENT MUST RESPECT CONTAINER */
            .stats-row,
            .guide-card,
            .calendar-card,
            .weekly-schedule-grid,
            .copy-schedule-tool,
            .card-header,
            .card-body,
            .weekly-header,
            .weekly-days-grid,
            .copy-tool-body,
            .onetime-slots-section {
                max-width: 100% !important;
                overflow-x: visible !important;
                box-sizing: border-box !important;
            }

            .stats-row {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
                box-sizing: border-box;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.4rem;
                color: white;
            }

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.red {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.gray {
                background: linear-gradient(135deg, #9ca3af, #6b7280);
            }

            .stat-info h3 {
                font-size: 1.6rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 0;
            }

            /* Guide Card - No Arrow */
            .guide-card {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-radius: 20px;
                margin-bottom: 25px;
                overflow: hidden;
                cursor: pointer;
            }

            .guide-header {
                padding: 16px 24px;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .guide-header i:first-child {
                font-size: 1.3rem;
                color: #7c3aed;
            }

            .guide-header h3 {
                margin: 0;
                font-size: 1rem;
                color: #1f2937;
                flex: 1;
            }

            .guide-content {
                padding: 0 24px 24px;
                display: block;
            }

            .guide-content.collapsed {
                display: none;
            }

            .guide-steps {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            .guide-step {
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }

            .step-icon {
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
                flex-shrink: 0;
            }

            .step-text strong {
                display: block;
                font-size: 0.85rem;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .step-text p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .calendar-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-header h3 {
                margin: 0;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .card-header h3 i {
                color: #7c3aed;
            }

            .btn-refresh,
            .btn-block-date {
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .btn-refresh {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-refresh:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-block-date {
                background: #fee2e2;
                color: #dc2626;
            }

            .btn-block-date:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            .card-body {
                padding: 20px;
            }

            .calendar-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #calendar {
                min-height: 550px;
                width: 100%;
                min-width: 300px;
            }

            .weekly-schedule-grid {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .weekly-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .weekly-header h3 {
                margin: 0;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .weekly-header h3 i {
                color: #7c3aed;
            }

            .header-buttons {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .btn-add-slot-global,
            .btn-add-onetime {
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .btn-add-slot-global {
                background: #7c3aed;
                color: white;
            }

            .btn-add-slot-global:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-add-onetime {
                background: #8b5cf6;
                color: white;
            }

            .btn-add-onetime:hover {
                background: #7c3aed;
                transform: translateY(-2px);
            }

            .weekly-days-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                padding: 20px;
            }

            .schedule-day-card {
                background: #f9fafb;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #f0f0f0;
                transition: all 0.2s;
            }

            .schedule-day-card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .day-card-header {
                background: white;
                padding: 12px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f0f0f0;
            }

            .day-name {
                font-weight: 600;
                color: #1f2937;
                font-size: 0.9rem;
            }

            .btn-add-slot-day {
                background: none;
                border: none;
                color: #7c3aed;
                cursor: pointer;
                font-size: 1rem;
                transition: transform 0.2s;
            }

            .btn-add-slot-day:hover {
                transform: scale(1.1);
            }

            .day-slots-list {
                padding: 12px;
                display: flex;
                flex-direction: column;
                gap: 8px;
                min-height: 120px;
            }

            .time-slot-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 12px;
                background: white;
                border-radius: 10px;
                border: 1px solid #e5e7eb;
                transition: all 0.2s;
                flex-wrap: wrap;
                gap: 8px;
            }

            .time-slot-item:hover {
                border-color: #c4b5fd;
                background: #f5f3ff;
            }

            .slot-time-range {
                font-size: 0.75rem;
                color: #1f2937;
                font-weight: 500;
            }

            .slot-actions-group {
                display: flex;
                gap: 6px;
            }

            .edit-slot-btn,
            .delete-slot-btn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 4px 6px;
                border-radius: 6px;
                transition: all 0.2s;
            }

            .edit-slot-btn {
                color: #7c3aed;
            }

            .edit-slot-btn:hover {
                background: #ede9fe;
            }

            .delete-slot-btn {
                color: #ef4444;
            }

            .delete-slot-btn:hover {
                background: #fee2e2;
            }

            .empty-slots-message {
                text-align: center;
                padding: 20px 10px;
                color: #9ca3af;
                font-size: 0.7rem;
            }

            .empty-slots-message i {
                font-size: 1rem;
                margin-bottom: 5px;
                display: block;
            }

            /* One-Time Slots Section */
            .onetime-slots-section {
                margin: 0 20px 20px;
                padding: 15px;
                background: #f5f3ff;
                border-radius: 16px;
            }

            .onetime-header {
                margin-bottom: 12px;
            }

            .onetime-header h4 {
                margin: 0;
                font-size: 0.85rem;
                color: #7c3aed;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .onetime-slots-list {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .onetime-slot-item {
                display: flex;
                align-items: center;
                gap: 12px;
                background: white;
                padding: 8px 15px;
                border-radius: 40px;
                border: 1px solid #e5e7eb;
                font-size: 0.75rem;
                flex-wrap: wrap;
            }

            .onetime-date {
                display: flex;
                align-items: center;
                gap: 5px;
                color: #1f2937;
            }

            .onetime-date i {
                color: #7c3aed;
                font-size: 0.7rem;
            }

            .onetime-time {
                color: #6b7280;
            }

            .delete-onetime-btn {
                background: none;
                border: none;
                color: #ef4444;
                cursor: pointer;
                font-size: 0.7rem;
                padding: 4px 8px;
                border-radius: 20px;
                transition: all 0.2s;
            }

            .delete-onetime-btn:hover {
                background: #fee2e2;
            }

            .copy-schedule-tool {
                margin: 0 20px 20px;
                padding-top: 20px;
                border-top: 1px solid #f0f0f0;
                overflow: visible !important;
            }

            .copy-tool-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
            }

            .copy-tool-header i {
                font-size: 1.1rem;
                color: #7c3aed;
            }

            .copy-tool-header h4 {
                margin: 0;
                font-size: 0.9rem;
                color: #1f2937;
            }

            .copy-tool-body {
                display: grid;
                grid-template-columns: 1fr 2fr auto;
                gap: 20px;
                align-items: end;
                overflow: visible !important;
            }

            .copy-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
                overflow: visible !important;
            }

            .copy-field label {
                font-size: 0.7rem;
                font-weight: 500;
                color: #374151;
            }

            .form-control {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.85rem;
                background: white;
                box-sizing: border-box;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            select[multiple] {
                height: auto;
                min-height: 100px;
            }

            .form-hint {
                font-size: 0.6rem;
                color: #9ca3af;
                margin-top: 5px;
                display: block;
            }

            .btn-copy-schedule {
                padding: 10px 24px;
                background: #10b981;
                color: white;
                border: none;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.8rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .btn-copy-schedule:hover {
                background: #059669;
                transform: translateY(-2px);
            }

            /* FullCalendar Styles */
            .fc .fc-toolbar {
                flex-wrap: wrap;
            }

            .fc .fc-toolbar-title {
                font-size: 1.2rem;
            }

            .fc .fc-button-primary {
                background-color: #7c3aed;
                border-color: #7c3aed;
                text-transform: capitalize;
            }

            .fc .fc-button-primary:hover {
                background-color: #6d28d9;
                border-color: #6d28d9;
            }

            .fc .fc-button-primary:focus {
                box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
            }

            .fc .fc-button-primary:not(:disabled).fc-button-active,
            .fc .fc-button-primary:not(:disabled):active {
                background-color: #5b21b6;
                border-color: #5b21b6;
            }

            .fc .fc-today-button {
                background-color: #7c3aed;
                border-color: #7c3aed;
            }

            .fc .fc-today-button:disabled {
                background-color: #c4b5fd;
                border-color: #c4b5fd;
            }

            .fc .fc-daygrid-day.fc-day-today {
                background-color: #f5f3ff;
            }

            .fc .fc-timegrid-col.fc-day-today {
                background-color: #f5f3ff;
            }

            .fc .fc-daygrid-day-number,
            .fc .fc-col-header-cell-cushion,
            .fc .fc-timegrid-slot-label-cushion {
                color: #374151;
            }

            /* Modals */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.2s;
            }

            .modal-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .modal-container {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                transform: scale(0.9);
                transition: transform 0.2s;
                box-sizing: border-box;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-container.small {
                max-width: 400px;
            }

            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h3 {
                margin: 0;
                font-size: 1.2rem;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                color: #6b7280;
            }

            .modal-body {
                padding: 24px;
            }

            .modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-top: 15px;
            }

            .required {
                color: #ef4444;
            }

            .btn-cancel,
            .btn-save,
            .btn-danger {
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                transition: all 0.2s;
            }

            .btn-cancel {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
            }

            .btn-save {
                background: #7c3aed;
                color: white;
            }

            .btn-save:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-danger {
                background: #ef4444;
                color: white;
            }

            .btn-danger:hover {
                background: #dc2626;
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

            .animate-fade-in {
                animation: fadeIn 0.4s ease;
            }

            /* ========== RESPONSIVE BREAKPOINTS ========== */

            /* Tablet (641px - 1024px) */
            @media (min-width: 641px) and (max-width: 1024px) {
                .weekly-days-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }

                .stats-row {
                    grid-template-columns: repeat(2, 1fr) !important;
                }

                .guide-steps {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }

            /* Mobile (up to 640px) */
            @media (max-width: 640px) {
                .schedule-container {
                    padding: 12px !important;
                }

                .stats-row {
                    grid-template-columns: repeat(2, 1fr) !important;
                    gap: 12px !important;
                }

                .stat-card {
                    padding: 12px !important;
                    gap: 10px !important;
                }

                .stat-icon {
                    width: 40px !important;
                    height: 40px !important;
                }

                .stat-icon i {
                    font-size: 1.1rem !important;
                }

                .stat-info h3 {
                    font-size: 1.2rem !important;
                }

                .stat-info p {
                    font-size: 0.65rem !important;
                }

                .guide-steps {
                    grid-template-columns: 1fr !important;
                    gap: 12px !important;
                }

                .guide-header {
                    padding: 12px 16px !important;
                }

                .guide-content {
                    padding: 0 16px 16px !important;
                }

                .weekly-days-grid {
                    grid-template-columns: 1fr !important;
                    gap: 12px !important;
                    padding: 15px !important;
                }

                .copy-tool-body {
                    grid-template-columns: 1fr !important;
                    gap: 12px !important;
                }

                .btn-copy-schedule {
                    white-space: normal !important;
                    width: 100% !important;
                }

                .card-header {
                    flex-direction: column !important;
                    align-items: stretch !important;
                }

                .header-actions {
                    display: flex !important;
                    gap: 10px !important;
                }

                .btn-refresh,
                .btn-block-date {
                    flex: 1 !important;
                    text-align: center !important;
                }

                .weekly-header {
                    flex-direction: column !important;
                    align-items: stretch !important;
                }

                .header-buttons {
                    width: 100% !important;
                }

                .btn-add-slot-global,
                .btn-add-onetime {
                    flex: 1 !important;
                    text-align: center !important;
                }

                .onetime-slots-list {
                    flex-direction: column !important;
                }

                .onetime-slot-item {
                    flex-wrap: wrap !important;
                    justify-content: space-between !important;
                    border-radius: 16px !important;
                }

                .time-slot-item {
                    flex-direction: row !important;
                    justify-content: space-between !important;
                }

                /* Mobile calendar adjustments */
                .fc .fc-toolbar {
                    flex-direction: column !important;
                    gap: 10px !important;
                }

                .fc .fc-toolbar-title {
                    font-size: 1rem !important;
                }

                .fc .fc-button {
                    padding: 0.3rem 0.6rem !important;
                    font-size: 0.7rem !important;
                }
            }

            /* iPhone SE specific (up to 380px) - Smaller button text */
            @media (max-width: 380px) {
                .schedule-container {
                    padding: 10px !important;
                }

                .stats-row {
                    gap: 8px !important;
                }

                .stat-card {
                    padding: 10px !important;
                }

                .stat-icon {
                    width: 35px !important;
                    height: 35px !important;
                }

                .stat-info h3 {
                    font-size: 1rem !important;
                }

                .guide-step {
                    gap: 8px !important;
                }

                .step-icon {
                    width: 24px !important;
                    height: 24px !important;
                    font-size: 0.7rem !important;
                }

                .step-text strong {
                    font-size: 0.7rem !important;
                }

                .step-text p {
                    font-size: 0.6rem !important;
                }

                .day-card-header {
                    padding: 10px 12px !important;
                }

                .slot-time-range {
                    font-size: 0.65rem !important;
                }

                .edit-slot-btn,
                .delete-slot-btn {
                    padding: 4px 5px !important;
                }

                /* Smaller button text for iPhone SE */
                .btn-add-slot-global,
                .btn-add-onetime {
                    font-size: 0.65rem !important;
                    padding: 6px 10px !important;
                }

                .btn-add-slot-global i,
                .btn-add-onetime i {
                    font-size: 0.6rem !important;
                }

                .btn-refresh,
                .btn-block-date {
                    font-size: 0.65rem !important;
                    padding: 6px 10px !important;
                }

                .btn-copy-schedule {
                    font-size: 0.7rem !important;
                    padding: 8px 12px !important;
                }

                .copy-tool-header h4 {
                    font-size: 0.8rem !important;
                }

                .onetime-header h4 {
                    font-size: 0.75rem !important;
                }

                .onetime-slot-item {
                    font-size: 0.65rem !important;
                    padding: 6px 12px !important;
                }
            }

            body.rtl .fc .fc-toolbar-title {
                text-align: right;
            }

            body.rtl .fc .fc-button-group {
                flex-direction: row-reverse;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let calendar = null;

            document.addEventListener('DOMContentLoaded', function () {
                // Guide Card Toggle - No Arrow, click anywhere on card
                const guideCard = document.querySelector('.guide-card');
                const guideContent = document.getElementById('guideContent');

                if (guideCard && guideContent) {
                    guideCard.addEventListener('click', function (e) {
                        // Don't close if clicking on buttons inside (if any future buttons added)
                        if (e.target.closest('button')) return;
                        guideContent.classList.toggle('collapsed');
                    });
                }

                // Initialize Calendar
                const calendarEl = document.getElementById('calendar');
                if (calendarEl && typeof FullCalendar !== 'undefined') {
                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: window.innerWidth < 768 ? 'dayGridMonth' : 'timeGridWeek',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: window.innerWidth < 768 ? 'dayGridMonth,timeGridDay' : 'timeGridWeek,timeGridDay,dayGridMonth'
                        },
                        slotMinTime: '00:00:00',
                        slotMaxTime: '24:00:00',
                        allDaySlot: true,
                        height: 'auto',
                        events: '{{ route("specialist.schedule.events") }}',
                        eventClick: function (info) {
                            let title = info.event.title;
                            let isAllDay = info.event.allDay;

                            if (isAllDay) {
                                let startDate = info.event.start ? info.event.start.toLocaleDateString() : '';
                                let endDate = info.event.end ? new Date(info.event.end.getTime() - 86400000).toLocaleDateString() : '';

                                Swal.fire({
                                    title: title,
                                    html: `<strong>{{ __('Date') }}:</strong> ${startDate}${endDate && endDate !== startDate ? ' - ' + endDate : ''}<br>
                                                                   <strong>{{ __('Type') }}:</strong> {{ __('All Day (Unavailable)') }}`,
                                    icon: 'warning',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: '{{ __("OK") }}'
                                });
                            } else {
                                let start = info.event.start;
                                let end = info.event.end;
                                let startStr = start ? start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                                let endStr = end ? end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

                                Swal.fire({
                                    title: title,
                                    html: `<strong>{{ __('Time') }}:</strong> ${startStr} - ${endStr}`,
                                    icon: 'info',
                                    confirmButtonColor: '#7c3aed',
                                    confirmButtonText: '{{ __("OK") }}'
                                });
                            }
                        }
                    });
                    calendar.render();
                }

                // Handle window resize for calendar view
                let resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        if (calendar) {
                            const isMobile = window.innerWidth < 768;
                            const currentView = calendar.view.type;
                            if (isMobile && (currentView === 'timeGridWeek' || currentView === 'timeGridDay')) {
                                calendar.changeView('dayGridMonth');
                            } else if (!isMobile && currentView === 'dayGridMonth') {
                                calendar.changeView('timeGridWeek');
                            }
                        }
                    }, 250);
                });

                // Refresh Calendar
                document.getElementById('refreshCalendar')?.addEventListener('click', function () {
                    if (calendar) calendar.refetchEvents();
                    Swal.fire({ icon: 'success', title: '{{ __("Refreshed") }}', timer: 1500, showConfirmButton: false });
                });

                // Modal functions
                const slotModal = document.getElementById('slotModal');
                const oneTimeModal = document.getElementById('oneTimeModal');
                const blockModal = document.getElementById('blockModal');

                function closeModal(modal) { if (modal) modal.classList.remove('active'); }
                function openModal(modal) { if (modal) modal.classList.add('active'); }

                document.querySelectorAll('.modal-close, .btn-cancel').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
                    });
                });

                // Close modal on backdrop click
                document.querySelectorAll('.modal-overlay').forEach(overlay => {
                    overlay.addEventListener('click', function (e) {
                        if (e.target === this) {
                            this.classList.remove('active');
                        }
                    });
                });

                // Block Date Modal
                document.getElementById('blockDateBtn')?.addEventListener('click', () => { openModal(blockModal); });

                document.getElementById('blockForm')?.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const date = document.getElementById('blockDate').value;
                    const reason = document.getElementById('blockReason').value;
                    const submitBtn = blockModal.querySelector('.btn-danger');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch('{{ route("specialist.schedule.block") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ specific_date: date, reason: reason })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                            closeModal(blockModal);
                            location.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });

                // Add Recurring Slot Modal
                document.getElementById('addRecurringBtn')?.addEventListener('click', () => {
                    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> {{ __("Add Recurring Time Slot") }}';
                    document.getElementById('slotId').value = '';
                    document.getElementById('slotDaySelect').value = '0';
                    document.getElementById('startTime').value = '09:00';
                    document.getElementById('endTime').value = '17:00';
                    openModal(slotModal);
                });

                // Add One-Time Slot Modal
                document.getElementById('addOneTimeBtn')?.addEventListener('click', () => {
                    document.getElementById('oneTimeDate').value = '';
                    document.getElementById('oneTimeStart').value = '09:00';
                    document.getElementById('oneTimeEnd').value = '17:00';
                    openModal(oneTimeModal);
                });

                // Add slot for specific day (recurring)
                document.querySelectorAll('.btn-add-slot-day').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const day = btn.dataset.day;
                        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> {{ __("Add Recurring Time Slot") }}';
                        document.getElementById('slotId').value = '';
                        document.getElementById('slotDaySelect').value = day;
                        document.getElementById('startTime').value = '09:00';
                        document.getElementById('endTime').value = '17:00';
                        openModal(slotModal);
                    });
                });

                // Edit recurring slot (event delegation)
                document.addEventListener('click', function (e) {
                    const editBtn = e.target.closest('.edit-slot-btn');
                    if (editBtn) {
                        const id = editBtn.dataset.id;
                        const day = editBtn.dataset.day;
                        const start = editBtn.dataset.start;
                        const end = editBtn.dataset.end;

                        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> {{ __("Edit Time Slot") }}';
                        document.getElementById('slotId').value = id;
                        document.getElementById('slotDaySelect').value = day;
                        document.getElementById('startTime').value = start;
                        document.getElementById('endTime').value = end;
                        openModal(slotModal);
                    }

                    const deleteBtn = e.target.closest('.delete-slot-btn');
                    if (deleteBtn) {
                        const id = deleteBtn.dataset.id;
                        Swal.fire({
                            title: '{{ __("Delete Slot") }}',
                            text: '{{ __("Are you sure you want to delete this time slot?") }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: '{{ __("Yes, delete") }}',
                            cancelButtonText: '{{ __("Cancel") }}'
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                const response = await fetch(`/specialist/schedule/availability/${id}`, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false });
                                    location.reload();
                                }
                            }
                        });
                    }

                    const deleteOneTimeBtn = e.target.closest('.delete-onetime-btn');
                    if (deleteOneTimeBtn) {
                        const id = deleteOneTimeBtn.dataset.id;
                        Swal.fire({
                            title: '{{ __("Delete One-Time Slot") }}',
                            text: '{{ __("Are you sure you want to remove this one-time availability?") }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: '{{ __("Yes, remove") }}',
                            cancelButtonText: '{{ __("Cancel") }}'
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                const response = await fetch(`/specialist/schedule/availability/${id}`, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    Swal.fire({ icon: 'success', title: '{{ __("Deleted") }}', text: data.message, timer: 1500, showConfirmButton: false });
                                    location.reload();
                                }
                            }
                        });
                    }
                });

                // Submit Recurring Slot Form
                document.getElementById('slotForm')?.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const id = document.getElementById('slotId').value;
                    const day = document.getElementById('slotDaySelect').value;
                    const startTime = document.getElementById('startTime').value;
                    const endTime = document.getElementById('endTime').value;
                    const submitBtn = document.querySelector('#slotForm .btn-save');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const url = id ? `/specialist/schedule/availability/${id}` : '{{ route("specialist.schedule.availability.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ day_of_week: day, start_time: startTime, end_time: endTime })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                            closeModal(slotModal);
                            location.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });

                // Submit One-Time Slot Form
                document.getElementById('oneTimeForm')?.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const date = document.getElementById('oneTimeDate').value;
                    const startTime = document.getElementById('oneTimeStart').value;
                    const endTime = document.getElementById('oneTimeEnd').value;
                    const submitBtn = oneTimeModal.querySelector('.btn-save');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    try {
                        const response = await fetch('{{ route("specialist.schedule.one-time") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ specific_date: date, start_time: startTime, end_time: endTime })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                            closeModal(oneTimeModal);
                            location.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message });
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}' });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });

                // Copy Week
                document.getElementById('copyWeekBtn')?.addEventListener('click', async () => {
                    const sourceDay = document.getElementById('copySourceDay').value;
                    const targetDays = Array.from(document.getElementById('copyTargetDays').selectedOptions).map(opt => opt.value);

                    if (!sourceDay) {
                        Swal.fire({ icon: 'warning', title: '{{ __("Error") }}', text: '{{ __("Please select a source day") }}', confirmButtonText: '{{ __("OK") }}' });
                        return;
                    }
                    if (targetDays.length === 0) {
                        Swal.fire({ icon: 'warning', title: '{{ __("Error") }}', text: '{{ __("Please select at least one target day") }}', confirmButtonText: '{{ __("OK") }}' });
                        return;
                    }

                    const copyBtn = document.getElementById('copyWeekBtn');
                    const originalText = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Copying...") }}';
                    copyBtn.disabled = true;

                    try {
                        const response = await fetch('{{ route("specialist.schedule.copy-week") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ source_day: sourceDay, target_days: targetDays })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: data.message, timer: 1500, showConfirmButton: false });
                            location.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: data.message, confirmButtonText: '{{ __("OK") }}' });
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Network error") }}', confirmButtonText: '{{ __("OK") }}' });
                    } finally {
                        copyBtn.innerHTML = originalText;
                        copyBtn.disabled = false;
                    }
                });
            });
        </script>
    @endpush
@endsection