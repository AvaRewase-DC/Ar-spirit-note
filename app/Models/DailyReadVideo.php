<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReadVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_read_id',
        'video',
    ];
}
