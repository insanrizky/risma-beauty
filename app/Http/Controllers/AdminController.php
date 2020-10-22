<?php

namespace App\Http\Controllers;

use App\Models\PointSetting;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function showAgents()
    {
        return Inertia::render('Admin/AgentList');
    }

    public function showResellers($identifier)
    {
        return Inertia::render('Admin/ResellerList', [
            'identifier' => $identifier,
        ]);
    }

    public function getUserList(Request $request)
    {
        $builder = UserDetail::with(['bank', 'province', 'city'])
                            ->where('users.type', $request->input('type'))
                            ->where('status', '<>', config('global.status.in_registration'))
                            ->leftJoin('users', 'user_details.user_id', '=', 'users.id');

        if ($status = $request->input('status')) {
            $builder->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $keyword = '%'.$search.'%';
            $builder->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', $keyword)
                    ->orWhere('users.email', 'like', $keyword);
            });
        }

        $totalMember = $builder->count();
        $totalPoint = $builder->sum('user_details.total_point');
        $pointSetting = PointSetting::where('type', $request->input('type'))->first();

        $data = $builder->selectRaw('users.profile_photo_path, users.type, users.name, users.email, user_details.*')
                    ->orderBy('users.created_at', 'desc')
                    ->get()
                    ->toArray();

        return response()->json([
            'data' => $data,
            'base_url' => url('/'),
            'total_member' => $totalMember,
            'total_point' => $totalPoint,
            'multiplier' => $pointSetting ? $pointSetting->amount : 0,
        ]);
    }

    public function verify(Request $request, $userId)
    {
        $input = $request->all();

        $status = config('global.status.rejected');
        $identifier = null;
        if ($input['is_verified']) {
            $status = config('global.status.active');
            $identifier = strtoupper(uniqid());
        }

        UserDetail::where('user_id', $userId)->update([
            'status' => $status,
            'identifier' => $identifier,
        ]);

        return response()->json(['data' => [
            'is_verified' => $input['is_verified'],
            'status' => $status,
            'identifier' => $identifier,
        ]]);
    }
}
