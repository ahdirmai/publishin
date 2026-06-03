<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_weekly_summary')->default(true);
            $table->boolean('push_post_published')->default(true);
            $table->boolean('push_mentions')->default(false);
            $table->boolean('email_monthly_report')->default(true);
            $table->boolean('push_schedule_reminder')->default(true);
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void { Schema::dropIfExists('notification_settings'); }
};
