<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'order_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}