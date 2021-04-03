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
        if ($userDetail->status !== config('global.status.active')) {
            return redirect('dashboard');
        }
        return Inertia::render('Admin/PointList', [
            'user_detail' => $userDetail,
        ]);
    }

    public function getPoints(Request $request)
    {
        $builder = Claim::leftJoin('users', 'claims.user_id', '=', 'users.id')
                    ->leftJoin('user_details', 'claims.user_id', '=', 'user_details.user_id');

        if ($request->input('status') !== 'Semua') {
            $builder->where('claims.status', strtoupper($request->input('status')));
        }

        if ($request->input('type') !== 'Semua') {
            $builder->where('users.type', strtoupper($request->input('type')));
        }

        // Handle Agent for Verify Reseller's Claims
        if ($dxpoint = $request->input('dxpoint')) {
            $userDetail = UserDetail::with('user')->where('user_id', $dxpoint)->first();
            if ($request->input('type') !== 'Semua') {
                if (
                    $userDetail->user->type === config('global.type.agent') &&
                    strtoupper($request->input('type')) === config('global.type.reseller')
                ) {
                    $builder->where('user_details.upline_identifier', $userDetail->identifier);
                } else {
                    $builder->where('claims.user_id', $dxpoint);
                }
            } else {
                $builder->where(function ($query) use ($dxpoint, $userDetail) {
                    $query->orWhere('user_details.upline_identifier', $userDetail->identifier)
                            ->orWhere('claims.user_id', $dxpoint);
                });
            }
        }

        if ($search = $request->input('search')) {
            $keyword = '%'.$search.'%';
            $builder->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', $keyword)
                    ->orWhere('users.email', 'like', $keyword);
            });
        }

        // Pagination
        $page = $this->getCurrentPage($request->input('page'));
        $limit = $this->getCurrentLimit($request->input('limit'));
        $skip = $this->getOffsetFromPage($page, $limit);

        $data = $builder->selectRaw('users.*, user_details.*, claims.*, claims.created_at as created_at')
                        ->orderBy('claims.id', 'desc')
                        ->skip($skip)
                        ->take($limit)
                        ->paginate($limit);
        $pagination = $data->toArray();
        unset($pagination['data']);

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'pagination' => $pagination,
            ],
        ]);
    }

    public function claimPointsView()
    {
        $userDetail = UserDetail::where('user_id', Auth::user()->id)->first();
        if ($userDetail->status !== config('global.status.active')) {
            return redirect('dashboard');
        }
        return Inertia::render('Admin/ClaimPoint');
    }

    public function claimPoints(Request $request)
    {
        try {
            $userId = $request->input('id');

            $pointAdded = $request->input('total_item');
            $user = User::where('id', $userId)->first();
            $pointSetting = PointSetting::where('type', $user->type)
                                ->orderBy('id', 'desc')
                                ->first();
            $amount = $pointAdded * $pointSetting->amount;

            $inserted = Claim::create([
                'pointsetting_id' => $pointSetting->id,
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
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function verifyPoint(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            
            $claim = Claim::where('id', $id)->first();
            $user = User::where('id', $claim->user_id)->first();

            $status = config('global.claim_status.rejected');
            if ($input['is_verified']) {
                // Compare total poin agen dan reseller
                if ($user->type === config('global.type.reseller')) {
                    $reseller = UserDetail::where('user_id', $user->id)->first();
                    $upliner = UserDetail::where('identifier', $reseller->upline_identifier)->first();
                    $total_point_reseller = (int) UserDetail::leftJoin('claims', 'user_details.user_id', '=', 'claims.user_id')
                                                ->where('upline_identifier', $upliner->identifier)
                                                ->where('claims.status', config('global.claim_status.claimed'))
                                                ->sum('total_pcs');
                    if ($total_point_reseller + $claim->total_pcs > $upliner->total_point) {
                        throw new Exception('Total pcs klaim akan melebihi total poin agen');
                    }
                }
                $status = config('global.claim_status.claimed');
            }
            Claim::where('id', $id)->update([
                'status' => $status,
            ]);

            $totalPoint = Claim::where('user_id', $claim->user_id)
                            ->where('status', '=', config('global.claim_status.claimed'))
                            ->sum('total_pcs');
            $pointSetting = PointSetting::where('id', $claim->pointsetting_id)->first();
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
        $agent_builder->where('type', config('global.type.agent'));
        $total_point_agent = $agent_builder->sum('total_pcs');
        $total_amount_agent = $agent_builder->sum('amount');

        $reseller_builder = clone $builder;
        $reseller_builder->where('type', config('global.type.reseller'));
        $total_point_reseller = $reseller_builder->sum('total_pcs');
        $total_amount_reseller = $reseller_builder->sum('amount');

        $claims = $builder->get();

        return response()->json([
            'data' => $claims,
            'meta' => [
                'total_point_agent' => (int) $total_point_agent,
                'total_point_reseller' => (int) $total_point_reseller,
                'total_amount_agent' => (int) $total_amount_agent,
                'total_amount_reseller' => (int) $total_amount_reseller,
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
            ], 422);
        }
    }
}
