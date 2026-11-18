<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StudioDraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function seedPageContent(): void
    {
        PageContent::create([
            'page_slug' => '/',
            'section_id' => 'hero',
            'section_title' => 'Hero',
            'field_key' => 'hero_tagline_1',
            'field_label' => 'Tagline 1',
            'field_type' => 'text',
            'value' => ['vi' => 'LIVE HOME TAGLINE', 'en' => 'LIVE HOME TAGLINE EN'],
            'draft_value' => ['vi' => 'DRAFT HOME TAGLINE', 'en' => 'DRAFT HOME TAGLINE EN'],
        ]);

        PageContent::create([
            'page_slug' => '/about-us',
            'section_id' => 'about_intro',
            'section_title' => 'Intro',
            'field_key' => 'about_heading_1',
            'field_label' => 'Heading 1',
            'field_type' => 'text',
            'value' => ['vi' => 'LIVE ABOUT HEADING', 'en' => 'LIVE ABOUT HEADING EN'],
            'draft_value' => ['vi' => 'DRAFT ABOUT HEADING', 'en' => 'DRAFT ABOUT HEADING EN'],
        ]);

        PageContent::create([
            'page_slug' => '/work',
            'section_id' => 'work_hero',
            'section_title' => 'Work Hero',
            'field_key' => 'filter_all',
            'field_label' => 'Filter All',
            'field_type' => 'text',
            'value' => ['vi' => 'LIVE WORK FILTER', 'en' => 'LIVE WORK FILTER EN'],
            'draft_value' => ['vi' => 'DRAFT WORK FILTER', 'en' => 'DRAFT WORK FILTER EN'],
        ]);

        PageContent::create([
            'page_slug' => '/news',
            'section_id' => 'news_header',
            'section_title' => 'News Header',
            'field_key' => 'news_title1',
            'field_label' => 'News Title 1',
            'field_type' => 'text',
            'value' => ['vi' => 'LIVE NEWS TITLE', 'en' => 'LIVE NEWS TITLE EN'],
            'draft_value' => ['vi' => 'DRAFT NEWS TITLE', 'en' => 'DRAFT NEWS TITLE EN'],
        ]);
    }

    public function test_draft_preview_hides_notification_banner_and_propagates_across_pages(): void
    {
        $this->seedPageContent();
        $user = User::factory()->create();

        $home = $this->actingAs($user)->get('/?preview=draft');
        $home->assertOk();
        $home->assertSee('DRAFT HOME TAGLINE');
        $home->assertDontSee('LIVE HOME TAGLINE');
        $home->assertDontSee('draftPreviewNotification');
        $home->assertDontSee('Xem bản Live');
        $home->assertSee('/about-us?preview=draft', false);
        $home->assertSee('/work?preview=draft', false);
        $home->assertSee('/news?preview=draft', false);
        $home->assertSee('/en?preview=draft', false);
        $home->assertSee('<link rel="canonical" href="' . url('/') . '">', false);

        $about = $this->actingAs($user)->get('/about-us?preview=draft');
        $about->assertOk();
        $about->assertSee('DRAFT ABOUT HEADING');
        $about->assertDontSee('LIVE ABOUT HEADING');
        $about->assertDontSee('draftPreviewNotification');
        $about->assertSee('/work?preview=draft', false);
        $about->assertSee('/news?preview=draft', false);

        $work = $this->actingAs($user)->get('/work?preview=draft');
        $work->assertOk();
        $work->assertSee('DRAFT WORK FILTER');
        $work->assertDontSee('LIVE WORK FILTER');
        $work->assertDontSee('draftPreviewNotification');

        $news = $this->actingAs($user)->get('/news?preview=draft');
        $news->assertOk();
        $news->assertSee('DRAFT NEWS TITLE');
        $news->assertDontSee('LIVE NEWS TITLE');
        $news->assertDontSee('draftPreviewNotification');

        $enAbout = $this->actingAs($user)->get('/en/about-us?preview=draft');
        $enAbout->assertOk();
        $enAbout->assertSee('DRAFT ABOUT HEADING EN');
        $enAbout->assertDontSee('LIVE ABOUT HEADING EN');
        $enAbout->assertSee('/en/work?preview=draft', false);
    }

    public function test_live_mode_and_guest_preview_show_live_content_without_preview_query_links(): void
    {
        $this->seedPageContent();
        $user = User::factory()->create();

        $live = $this->actingAs($user)->get('/');
        $live->assertOk();
        $live->assertSee('LIVE HOME TAGLINE');
        $live->assertDontSee('DRAFT HOME TAGLINE');
        $live->assertDontSee('?preview=draft', false);

        $guest = $this->get('/?preview=draft');
        $guest->assertOk();
        $guest->assertSee('LIVE HOME TAGLINE');
        $guest->assertDontSee('DRAFT HOME TAGLINE');
        $guest->assertDontSee('?preview=draft', false);
    }

    public function test_module_drafts_do_not_modify_live_data_until_published(): void
    {
        $user = User::factory()->create();
        $cat = ProjectCategory::create([
            'name' => ['vi' => 'Branding', 'en' => 'Branding'],
            'slug' => 'branding',
            'sort_order' => 1,
        ]);

        $project = Project::create([
            'title' => ['vi' => 'LIVE PROJECT', 'en' => 'LIVE PROJECT'],
            'slug' => 'live-project',
            'category' => ['vi' => 'Branding', 'en' => 'Branding'],
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $project->categories()->sync([$cat->id]);

        $service = Service::create([
            'number' => '01',
            'title' => ['vi' => 'LIVE SERVICE', 'en' => 'LIVE SERVICE'],
            'description' => ['vi' => 'Line 1', 'en' => 'Line 1'],
            'content' => ['vi' => ['Line 1'], 'en' => ['Line 1']],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $blog = Blog::create([
            'title' => ['vi' => 'LIVE BLOG', 'en' => 'LIVE BLOG'],
            'slug' => 'live-blog',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
        ]);

        $review = Review::create([
            'author' => 'LIVE AUTHOR',
            'role' => ['vi' => 'CEO', 'en' => 'CEO'],
            'content' => ['vi' => 'LIVE QUOTE', 'en' => 'LIVE QUOTE'],
            'rating' => 5,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Setting::set('hotline', '0900000000');

        // Save drafts for all 5 modules
        $this->actingAs($user)->post('/admin/projects/draft', [
            'items' => [[
                'id' => 'temp_1',
                'title' => ['vi' => 'DRAFT PROJECT', 'en' => 'DRAFT PROJECT'],
                'slug' => 'draft-project',
                'category_ids' => [$cat->id],
                'is_featured' => true,
            ]],
            'deleted_ids' => [$project->id],
        ])->assertRedirect();

        $this->actingAs($user)->post('/admin/services/draft', [
            'items' => [[
                'id' => $service->id,
                'number' => '01',
                'title' => ['vi' => 'DRAFT SERVICE', 'en' => 'DRAFT SERVICE'],
                'description' => ['vi' => 'Draft Line', 'en' => 'Draft Line'],
            ]],
            'deleted_ids' => [],
        ])->assertRedirect();

        $this->actingAs($user)->post('/admin/news/draft', [
            'items' => [[
                'id' => 'temp_b1',
                'title' => ['vi' => 'DRAFT BLOG', 'en' => 'DRAFT BLOG'],
                'slug' => 'draft-blog',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->toIso8601String(),
            ]],
            'deleted_ids' => [$blog->id],
        ])->assertRedirect();

        $this->actingAs($user)->post('/admin/reviews/draft', [
            'items' => [[
                'id' => $review->id,
                'author' => 'DRAFT AUTHOR',
                'role' => ['vi' => 'CTO', 'en' => 'CTO'],
                'content' => ['vi' => 'DRAFT QUOTE', 'en' => 'DRAFT QUOTE'],
                'rating' => 5,
            ]],
            'deleted_ids' => [],
        ])->assertRedirect();

        $this->actingAs($user)->post('/admin/settings/draft', [
            'settings' => [
                'hotline' => '0999999999',
            ],
        ])->assertRedirect();

        // Live tables remain untouched
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'slug' => 'live-project']);
        $this->assertDatabaseMissing('projects', ['slug' => 'draft-project']);
        $this->assertEquals('LIVE SERVICE', $service->fresh()->getTitle('vi'));
        $this->assertDatabaseHas('blogs', ['id' => $blog->id, 'slug' => 'live-blog']);
        $this->assertEquals('LIVE AUTHOR', $review->fresh()->author);
        $this->assertEquals('0900000000', Setting::get('hotline'));

        // Live pages show live data
        $liveWork = $this->actingAs($user)->get('/work');
        $liveWork->assertSee('LIVE PROJECT');
        $liveWork->assertDontSee('DRAFT PROJECT');

        // Draft preview pages show draft data across all routes
        $previewHome = $this->actingAs($user)->get('/?preview=draft');
        $previewHome->assertSee('DRAFT PROJECT');
        $previewHome->assertSee('DRAFT SERVICE');
        $previewHome->assertSee('DRAFT BLOG');
        $previewHome->assertSee('DRAFT AUTHOR');
        $previewHome->assertSee('0999999999');
        $previewHome->assertDontSee('LIVE PROJECT');

        $previewWork = $this->actingAs($user)->get('/work?preview=draft');
        $previewWork->assertSee('DRAFT PROJECT');
        $previewWork->assertDontSee('LIVE PROJECT');

        $previewProjectDetail = $this->actingAs($user)->get('/work/draft-project?preview=draft');
        $previewProjectDetail->assertOk();
        $previewProjectDetail->assertSee('DRAFT PROJECT');

        $previewNewsDetail = $this->actingAs($user)->get('/news/draft-blog?preview=draft');
        $previewNewsDetail->assertOk();
        $previewNewsDetail->assertSee('DRAFT BLOG');

        // While services/blogs/reviews still have drafts, switching to /admin/projects still reports draftStatus.has_any = true
        $otherTab = $this->actingAs($user)->get('/admin/projects');
        $otherTab->assertOk();
        $otherTab->assertSee('"has_any":true', false);

        // Discard project draft restores live project in preview
        $this->actingAs($user)->post('/admin/projects/discard-draft')->assertRedirect();
        $this->assertNull(StudioDraft::where('module', 'projects')->first());
        $this->actingAs($user)->get('/work?preview=draft')->assertSee('LIVE PROJECT')->assertDontSee('DRAFT PROJECT');

        // Publish settings draft persists to live table and clears draft
        $this->actingAs($user)->post('/admin/settings', [
            'settings' => ['hotline' => '0999999999'],
        ])->assertRedirect();
        $this->assertEquals('0999999999', Setting::get('hotline'));
        $this->assertNull(StudioDraft::where('module', 'settings')->first());
    }
}
