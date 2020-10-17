<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasPaymentFile
{
    public function getPaymentFileUrlAttribute()
    {
        return $this->payment_file
                    ? Storage::disk('public')->url($this->payment_file)
                    : null;
    }
}
