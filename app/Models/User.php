<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birthday',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'birthday'          => 'date',
    ];

    // Quan hệ đơn hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Quan hệ tin nhắn
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}