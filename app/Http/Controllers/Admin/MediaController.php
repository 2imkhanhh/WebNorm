<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Allowed upload extensions grouped by media category.
     */
    private const ALLOWED_EXTENSIONS = [
        'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'ico'],
        'video' => ['mp4', 'webm', 'mov'],
        'model3d' => ['glb'],
        'file' => ['pdf'],
    ];

    public function index()
    {
        $media = Media::latest()->get();

        return Inertia::render('Media', [
            'media' => $media,
        ]);
    }

    public function upload(Request $request)
    {
        $allowedExts = array_merge(...array_values(self::ALLOWED_EXTENSIONS));

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200', // Max 50MB
                function (string $attribute, mixed $value, \Closure $fail) use ($allowedExts) {
                    $ext = strtolower((string) $value->getClientOriginalExtension());
                    if (! in_array($ext, $allowedExts, true)) {
                        $fail('Định dạng tệp .' . $ext . ' không được phép tải lên.');
                        return;
                    }

                    $realPath = $value->getRealPath() ?: $value->getPathname();
                    $mime = '';
                    if ($realPath && class_exists(\finfo::class)) {
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $detected = @$finfo->file($realPath);
                        if (is_string($detected)) {
                            $mime = strtolower($detected);
                        }
                    }
                    if ($mime === '') {
                        try {
                            $mime = strtolower((string) ($value->getMimeType() ?: ''));
                        } catch (\Throwable) {
                            $mime = '';
                        }
                    }

                    if (in_array($ext, self::ALLOWED_EXTENSIONS['image'], true)) {
                        if (! str_starts_with($mime, 'image/') || $mime === 'image/svg+xml') {
                            $fail('Tệp hình ảnh không hợp lệ.');
                        }
                        return;
                    }

                    if (in_array($ext, self::ALLOWED_EXTENSIONS['video'], true)) {
                        if (! str_starts_with($mime, 'video/') && $mime !== 'application/octet-stream') {
                            $fail('Tệp video không hợp lệ.');
                        }
                        return;
                    }

                    if ($ext === 'glb') {
                        $header = @file_get_contents($value->getRealPath(), false, null, 0, 4);
                        if ($header !== 'glTF' || ! in_array($mime, ['model/gltf-binary', 'application/octet-stream'], true)) {
                            $fail('Tệp mô hình 3D (.glb) không hợp lệ.');
                        }
                        return;
                    }

                    if ($ext === 'pdf') {
                        $header = @file_get_contents($value->getRealPath(), false, null, 0, 5);
                        if ($header !== '%PDF-' || $mime !== 'application/pdf') {
                            $fail('Tệp PDF không hợp lệ.');
                        }
                    }
                },
            ],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());
        $baseSlug = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'upload';
        $safeName = time() . '_' . Str::lower(Str::random(8)) . '_' . Str::limit($baseSlug, 40, '') . '.' . $ext;

        // Save to storage/app/public/uploads (accessible via public/storage/uploads)
        $file->storeAs('uploads', $safeName, 'public');

        $fileSize = $file->getSize();

        $fileType = 'file';
        foreach (self::ALLOWED_EXTENSIONS as $category => $exts) {
            if (in_array($ext, $exts, true)) {
                $fileType = $category;
                break;
            }
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
        $relPath = ltrim(str_replace('\\', '/', (string) $media->file_path), '/');
        if (str_starts_with($relPath, 'storage/uploads/') && ! str_contains($relPath, '..')) {
            $storageSub = substr($relPath, strlen('storage/'));
            Storage::disk('public')->delete($storageSub);
        }

        $media->delete();

        return back()->with('success', 'Đã xóa tệp thành công!');
    }
}
