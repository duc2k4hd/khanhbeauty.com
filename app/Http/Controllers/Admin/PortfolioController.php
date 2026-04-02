<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with(['afterImage'])->orderByDesc('created_at')->paginate(20);
        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'before_image' => 'nullable|image|max:5120',
            'after_image'  => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['title', 'description', 'category', 'client_name', 'sort_order']);
        $data['slug']        = Str::slug($request->title) . '-' . time();
        $data['is_featured'] = $request->boolean('is_featured');

        // ── Ảnh before/after → lưu media_id ──────────────────
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

        // ── Gallery → mảng media IDs ──────────────────────────
        if ($request->hasFile('gallery_files')) {
            $data['gallery_ids'] = MediaUploadService::uploadMultiple(
                $request->file('gallery_files'),
                'portfolio_gallery'
            );
        }

        Portfolio::create($data);
        return redirect()->route('admin.portfolios.index')->with('success', 'Đã thêm ảnh Portfolio thành công!');
    }

    public function edit(Portfolio $portfolio)
    {
        $portfolio->load(['beforeImage', 'afterImage']);
        return view('admin.portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $data = $request->only(['title', 'description', 'category', 'client_name', 'sort_order']);
        $data['is_featured'] = $request->boolean('is_featured');

        // ── Ảnh before/after → lưu media_id ──────────────────
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

        // ── Gallery: gỡ IDs cũ + thêm mới ────────────────────
        $currentIds = $portfolio->gallery_ids ?? [];
        $removeIds  = array_map('intval', (array) $request->input('gallery_remove_ids', []));
        $currentIds = array_values(array_filter($currentIds, fn($id) => !in_array($id, $removeIds)));

        if ($request->hasFile('gallery_files')) {
            $newIds     = MediaUploadService::uploadMultiple(
                $request->file('gallery_files'),
                'portfolio_gallery'
            );
            $currentIds = array_merge($currentIds, $newIds);
        }
        $data['gallery_ids'] = $currentIds;

        $portfolio->update($data);
        return redirect()->route('admin.portfolios.index')->with('success', 'Đã cập nhật Portfolio thành công!');
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        return redirect()->route('admin.portfolios.index')->with('success', 'Đã xóa Portfolio!');
    }
}
