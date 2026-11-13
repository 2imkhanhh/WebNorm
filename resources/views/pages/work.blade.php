@extends('layouts.app')

@section('title', __('site.title_work'))
@section('body_class', 'work-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/work.css') }}?v={{ filemtime(public_path('assets/css/work.css')) }}">
@endpush

@section('content')
    @php
        $locale = app()->getLocale();
        $filterAllLabel = \App\Helpers\NormHelper::val($pageContents['filter_all'] ?? null, 'TẤT CẢ DỰ ÁN');
        $filterFeaturedLabel = \App\Helpers\NormHelper::val($pageContents['filter_featured'] ?? null, 'NỔI BẬT');

        $dynamicFilters = [
            [
                'filter' => 'featured',
                'label' => $filterFeaturedLabel,
                'studioKey' => 'filter_featured',
            ],
        ];

        if (isset($categories) && $categories->isNotEmpty()) {
            foreach ($categories as $cat) {
                $dynamicFilters[] = [
                    'filter' => $cat->slug,
                    'label' => $cat->getName($locale),
                    'studioKey' => null,
                ];
            }
        }

        // Group filter items into staggered editorial rows while keeping long names from colliding
        $filterRows = [];
        $count = count($dynamicFilters);
        $idx = 0;
        $alignRightNext = true;

        while ($idx < $count) {
            $curr = $dynamicFilters[$idx];
            $currLen = mb_strlen(trim($curr['label']), 'UTF-8');

            if ($idx + 1 < $count) {
                $next = $dynamicFilters[$idx + 1];
                $nextLen = mb_strlen(trim($next['label']), 'UTF-8');

                if ($currLen <= 18 && $nextLen <= 18 && $currLen + $nextLen <= 28) {
                    $filterRows[] = [
                        'class' => 'wh-space-between',
                        'items' => [$curr, $next],
                    ];
                    $idx += 2;
                    $alignRightNext = true;
                    continue;
                }
            }

            $filterRows[] = [
                'class' => $alignRightNext ? 'wh-right' : '',
                'items' => [$curr],
            ];
            $alignRightNext = !$alignRightNext;
            $idx++;
        }

        $hasLongFilter = count($dynamicFilters) > 3
            || mb_strlen(trim($filterAllLabel), 'UTF-8') > 16
            || collect($dynamicFilters)->contains(fn($item) => mb_strlen(trim($item['label']), 'UTF-8') > 15);
    @endphp

    <section class="work-hero-section">
        <div class="work-hero-container {{ $hasLongFilter ? 'has-long-filter' : '' }}">
            <div class="wh-line">
                <a href="#" class="wh-text wh-dark filter-btn" data-filter="all"
                    data-studio-key="filter_all">{{ $filterAllLabel }}</a>
            </div>

            @foreach ($filterRows as $row)
                <div class="wh-line {{ $row['class'] }}">
                    @foreach ($row['items'] as $item)
                        <a href="#" class="wh-text wh-light filter-btn" data-filter="{{ $item['filter'] }}"
                            @if ($item['studioKey']) data-studio-key="{{ $item['studioKey'] }}" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <section class="work-list-section">
        <div class="work-list-header">
            <a href="#" class="view-list-btn" id="viewToggleBtn">
                <i class='bx bx-menu' id="viewToggleIcon"></i> <span id="viewToggleText">{{ __('site.work_view_list') }}</span>
            </a>
        </div>

        <div class="work-grid">
            @forelse($projects as $p)
                @php
                    $catSlugs = $p->categories->pluck('slug')->implode(',');
                @endphp
                <a href="{{ route('work.detail', $p->slug) }}" class="project-card"
                    data-category-slugs="{{ $catSlugs }}" data-category="{{ strtolower($p->getCategory($locale)) }}"
                    data-featured="{{ $p->is_featured ? '1' : '0' }}">
                    <div class="project-img-wrap">
                        <img src="{{ !empty($p->thumbnail) ? (str_starts_with($p->thumbnail, 'http') ? $p->thumbnail : asset($p->thumbnail)) : asset('assets/images/project1.png') }}"
                            alt="{{ $p->getTitle($locale) }}" class="project-img">
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">{{ $p->getTitle($locale) }}</h3>
                        <p class="project-category">{{ $p->getCategory($locale) }}</p>
                    </div>
                </a>
            @empty
                <div class="col-12 py-5 text-center text-muted">{{ __('site.work_empty') }}</div>
            @endforelse
        </div>

        <div id="workEmptyFilterMsg" class="py-5 text-center text-muted" style="display: none;">
            {{ __('site.work_empty_category') }}
        </div>

        <div class="load-more-wrap">
            <a href="#" class="load-more-btn" id="loadMoreBtn" style="display: none;">{{ __('site.work_load_more') }}</a>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const projects = document.querySelectorAll('.project-card');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const emptyFilterMsg = document.getElementById('workEmptyFilterMsg');

            const ITEMS_PER_LOAD = 6;
            let currentItems = ITEMS_PER_LOAD;
            let currentFilter = 'all';

            function updateProjects() {
                let visibleCount = 0;
                let matchingCount = 0;

                projects.forEach(project => {
                    const slugs = (project.getAttribute('data-category-slugs') || '').split(',').filter(
                        Boolean);
                    const category = (project.getAttribute('data-category') || '').toLowerCase();
                    const isFeatured = project.getAttribute('data-featured') === '1';

                    let matchesFilter = false;
                    if (currentFilter === 'all') {
                        matchesFilter = true;
                    } else if (currentFilter === 'featured') {
                        matchesFilter = isFeatured;
                    } else {
                        matchesFilter = slugs.includes(currentFilter) || category.includes(currentFilter);
                    }

                    if (matchesFilter) {
                        matchingCount++;
                        if (visibleCount < currentItems) {
                            if (project.style.display !== 'flex') {
                                project.style.display = 'flex';
                                project.style.animation = 'none';
                                project.offsetHeight;
                                project.style.animation = 'fadeIn 0.4s ease forwards';
                            }
                            visibleCount++;
                        } else {
                            project.style.display = 'none';
                        }
                    } else {
                        project.style.display = 'none';
                    }
                });

                if (emptyFilterMsg && projects.length > 0) {
                    emptyFilterMsg.style.display = matchingCount === 0 ? 'block' : 'none';
                }

                if (loadMoreBtn) {
                    if (matchingCount > currentItems) {
                        loadMoreBtn.style.display = 'inline-block';
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }
                }
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    filterBtns.forEach(fBtn => {
                        fBtn.classList.remove('wh-dark');
                        fBtn.classList.add('wh-light');
                    });
                    btn.classList.remove('wh-light');
                    btn.classList.add('wh-dark');

                    currentFilter = btn.getAttribute('data-filter');
                    currentItems = ITEMS_PER_LOAD;
                    updateProjects();
                });
            });

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentItems += ITEMS_PER_LOAD;
                    updateProjects();
                });
            }

            // initial display
            updateProjects();

            // View Toggle Logic
            const viewToggleBtn = document.getElementById('viewToggleBtn');
            const viewToggleIcon = document.getElementById('viewToggleIcon');
            const viewToggleText = document.getElementById('viewToggleText');
            const workGrid = document.querySelector('.work-grid');

            if (viewToggleBtn && workGrid) {
                viewToggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();

                    workGrid.classList.add('no-transition');
                    workGrid.classList.toggle('list-view');

                    if (workGrid.classList.contains('list-view')) {
                        viewToggleText.textContent = @json(__('site.work_view_grid'));
                        viewToggleIcon.className = 'bx bx-grid-alt';
                    } else {
                        viewToggleText.textContent = @json(__('site.work_view_list'));
                        viewToggleIcon.className = 'bx bx-menu';
                    }

                    workGrid.offsetHeight; // trigger reflow
                    setTimeout(() => {
                        workGrid.classList.remove('no-transition');
                    }, 50);
                });
            }
        });
    </script>
@endpush
