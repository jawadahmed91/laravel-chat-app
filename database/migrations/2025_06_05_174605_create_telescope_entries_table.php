<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     * 
     * -- Create telescope_entries table
    CREATE TABLE `telescope_entries` (
    `sequence` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `batch_id` CHAR(36) NOT NULL,
    `family_hash` VARCHAR(255) NULL,
    `should_display_on_index` TINYINT(1) NOT NULL DEFAULT 1,
    `type` VARCHAR(20) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`sequence`),
    UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
    INDEX `telescope_entries_batch_id_index` (`batch_id`),
    INDEX `telescope_entries_family_hash_index` (`family_hash`),
    INDEX `telescope_entries_created_at_index` (`created_at`),
    INDEX `telescope_entries_type_should_display_on_index_index` (`type`, `should_display_on_index`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Create telescope_entries_tags table
    CREATE TABLE `telescope_entries_tags` (
    `entry_uuid` CHAR(36) NOT NULL,
    `tag` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`entry_uuid`, `tag`),
    INDEX `telescope_entries_tags_tag_index` (`tag`),
    CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` 
        FOREIGN KEY (`entry_uuid`) 
        REFERENCES `telescope_entries` (`uuid`) 
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Create telescope_monitoring table
    CREATE TABLE `telescope_monitoring` (
    `tag` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`tag`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->uuid('uuid');
            $table->uuid('batch_id');
            $table->string('family_hash')->nullable();
            $table->boolean('should_display_on_index')->default(true);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable();

            $table->unique('uuid');
            $table->index('batch_id');
            $table->index('family_hash');
            $table->index('created_at');
            $table->index(['type', 'should_display_on_index']);
        });

        $schema->create('telescope_entries_tags', function (Blueprint $table) {
            $table->uuid('entry_uuid');
            $table->string('tag');

            $table->primary(['entry_uuid', 'tag']);
            $table->index('tag');

            $table->foreign('entry_uuid')
                ->references('uuid')
                ->on('telescope_entries')
                ->onDelete('cascade');
        });

        $schema->create('telescope_monitoring', function (Blueprint $table) {
            $table->string('tag')->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists('telescope_entries_tags');
        $schema->dropIfExists('telescope_entries');
        $schema->dropIfExists('telescope_monitoring');
    }
};
