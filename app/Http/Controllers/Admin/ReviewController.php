<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('sort_order')->latest('id')->get();

        return Inertia::render('Reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function publish(Request $request)
    {
        $items = $request->input('items', []);
        $deletedIds = $request->input('deleted_ids', []);

        DB::transaction(function () use ($items, $deletedIds) {
            if (!empty($deletedIds)) {
                Review::whereIn('id', $deletedIds)->delete();
            }

            foreach ($items as $index => $itemData) {
                $id = $itemData['id'] ?? null;

                $role = $itemData['role'] ?? '';
                if (is_string($role)) {
                    $role = ['vi' => $role, 'en' => $role];
                }
                $content = $itemData['content'] ?? '';
                if (is_string($content)) {
                    $content = ['vi' => $content, 'en' => $content];
                }

                $payload = [
                    'author' => $itemData['author'] ?? '',
                    'role' => $role,
                    'content' => $content,
                    'avatar' => $itemData['avatar'] ?? null,
                    'rating' => $itemData['rating'] ?? 5,
                    'sort_order' => $index + 1,
                ];

                if ($id && is_numeric($id) && Review::find($id)) {
                    Review::where('id', $id)->update($payload);
                } else {
                    Review::create($payload);
                }
            }
        });

        return back()->with('success', 'Đã xuất bản đánh giá vào cơ sở dữ liệu thành công!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'role' => 'nullable',
            'content' => 'required',
            'avatar' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if (isset($validated['role']) && is_string($validated['role'])) {
            $validated['role'] = ['vi' => $validated['role'], 'en' => $validated['role']];
        }
        if (is_string($validated['content'])) {
            $validated['content'] = ['vi' => $validated['content'], 'en' => $validated['content']];
        }

        $validated['rating'] = $validated['rating'] ?? 5;

        Review::create($validated);

        return back()->with('success', 'Đã thêm đánh giá thành công!');
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'role' => 'nullable',
            'content' => 'required',
            'avatar' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if (isset($validated['role']) && is_string($validated['role'])) {
            $validated['role'] = ['vi' => $validated['role'], 'en' => $review->role['en'] ?? $validated['role']];
        }
        if (is_string($validated['content'])) {
            $validated['content'] = ['vi' => $validated['content'], 'en' => $review->content['en'] ?? $validated['content']];
        }

        $review->update($validated);

        return back()->with('success', 'Đã cập nhật đánh giá thành công!');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công!');
    }
}
