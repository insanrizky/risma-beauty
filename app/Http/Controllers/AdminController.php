<?php

namespace App\Http\Controllers;

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
                            ->where('users.type', $request->input('type'));

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

        $data = $builder->leftJoin('users', 'user_details.user_id', '=', 'users.id')
                    ->selectRaw('users.profile_photo_path, users.type, users.name, users.email, user_details.*')
                    ->orderBy('users.created_at', 'desc')
                    ->get()
                    ->toArray();

        return response()->json(['data' => $data, 'base_url' => url('/')]);
    }

    public function verifyAgent(Request $request, $userId)
    {
        $input = $request->all();

        $status = config('global.status.rejected');
        $is_verified = false;
        $identifier = null;
        if ($input['is_verified']) {
            $status = config('global.status.active');
            $is_verified = true;
            $identifier = strtoupper(uniqid());
        }

        UserDetail::where('user_id', $userId)->update([
            'status' => $status,
            'identifier' => $identifier,
        ]);

        return response()->json(['data' => [
            'is_verified' => $is_verified,
            'status' => $status,
            'identifier' => $identifier,
        ]]);
    }
}
