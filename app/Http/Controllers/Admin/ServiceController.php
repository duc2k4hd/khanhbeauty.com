<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceVariant;
use App\Models\Faq;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['category', 'featuredImage'])->orderBy('sort_order')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'video_url'   => 'nullable|url',
        ]);

        $data = $request->only([
            'category_id', 'name', 'short_description', 'description',
            'price', 'sale_price', 'price_unit', 'duration_minutes', 'sort_order',
            'meta_title', 'meta_description', 'meta_keywords', 'video_url',
        ]);

        // Slug unique
        $data['slug'] = $request->slug ?: Str::slug($request->name);
        if (Service::where('slug', $data['slug'])->exists()) {
            $data['slug'] .= '-' . time();
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        // JSON fields
        $data['benefits']      = $this->filterRepeater($request->input('benefits', []));
        $data['process_steps'] = $this->filterRepeater($request->input('process_steps', []));
        $data['includes']      = array_values(array_filter((array) $request->input('includes', []), fn($v) => trim($v) !== ''));

        // ── Ảnh đại diện → lưu media_id ─────────────────────
        if ($request->hasFile('featured_image')) {
            $data['featured_image_id'] = MediaUploadService::upload(
                $request->file('featured_image'),
                'service_featured'
            );
        }

        // ── Gallery → lưu mảng media IDs ────────────────────
        if ($request->hasFile('gallery_files')) {
            $data['gallery_ids'] = MediaUploadService::uploadMultiple(
                $request->file('gallery_files'),
                'service_gallery'
            );
        }

        $service = Service::create($data);

        // Service Variants
        $this->syncVariants($service, $request->input('variants', []));

        // FAQs
        $this->syncFaqs($service->id, $request->input('faqs', []), [], []);

        return redirect()->route('admin.services.index')->with('success', 'Đã tạo dịch vụ thành công!');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $service->load(['variants', 'faqs', 'featuredImage']);
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'video_url'   => 'nullable|url',
        ]);

        $data = $request->only([
            'category_id', 'name', 'slug', 'short_description', 'description',
            'price', 'sale_price', 'price_unit', 'duration_minutes', 'sort_order',
            'meta_title', 'meta_description', 'meta_keywords', 'video_url',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        // JSON fields
        $data['benefits']      = $this->filterRepeater($request->input('benefits', []));
        $data['process_steps'] = $this->filterRepeater($request->input('process_steps', []));
        $data['includes']      = array_values(array_filter((array) $request->input('includes', []), fn($v) => trim($v) !== ''));

        // ── Ảnh đại diện → lưu media_id ─────────────────────
        if ($request->hasFile('featured_image')) {
            $data['featured_image_id'] = MediaUploadService::upload(
                $request->file('featured_image'),
                'service_featured'
            );
        }

        // ── Gallery: gỡ bỏ IDs cũ + thêm IDs mới ────────────
        $currentGalleryIds = $service->gallery_ids ?? [];
        $removeIds = array_map('intval', (array) $request->input('gallery_remove_ids', []));
        $currentGalleryIds = array_values(array_filter($currentGalleryIds, fn($id) => !in_array($id, $removeIds)));

        if ($request->hasFile('gallery_files')) {
            $newIds = MediaUploadService::uploadMultiple(
                $request->file('gallery_files'),
                'service_gallery'
            );
            $currentGalleryIds = array_merge($currentGalleryIds, $newIds);
        }
        $data['gallery_ids'] = $currentGalleryIds;

        $service->update($data);

        // Variants
        $deleteVariantIds = array_filter((array) $request->input('delete_variants', []));
        if (!empty($deleteVariantIds)) {
            ServiceVariant::where('service_id', $service->id)->whereIn('id', $deleteVariantIds)->delete();
        }
        $this->syncVariants($service, $request->input('variants', []));

        // FAQs
        $deleteFaqIds = array_filter((array) $request->input('delete_faqs', []));
        $this->syncFaqs($service->id, $request->input('faqs', []), $deleteFaqIds, []);

        return redirect()->route('admin.services.index')->with('success', 'Đã cập nhật dịch vụ thành công!');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Đã xóa dịch vụ!');
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return response()->json([
            'success'   => true,
            'is_active' => $service->is_active,
            'message'   => $service->is_active ? 'Dịch vụ đã BẬT hiển thị.' : 'Dịch vụ đã TẮT hiển thị.',
        ]);
    }

    // ─── PRIVATE HELPERS ─────────────────────────────────────────

    private function filterRepeater(array $items): array
    {
        return collect($items)
            ->filter(fn($item) => !empty(trim($item['title'] ?? '')))
            ->values()
            ->all();
    }

    private function syncVariants(Service $service, array $variants): void
    {
        foreach ($variants as $v) {
            if (empty(trim($v['variant_name'] ?? '')) || empty($v['price'])) continue;
            ServiceVariant::create([
                'service_id'       => $service->id,
                'variant_name'     => $v['variant_name'],
                'sku'              => 'SV-' . $service->id . '-' . Str::random(6),
                'price'            => $v['price'],
                'sale_price'       => $v['sale_price'] ?: null,
                'duration_minutes' => $v['duration_minutes'] ?: null,
                'is_active'        => true,
                'sort_order'       => 0,
            ]);
        }
    }

    private function syncFaqs(int $serviceId, array $newFaqs, array $deleteIds, array $updateFaqs): void
    {
        if (!empty($deleteIds)) {
            Faq::whereIn('id', $deleteIds)->where('related_service_id', $serviceId)->delete();
        }
        foreach ($newFaqs as $f) {
            if (empty(trim($f['question'] ?? '')) || empty(trim($f['answer'] ?? ''))) continue;
            Faq::create([
                'related_service_id' => $serviceId,
                'question'           => $f['question'],
                'answer'             => $f['answer'],
                'is_active'          => true,
                'schema_included'    => true,
                'sort_order'         => 0,
            ]);
        }
    }
}
