<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_pcs',
        'user_id',
        'payment_file',
        'point_added',
        'amount_added',
        'status',
        'created_at',
        'updated_at',
    ];
}
