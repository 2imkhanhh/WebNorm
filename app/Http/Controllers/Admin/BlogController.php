<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest('published_at')->latest('id')->get();

        return Inertia::render('Blogs', [
            'blogs' => $blogs,
        ]);
    }

    public function publish(Request $request)
    {
        $items = $request->input('items', []);
        $deletedIds = $request->input('deleted_ids', []);

        DB::transaction(function () use ($items, $deletedIds) {
            if (!empty($deletedIds)) {
                Blog::whereIn('id', $deletedIds)->delete();
            }

            foreach ($items as $index => $itemData) {
                $id = $itemData['id'] ?? null;

                $title = $itemData['title'] ?? '';
                if (is_string($title)) {
                    $title = ['vi' => $title, 'en' => $title];
                }
                $category = $itemData['category'] ?? '';
                if (is_string($category)) {
                    $category = ['vi' => $category, 'en' => $category];
                }
                $excerpt = $itemData['excerpt'] ?? '';
                if (is_string($excerpt)) {
                    $excerpt = ['vi' => $excerpt, 'en' => $excerpt];
                }

                $payload = [
                    'title' => $title,
                    'slug' => $itemData['slug'] ?? ('news-' . ($index + 1)),
                    'category' => $category,
                    'thumbnail' => $itemData['thumbnail'] ?? null,
                    'excerpt' => $excerpt,
                    'content' => $itemData['content'] ?? null,
                    'author' => $itemData['author'] ?? 'Admin',
                    'status' => $itemData['status'] ?? 'published',
                    'published_at' => $itemData['published_at'] ?? now(),
                ];

                if ($id && is_numeric($id) && Blog::find($id)) {
                    Blog::where('id', $id)->update($payload);
                } else {
                    Blog::create($payload);
                }
            }
        });

        return back()->with('success', 'Đã xuất bản tin tức vào cơ sở dữ liệu thành công!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|string|unique:blogs,slug',
            'category' => 'nullable',
            'thumbnail' => 'nullable|string',
            'excerpt' => 'nullable',
            'content' => 'nullable',
            'author' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $validated['title']];
        }
        if (isset($validated['category']) && is_string($validated['category'])) {
            $validated['category'] = ['vi' => $validated['category'], 'en' => $validated['category']];
        }
        if (isset($validated['excerpt']) && is_string($validated['excerpt'])) {
            $validated['excerpt'] = ['vi' => $validated['excerpt'], 'en' => $validated['excerpt']];
        }

        $validated['published_at'] = now();
        $validated['status'] = $validated['status'] ?? 'published';

        Blog::create($validated);

        return back()->with('success', 'Đã thêm bài viết thành công!');
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|string|unique:blogs,slug,' . $blog->id,
            'category' => 'nullable',
            'thumbnail' => 'nullable|string',
            'excerpt' => 'nullable',
            'content' => 'nullable',
            'author' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
        ]);

        if (is_string($validated['title'])) {
            $validated['title'] = ['vi' => $validated['title'], 'en' => $blog->title['en'] ?? $validated['title']];
        }
        if (isset($validated['category']) && is_string($validated['category'])) {
            $validated['category'] = ['vi' => $validated['category'], 'en' => $blog->category['en'] ?? $validated['category']];
        }
        if (isset($validated['excerpt']) && is_string($validated['excerpt'])) {
            $validated['excerpt'] = ['vi' => $validated['excerpt'], 'en' => $blog->excerpt['en'] ?? $validated['excerpt']];
        }

        $blog->update($validated);

        return back()->with('success', 'Đã cập nhật bài viết thành công!');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return back()->with('success', 'Đã xóa bài viết thành công!');
    }
}
