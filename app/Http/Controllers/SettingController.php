<?php

namespace App\Http\Controllers;

use App\Models\PointSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function showSettings()
    {
        $pointSettingAgent = PointSetting::where('type', 'AGENT')->first();
        $pointSettingReseller = PointSetting::where('type', 'RESELLER')->first();

        return Inertia::render('Admin/Setting', [
            'amount_agent' => $pointSettingAgent->amount,
            'amount_reseller' => $pointSettingReseller->amount,
        ]);
    }

    public function updatePointSetting(Request $request)
    {
        PointSetting::where('type', config('global.type.agent'))->update([
            'amount' => $request->input('amount_agent')
        ]);
        PointSetting::where('type', config('global.type.reseller'))->update([
            'amount' => $request->input('amount_reseller')
        ]);

        $response = $request->wantsJson()
                        ? new JsonResponse('', 200)
                        : back()->with('status', 'point-setting-updated');

        return $response;
    }
}
