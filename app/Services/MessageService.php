<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MessageService
{
    public function sendMessage(int $roomId, int $senderId, string $message, string $type = 'text', $file = null)
    {
        $tableName = 'chat_room_' . $roomId;

        if (!Schema::hasTable($tableName)) {
            throw new \Exception("Chat room $roomId does not exist.");
        }

        $fileUrl = null;
        $fileType = null;

        if ($file) {
            $path = $file->store("chat/room_$roomId", 'public');
            $fileUrl = url('storage/' . $path);
            $fileType = $file->getClientMimeType();
        }

        return DB::table($tableName)->insertGetId([
            'sender_id' => $senderId,
            'message' => $message,
            'type' => $type,
            'file_url' => $fileUrl,
            'file_type' => $fileType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getMessages(int $roomId, int $perPage = 20)
    {
        $tableName = 'chat_room_' . $roomId;

        if (!Schema::hasTable($tableName)) {
            throw new \Exception("Chat room $roomId does not exist.");
        }

        return DB::table($tableName)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
