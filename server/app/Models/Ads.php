<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at', 'ad_start_date', 'ad_end_date'];

    protected $fillable = [
        'name',
        'file_name',
        'url'
    ];


    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class);
    }
    public function logs()
    {
        return $this->hasMany(Logs::class);
    }
    public function adsStats()
    {
        return $this->hasMany(Ads_stats::class);
    }
}
