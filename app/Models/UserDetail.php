<?php

namespace App\Models;

use App\Traits\HasKtpFile;
use App\Traits\HasPaymentFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    use HasKtpFile;
    use HasPaymentFile;

    protected $fillable = [
        'user_id',
        'identifier',
        'upline_identifier',
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

    protected $appends = [
        'ktp_file_url',
        'payment_file_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
}
