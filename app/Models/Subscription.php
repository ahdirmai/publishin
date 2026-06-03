<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model {
    protected $fillable = [
        'user_id', 'plan_id', 'status', 'billing_cycle',
        'current_period_start', 'current_period_end',
        'cancelled_at', 'trial_ends_at', 'payment_gateway', 'external_id',
    ];
    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end'   => 'datetime',
        'cancelled_at'         => 'datetime',
        'trial_ends_at'        => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function plan(): BelongsTo { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isExpired(): bool { return $this->current_period_end->isPast(); }
}
