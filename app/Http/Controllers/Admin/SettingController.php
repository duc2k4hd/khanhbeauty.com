<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Rules\AcceptedImageUpload;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'favicon' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
            'logo' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
            'og_image' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
        ]);

        try {
            DB::beginTransaction();

            $settings = $request->input('settings');
            if (is_array($settings)) {
                foreach ($settings as $key => $value) {
                    if (!empty($value) || $value === '0') {
                        SiteSetting::updateOrCreate(
                            ['setting_key' => $key],
                            ['setting_value' => $value, 'setting_group' => 'general']
                        );
                    }
                }
            }

            $fileFields = [
                'favicon' => ['type' => 'misc', 'key' => 'favicon_url'],
                'logo' => ['type' => 'misc', 'key' => 'logo_url'],
                'og_image' => ['type' => 'misc', 'key' => 'og_image_url'],
            ];

            foreach ($fileFields as $field => $config) {
                if ($request->hasFile($field)) {
                    $mediaId = MediaUploadService::upload($request->file($field), $config['type'], auth()->id());
                    $url = MediaUploadService::url($mediaId);

                    SiteSetting::updateOrCreate(
                        ['setting_key' => $config['key']],
                        ['setting_value' => $url, 'setting_group' => 'general']
                    );
                }
            }

            DB::commit();
            Cache::forget('home_v8_safe');
            Cache::forget('home_data');

            return back()->with('success', 'Cap nhat cau hinh website thanh cong.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Settings update failed', [
                'message' => $e->getMessage(),
                'user_id' => optional($request->user())->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Khong the cap nhat cau hinh website. Vui long kiem tra du lieu va thu lai.');
        }
    }
}
