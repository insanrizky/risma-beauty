<?php

namespace App\Http\Controllers;

use App\Models\PointSetting;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RankController extends Controller
{
    public function showRanks()
    {
        return Inertia::render('Admin/RankList');
    }

    public function getRanks(Request $request)
    {
        $builder = UserDetail::leftJoin('users', 'user_details.user_id', '=', 'users.id');

        $pointSetting = null;
        if ($request->input('filter') !== 'Semua') {
            $builder->where('users.type', $request->input('filter'));
            $pointSetting = PointSetting::where('type', $request->input('filter'))->first();
        } else {
            $builder->where('users.type', '<>', 'ADMIN');
        }

        $total_point = $builder->sum('total_point');
        $total_member = $builder->count();
        $ranks = $builder->orderBy('total_point', 'desc')
                        ->take(10)
                        ->get();

        return response()->json([
            'data' => $ranks,
            'total_point' => $total_point,
            'total_member' => $total_member,
            'multiplier' => $pointSetting ? $pointSetting->amount : null,
        ]);
    }
}
