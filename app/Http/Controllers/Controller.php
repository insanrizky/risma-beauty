<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function uploadFile(Request $request, $condition, $table, $attributeName, $storePath, $disk = 'public')
    {
        $userDetail = DB::table($table)->where($condition)->first();
        $old = $userDetail->{$attributeName};

        $path = $request->file($attributeName)->storePublicly($storePath, $disk);
        DB::table($table)->where($condition)->update([
            $attributeName => $path,
        ]);

        if ($old) {
            Storage::disk($disk)->delete($old);
        }
    }

    public function deleteFile($old, $disk = 'public')
    {
        Storage::disk($disk)->delete($old);
    }
}
