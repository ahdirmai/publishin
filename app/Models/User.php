<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function subscription(): HasOne { return $this->hasOne(Subscription::class)->latestOfMany(); }
    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class); }
    public function socialAccounts(): HasMany { return $this->hasMany(SocialAccount::class); }
    public function posts(): HasMany { return $this->hasMany(Post::class); }
    public function organizations(): HasMany { return $this->hasMany(Organization::class, 'owner_id'); }
    public function tags(): HasMany { return $this->hasMany(Tag::class); }

    // Helpers
    public function currentPlan(): ?string {
        return $this->subscription?->plan?->slug;
    }
    public function hasFeature(string $feature): bool {
        $plan = $this->subscription?->plan;
        return match($feature) {
            'ai'         => $plan?->has_ai_features ?? false,
            'white_label'=> $plan?->has_white_label ?? false,
            'api'        => $plan?->has_api_access ?? false,
            default      => false,
        };
    }
}
