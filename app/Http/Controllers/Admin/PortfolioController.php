<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Rules\AcceptedImageUpload;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with(['beforeImage', 'afterImage'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolios.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        try {
            DB::beginTransaction();

            $data = $request->only(['title', 'description', 'category', 'client_name', 'sort_order']);
            $data['slug'] = Str::slug($request->title) . '-' . time();
            $data['is_featured'] = $request->boolean('is_featured');

            if ($request->hasFile('before_image')) {
                $data['before_image_id'] = MediaUploadService::upload(
                    $request->file('before_image'),
                    'portfolio_before'
                );
            }

            if ($request->hasFile('after_image')) {
                $data['after_image_id'] = MediaUploadService::upload(
                    $request->file('after_image'),
                    'portfolio_after'
                );
            }

            if ($request->hasFile('gallery_files')) {
                $data['gallery_ids'] = MediaUploadService::uploadMultiple(
                    $request->file('gallery_files'),
                    'portfolio_gallery'
                );
            }

            Portfolio::create($data);
            DB::commit();
            $this->clearHomepageCache();

            return redirect()->route('admin.portfolios.index')->with('success', 'Da them portfolio thanh cong.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Portfolio create failed', [
                'message' => $e->getMessage(),
                'user_id' => optional($request->user())->id,
                'title' => $request->input('title'),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Khong the tao portfolio. Vui long kiem tra du lieu va thu lai.');
        }
    }

    public function edit(Portfolio $portfolio)
    {
        $portfolio->load(['beforeImage', 'afterImage']);

        return view('admin.portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $request->validate($this->validationRules());

        try {
            DB::beginTransaction();

            $data = $request->only(['title', 'description', 'category', 'client_name', 'sort_order']);
            $data['is_featured'] = $request->boolean('is_featured');

            if ($request->hasFile('before_image')) {
                $data['before_image_id'] = MediaUploadService::upload(
                    $request->file('before_image'),
                    'portfolio_before'
                );
            }

            if ($request->hasFile('after_image')) {
                $data['after_image_id'] = MediaUploadService::upload(
                    $request->file('after_image'),
                    'portfolio_after'
                );
            }

            $currentIds = $portfolio->gallery_ids ?? [];
            $removeIds = array_map('intval', (array) $request->input('gallery_remove_ids', []));
            $currentIds = array_values(array_filter($currentIds, fn ($id) => !in_array($id, $removeIds, true)));

            if ($request->hasFile('gallery_files')) {
                $currentIds = array_merge(
                    $currentIds,
                    MediaUploadService::uploadMultiple($request->file('gallery_files'), 'portfolio_gallery')
                );
            }

            $data['gallery_ids'] = $currentIds;

            $portfolio->update($data);
            DB::commit();
            $this->clearHomepageCache();

            return redirect()->route('admin.portfolios.index')->with('success', 'Da cap nhat portfolio thanh cong.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Portfolio update failed', [
                'message' => $e->getMessage(),
                'user_id' => optional($request->user())->id,
                'portfolio_id' => $portfolio->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Khong the cap nhat portfolio. Vui long kiem tra du lieu va thu lai.');
        }
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        $this->clearHomepageCache();

        return redirect()->route('admin.portfolios.index')->with('success', 'Da xoa portfolio.');
    }

    private function validationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'before_image' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
            'after_image' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
            'gallery_files' => ['nullable', 'array'],
            'gallery_files.*' => ['nullable', 'file', new AcceptedImageUpload(), 'max:5120'],
        ];
    }

    private function clearHomepageCache(): void
    {
        Cache::forget('home_v8_safe');
        Cache::forget('home_data');
    }
}
