<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function showAgents()
    {
        return Inertia::render('Admin/AgentList');
    }

    public function getUserList(Request $request)
    {
        $builder = User::where('type', $request->input('type'));

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

        $data = $builder->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                    ->leftJoin('banks', 'user_details.bank_id', '=', 'banks.id')
                    ->selectRaw('users.*, banks.name as bank_name, user_details.*', )
                    ->orderBy('users.created_at', 'desc')
                    ->get()
                    ->toArray();

        return response()->json(['data' => $data]);
    }

    public function verifyAgent(Request $request, $userId)
    {
        $input = $request->all();

        $status = config('global.status.rejected');
        $is_verified = false;
        if ($input['is_verified']) {
            $status = config('global.status.active');
            $is_verified = true;
        }

        UserDetail::where('user_id', $userId)->update([
            'status' => $status,
        ]);

        return response()->json(['data' => [
            'is_verified' => $is_verified,
            'status' => $status,
        ]]);
    }
}
