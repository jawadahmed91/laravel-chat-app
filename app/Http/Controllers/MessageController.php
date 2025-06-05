<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Services\MessageService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class MessageController extends Controller
{
    public function send(Request $request, MessageService $service)
    {
        $data = $request->validate([
            'room_id' => 'required|integer',
            'message' => 'required|string',
            'type' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10 MB max
        ]);

        $file = $request->file('file');
        $messageId = $service->sendMessage(
            $data['room_id'],
            1,//auth()->id(),
            $data['message'],
            $data['type'] ?? 'text',
            $file
        );

        // Broadcast event (next step)
        try {
            // Force sync to catch broadcast failure directly
            Config::set('broadcasting.default', 'reverb');
            Config::set('queue.default', 'sync');
            event(
                new MessageSent($data['room_id'], [
                'id' => $messageId,
                'sender_id' => 1, //auth()->id(),
                'message' => $data['message'],
                'type' => $data['type'] ?? 'text',
                'created_at' => now()->toDateTimeString()
                ])
            );
        } catch (\Exception $e) {
            // Log the Reverb failure
            Log::error('Reverb broadcast failed', ['error' => $e->getMessage()]);

            // Fallback: broadcast normally using Laravel's `event()` system
            config(['broadcasting.default' => 'pusher']); // temporarily switch

            // Fallback: push manually using Pusher
            event(new MessageSent($data['room_id'], [
                'id' => $messageId,
                'sender_id' => 1, //auth()->id(),
                'message' => $data['message'],
                'type' => $data['type'] ?? 'text',
                'created_at' => now()->toDateTimeString()
            ]));

            // Optional: Restore queue/broadcasting config to default
            Config::set('queue.default', 'redis');
            Config::set('broadcasting.default', 'reverb');
        }
        return response()->json(['message_id' => $messageId]);
    }

    public function fetch(Request $request, MessageService $service)
    {
        $data = $request->validate([
            'room_id' => 'required|integer',
        ]);

        $messages = $service->getMessages($data['room_id']);
        return response()->json($messages);
    }
}
