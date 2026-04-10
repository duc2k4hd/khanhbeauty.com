<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaUploadService
{
    protected static array $folderMap = [
        'service_featured' => 'services',
        'service_gallery' => 'services/gallery',
        'product_featured' => 'products',
        'product_gallery' => 'products/gallery',
        'post_featured' => 'posts',
        'post_og' => 'posts/og',
        'page_featured' => 'pages',
        'page_og' => 'pages/og',
        'portfolio_before' => 'portfolios/before',
        'portfolio_after' => 'portfolios/after',
        'portfolio_gallery' => 'portfolios/gallery',
        'service_category' => 'categories/services',
        'product_category' => 'categories/products',
        'post_category' => 'categories/posts',
        'user_avatar' => 'users/avatars',
        'product_variant' => 'products/variants',
        'product_attr' => 'products/attributes',
        'review_image' => 'reviews',
        'about' => 'homepage/about',
        'showcase' => 'homepage/showcase',
        'misc' => 'misc',
    ];

    protected static array $acceptedImageExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'bmp',
        'webp',
        'avif',
        'svg',
        'ico',
        'tif',
        'tiff',
        'heic',
        'heif',
    ];

    protected static array $acceptedImageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/avif',
        'image/svg+xml',
        'image/x-icon',
        'image/vnd.microsoft.icon',
        'image/tiff',
        'image/heic',
        'image/heif',
    ];

    protected static array $acceptedVideoExtensions = [
        'mp4',
        'mov',
        'avi',
        'webm',
    ];

    protected static array $acceptedAudioExtensions = [
        'mp3',
        'wav',
        'ogg',
        'm4a',
        'aac',
    ];

    public static function acceptedImageExtensions(): array
    {
        return static::$acceptedImageExtensions;
    }

    public static function acceptedImageMimeTypes(): array
    {
        return static::$acceptedImageMimeTypes;
    }

    public static function acceptedImageFileTypesForForms(): array
    {
        return array_values(array_unique(array_merge(['image/*'], static::$acceptedImageMimeTypes)));
    }

    public static function isAcceptedImageUpload(UploadedFile $file): bool
    {
        return static::matchesUpload(
            $file,
            static::$acceptedImageExtensions,
            static::$acceptedImageMimeTypes,
            ['image/']
        );
    }

    public static function isAcceptedMediaLibraryUpload(UploadedFile $file): bool
    {
        return static::isAcceptedImageUpload($file)
            || static::matchesUpload($file, static::$acceptedVideoExtensions, [], ['video/'])
            || static::matchesUpload($file, static::$acceptedAudioExtensions, [], ['audio/']);
    }

    public static function guessMediaKind(UploadedFile $file): string
    {
        if (static::isAcceptedImageUpload($file)) {
            return 'image';
        }

        if (static::matchesUpload($file, static::$acceptedVideoExtensions, [], ['video/'])) {
            return 'video';
        }

        if (static::matchesUpload($file, static::$acceptedAudioExtensions, [], ['audio/'])) {
            return 'audio';
        }

        return 'other';
    }

    public static function upload(UploadedFile $file, string $imageType, ?int $uploaderId = null): int
    {
        $folder = static::$folderMap[$imageType] ?? static::$folderMap['misc'];

        return static::storePreservingOriginalName($file, 'public', $folder, $imageType, $uploaderId);
    }

    public static function uploadToDisk(
        UploadedFile $file,
        string $disk,
        string $folder,
        string $imageType = 'misc',
        ?int $uploaderId = null
    ): int {
        return static::storePreservingOriginalName(
            $file,
            $disk,
            static::normalizeFolder($folder),
            $imageType,
            $uploaderId
        );
    }

    public static function uploadMultiple(array $files, string $imageType): array
    {
        return array_map(
            fn (UploadedFile $file) => static::upload($file, $imageType),
            $files
        );
    }

    public static function saveFilamentUpload(string $tmpPath, string $imageType): int
    {
        $disk = 'public';
        $folder = static::$folderMap[$imageType] ?? static::$folderMap['misc'];
        $fileName = basename(str_replace('\\', '/', $tmpPath));
        $targetPath = static::buildPath($folder, $fileName);

        $stream = Storage::disk($disk)->readStream($tmpPath);

        if ($stream === false) {
            throw new RuntimeException("Unable to read temporary upload [{$tmpPath}].");
        }

        try {
            $written = Storage::disk($disk)->put($targetPath, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (!$written) {
            throw new RuntimeException("Unable to move temporary upload to [{$targetPath}] on disk [{$disk}].");
        }

        Storage::disk($disk)->delete($tmpPath);

        return static::upsertMediaRecord(
            $disk,
            static::normalizeFolder($folder),
            $fileName,
            $targetPath,
            $imageType,
            Auth::id(),
            Storage::disk($disk)->mimeType($targetPath) ?: static::mimeTypeFromExtension(static::extensionFromFileName($fileName)),
            Storage::disk($disk)->size($targetPath) ?: 0
        );
    }

    public static function url(?int $mediaId): ?string
    {
        if (!$mediaId) {
            return null;
        }

        return Media::find($mediaId)?->file_url;
    }

    public static function urls(array $mediaIds): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        $medias = Media::whereIn('id', $mediaIds)->get()->keyBy('id');

        return array_map(fn ($id) => $medias[$id]?->file_url ?? '', $mediaIds);
    }

    protected static function storePreservingOriginalName(
        UploadedFile $file,
        string $disk,
        string $folder,
        string $imageType,
        ?int $uploaderId = null
    ): int {
        $folder = static::normalizeFolder($folder);
        $fileName = static::originalFileName($file);
        $path = static::buildPath($folder, $fileName);
        $stream = fopen($file->getRealPath(), 'rb');

        if ($stream === false) {
            throw new RuntimeException("Unable to open uploaded file [{$fileName}].");
        }

        try {
            $written = Storage::disk($disk)->put($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (!$written) {
            throw new RuntimeException("Unable to store file [{$path}] on disk [{$disk}].");
        }

        return static::upsertMediaRecord(
            $disk,
            $folder,
            $fileName,
            $path,
            $imageType,
            $uploaderId ?? Auth::id(),
            static::detectMimeType($file),
            $file->getSize() ?: 0
        );
    }

    protected static function upsertMediaRecord(
        string $disk,
        string $folder,
        string $fileName,
        string $path,
        string $imageType,
        ?int $uploaderId,
        ?string $mimeType,
        int $fileSizeBytes
    ): int {
        $existing = static::findExistingMedia($disk, $folder, $fileName);
        $storage = Storage::disk($disk);
        $resolvedMimeType = $mimeType ?: $storage->mimeType($path) ?: static::mimeTypeFromExtension(static::extensionFromFileName($fileName));
        $resolvedSize = $storage->size($path) ?: $fileSizeBytes;

        $payload = [
            'uploader_id' => $uploaderId,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_url' => $storage->url($path),
            'disk' => $disk,
            'mime_type' => $resolvedMimeType,
            'file_size_bytes' => $resolvedSize,
            'image_type' => $imageType,
            'folder' => $folder,
        ];

        if ($existing) {
            $existing->update($payload);

            return $existing->id;
        }

        return Media::create($payload)->id;
    }

    protected static function findExistingMedia(string $disk, string $folder, string $fileName): ?Media
    {
        return Media::query()
            ->where('disk', $disk)
            ->where('file_name', $fileName)
            ->where(function ($query) use ($folder) {
                if ($folder === '') {
                    $query->whereNull('folder')->orWhere('folder', '');

                    return;
                }

                $query->where('folder', $folder);
            })
            ->first();
    }

    protected static function matchesUpload(
        UploadedFile $file,
        array $extensions,
        array $mimeTypes = [],
        array $mimePrefixes = []
    ): bool {
        $extension = static::extensionFromFileName(static::originalFileName($file));

        if ($extension !== '' && in_array($extension, $extensions, true)) {
            return true;
        }

        foreach (array_filter([$file->getMimeType(), $file->getClientMimeType()]) as $mimeType) {
            $normalizedMimeType = strtolower((string) $mimeType);

            if ($normalizedMimeType !== '' && in_array($normalizedMimeType, $mimeTypes, true)) {
                return true;
            }

            foreach ($mimePrefixes as $prefix) {
                if (str_starts_with($normalizedMimeType, strtolower($prefix))) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function originalFileName(UploadedFile $file): string
    {
        $originalName = trim(basename(str_replace('\\', '/', $file->getClientOriginalName())));

        if ($originalName !== '') {
            return $originalName;
        }

        $extension = static::extensionFromFileName($file->getClientOriginalName());

        return $extension !== '' ? "upload.{$extension}" : 'upload';
    }

    protected static function normalizeFolder(string $folder): string
    {
        return trim($folder, " \t\n\r\0\x0B/");
    }

    protected static function buildPath(string $folder, string $fileName): string
    {
        return $folder === '' ? $fileName : "{$folder}/{$fileName}";
    }

    protected static function extensionFromFileName(string $fileName): string
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }

    protected static function detectMimeType(UploadedFile $file): string
    {
        foreach ([$file->getMimeType(), $file->getClientMimeType()] as $mimeType) {
            if (is_string($mimeType) && trim($mimeType) !== '') {
                return strtolower($mimeType);
            }
        }

        return static::mimeTypeFromExtension(static::extensionFromFileName(static::originalFileName($file)));
    }

    protected static function mimeTypeFromExtension(string $extension): string
    {
        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'tif', 'tiff' => 'image/tiff',
            'heic' => 'image/heic',
            'heif' => 'image/heif',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'webm' => 'video/webm',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'aac' => 'audio/aac',
            default => 'application/octet-stream',
        };
    }
}
