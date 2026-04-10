<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Rules\AcceptedMediaLibraryUpload;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    private const FOLDERS_CACHE_KEY = 'admin_media_folders_v3';
    private const MAX_PER_PAGE = 200;

    public function index(Request $request)
    {
        $limit = max(1, min((int) $request->input('per_page', 50), self::MAX_PER_PAGE));
        $cursorId = max(0, (int) $request->input('cursor_id', 0));
        $query = Media::query()->select([
            'id',
            'file_name',
            'file_url',
            'mime_type',
            'folder',
            'alt_text',
            'title',
            'thumbnails',
            'file_size_bytes',
            'created_at',
        ]);

        if ($request->filled('folder')) {
            $folder = trim((string) $request->input('folder'), '/');

            if ($folder === 'root') {
                $query->where(function ($builder) {
                    $builder->whereNull('folder')
                        ->orWhere('folder', '');
                });
            } elseif ($folder !== 'all') {
                $query->where(function ($builder) use ($folder) {
                    $builder->where('folder', $folder)
                        ->orWhere('folder', 'like', "{$folder}/%");
                });
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            if (mb_strlen($search) >= 2) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('file_name', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('folder', 'like', "%{$search}%");
                });
            }
        }

        if ($request->filled('type')) {
            $query->where('mime_type', 'like', "{$request->type}%");
        }

        if ($cursorId > 0) {
            $query->where('id', '<', $cursorId);
        }

        $items = $query
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $items->count() > $limit;
        $data = $items->take($limit)->values();
        $nextCursor = $hasMore ? $data->last()?->id : null;

        return response()->json([
            'data' => $data,
            'per_page' => $limit,
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Media upload started', [
            'folder' => $request->input('folder', 'uploads'),
            'files' => count($request->file('files', [])),
        ]);

        try {
            $request->validate([
                'files' => ['required', 'array', 'min:1'],
                'files.*' => ['required', 'file', new AcceptedMediaLibraryUpload(), 'max:102400'],
                'folder' => ['nullable', 'string', 'max:100'],
            ]);

            $folder = trim((string) $request->input('folder', 'uploads'), '/');
            $uploaded = [];

            foreach ($request->file('files', []) as $file) {
                $mediaId = MediaUploadService::uploadToDisk(
                    $file,
                    'clients_public',
                    $folder,
                    MediaUploadService::guessMediaKind($file),
                    Auth::id()
                );

                $uploaded[] = Media::find($mediaId);
            }

            Log::info('Media upload finished', [
                'folder' => $folder,
                'uploaded' => count($uploaded),
            ]);

            $this->clearFolderCache();

            return response()->json([
                'success' => true,
                'message' => 'Đã hoàn tất tải lên/ghi đè ' . count($uploaded) . ' tệp.',
                'uploaded_count' => count($uploaded),
                'data' => $uploaded,
            ]);
        } catch (\Throwable $e) {
            Log::error('Media upload failed', [
                'message' => $e->getMessage(),
                'folder' => $request->input('folder', 'uploads'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lên tệp. Vui lòng kiểm tra định dạng và thử lại.',
            ], 422);
        }
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'alt_text' => 'nullable|string|max:300',
            'title' => 'nullable|string|max:300',
            'caption' => 'nullable|string',
            'folder' => 'nullable|string|max:100',
        ]);

        $media->update($data);
        $this->clearFolderCache();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thông tin media.',
            'media' => $media,
        ]);
    }

    public function destroy(Media $media)
    {
        $disk = $media->disk ?: 'clients_public';
        $storage = Storage::disk($disk);

        Log::info('Media delete request', [
            'id' => $media->id,
            'disk' => $disk,
            'path' => $media->file_path,
        ]);

        if ($storage->exists($media->file_path)) {
            $storage->delete($media->file_path);
        }

        $media->delete();
        $this->clearFolderCache();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tệp vĩnh viễn.',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $items = Media::whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($items as $item) {
            $storage = Storage::disk($item->disk ?: 'clients_public');

            if ($storage->exists($item->file_path)) {
                $storage->delete($item->file_path);
            }

            $item->delete();
            $deletedCount++;
        }

        Log::info('Media bulk delete finished', [
            'ids' => $ids,
            'deleted' => $deletedCount,
        ]);

        if ($deletedCount > 0) {
            $this->clearFolderCache();
        }

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} tệp.",
        ]);
    }

    public function moveFolder(Request $request)
    {
        $ids = $request->input('ids', []);
        $newFolder = trim((string) $request->input('folder'), '/');
        $medias = Media::whereIn('id', $ids)->get();
        $movedCount = 0;

        foreach ($medias as $media) {
            $disk = $media->disk ?: 'clients_public';
            $storage = Storage::disk($disk);
            $oldPath = $media->file_path;
            $newPath = $newFolder === '' ? $media->file_name : "{$newFolder}/{$media->file_name}";

            if ($oldPath === $newPath) {
                continue;
            }

            $duplicate = Media::query()
                ->where('disk', $disk)
                ->where('folder', $newFolder)
                ->where('file_name', $media->file_name)
                ->where('id', '!=', $media->id)
                ->first();

            if ($duplicate && $storage->exists($duplicate->file_path)) {
                $storage->delete($duplicate->file_path);
                $duplicate->delete();
            }

            if ($storage->exists($newPath)) {
                $storage->delete($newPath);
            }

            if ($storage->exists($oldPath)) {
                $storage->move($oldPath, $newPath);

                $media->update([
                    'folder' => $newFolder,
                    'file_path' => $newPath,
                    'file_url' => $storage->url($newPath),
                ]);

                $movedCount++;
            }
        }

        if ($movedCount > 0) {
            $this->clearFolderCache();
        }

        return response()->json([
            'success' => true,
            'message' => "Đã di chuyển {$movedCount} tệp vào thư mục {$newFolder}.",
        ]);
    }

    public function listFolders()
    {
        $folders = Cache::remember(self::FOLDERS_CACHE_KEY, now()->addMinutes(30), function () {
            $rawFolders = Media::query()
                ->whereNotNull('folder')
                ->where('folder', '!=', '')
                ->orderBy('folder')
                ->distinct()
                ->pluck('folder')
                ->values()
                ->all();

            $expandedFolders = [];

            foreach ($rawFolders as $folder) {
                $expandedFolders[] = $folder;

                $parts = explode('/', $folder);
                array_pop($parts);

                while (! empty($parts)) {
                    $expandedFolders[] = implode('/', $parts);
                    array_pop($parts);
                }
            }

            $expandedFolders = array_values(array_unique($expandedFolders));
            sort($expandedFolders, SORT_NATURAL | SORT_FLAG_CASE);

            return $expandedFolders;
        });

        return response()->json($folders);
    }

    private function clearFolderCache(): void
    {
        Cache::forget(self::FOLDERS_CACHE_KEY);
    }
}
