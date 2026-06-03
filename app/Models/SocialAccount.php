<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class SocialAccount extends Model {
    protected $fillable = [
        'user_id', 'organization_id', 'platform', 'platform_user_id',
        'username', 'display_name', 'avatar_url',
        'access_token', 'refresh_token', 'token_expires_at',
        'page_id', 'scopes', 'follower_count', 'is_active', 'last_synced_at',
    ];
    protected $hidden = ['access_token', 'refresh_token'];
    protected $casts = [
        'scopes' => 'array',
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function setAccessTokenAttribute(string $value): void {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }
    public function getAccessTokenAttribute(string $value): string {
        return Crypt::decryptString($value);
    }
    public function setRefreshTokenAttribute(?string $value): void {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getRefreshTokenAttribute(?string $value): ?string {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function isTokenExpiringSoon(): bool {
        return $this->token_expires_at && $this->token_expires_at->diffInDays(now()) <= 7;
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function postVersions(): HasMany { return $this->hasMany(PostVersion::class); }
    public function accountAnalytics(): HasMany { return $this->hasMany(AccountAnalytic::class); }
}
