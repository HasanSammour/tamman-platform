<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all recurring availability
        $recurringSlots = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
        
        // Calculate days with slots (unique days that have at least one slot)
        $daysWithSlots = $recurringSlots->pluck('day_of_week')->unique()->count();
        
        // Calculate schedule status
        $scheduleStatus = $recurringSlots->count() > 0 ? 'active' : 'inactive';
        
        $weeklySchedule = [];
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $index => $day) {
            $weeklySchedule[$index] = [
                'day' => $day,
                'day_index' => $index,
                'slots' => $recurringSlots->filter(function($slot) use ($index) {
                    return $slot->day_of_week == $index;
                })->values(),
            ];
        }
        
        // Get blocked dates
        $blockedDates = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('is_available', false)
            ->where('specific_date', '>=', Carbon::today())
            ->get()
            ->pluck('specific_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
        
        // Get one-time availability (non-recurring, available)
        $oneTimeSlots = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('is_available', true)
            ->where('specific_date', '>=', Carbon::today())
            ->orderBy('specific_date')
            ->orderBy('start_time')
            ->get();
        
        return view('specialist.schedule.index', compact(
            'user',
            'recurringSlots',
            'weeklySchedule',
            'blockedDates',
            'oneTimeSlots',
            'daysWithSlots',
            'scheduleStatus'
        ));
    }
    
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->start ? Carbon::parse($request->start) : Carbon::today();
        $endDate = $request->end ? Carbon::parse($request->end) : Carbon::today()->addDays(30);
        
        $events = [];
        
        // Get blocked dates (vacation, days off)
        $blockedDates = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('is_available', false)
            ->where('specific_date', '>=', $startDate)
            ->where('specific_date', '<=', $endDate)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->specific_date)->format('Y-m-d');
            });
        
        // Get one-time availability (non-recurring, available)
        $oneTimeSlots = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('is_available', true)
            ->where('specific_date', '>=', $startDate)
            ->where('specific_date', '<=', $endDate)
            ->get();
        
        // Add one-time availability events
        foreach ($oneTimeSlots as $slot) {
            $date = Carbon::parse($slot->specific_date);
            $startDateTime = $date->copy()->setTimeFromTimeString($slot->start_time);
            $endDateTime = $date->copy()->setTimeFromTimeString($slot->end_time);
            
            $events[] = [
                'id' => 'onetime_' . $slot->id,
                'title' => '⭐ ' . __('Available (One-time)'),
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'backgroundColor' => '#8b5cf6',
                'borderColor' => '#8b5cf6',
                'textColor' => 'white',
                'allDay' => false,
            ];
        }
        
        // Get recurring availability
        $recurringSlots = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', true)
            ->where('is_available', true)
            ->get();
        
        // Generate recurring events, skipping blocked dates
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            
            // Skip if this date is blocked
            if ($blockedDates->has($dateKey)) {
                continue;
            }
            
            $dayOfWeek = $date->dayOfWeek;
            $daySlots = $recurringSlots->filter(function($slot) use ($dayOfWeek) {
                return $slot->day_of_week == $dayOfWeek;
            });
            
            foreach ($daySlots as $slot) {
                $startDateTime = $date->copy()->setTimeFromTimeString($slot->start_time);
                $endDateTime = $date->copy()->setTimeFromTimeString($slot->end_time);
                
                $events[] = [
                    'id' => 'recurring_' . $slot->id . '_' . $dateKey,
                    'title' => '📅 ' . __('Available'),
                    'start' => $startDateTime->toIso8601String(),
                    'end' => $endDateTime->toIso8601String(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                    'textColor' => 'white',
                    'allDay' => false,
                ];
            }
        }
        
        // Add blocked dates as all-day events
        foreach ($blockedDates as $blockedDate => $block) {
            $date = Carbon::parse($blockedDate);
            
            $events[] = [
                'id' => 'blocked_' . $block->id,
                'title' => '🚫 ' . __('Unavailable'),
                'start' => $date->toIso8601String(),
                'end' => $date->copy()->addDay()->toIso8601String(),
                'backgroundColor' => '#ef4444',
                'borderColor' => '#ef4444',
                'textColor' => 'white',
                'allDay' => true,
            ];
        }
        
        // Add booked sessions
        $bookedSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'scheduled')
            ->where('session_datetime', '>=', $startDate)
            ->where('session_datetime', '<=', $endDate)
            ->with('patient')
            ->get();
        
        foreach ($bookedSessions as $session) {
            $sessionDate = Carbon::parse($session->session_datetime)->format('Y-m-d');
            
            // Skip if the session date is blocked
            if ($blockedDates->has($sessionDate)) {
                continue;
            }
            
            $sessionTime = Carbon::parse($session->session_datetime);
            $endTime = $sessionTime->copy()->addMinutes($session->duration_minutes);
            
            $events[] = [
                'id' => 'session_' . $session->id,
                'title' => '👤 ' . $session->patient->name,
                'start' => $sessionTime->toIso8601String(),
                'end' => $endTime->toIso8601String(),
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'textColor' => 'white',
                'allDay' => false,
            ];
        }
        
        return response()->json($events);
    }
    
    public function storeAvailability(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        $user = Auth::user();
        
        $availability = Availability::create([
            'specialist_id' => $user->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time . ':00',
            'end_time' => $request->end_time . ':00',
            'is_recurring' => true,
            'is_available' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('Recurring availability added successfully'),
            'availability' => $availability
        ]);
    }
    
    public function storeOneTime(Request $request)
    {
        $request->validate([
            'specific_date' => 'required|date|after:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);
        
        $user = Auth::user();
        $date = Carbon::parse($request->specific_date);
        
        // Check if already exists for this date and time
        $exists = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('specific_date', $date->format('Y-m-d'))
            ->where('start_time', $request->start_time . ':00')
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => __('This time slot already exists for this date.')
            ], 422);
        }
        
        $availability = Availability::create([
            'specialist_id' => $user->id,
            'day_of_week' => null,
            'start_time' => $request->start_time . ':00',
            'end_time' => $request->end_time . ':00',
            'is_recurring' => false,
            'specific_date' => $date->format('Y-m-d'),
            'is_available' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('One-time availability added successfully'),
            'availability' => $availability
        ]);
    }
    
    public function updateAvailability(Request $request, $id)
    {
        $request->validate([
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        $availability = Availability::where('id', $id)
            ->where('specialist_id', Auth::id())
            ->firstOrFail();
        
        $availability->update([
            'start_time' => $request->start_time . ':00',
            'end_time' => $request->end_time . ':00',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('Availability updated successfully')
        ]);
    }
    
    public function destroyAvailability($id)
    {
        $availability = Availability::where('id', $id)
            ->where('specialist_id', Auth::id())
            ->firstOrFail();
        
        $availability->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('Availability removed successfully')
        ]);
    }
    
    public function blockTime(Request $request)
    {
        $request->validate([
            'specific_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        $date = Carbon::parse($request->specific_date);
        
        // Check if already blocked
        $exists = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', false)
            ->where('specific_date', $date->format('Y-m-d'))
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => __('This date is already marked as unavailable.')
            ], 422);
        }
        
        $availability = Availability::create([
            'specialist_id' => $user->id,
            'day_of_week' => null,
            'start_time' => '00:00:00',
            'end_time' => '23:59:00',
            'is_recurring' => false,
            'specific_date' => $date->format('Y-m-d'),
            'is_available' => false,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('Date blocked successfully'),
            'availability' => $availability
        ]);
    }
    
    public function copyWeek(Request $request)
    {
        $request->validate([
            'source_day' => 'required|integer|min:0|max:6',
            'target_days' => 'required|array',
            'target_days.*' => 'integer|min:0|max:6',
        ]);
        
        $user = Auth::user();
        
        $sourceSlots = Availability::where('specialist_id', $user->id)
            ->where('is_recurring', true)
            ->where('day_of_week', $request->source_day)
            ->get();
        
        if ($sourceSlots->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('No availability found for the source day.')
            ], 422);
        }
        
        $created = 0;
        
        foreach ($request->target_days as $targetDay) {
            if ($targetDay == $request->source_day) continue;
            
            foreach ($sourceSlots as $slot) {
                $exists = Availability::where('specialist_id', $user->id)
                    ->where('is_recurring', true)
                    ->where('day_of_week', $targetDay)
                    ->where('start_time', $slot->start_time)
                    ->where('end_time', $slot->end_time)
                    ->exists();
                
                if (!$exists) {
                    Availability::create([
                        'specialist_id' => $user->id,
                        'day_of_week' => $targetDay,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'is_recurring' => true,
                        'is_available' => true,
                    ]);
                    $created++;
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => __('Copied :count slots to selected days', ['count' => $created]),
            'created' => $created
        ]);
    }
}