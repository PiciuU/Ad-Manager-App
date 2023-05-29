<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at', 'date'];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
