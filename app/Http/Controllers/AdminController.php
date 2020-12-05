<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $agent = UserDetail::with('user')
                        ->where('identifier', $identifier)
                        ->first();

        $agent_name = null;
        if ($agent) {
            $agent_name = $agent->user->name;
        }

        return Inertia::render('Admin/ResellerList', [
            'identifier' => $identifier,
            'agent_name' => $agent_name,
        ]);
    }

    public function getUserList(Request $request)
    {
        $builder = UserDetail::with(['bank', 'province', 'city'])
                            ->where('users.type', $request->input('type'))
                            ->where('user_details.status', '<>', config('global.status.in_registration'))
                            ->leftJoin('users', 'user_details.user_id', '=', 'users.id');

        if ($request->input('type') === config('global.type.reseller')) {
            $builder->where('user_details.upline_identifier', $request->input('upline'));
        }

        if ($status = $request->input('status')) {
            $builder->where('user_details.status', $status);
        }

        if ($search = $request->input('search')) {
            $keyword = '%'.$search.'%';
            $builder->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', $keyword)
                    ->orWhere('users.email', 'like', $keyword);
            });
        }

        $totalPoint = $builder->sum('user_details.total_point');

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

        if ($request->input('type') === config('global.type.agent')) {
            foreach ($data->items() as $agent) {
                $agent->total_point_reseller = (int) UserDetail::where('upline_identifier', $agent->identifier)->sum('total_point');
            }
        }

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'base_url' => env('MINIO_ENDPOINT').'/'.env('MINIO_BUCKET'),
                'total_point' => (int) $totalPoint,
                'pagination' => $pagination,
            ],
        ]);
    }

    private function validateUserProfile($userDetail)
    {
        $requiredFields = [
            'bank_id' => 'Bank',
            'account_number' => 'Nomor Rekening',
            'contact' => 'Nomor HP',
            'province_id' => 'Provinsi',
            'city_id' => 'Kab/Kota',
            'address' => 'Alamat',
            'ktp_file' => 'KTP',
            'payment_file' => 'Bukti Pembayaran',
            'instagram_link' => 'Instagram Link',
        ];

        $notCompleteAttributes = [];

        foreach ($requiredFields as $field => $name) {
            if (!$userDetail->$field) {
                array_push($notCompleteAttributes, $name);
            }
        }

        return $notCompleteAttributes ?: null;
    }

    public function verify(Request $request, $userId)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $userDetail = UserDetail::where('user_id', $userId)->first();
            if ($userDetail->identifier) {
                throw new Exception('Akun sudah diverifikasi (AKTIF)');
            }

            if ($notCompleteAttributes = $this->validateUserProfile($userDetail)) {
                $message = 'Profil tidak lengkap: '.implode(', ', $notCompleteAttributes);
                throw new Exception($message);
            }

            $status = config('global.status.rejected');
            $identifier = null;
            if ($input['is_verified']) {
                $status = config('global.status.active');
                do {
                    $identifier = generateUniqueId(8);
                    $found = UserDetail::where('identifier', $identifier)->first();
                } while ($found);
            }

            UserDetail::where('user_id', $userId)->update([
                'status' => $status,
                'identifier' => $identifier,
            ]);

            DB::commit();

            return response()->json(['data' => [
                'is_verified' => $input['is_verified'],
                'status' => $status,
                'identifier' => $identifier,
            ]]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function suspend($userId)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'status' => config('global.status.suspended'),
            ];

            $user = UserDetail::with('user')
                            ->where('user_id', $userId)
                            ->first();
            $user->update($payload);

            // Suspend Resellers as well
            if ($user->user->type === config('global.type.agent')) {
                UserDetail::where('upline_identifier', $user->identifier)
                        ->update($payload);
            }
            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function deleteByUserId($userId)
    {
        DB::beginTransaction();
        try {
            $user = UserDetail::with('user')
                            ->where('user_id', $userId)
                            ->first();

            // Delete All Resellers [Downline]
            if ($user->user->type === config('global.type.agent')) {
                UserDetail::where('upline_identifier', $user->identifier)
                        ->delete();
            }
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
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
