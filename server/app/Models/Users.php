<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;


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
        'notes'
    ];

    public function userRole()
    {
        return $this->belongsTo(Users_roles::class, 'user_roles');
    }

    public function notifications()
    {
        return $this->hasMany(Notifications::class);
    }

    public function ads()
    {
        return $this->hasMany(Ads::class);
    }

    public function logs()
    {
        return $this->hasMany(Logs::class);
    }
}
