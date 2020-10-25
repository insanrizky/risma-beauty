<?php

namespace App\Http\Controllers;

use App\Models\PointSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function showSettings()
    {
        $pointSettingAgent = PointSetting::where('type', config('global.type.agent'))
                                    ->orderBy('id', 'desc')
                                    ->first();
        $pointSettingReseller = PointSetting::where('type', config('global.type.reseller'))
                                    ->orderBy('id', 'desc')
                                    ->first();

        return Inertia::render('Admin/Setting', [
            'amount_agent' => $pointSettingAgent->amount,
            'amount_reseller' => $pointSettingReseller->amount,
        ]);
    }

    public function updatePointSetting(Request $request)
    {
        DB::beginTransaction();
        try {
            PointSetting::create([
                'type' => config('global.type.agent'),
                'amount' => $request->input('amount_agent')
            ]);
            PointSetting::create([
                'type' => config('global.type.reseller'),
                'amount' => $request->input('amount_reseller')
            ]);

            DB::commit();
            return back()->with('status', 'point-setting-updated');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
