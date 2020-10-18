<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PointController extends Controller
{
    public function showPoints()
    {
        return Inertia::render('Admin/PointList');
    }

    public function getPoints()
    {
        $data = Claim::get();
        return response()->json([ 'data' => $data ]);
    }

    public function claimPointsView()
    {
        return Inertia::render('Admin/ClaimPoint');
    }

    public function claimPoints(Request $request)
    {
        return response()->json([ 'data' => 123 ]);
    }
}
