<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TherapySession;
use App\Models\Conversation;
use App\Models\Message;

class UnlockTextSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unlock:text-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock text session conversations when session time arrives';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for text sessions to unlock...');

        // Get text sessions that started within the last 30 minutes and are still scheduled
        $sessions = TherapySession::where('session_type', 'text')
            ->where('session_datetime', '<=', now())
            ->where('session_datetime', '>', now()->subMinutes(30))
            ->where('status', 'scheduled')
            ->get();

        $unlockedCount = 0;

        foreach ($sessions as $session) {
            // Find conversation linked to this session
            $conversation = Conversation::where('therapy_session_id', $session->id)->first();

            if ($conversation && $conversation->is_locked) {
                // Unlock the conversation
                $conversation->update([
                    'is_locked' => false,
                    'locked_at' => null,
                ]);

                // Add system message to notify users
                Message::create([
                    'sender_id' => $session->specialist_id,
                    'receiver_id' => $session->patient_id,
                    'conversation_id' => $conversation->id,
                    'content' => '🔓 ' . __('Your text session has started! You can now send messages.'),
                    'is_system_message' => true,
                    'sent_at' => now(),
                ]);

                // Also update conversation last message
                $conversation->updateLastMessage(
                    Message::where('conversation_id', $conversation->id)->latest('sent_at')->first()
                );

                $unlockedCount++;
                $this->info("Unlocked conversation ID: {$conversation->id} for session ID: {$session->id}");
            }
        }

        $this->info("Completed. Unlocked {$unlockedCount} conversations.");
    }
}
