<?php

namespace App\Services;

use App\Models\City;
use App\Models\Claim;
use App\Models\Province;
use App\Models\UserDetail;
use Carbon\Carbon;

class AnalyticService
{
    private $admin;
    private $active;

    public function __construct()
    {
        $this->admin = config('global.type.admin');
        $this->active = config('global.status.active');
    }

    public function getTotalUser($requestType)
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
            'attributes' => [
                'report' => $result,
            ],
        ];
    }

    public function getTotalUserByProvince($provinceId, $requestType)
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
            'attributes' => [
                'report' => $result,
            ],
        ];
    }

    public function getUserStatus($requestType, $provinceId = null)
    {
        $type = 'all';
        $builder = UserDetail::join('users', function ($join) {
            $join->on('user_details.user_id', '=', 'users.id');
        });

        $condition = "users.type <> '$this->admin'";
        if ($requestType) {
            $type = $requestType;
            $condition = "users.type = '$type'";
        }
        $builder->whereRaw($condition);

        if ($provinceId) {
            $builder->where('province_id', $provinceId);
            $province = Province::find($provinceId);
        }

        $unionQuery = clone $builder;
        $builder->selectRaw('user_details.status, count(user_details.status) as total')
            ->groupBy('user_details.status');
        $unionQuery->selectRaw('null, count(user_details.status)');

        $result = $builder->union($unionQuery)->get()->each->setAppends([]);
        $total = $result->pop()->total;

        return [
            'type' => $type,
            'total' => $total,
            'attributes' => [
                'province' => $province->name ?? null,
                'report' => $result,
            ],
        ];
    }

    public function getClaimsByDate($start_date, $end_date, $requestType = null)
    {
        $type = 'all';
        $start_date = Carbon::parse($start_date)->toDateTimeString();
        $end_date = Carbon::parse($end_date)->toDateTimeString();

        $builder = Claim::where('updated_at', '>=', $start_date)
                    ->where('updated_at', '<=', $end_date)
                    ->orderByRaw('DATE(updated_at)');

        if ($requestType) {
            $type = $requestType;
            $builder->where('type', $type);
        }

        $unionQuery = clone $builder;
        $builder->groupByRaw('DATE(updated_at), status')
                ->selectRaw('DATE(updated_at) as date, status, count(DATE(updated_at)) as total');
        $unionQuery->selectRaw('null, null, count(DATE(updated_at))');

        $result = $builder->union($unionQuery)->get()->each->setAppends([]);
        $total = $result->pop()->total;

        return [
            'type' => $type,
            'total' => $total,
            'attributes' => [
                'report' => $result,
            ],
        ];
    }
}
