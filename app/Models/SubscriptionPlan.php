<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model {
    protected $fillable = [
        'name', 'slug', 'price_monthly', 'price_yearly',
        'max_social_accounts', 'max_scheduled_posts', 'max_team_members', 'max_clients',
        'has_ai_features', 'has_white_label', 'has_api_access', 'is_active',
    ];
    protected $casts = [
        'has_ai_features' => 'boolean',
        'has_white_label' => 'boolean',
        'has_api_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class, 'plan_id'); }
}
