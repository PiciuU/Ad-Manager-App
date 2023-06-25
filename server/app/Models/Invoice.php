<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'ad_id',
        'number',
        'price',
        'date',
        'status',
        'notes'
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
