<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointSetting extends Model
{
    use HasFactory;

    protected $appends = [
        'user_detail'
    ];

    public function getUserDetailAttribute() {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
