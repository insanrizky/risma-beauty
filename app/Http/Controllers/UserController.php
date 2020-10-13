<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function showPassword()
    {
        return Inertia::render('Profile/Password');
    }

    public function showDetail()
    {
        $userDetail = UserDetail::where('user_id', Auth::user()->id)->first();
        $result = Arr::collapse([
            $userDetail->toArray(),
            [
                'ktp_file_url' => $userDetail->ktp_file ? url('/storage/'.$userDetail->ktp_file) : null,
                'payment_file_url' => $userDetail->payment_file ? url('/storage/'.$userDetail->payment_file) : null,
            ],
        ]);

        return Inertia::render('Profile/Detail', ['user_detail' => $result]);
    }

    public function updateUserDetail(Request $request)
    {
        $userId = Auth::user()->id;
        try {
            if ($request->hasFile('ktp_file')) {
                $this->uploadFile($request, $userId, 'user_details', 'ktp_file', 'ktp_files');
            }

            if ($request->hasFile('payment_file')) {
                $this->uploadFile($request, $userId, 'user_details', 'payment_file', 'registration_payment_files');
            }

            $userDetail = UserDetail::where('user_id', $userId)->first();
            $status = $userDetail->status;
            if ($status === config('global.status.in_registration')) {
                $status = config('global.status.in_review');
            }
            $userDetail->update([
                'contact' => $request->input('contact'),
                'province_id' => $request->input('province_id'),
                'city_id' => $request->input('city_id'),
                'address' => $request->input('address'),
                'bank_id' => $request->input('bank_id'),
                'account_number' => $request->input('account_number'),
                'instagram_link' => $request->input('instagram_link'),
                'shopee_link' => $request->input('shopee_link'),
                'status' => $status,
            ]);

            $response = $request->wantsJson()
                        ? new JsonResponse('', 200)
                        : back()->with('status', 'user-detail-updated');

            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
