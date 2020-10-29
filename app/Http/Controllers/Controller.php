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

    public function uploadFile(Request $request, $condition, $table, $attributeName, $storePath, $disk = 'minio')
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

    public function deleteFile($old, $disk = 'minio')
    {
        Storage::disk($disk)->delete($old);
    }

    public function getOffsetFromPage($page, $limit)
    {
        return ($page - 1) * $limit;
    }

    public function getTotalPage($total, $limit)
    {
        return ceil($total / $limit);
    }

    public function getCurrentPage($page)
    {
        if (!$page) {
            return 1;
        }
        return $page;
    }

    public function getCurrentLimit($limit)
    {
        if (!$limit) {
            return config('global.pagination.limit');
        }
        return $limit;
    }
}
