<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SimpleChat extends Controller
{
    public function simple()
    {
        $messages = Message::with('sender')
            ->where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        $users = User::where('id', '!=', auth()->id())->get();

        return view('chat.simple', compact('messages', 'users'));
    }

    public function send(Request $request)
    {
        Log::info('Chat send called', $request->all());

        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'content' => 'required|string|max:5000',
            ]);

            $user = Auth::user();

            $participantOne = min($user->id, $request->receiver_id);
            $participantTwo = max($user->id, $request->receiver_id);

            $conversation = Conversation::firstOrCreate([
                'participant_one' => $participantOne,
                'participant_two' => $participantTwo,
                'type' => 'general',
            ]);

            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'conversation_id' => $conversation->id,
                'content' => $request->content,
                'sent_at' => now(),
            ]);

            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Chat send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
