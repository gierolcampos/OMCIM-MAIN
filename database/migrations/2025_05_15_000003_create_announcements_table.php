<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->enum('status', ['published', 'draft', 'archived'])->default('published');
                $table->boolean('is_boosted')->default(false);
                $table->enum('priority', ['low', 'medium', 'high', 'normal'])->default('normal');
                $table->timestamp('publish_date')->nullable();
                $table->timestamp('expiry_date')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->string('image_path')->nullable();
                $table->string('media_path')->nullable();
                $table->string('text_color')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
