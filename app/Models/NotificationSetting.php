<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_weekly_summary',
        'push_post_published',
        'push_mentions',
        'email_monthly_report',
        'push_schedule_reminder',
    ];

    protected $casts = [
        'email_weekly_summary'   => 'boolean',
        'push_post_published'    => 'boolean',
        'push_mentions'          => 'boolean',
        'email_monthly_report'   => 'boolean',
        'push_schedule_reminder' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
