<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TherapySession;
use App\Models\Notification;
use Carbon\Carbon;

class UpdateNoShowSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:update-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sessions to no_show status if patient did not join within 1 hour after scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get sessions that were scheduled, have passed, and are still in scheduled status
        $oneHourAgo = Carbon::now()->subHour();
        
        $sessions = TherapySession::where('status', 'scheduled')
            ->where('session_datetime', '<=', $oneHourAgo)
            ->get();

        $count = 0;

        foreach ($sessions as $session) {
            $session->status = 'no_show';
            $session->save();

            // Create notification for patient
            Notification::create([
                'user_id' => $session->patient_id,
                'title' => __('Session Marked as No-Show'),
                'message' => __('You did not join your session with :specialist on :date. The session has been marked as no-show.', [
                    'specialist' => $session->specialist->name,
                    'date' => Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y \a\t h:i A')
                ]),
                'type' => 'session_reminder',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            // Create notification for specialist
            Notification::create([
                'user_id' => $session->specialist_id,
                'title' => __('Patient No-Show'),
                'message' => __('The patient :patient did not join the scheduled session on :date and has been marked as no-show.', [
                    'patient' => $session->patient->name,
                    'date' => Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y \a\t h:i A')
                ]),
                'type' => 'session_reminder',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            $count++;
            $this->info("Session #{$session->id} marked as no_show");
        }

        $this->info("Updated {$count} sessions to no_show status");
        
        return Command::SUCCESS;
    }
}
