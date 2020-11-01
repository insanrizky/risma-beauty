<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AnalyticController extends Controller
{
    public function showAnalytics()
    {
        return Inertia::render('Admin/Analytic');
    }
}
