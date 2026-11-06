@extends('layouts.app')

@section('title', 'Dự án | Brand Design Agency')
@section('body_class', 'work-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/work.css') }}">
@endpush

@section('content')
<section class="work-hero-section">
        <div class="work-hero-container">
            <div class="wh-line">
                <a href="#" class="wh-text wh-dark filter-btn" data-filter="all" data-studio-key="filter_all">@normVal($pageContents['filter_all'] ?? null, 'TẤT CẢ DỰ ÁN')</a>
            </div>

            <div class="wh-line wh-space-between">
                <a href="#" class="wh-text wh-light filter-btn" data-filter="featured" data-studio-key="filter_featured">@normVal($pageContents['filter_featured'] ?? null, 'NỔI BẬT')</a>
                <a href="#" class="wh-text wh-light filter-btn" data-filter="identity" data-studio-key="filter_identity">@normVal($pageContents['filter_identity'] ?? null, 'NHẬN DIỆN')</a>
            </div>

            <div class="wh-line wh-right">
                <a href="#" class="wh-text wh-light filter-btn" data-filter="website" data-studio-key="filter_website">@normVal($pageContents['filter_website'] ?? null, 'WEBSITE')</a>
            </div>
        </div>
    </section>

    <section class="work-list-section">
        <div class="work-list-header">
            <a href="#" class="view-list-btn" id="viewToggleBtn">
                <i class='bx bx-menu' id="viewToggleIcon"></i> <span id="viewToggleText">DANH SÁCH</span>
            </a>
        </div>

        <div class="work-grid">
            @forelse($projects as $p)
            <a href="{{ route('work.detail', $p->slug) }}" class="project-card" data-category="{{ strtolower($p->getCategory()) }}">
                <div class="project-img-wrap">
                    <img src="{{ !empty($p->thumbnail) ? (str_starts_with($p->thumbnail, 'http') ? $p->thumbnail : asset($p->thumbnail)) : asset('assets/images/project1.png') }}" alt="{{ $p->getTitle() }}" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">{{ $p->getTitle() }}</h3>
                    <p class="project-category">{{ $p->getCategory() }}</p>
                </div>
            </a>
            @empty
            <div class="col-12 py-5 text-center text-muted">Chưa có dự án nào được cập nhật.</div>
            @endforelse
        </div>

        <div class="load-more-wrap">
            <a href="#" class="load-more-btn" id="loadMoreBtn" style="display: none;">XEM THÊM</a>
        </div>
    </section>

    @endsection

@push('scripts')
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const projects = document.querySelectorAll('.project-card');
            const loadMoreBtn = document.getElementById('loadMoreBtn');

            let currentItems = 6;
            let currentFilter = 'all';

            function updateProjects() {
                let visibleCount = 0;
                let matchingCount = 0;

                projects.forEach(project => {
                    const category = project.getAttribute('data-category') || '';
                    const matchesFilter = currentFilter === 'all' || category.includes(currentFilter);

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
                    currentItems = 6;
                    updateProjects();
                });
            });

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentItems += 6;
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
                        viewToggleText.textContent = 'LƯỚI';
                        viewToggleIcon.className = 'bx bx-grid-alt';
                    } else {
                        viewToggleText.textContent = 'DANH SÁCH';
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
