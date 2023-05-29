<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at', 'date'];

    public function ad()
    {
        return $this->belongsTo(Ads::class);
    }
}
