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
        Schema::table('post_versions', function (Blueprint $table) {
            $table->unsignedBigInteger('analytics_sum_reach')->nullable()->after('published_at');
            $table->unsignedBigInteger('analytics_sum_impressions')->nullable()->after('analytics_sum_reach');
            $table->unsignedBigInteger('analytics_sum_likes')->nullable()->after('analytics_sum_impressions');
            $table->unsignedBigInteger('analytics_sum_comments')->nullable()->after('analytics_sum_likes');
            $table->unsignedBigInteger('analytics_sum_shares')->nullable()->after('analytics_sum_comments');
            $table->unsignedBigInteger('analytics_sum_saves')->nullable()->after('analytics_sum_shares');
            $table->decimal('analytics_avg_engagement_rate', 8, 4)->nullable()->after('analytics_sum_saves');
            $table->timestamp('analytics_fetched_at')->nullable()->after('analytics_avg_engagement_rate');
        });
    }

    public function down(): void
    {
        Schema::table('post_versions', function (Blueprint $table) {
            $table->dropColumn([
                'analytics_sum_reach',
                'analytics_sum_impressions',
                'analytics_sum_likes',
                'analytics_sum_comments',
                'analytics_sum_shares',
                'analytics_sum_saves',
                'analytics_avg_engagement_rate',
                'analytics_fetched_at',
            ]);
        });
    }
};
