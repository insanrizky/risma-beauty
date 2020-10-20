<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PointController extends Controller
{
    public function showPoints()
    {
        return Inertia::render('Admin/PointList');
    }

    public function getPoints(Request $request)
    {
        $data = Claim::with('userDetail')
                    ->with('user')
                    ->where('user_id', $request->input('dxpoint'))
                    ->orderBy('id', 'desc')
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
            $inserted = Claim::create([
                'user_id' => $userId,
                'total_pcs' => $request->input('total_item'),
                'status' => 'MENUNGGU VERIFIKASI',
            ]);

            if ($request->hasFile('payment_file')) {
                $this->uploadFile($request, $userId, 'claims', 'payment_file', 'claim_payment_files');
            }

            $data = Claim::where('id', $inserted->id)->first();

            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}
