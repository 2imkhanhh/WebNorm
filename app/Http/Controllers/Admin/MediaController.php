<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->get();

        return Inertia::render('Media', [
            'media' => $media,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // Max 50MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);

        // Save to storage/app/public/uploads (accessible via public/storage/uploads)
        $file->storeAs('uploads', $safeName, 'public');

        $fileSize = $file->getSize();
        $mime = $file->getClientMimeType() ?: '';
        $ext = strtolower($file->getClientOriginalExtension());

        $fileType = 'file';
        if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $fileType = 'image';
        } elseif (str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'mov', 'avi'])) {
            $fileType = 'video';
        } elseif ($ext === 'glb') {
            $fileType = 'model3d';
        }

        $filePath = '/storage/uploads/' . $safeName;

        $media = Media::create([
            'name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'file_path' => 'storage/uploads/' . $safeName,
                'url' => asset('storage/uploads/' . $safeName),
                'name' => $originalName,
                'media_id' => $media->id,
            ]);
        }

        return back()->with('success', 'Đã tải lên tệp thành công!');
    }

    public function destroy(Media $media)
    {
        $relPath = ltrim($media->file_path, '/');
        if (str_starts_with($relPath, 'storage/')) {
            $storageSub = substr($relPath, strlen('storage/'));
            Storage::disk('public')->delete($storageSub);
        } elseif (file_exists(public_path($relPath)) && is_file(public_path($relPath))) {
            @unlink(public_path($relPath));
        }

        $media->delete();

        return back()->with('success', 'Đã xóa tệp thành công!');
    }
}
