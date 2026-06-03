<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->unsignedInteger('price_monthly');
            $table->unsignedInteger('price_yearly');
            $table->unsignedInteger('max_social_accounts')->default(3);
            $table->unsignedInteger('max_scheduled_posts')->default(30); // 0 = unlimited
            $table->unsignedInteger('max_team_members')->default(1);
            $table->unsignedInteger('max_clients')->default(1);
            $table->boolean('has_ai_features')->default(false);
            $table->boolean('has_white_label')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscription_plans'); }
};
