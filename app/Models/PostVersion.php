<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostVersion extends Model implements HasMedia {
    use InteractsWithMedia;

    protected $fillable = [
        'post_id', 'social_account_id', 'caption', 'content_type',
        'status', 'platform_post_id', 'post_url', 'error_message',
        'retry_count', 'published_at', 'platform_options',
    ];
    protected $casts = [
        'platform_options' => 'array',
        'published_at' => 'datetime',
    ];

    public function registerMediaCollections(): void {
        $this->addMediaCollection('post_media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/quicktime']);
    }

    public function registerMediaConversions(?Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(36)->height(36)->nonQueued();
        $this->addMediaConversion('preview')
            ->width(800)->nonQueued();
    }

    public function post(): BelongsTo { return $this->belongsTo(Post::class); }
    public function socialAccount(): BelongsTo { return $this->belongsTo(SocialAccount::class); }
    public function analytics(): HasMany { return $this->hasMany(PostAnalytic::class); }
}
