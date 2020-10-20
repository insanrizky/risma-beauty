<?php

namespace App\Models;

use App\Traits\HasPaymentFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;
    use HasPaymentFile;

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

    protected $appends = [
        'payment_file_url',
    ];

    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
