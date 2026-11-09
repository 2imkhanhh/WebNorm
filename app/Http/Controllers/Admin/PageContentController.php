<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageContentController extends Controller
{
    public function index()
    {
        $rawContents = PageContent::orderBy('sort_order')->get();

        $pageNames = [
            '/' => 'Trang chủ',
            '/about-us' => 'Về chúng tôi',
            '/work' => 'Dự án',
            '/news' => 'Bài viết / Tin tức',
        ];

        $pagesGrouped = [];
        foreach ($pageNames as $slug => $name) {
            $pageRows = $rawContents->where('page_slug', $slug);
            $sectionsGrouped = [];

            $hasDraft = false;
            $latestDraftTime = null;

            foreach ($pageRows->groupBy('section_id') as $sectionId => $fields) {
                $first = $fields->first();
                $fieldList = [];

                foreach ($fields as $f) {
                    $fieldHasDraft = ($f->draft_value !== null);
                    if ($fieldHasDraft) {
                        $hasDraft = true;
                        if (!$latestDraftTime || ($f->draft_updated_at && $f->draft_updated_at > $latestDraftTime)) {
                            $latestDraftTime = $f->draft_updated_at;
                        }
                    }

                    // Value given to frontend editor: draft_value if exists, otherwise published value
                    $currentValue = $fieldHasDraft ? $f->draft_value : $f->value;

                    $fieldList[] = [
                        'id' => $f->id,
                        'page_slug' => $f->page_slug,
                        'section_id' => $f->section_id,
                        'key' => $f->field_key,
                        'label' => $f->field_label,
                        'type' => $f->field_type,
                        'value' => $currentValue,
                        'published_value' => $f->value,
                        'draft_value' => $f->draft_value,
                        'has_draft' => $fieldHasDraft,
                    ];
                }

                $sectionsGrouped[] = [
                    'id' => $sectionId,
                    'title' => $first->section_title ?? $sectionId,
                    'fields' => $fieldList,
                ];
            }

            $pagesGrouped[] = [
                'name' => $name,
                'slug' => $slug,
                'has_draft' => $hasDraft,
                'draft_updated_at' => $latestDraftTime ? $latestDraftTime->toIso8601String() : null,
                'sections' => $sectionsGrouped,
            ];
        }

        return Inertia::render('Pages', [
            'initialPages' => $pagesGrouped,
        ]);
    }

    public function update(Request $request)
    {
        $fields = $request->input('fields', []);
        $action = $request->input('action', 'publish');

        if ($action === 'draft') {
            foreach ($fields as $f) {
                if (isset($f['page_slug'], $f['section_id'], $f['key'])) {
                    PageContent::where('page_slug', $f['page_slug'])
                        ->where('section_id', $f['section_id'])
                        ->where('field_key', $f['key'])
                        ->update([
                            'draft_value' => $f['value'],
                            'draft_updated_at' => now(),
                        ]);
                }
            }

            return back()->with('success', 'Đã lưu bản nháp thành công!');
        }

        foreach ($fields as $f) {
            if (isset($f['page_slug'], $f['section_id'], $f['key'])) {
                PageContent::where('page_slug', $f['page_slug'])
                    ->where('section_id', $f['section_id'])
                    ->where('field_key', $f['key'])
                    ->update([
                        'value' => $f['value'],
                        'draft_value' => null,
                        'draft_updated_at' => null,
                    ]);
            }
        }

        return back()->with('success', 'Đã xuất bản cấu hình thành công lên website!');
    }

    public function discardDraft(Request $request)
    {
        $pageSlug = $request->input('page_slug');

        if ($pageSlug) {
            PageContent::where('page_slug', $pageSlug)->update([
                'draft_value' => null,
                'draft_updated_at' => null,
            ]);

            return back()->with('success', 'Đã hủy bản nháp, quay về nội dung đang xuất bản!');
        }

        return back()->with('error', 'Không tìm thấy trang để hủy bản nháp.');
    }
}
