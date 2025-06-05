<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 
     * -- Create cache table
        CREATE TABLE `cache` (
        `key` VARCHAR(255) NOT NULL,
        `value` MEDIUMTEXT NOT NULL,
        `expiration` INT NOT NULL,
        PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Create cache_locks table
        CREATE TABLE `cache_locks` (
        `key` VARCHAR(255) NOT NULL,
        `owner` VARCHAR(255) NOT NULL,
        `expiration` INT NOT NULL,
        PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Optional: Add indexes for better performance
        CREATE INDEX `idx_cache_expiration` ON `cache` (`expiration`);
        CREATE INDEX `idx_cache_locks_expiration` ON `cache_locks` (`expiration`);

     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
