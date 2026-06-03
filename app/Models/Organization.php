<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model {
    use SoftDeletes;
    protected $fillable = ['owner_id', 'name', 'slug', 'logo', 'brand_color', 'website'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function members(): HasMany { return $this->hasMany(OrganizationMember::class); }
    public function socialAccounts(): HasMany { return $this->hasMany(SocialAccount::class); }
    public function posts(): HasMany { return $this->hasMany(Post::class); }
}
