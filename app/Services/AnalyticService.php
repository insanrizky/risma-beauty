<?php

namespace App\Services;

use App\Models\City;
use App\Models\Province;

class AnalyticService
{
    private $admin;
    private $active;

    public function __construct()
    {
        $this->admin = config('global.type.admin');
        $this->active = config('global.status.active');
    }

    public function getReportByProvince($requestType)
    {
        $builder = Province::leftJoin('user_details', function ($join) {
            $join->on('provinces.id', '=', 'user_details.province_id');
        })->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'user_details.user_id');
        });
        $unionQuery = clone $builder;

        $type = 'all';
        $counterCondition = "users.type <> '$this->admin' and user_details.status = '$this->active'";
        if ($requestType) {
            $type = $requestType;
            $counterCondition = "users.type = '$type' and user_details.status = '$this->active'";
        }

        $builder->selectRaw("provinces.id as id, provinces.name as name, count(case when $counterCondition then user_details.province_id else null end) as total")
                ->groupBy('provinces.id');
        $unionQuery->whereRaw($counterCondition)
                ->selectRaw('null, null, count(user_details.province_id)');

        $result = $builder->union($unionQuery)->get()->each->setAppends([]);
        $totalAll = $result->pop()->total;

        return [
            'type' => $type,
            'total' => $totalAll,
            'attributes' => $result,
        ];
    }

    public function getReportByCity($provinceId, $requestType)
    {
        $builder = City::leftJoin('user_details', function ($join) {
            $join->on('cities.id', '=', 'user_details.city_id');
        })->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'user_details.user_id');
        })->where('cities.province_id', $provinceId);
        $unionQuery = clone $builder;

        $type = 'ALL';
        $counterCondition = "users.type <> '$this->admin' and cities.province_id = $provinceId and user_details.status = '$this->active'";
        if ($requestType) {
            $type = $requestType;
            $counterCondition = "users.type = '$type' and cities.province_id = $provinceId and user_details.status = '$this->active'";
        }

        $builder->selectRaw("cities.id as id, cities.name as name, count(case when $counterCondition then user_details.city_id else null end) as total")
                ->groupBy('cities.id');
        $unionQuery->whereRaw($counterCondition)
                ->selectRaw('null, null, count(user_details.city_id)');

        $result = $builder->union($unionQuery)->get()->each->setAppends([]);
        $totalAll = $result->pop()->total;

        return [
            'type' => $type,
            'total' => $totalAll,
            'attributes' => $result,
        ];
    }
}
