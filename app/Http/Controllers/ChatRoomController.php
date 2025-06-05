<?php

namespace App\Http\Controllers;

use App\Services\ChatRoomService;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function store(Request $request, ChatRoomService $service)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:2',
            'type' => 'required|in:private,group',
            'name' => 'nullable|string'
        ]);

        $room = $service->createRoom(
            $validated['user_ids'],
            $validated['type'],
            $validated['name']
        );

        return response()->json(['room' => $room]);
    }
}
