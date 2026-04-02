<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings');
        if (is_array($settings)) {
            foreach ($settings as $key => $value) {
                // Chỉ nhận update nếu value có dữ liệu thật (Không xoá trắng)
                if(!empty($value)){
                    SiteSetting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }
        
        return back()->with('success', 'Cập nhật cấu hình website thành công!');
    }
}

