<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasKtpFile
{
    public function getKtpFileUrlAttribute()
    {
        return $this->ktp_file
                    ? Storage::disk('minio')->url($this->ktp_file)
                    : null;
    }
}
