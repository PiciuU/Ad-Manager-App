<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'user_id',
        'status',
        'file_name',
        'file_type',
        'url',
        'ad_start_date',
        'ad_end_date'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function adStats()
    {
        return $this->hasMany(AdStats::class);
    }
}
