<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    public function getBanks() {
        $result = Bank::all();
        return response()->json($result);
    }

    public function getProvinces() {
        $result = Province::all();
        return response()->json($result);
    }

    public function getCities() {
        $result = City::all();
        return response()->json($result);
    }

    public function getCitiesByProvince($provinceId) {
        $result = City::where('province_id', $provinceId)->get();
        return response()->json($result);
    }
}
