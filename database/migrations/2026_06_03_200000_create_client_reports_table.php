<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('platforms')->nullable();
            $table->boolean('include_kpi')->default(true);
            $table->boolean('include_chart')->default(true);
            $table->boolean('include_top_posts')->default(true);
            $table->boolean('include_demographics')->default(false);
            $table->boolean('white_label')->default(false);
            $table->string('status', 20)->default('pending');
            $table->string('file_path', 500)->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_reports');
    }
};
