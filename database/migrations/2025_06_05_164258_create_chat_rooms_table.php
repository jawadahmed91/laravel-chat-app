<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * -- Create chat_rooms table
    CREATE TABLE `chat_rooms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` ENUM('private', 'group') NOT NULL,
    `name` VARCHAR(255) NULL,
    `participants` JSON NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     */
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['private', 'group']);
            $table->string('name')->nullable(); // for group chats
            $table->json('participants');       // [1, 2] or [1, 3, 4]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
