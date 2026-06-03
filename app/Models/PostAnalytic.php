<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAnalytic extends Model {
    protected $fillable = [
        'post_version_id', 'date', 'reach', 'impressions',
        'likes', 'comments', 'shares', 'saves', 'engagement_rate',
    ];
    protected $casts = ['date' => 'date', 'engagement_rate' => 'decimal:2'];

    public function postVersion(): BelongsTo { return $this->belongsTo(PostVersion::class); }
}
