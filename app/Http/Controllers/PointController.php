<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\PointSetting;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'type' => $user->type,
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
        DB::beginTransaction();
        try {
            $input = $request->all();

            $status = config('global.claim_status.rejected');
            if ($input['is_verified']) {
                $status = config('global.claim_status.claimed');
            }
            Claim::where('id', $id)->update([
                'status' => $status,
            ]);

            $claim = Claim::where('id', $id)->first();
            $user = User::where('id', $claim->user_id)->first();

            $totalPoint = Claim::where('user_id', $claim->user_id)
                            ->where('status', '=', config('global.claim_status.claimed'))
                            ->sum('total_pcs');
            $pointSetting = PointSetting::where('type', $user->type)->first();
            $totalAmount = $totalPoint * $pointSetting->amount;
            UserDetail::where('user_id', $claim->user_id)->update([
                'total_point' => $totalPoint,
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return response()->json(['data' => [
                'is_verified' => $input['is_verified'],
                'status' => $status,
            ]]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function calculatePoint(Request $request)
    {
        $input = $request->all();
        $builder = Claim::where('created_at', '>=', $input['start_date'])
                        ->where('created_at', '<=', $input['end_date'])
                        ->where('status', config('global.claim_status.claimed'));

        $agent_builder = clone $builder;
        $total_point_agent = $agent_builder->where('type', config('global.type.agent'))->sum('total_pcs');

        $reseller_builder = clone $builder;
        $total_point_reseller = $reseller_builder->where('type', config('global.type.reseller'))->sum('total_pcs');
        $claims = $builder->get();

        $agent_setting = PointSetting::where('type', config('global.type.agent'))->first();
        $reseller_setting = PointSetting::where('type', config('global.type.reseller'))->first();

        return response()->json([
            'data' => $claims,
            'meta' => [
                'total_point_agent' => (int) $total_point_agent,
                'total_point_reseller' => (int) $total_point_reseller,
                'agent_multiplier' => (int) $agent_setting->amount,
                'reseller_multiplier' => (int) $reseller_setting->amount,
            ],
        ]);
    }

    public function withdrawal(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            $builder = Claim::where('created_at', '>=', $input['start_date'])
                            ->where('created_at', '<=', $input['end_date']);
            $fetcher = clone $builder;

            $builder->where('status', config('global.claim_status.claimed'))
                    ->update([
                        'status' => config('global.claim_status.withdrawn'),
                    ]);

            $result = $fetcher->where('status', config('global.claim_status.withdrawn'))
                        ->get();
            $userIds = $result->pluck('user_id');
            UserDetail::whereIn('user_id', $userIds)
                ->update([
                    'total_point' => 0,
                    'total_amount' => 0,
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
