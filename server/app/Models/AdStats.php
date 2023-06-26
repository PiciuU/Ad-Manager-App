<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdStats extends Model
{
    use HasFactory;

    protected $table = 'ads_stats';

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'ad_id',
        'date',
        'views',
        'clicks',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
