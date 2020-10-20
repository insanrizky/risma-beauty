<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\PointSetting;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PointController extends Controller
{
    public function showPoints()
    {
        $userDetail = UserDetail::where('user_id', Auth::user()->id)->first();

        return Inertia::render('Admin/PointList', [
            'user_detail' => $userDetail,
        ]);
    }

    public function getPoints(Request $request)
    {
        $builder = Claim::leftJoin('users', 'claims.user_id', '=', 'users.id')
                    ->leftJoin('user_details', 'claims.user_id', '=', 'user_details.id');

        if ($dxpoint = $request->input('dxpoint')) {
            $builder->where('claims.user_id', $dxpoint);
        }

        if ($request->input('status') !== 'Semua') {
            $builder->where('claims.status', strtoupper($request->input('status')));
        }

        if ($request->input('type') !== 'Semua') {
            $builder->where('users.type', strtoupper($request->input('type')));
        }

        if ($search = $request->input('search')) {
            $keyword = '%'.$search.'%';
            $builder->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', $keyword)
                    ->orWhere('users.email', 'like', $keyword);
            });
        }

        $data = $builder->selectRaw('users.*, user_details.*, claims.*, claims.created_at as created_at')
                        ->orderBy('claims.id', 'desc')
                        ->get();

        return response()->json(['data' => $data]);
    }

    public function claimPointsView()
    {
        return Inertia::render('Admin/ClaimPoint');
    }

    public function claimPoints(Request $request)
    {
        try {
            $userId = $request->input('id');

            $pointAdded = $request->input('total_item');
            $user = User::where('id', $userId)->first();
            $pointSetting = PointSetting::where('type', $user->type)->first();
            $amount = $pointAdded * $pointSetting->amount;

            $inserted = Claim::create([
                'user_id' => $userId,
                'total_pcs' => $pointAdded,
                'amount' => $amount,
                'status' => config('global.claim_status.in_review'),
            ]);

            if ($request->hasFile('payment_file')) {
                $this->uploadFile($request, ['id' => $inserted->id], 'claims', 'payment_file', 'claim_payment_files');
            }

            $data = Claim::where('id', $inserted->id)->first();

            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function deleteClaim($id)
    {
        try {
            $claim = Claim::where('id', $id)->first();
            $this->deleteFile($claim->payment_file);
            $claim->delete();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function verifyPoint(Request $request, $id)
    {
        $input = $request->all();

        $status = config('global.claim_status.rejected');
        if ($input['is_verified']) {
            $status = config('global.claim_status.claimed');
        }
        Claim::where('id', $id)->update([
            'status' => $status,
        ]);

        return response()->json(['data' => [
            'is_verified' => $input['is_verified'],
            'status' => $status,
        ]]);
    }
}
