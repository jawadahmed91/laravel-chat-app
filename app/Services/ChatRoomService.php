<?php
namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\ChatRoom;

class ChatRoomService
{
    public function createRoom(array $userIds, string $type = 'private', string $name = null)
    {
        $room = ChatRoom::create([
            'type' => $type,
            'name' => $name,
            'participants' => json_encode($userIds),
        ]);

        $this->createMessagesTable($room->id);

        return $room;
    }

    protected function createMessagesTable($roomId)
    {
        $tableName = 'chat_room_' . $roomId;

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id');
                $table->text('message');
                $table->string('type')->default('text'); // image, file, etc.
                $table->string('file_url')->nullable();
                $table->string('file_type')->nullable(); // image, video, doc, etc.
                $table->json('seen_by')->nullable(); // e.g., [1, 2]
                $table->timestamps();
            });
        }
    }
}
