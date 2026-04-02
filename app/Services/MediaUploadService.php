<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * MediaUploadService
 *
 * Xử lý upload ảnh tập trung:
 * - Lưu file vào đúng folder theo loại ảnh
 * - Tạo record trong bảng media
 * - Trả về media->id để gắn vào model
 *
 * Cách dùng:
 *   $mediaId = MediaUploadService::upload($file, 'service_featured');
 *   $service->update(['featured_image_id' => $mediaId]);
 */
class MediaUploadService
{
    /**
     * Cấu hình thư mục mặc định cho từng loại ảnh.
     * Key = image_type, Value = folder trong storage/public/
     */
    protected static array $folderMap = [
        // Services
        'service_featured' => 'services',
        'service_gallery'  => 'services/gallery',

        // Products
        'product_featured' => 'products',
        'product_gallery'  => 'products/gallery',

        // Posts / Blog
        'post_featured'    => 'posts',
        'post_og'          => 'posts/og',

        // Pages
        'page_featured'    => 'pages',
        'page_og'          => 'pages/og',

        // Portfolios
        'portfolio_before' => 'portfolios/before',
        'portfolio_after'  => 'portfolios/after',
        'portfolio_gallery'=> 'portfolios/gallery',

        // Categories
        'service_category' => 'categories/services',
        'product_category' => 'categories/products',
        'post_category'    => 'categories/posts',

        // Users
        'user_avatar'      => 'users/avatars',

        // Product Variants & Attributes
        'product_variant'  => 'products/variants',
        'product_attr'     => 'products/attributes',

        // Reviews
        'review_image'     => 'reviews',

        // Fallback
        'misc'             => 'misc',
    ];

    /**
     * Upload file và tạo Media record.
     *
     * @param  UploadedFile  $file       File được upload
     * @param  string        $imageType  Loại ảnh theo folderMap (vd: 'service_featured')
     * @param  int|null      $uploaderId Override uploader ID (mặc định dùng Auth::id())
     * @return int                       ID của Media record vừa tạo
     */
    public static function upload(UploadedFile $file, string $imageType, ?int $uploaderId = null): int
    {
        $folder = static::$folderMap[$imageType] ?? 'misc';
        $path   = $file->store($folder, 'public');
        $url    = '/storage/' . $path;

        $media = Media::create([
            'uploader_id'     => $uploaderId ?? Auth::id(),
            'file_name'       => $file->getClientOriginalName(),
            'file_path'       => $path,
            'file_url'        => $url,
            'disk'            => 'public',
            'mime_type'       => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
            'image_type'      => $imageType,
            'folder'          => $folder,
        ]);

        return $media->id;
    }

    /**
     * Upload nhiều file (gallery) và trả về mảng IDs.
     *
     * @param  UploadedFile[]  $files
     * @param  string          $imageType
     * @return int[]
     */
    public static function uploadMultiple(array $files, string $imageType): array
    {
        return array_map(
            fn(UploadedFile $file) => static::upload($file, $imageType),
            $files
        );
    }

    /**
     * Dùng trong Filament: saveUploadedFileUsing callback.
     * Nhận path tương đối (Livewire tmp), move sang storage, tạo Media record.
     *
     * @param  string  $tmpPath   Path tạm của Filament/Livewire
     * @param  string  $imageType
     * @return int                Media ID
     */
    public static function saveFilamentUpload(string $tmpPath, string $imageType): int
    {
        $folder    = static::$folderMap[$imageType] ?? 'misc';
        $fileName  = basename($tmpPath);
        $finalPath = $folder . '/' . $fileName;

        // Move từ tmp (livewire-tmp) sang public storage
        Storage::disk('public')->move($tmpPath, $finalPath);

        $url = '/storage/' . $finalPath;

        $media = Media::create([
            'uploader_id'     => Auth::id(),
            'file_name'       => $fileName,
            'file_path'       => $finalPath,
            'file_url'        => $url,
            'disk'            => 'public',
            'mime_type'       => Storage::disk('public')->mimeType($finalPath),
            'file_size_bytes' => Storage::disk('public')->size($finalPath),
            'image_type'      => $imageType,
            'folder'          => $folder,
        ]);

        return $media->id;
    }

    /**
     * Lấy URL từ media ID (dùng ở Views).
     * Trả về null nếu không tìm thấy.
     *
     * @param  int|null  $mediaId
     * @return string|null
     */
    public static function url(?int $mediaId): ?string
    {
        if (!$mediaId) return null;
        return Media::find($mediaId)?->file_url;
    }

    /**
     * Lấy danh sách URLs từ mảng media IDs.
     *
     * @param  array  $mediaIds
     * @return string[]   Mảng URLs theo đúng thứ tự IDs
     */
    public static function urls(array $mediaIds): array
    {
        if (empty($mediaIds)) return [];
        $medias = Media::whereIn('id', $mediaIds)->get()->keyBy('id');
        return array_map(fn($id) => $medias[$id]?->file_url ?? '', $mediaIds);
    }
}
