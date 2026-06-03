<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountAnalytic extends Model {
    protected $fillable = [
        'social_account_id', 'date', 'followers', 'follower_change',
        'reach', 'impressions', 'engagement_rate', 'posts_published',
    ];
    protected $casts = ['date' => 'date', 'engagement_rate' => 'decimal:2'];

    public function socialAccount(): BelongsTo { return $this->belongsTo(SocialAccount::class); }
}
