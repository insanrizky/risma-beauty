<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
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
        $result = UserDetail::where('user_id', Auth::user()->id)->first();

        return Inertia::render('Profile/Detail', ['user_detail' => $result]);
    }

    public function updateUserDetail(Request $request)
    {
        $userId = Auth::user()->id;
        $type = Auth::user()->type;

        try {
            if ($request->hasFile('ktp_file')) {
                $this->uploadFile($request, ['user_id' => $userId], 'user_details', 'ktp_file', 'ktp_files');
            }

            if ($request->hasFile('payment_file')) {
                $this->uploadFile($request, ['user_id' => $userId], 'user_details', 'payment_file', 'registration_payment_files');
            }

            $userDetail = UserDetail::where('user_id', $userId)->first();
            $payload = [
                'contact' => $request->input('contact'),
                'province_id' => $request->input('province_id'),
                'city_id' => $request->input('city_id'),
                'address' => $request->input('address'),
                'bank_id' => $request->input('bank_id'),
                'account_number' => $request->input('account_number'),
                'instagram_link' => $request->input('instagram_link'),
                'shopee_link' => $request->input('shopee_link'),
            ];

            if ($userDetail->status === config('global.status.in_registration')) {
                if ($type === config('global.type.agent')) {
                    $payload['status'] = config('global.status.in_review');
                } elseif ($type === config('global.type.reseller')) {
                    $payload['status'] = config('global.status.active');
                    do {
                        $identifier = generateUniqueId(8);
                        $found = UserDetail::where('identifier', $identifier)->first();
                    } while ($found);
                    $payload['identifier'] = $identifier;
                } else {
                    $payload['status'] = config('global.status.active');
                }
            }

            $userDetail->update($payload);

            return back()->with([
                'message' => 'success',
            ]);
        } catch (Exception $e) {
            return back()->withErrors([
                'failed' => $e->getMessage(),
            ]);
        }
    }
}
