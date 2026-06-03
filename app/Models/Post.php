<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model {
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'organization_id', 'title', 'status',
        'scheduled_at', 'published_at', 'is_recurring',
    ];
    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function versions(): HasMany { return $this->hasMany(PostVersion::class); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class); }

    public function scopeScheduled($query) { return $query->where('status', 'scheduled'); }
    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    public function scopePublished($query) { return $query->where('status', 'published'); }
}
