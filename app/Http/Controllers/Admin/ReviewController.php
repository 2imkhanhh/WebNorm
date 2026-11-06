<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
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
