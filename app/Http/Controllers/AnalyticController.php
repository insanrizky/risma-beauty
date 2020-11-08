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
            $result = $this->service->getReportByCity($provinceId, $type);
        } else {
            $result = $this->service->getReportByProvince($type);
        }

        return response()->json([
            'data' => $result,
        ]);
    }
}
