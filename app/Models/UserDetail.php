<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact',
        'province_id',
        'city_id',
        'address',
        'bank_id',
        'account_number',
        'instagram_link',
        'shopee_link',
        'status',
        'ktp_file',
        'payment_file',
    ];
}
