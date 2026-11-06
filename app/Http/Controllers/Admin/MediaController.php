<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
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
            'file' => 'required|file|max:25600', // Max 25MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);

        $destinationPath = public_path('assets/uploads');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileSize = $file->getSize();
        $mime = $file->getClientMimeType();
        $file->move($destinationPath, $safeName);

        Media::create([
            'name' => $originalName,
            'file_path' => '/assets/uploads/' . $safeName,
            'file_type' => str_starts_with($mime, 'image/') ? 'image' : 'file',
            'file_size' => $fileSize,
        ]);

        return back()->with('success', 'Đã tải lên tệp thành công!');
    }

    public function destroy(Media $media)
    {
        $realPath = public_path(ltrim($media->file_path, '/'));
        if (file_exists($realPath) && is_file($realPath)) {
            @unlink($realPath);
        }

        $media->delete();

        return back()->with('success', 'Đã xóa tệp thành công!');
    }
}
