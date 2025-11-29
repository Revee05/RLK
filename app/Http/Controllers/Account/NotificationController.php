<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserNotificationSetting;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = UserNotificationSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enabled' => true,
                'email_order_status' => false,
                'email_promo' => false,
                'wa_enabled' => true,
                'wa_order_status' => false,
                'wa_promo' => false,
                'banner_enabled' => true,
                'banner_order_status' => true,
            ]
        );

        return view('account.notifications.notifications', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $settings = UserNotificationSetting::firstOrNew(['user_id' => $user->id]);

        $data = $request->only([
            'email_enabled',
            'email_order_status',
            'email_promo',
            'wa_enabled',
            'wa_order_status',
            'wa_promo',
            'banner_enabled',
            'banner_order_status'
        ]);

        // Normalize checkboxes
        foreach ($data as $k => $v) {
            $data[$k] = (bool) $v;
        }

        $settings->fill($data);
        $settings->user_id = $user->id;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengaturan notifikasi berhasil disimpan.']);
        }

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }
}
