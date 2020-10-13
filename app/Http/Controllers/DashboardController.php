<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userDetail = UserDetail::where('user_id', Auth::user()->id)->first();

        return Inertia::render('Dashboard', ['data' => $userDetail]);
    }
}
