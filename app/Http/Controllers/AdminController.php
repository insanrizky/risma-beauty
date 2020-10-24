<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\PointSetting;
use App\Models\UserDetail;
use App\Transformers\PaginationTransformer;
use Cyvelnet\Laravel5Fractal\Facades\Fractal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function showAgents()
    {
        return Inertia::render('Admin/AgentList');
    }

    public function showResellers($identifier)
    {
        return Inertia::render('Admin/ResellerList', [
            'identifier' => $identifier,
        ]);
    }

    public function getUserList(Request $request)
    {
        $builder = UserDetail::with(['bank', 'province', 'city'])
                            ->where('users.type', $request->input('type'))
                            ->where('status', '<>', config('global.status.in_registration'))
                            ->leftJoin('users', 'user_details.user_id', '=', 'users.id');

        if ($status = $request->input('status')) {
            $builder->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $keyword = '%'.$search.'%';
            $builder->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', $keyword)
                    ->orWhere('users.email', 'like', $keyword);
            });
        }

        $totalPoint = $builder->sum('user_details.total_point');
        $pointSetting = PointSetting::where('type', $request->input('type'))->first();

        // Pagination
        $page = $this->getCurrentPage($request->input('page'));
        $limit = $this->getCurrentLimit($request->input('limit'));
        $skip = $this->getOffsetFromPage($page, $limit);

        $data = $builder->selectRaw('users.profile_photo_path, users.type, users.name, users.email, user_details.*')
                    ->orderBy('users.created_at', 'desc')
                    ->skip($skip)
                    ->take($limit)
                    ->paginate($limit);
        $pagination = $data->toArray();
        unset($pagination['data']);

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'base_url' => url('/'),
                'total_point' => (int) $totalPoint,
                'multiplier' => $pointSetting ? $pointSetting->amount : 0,
                'pagination' => $pagination,
            ],
        ]);
    }

    public function verify(Request $request, $userId)
    {
        $input = $request->all();

        $status = config('global.status.rejected');
        $identifier = null;
        if ($input['is_verified']) {
            $status = config('global.status.active');
            $identifier = strtoupper(uniqid());
        }

        UserDetail::where('user_id', $userId)->update([
            'status' => $status,
            'identifier' => $identifier,
        ]);

        return response()->json(['data' => [
            'is_verified' => $input['is_verified'],
            'status' => $status,
            'identifier' => $identifier,
        ]]);
    }

    public function export(Request $request)
    {
        $input = $request->all();
        $start_date = $input['start_date'];
        $end_date = $input['end_date'];

        $file_name = "Rekapitulasi $start_date - $end_date.xlsx";

        return Excel::download(new DataExport($start_date, $end_date), $file_name);
    }
}
