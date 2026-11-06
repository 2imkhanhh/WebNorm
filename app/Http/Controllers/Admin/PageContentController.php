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

            foreach ($pageRows->groupBy('section_id') as $sectionId => $fields) {
                $first = $fields->first();
                $fieldList = [];

                foreach ($fields as $f) {
                    $fieldList[] = [
                        'id' => $f->id,
                        'page_slug' => $f->page_slug,
                        'section_id' => $f->section_id,
                        'key' => $f->field_key,
                        'label' => $f->field_label,
                        'type' => $f->field_type,
                        'value' => $f->value, // {"vi": "...", "en": "..."}
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

        foreach ($fields as $f) {
            if (isset($f['page_slug'], $f['section_id'], $f['key'])) {
                PageContent::where('page_slug', $f['page_slug'])
                    ->where('section_id', $f['section_id'])
                    ->where('field_key', $f['key'])
                    ->update([
                        'value' => $f['value'],
                    ]);
            }
        }

        return back()->with('success', 'Đã lưu cấu hình trang thành công!');
    }
}

