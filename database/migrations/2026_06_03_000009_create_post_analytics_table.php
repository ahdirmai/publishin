<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('post_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_version_id')->constrained('post_versions')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('reach')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('comments')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->unsignedBigInteger('saves')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['post_version_id', 'date']);
        });
    }
    public function down(): void { Schema::dropIfExists('post_analytics'); }
};
