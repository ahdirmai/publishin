<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientReport extends Model
{
    protected $fillable = [
        'user_id', 'name', 'period_start', 'period_end', 'platforms',
        'include_kpi', 'include_chart', 'include_top_posts', 'include_demographics',
        'white_label', 'status', 'file_path', 'generated_at',
    ];

    protected $attributes = [
        'platforms' => '[]',
        'status'    => 'pending',
    ];

    protected $casts = [
        'period_start'        => 'date',
        'period_end'          => 'date',
        'platforms'           => 'array',
        'include_kpi'         => 'boolean',
        'include_chart'       => 'boolean',
        'include_top_posts'   => 'boolean',
        'include_demographics' => 'boolean',
        'white_label'         => 'boolean',
        'generated_at'        => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
