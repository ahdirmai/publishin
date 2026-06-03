<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('platform', ['instagram', 'facebook', 'tiktok', 'twitter', 'youtube']);
            $table->string('platform_user_id');
            $table->string('username');
            $table->string('display_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->text('access_token');   // encrypted
            $table->text('refresh_token')->nullable(); // encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->string('page_id')->nullable();
            $table->json('scopes')->nullable();
            $table->unsignedBigInteger('follower_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['platform', 'platform_user_id', 'user_id']);
            $table->index(['user_id', 'platform']);
        });
    }
    public function down(): void { Schema::dropIfExists('social_accounts'); }
};
