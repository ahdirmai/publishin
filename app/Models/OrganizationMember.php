<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationMember extends Model {
    public $timestamps = false;
    protected $fillable = ['organization_id', 'user_id', 'role', 'invited_at', 'joined_at'];
    protected $casts = ['invited_at' => 'datetime', 'joined_at' => 'datetime'];

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
