<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('account_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('followers')->default(0);
            $table->bigInteger('follower_change')->default(0);
            $table->unsignedBigInteger('reach')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->unsignedInteger('posts_published')->default(0);
            $table->timestamps();
            $table->unique(['social_account_id', 'date']);
            $table->index(['social_account_id', 'date']);
        });
    }
    public function down(): void { Schema::dropIfExists('account_analytics'); }
};
