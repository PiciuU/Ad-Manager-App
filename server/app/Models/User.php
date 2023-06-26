<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Laravel\Sanctum\PersonalAccessToken;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'users';

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
        'activated_at',
        'user_role_id',
        'is_banned',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'updated_at',
        'created_at'
    ];

    public function isAdmin()
    {
        return $this->userRole->name === 'Admin';
    }

    public function hasAdminPrivileges()
    {
        return $this->tokenCan('admin') && $this->userRole->name === 'Admin';
    }

    public function lastUsedToken()
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id')
            ->orderByDesc('last_used_at')
            ->limit(1);
    }

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
        return $this->hasMany(Log::class, 'user_id');
    }
}
