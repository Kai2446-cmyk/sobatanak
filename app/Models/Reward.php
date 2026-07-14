<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
        'discount_value' => 'integer',
        'max_discount' => 'integer',
        'min_order' => 'integer',
        'claim_limit' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function claims()
    {
        return $this->hasMany(RewardClaim::class);
    }
}
