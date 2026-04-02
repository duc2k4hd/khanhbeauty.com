<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Thêm Log facade
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * API: Lấy danh sách Media (phân trang, lọc theo folder, search)
     */
    public function index(Request $request)
    {
        $query = Media::query();

        // Lọc theo folder - Nếu để trống thì lấy tất cả
        if ($request->filled('folder')) {
            $folder = $request->folder;
            if ($folder === 'root') {
                $query->whereNull('folder')->orWhere('folder', '');
            } else if ($folder !== 'all') {
                $query->where('folder', $folder);
            }
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('file_name', 'like', "%{$request->search}%")
                  ->orWhere('title', 'like', "%{$request->search}%");
            });
        }

        // Lọc theo type (image, video, audio)
        if ($request->filled('type')) {
            $query->where('mime_type', 'like', "{$request->type}%");
        }

        $perPage = $request->input('per_page', 50);
        $media = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($media);
    }

    /**
     * API: Upload ảnh/video (Xử lý hàng loạt đợt 20 file)
     */
    public function store(Request $request)
    {
        Log::info("Media Upload Start: " . count($request->file('files', [])) . " files to folder '{$request->folder}'");
        
        try {
            $request->validate([
                'files.*' => 'required|file|extensions:jpg,jpeg,png,webp,avif,svg,ico,gif,mp4,mp3|max:102400', // Đã bổ sung ico, gif
                'folder'  => 'nullable|string|max:100',
            ]);
        } catch (\Exception $e) {
            Log::error("Media Validation Failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi kiểm định: ' . $e->getMessage()], 422);
        }

        $uploaded = [];
        $folderName = $request->input('folder', 'uploads');
        $baseDir = $folderName; // Disk 'clients_public' đã trỏ vào public/images/clients/

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                Log::info("Processing file: " . $originalName);
                
                // Kiểm tra xem đã tồn tại bản ghi và file chưa để xử lý OVERWRITE
                $existing = Media::where('folder', $folderName)
                                 ->where('file_name', $originalName)
                                 ->first();

                if ($existing) {
                    Log::info("Existing file found for '{$originalName}' in '{$folderName}'. Overwriting...");
                    if (Storage::disk('clients_public')->exists($existing->file_path)) {
                        Storage::disk('clients_public')->delete($existing->file_path);
                    }
                    
                    // Lưu file mới với đúng tên gốc
                    $path = $file->storeAs($baseDir, $originalName, 'clients_public');
                    if (!$path) {
                        Log::error("Failed to store file '{$originalName}' to disk.");
                        continue;
                    }
                    
                    $url = Storage::disk('clients_public')->url($path);

                    // Cập nhật bản ghi cũ
                    $existing->update([
                        'file_path'       => $path,
                        'file_url'        => $url,
                        'mime_type'       => $file->getMimeType(),
                        'file_size_bytes' => $file->getSize(),
                        'is_optimized'    => false,
                    ]);
                    $uploaded[] = $existing;
                    Log::info("Updated existing record ID: " . $existing->id);
                } else {
                    // Nếu chưa có: Lưu mới
                    $path = $file->storeAs($baseDir, $originalName, 'clients_public');
                    if (!$path) {
                        Log::error("Failed to store new file '{$originalName}' to disk.");
                        continue;
                    }
                    
                    $url = Storage::disk('clients_public')->url($path);

                    $media = Media::create([
                        'uploader_id'     => Auth::id(),
                        'file_name'       => $originalName,
                        'file_path'       => $path,
                        'file_url'        => $url,
                        'disk'            => 'clients_public',
                        'mime_type'       => $file->getMimeType() ?: 'image/avif', // Backup mime if null
                        'file_size_bytes' => $file->getSize(),
                        'folder'          => $folderName,
                        'is_optimized'    => false,
                        'image_type'      => $this->guessImageType($file),
                    ]);
                    
                    if ($media) {
                        $uploaded[] = $media;
                        Log::info("Created new record ID: " . $media->id . " at " . $path);
                    } else {
                        Log::error("Failed to create DB record for '{$originalName}'.");
                    }
                }
            }
        }

        Log::info("Media Upload Finished. Total successfully uploaded/overwritten: " . count($uploaded));
        return response()->json([
            'success'   => true,
            'message'   => 'Đã hoàn tất tải lên/ghi đè ' . count($uploaded) . ' tệp vào Public Storage.',
            'data'      => $uploaded
        ]);
    }

    /**
     * API: Cập nhật thông tin Alt, Title...
     */
    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'alt_text' => 'nullable|string|max:300',
            'title'    => 'nullable|string|max:300',
            'caption'  => 'nullable|string',
            'folder'   => 'nullable|string|max:100',
        ]);

        $media->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thông tin Media.',
            'media'   => $media
        ]);
    }

    public function destroy(Media $media)
    {
        Log::info("Delete Request: Media ID {$media->id}, Path: {$media->file_path}");
        
        if (Storage::disk('clients_public')->exists($media->file_path)) {
            Storage::disk('clients_public')->delete($media->file_path);
            Log::info("Physical file deleted: " . $media->file_path);
        } else {
            Log::warning("Physical file not found for deletion: " . $media->file_path);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tệp vĩnh viễn.'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        Log::info("Bulk Delete Request for IDs: " . implode(', ', $ids));
        
        $items = Media::whereIn('id', $ids)->get();
        $deletedCount = 0;
        
        foreach ($items as $item) {
            if (Storage::disk('clients_public')->exists($item->file_path)) {
                Storage::disk('clients_public')->delete($item->file_path);
            }
            $item->delete();
            $deletedCount++;
        }

        Log::info("Bulk Delete Finished. Total items removed: " . $deletedCount);
        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} tệp."
        ]);
    }

    /**
     * API: Di chuyển Folder hàng loạt
     */
    public function moveFolder(Request $request)
    {
        $ids = $request->input('ids', []);
        $newFolder = $request->input('folder');
        $baseDir = $newFolder;

        $medias = Media::whereIn('id', $ids)->get();
        $movedCount = 0;

        foreach ($medias as $media) {
            $oldPath = $media->file_path;
            $newPath = "{$baseDir}/{$media->file_name}";

            if ($oldPath === $newPath) continue;

            if (Storage::disk('clients_public')->exists($newPath)) {
                Storage::disk('clients_public')->delete($newPath);
                Media::where('folder', $newFolder)->where('file_name', $media->file_name)->delete();
            }

            if (Storage::disk('clients_public')->exists($oldPath)) {
                Storage::disk('clients_public')->move($oldPath, $newPath);
                
                $media->update([
                    'folder'    => $newFolder,
                    'file_path' => $newPath,
                    'file_url'  => Storage::disk('clients_public')->url($newPath)
                ]);
                $movedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã di chuyển vật lý {$movedCount} tệp vào thư mục {$newFolder}."
        ]);
    }

    /**
     * API: Lấy danh sách Folder duy nhất
     */
    public function listFolders()
    {
        $folders = Media::distinct()->pluck('folder')->filter()->values();
        return response()->json($folders);
    }

    private function guessImageType($file)
    {
        $mime = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (str_contains($mime, 'image') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif', 'svg', 'ico', 'gif'])) return 'image';
        if (str_contains($mime, 'video') || in_array($extension, ['mp4', 'mov', 'avi'])) return 'video';
        if (str_contains($mime, 'audio') || in_array($extension, ['mp3', 'wav'])) return 'audio';
        return 'other';
    }
}
