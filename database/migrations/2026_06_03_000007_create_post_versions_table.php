<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('post_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_account_id')->constrained()->cascadeOnDelete();
            $table->text('caption')->nullable();
            $table->enum('content_type', ['feed', 'reel', 'story', 'video', 'carousel', 'thread', 'foto'])->default('feed');
            $table->enum('status', ['pending', 'publishing', 'published', 'failed', 'cancelled'])->default('pending');
            $table->string('platform_post_id')->nullable();
            $table->string('post_url')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->json('platform_options')->nullable();
            $table->timestamps();
            $table->index(['post_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('post_versions'); }
};
