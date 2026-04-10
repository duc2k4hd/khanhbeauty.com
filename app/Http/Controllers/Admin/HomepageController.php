<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Rules\AcceptedImageUpload;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    public function index()
    {
        return view('admin.homepage.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'about_image' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
            'showcase' => ['nullable', 'array'],
            'showcase.*.image' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
        ]);

        try {
            DB::beginTransaction();

            $heroKeys = ['hero_badge', 'hero_title', 'hero_subtitle', 'hero_cta_primary', 'hero_cta_secondary'];
            foreach ($heroKeys as $key) {
                if ($request->has("settings.$key")) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => $key],
                        ['setting_value' => $request->input("settings.$key"), 'setting_group' => 'homepage']
                    );
                }
            }

            $aboutKeys = ['about_label', 'about_title', 'about_desc_1', 'about_desc_2'];
            foreach ($aboutKeys as $key) {
                if ($request->has("settings.$key")) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => $key],
                        ['setting_value' => $request->input("settings.$key"), 'setting_group' => 'homepage']
                    );
                }
            }

            if ($request->hasFile('about_image')) {
                $mediaId = MediaUploadService::upload($request->file('about_image'), 'about', auth()->id());
                SiteSetting::updateOrCreate(
                    ['setting_key' => 'about_image_id'],
                    ['setting_value' => $mediaId, 'setting_group' => 'homepage']
                );
            }

            if ($request->has('about_stats')) {
                $stats = collect($request->input('about_stats'))
                    ->filter(fn ($stat) => !empty($stat['value']) && !empty($stat['label']))
                    ->values()
                    ->toArray();

                SiteSetting::updateOrCreate(
                    ['setting_key' => 'about_stats'],
                    ['setting_value' => json_encode($stats, JSON_UNESCAPED_UNICODE), 'setting_group' => 'homepage']
                );
            }

            if ($request->has('showcase')) {
                if ($request->has('settings.showcase_label')) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => 'showcase_label'],
                        ['setting_value' => $request->input('settings.showcase_label'), 'setting_group' => 'homepage']
                    );
                }

                if ($request->has('settings.showcase_title')) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => 'showcase_title'],
                        ['setting_value' => $request->input('settings.showcase_title'), 'setting_group' => 'homepage']
                    );
                }

                $showcaseData = [];
                foreach ($request->input('showcase', []) as $index => $item) {
                    if (empty($item['title'])) {
                        continue;
                    }

                    $imageUrl = $item['current_image'] ?? '';
                    if ($request->hasFile("showcase.$index.image")) {
                        $mediaId = MediaUploadService::upload(
                            $request->file("showcase.$index.image"),
                            'showcase',
                            auth()->id()
                        );

                        $media = Media::find($mediaId);
                        $imageUrl = $media?->file_url ?? $imageUrl;
                    }

                    $showcaseData[] = [
                        'image_url' => $imageUrl,
                        'tag' => $item['tag'] ?? '',
                        'step' => $item['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'title' => $item['title'],
                        'description' => $item['description'] ?? '',
                        'skills' => array_filter(array_map('trim', explode(',', $item['skills'] ?? ''))),
                    ];
                }

                SiteSetting::updateOrCreate(
                    ['setting_key' => 'showcase_items'],
                    ['setting_value' => json_encode($showcaseData, JSON_UNESCAPED_UNICODE), 'setting_group' => 'homepage']
                );
            }

            if ($request->has('testimonials')) {
                if ($request->has('settings.testimonials_label')) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => 'testimonials_label'],
                        ['setting_value' => $request->input('settings.testimonials_label'), 'setting_group' => 'homepage']
                    );
                }

                if ($request->has('settings.testimonials_title')) {
                    SiteSetting::updateOrCreate(
                        ['setting_key' => 'testimonials_title'],
                        ['setting_value' => $request->input('settings.testimonials_title'), 'setting_group' => 'homepage']
                    );
                }

                $testimonials = collect($request->input('testimonials'))
                    ->filter(fn ($testimonial) => !empty($testimonial['name']) && !empty($testimonial['text']))
                    ->values()
                    ->map(fn ($testimonial) => [
                        'stars' => (int) ($testimonial['stars'] ?? 5),
                        'text' => $testimonial['text'],
                        'name' => $testimonial['name'],
                        'role' => $testimonial['role'] ?? '',
                        'avatar' => $testimonial['avatar'] ?? mb_substr($testimonial['name'], 0, 1),
                    ])
                    ->toArray();

                SiteSetting::updateOrCreate(
                    ['setting_key' => 'testimonials'],
                    ['setting_value' => json_encode($testimonials, JSON_UNESCAPED_UNICODE), 'setting_group' => 'homepage']
                );
            }

            if ($request->has('numbers')) {
                $numbers = collect($request->input('numbers'))
                    ->filter(fn ($number) => !empty($number['value']) && !empty($number['label']))
                    ->values()
                    ->toArray();

                SiteSetting::updateOrCreate(
                    ['setting_key' => 'numbers_stats'],
                    ['setting_value' => json_encode($numbers, JSON_UNESCAPED_UNICODE), 'setting_group' => 'homepage']
                );
            }

            DB::commit();
            Cache::forget('home_v8_safe');
            Cache::forget('home_data');

            return back()->with('success', 'Cap nhat trang chu thanh cong.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Homepage update failed', [
                'message' => $e->getMessage(),
                'user_id' => optional($request->user())->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Khong the cap nhat trang chu. Vui long kiem tra du lieu va thu lai.');
        }
    }
}
