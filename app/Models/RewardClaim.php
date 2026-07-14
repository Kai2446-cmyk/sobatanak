<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardClaim extends Model
{
    protected $guarded = [];

    protected $casts = [
        'points_used' => 'integer',
        'discount_value' => 'integer',
        'max_discount' => 'integer',
        'min_order' => 'integer',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
