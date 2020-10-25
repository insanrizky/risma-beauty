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

        $requestor = UserDetail::with('user')
                            ->where('user_id', $request->input('self_id'))
                            ->first();
        if ($requestor->user->type === config('global.type.reseller')) {
            $builder->where('user_details.upline_identifier', $requestor->upline_identifier);
        }

        if ($request->input('filter') !== 'Semua') {
            $builder->where('users.type', $request->input('filter'));
        } else {
            $builder->where('users.type', '<>', config('global.type.admin'));
        }

        $self_rank = $requestor;
        if ($requestor->type === config('global.type.admin')) {
            $self_rank = null;
        }

        $total_point = $builder->sum('total_point');
        $total_member = $builder->count();

        $data = $builder->orderBy('total_point', 'desc')
                        ->take(10)
                        ->paginate();
        $pagination = $data->toArray();
        unset($pagination['data']);

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'self_rank' => $self_rank,
                'pagination' => $pagination,
                'total_point' => $total_point,
                'total_member' => $total_member,
            ],
        ]);
    }
}
