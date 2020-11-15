<?php

namespace App\Http\Controllers;

use App\Services\AnalyticService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticController extends Controller
{
    public function __construct(AnalyticService $service)
    {
        $this->service = $service;
    }

    public function showAnalytics()
    {
        return Inertia::render('Admin/Analytic');
    }

    public function getTotalUserByArea(Request $request)
    {
        $type = $request->input('type');
        if ($provinceId = $request->input('province_id')) {
            $result = $this->service->getTotalUserByProvince($provinceId, $type);
        } else {
            $result = $this->service->getTotalUser($type);
        }

        return response()->json([
            'data' => $result,
        ]);
    }

    public function getUserStatusByArea(Request $request)
    {
        $type = $request->input('type');
        $provinceId = $request->input('province_id');
        $result = $this->service->getUserStatus($type, $provinceId);

        return response()->json([
            'data' => $result,
        ]);
    }
}
