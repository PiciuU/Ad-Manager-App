<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads_stats extends Model
{
    use HasFactory;

    protected $table = 'ads_stats';
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at', 'date'];

    public function ad()
    {
        return $this->belongsTo(Ads::class, 'ad_id');
    }
}
