<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $casts = [
        'is_banned' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'activated_at',
    ];

    protected $fillable = [
        'name',
        'login',
        'password',
        'email',
        'activation_key',
        'nip',
        'address',
        'postal_code',
        'country',
        'company_email',
        'company_phone',
        'representative',
        'representative_phone',
        'notes',
        'activated_at'
    ];

    protected $hidden = [
        'password',
        'updated_at',
        'created_at'
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
