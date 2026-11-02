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
                <a href="#" class="wh-text wh-dark filter-btn" data-filter="all">TẤT CẢ DỰ ÁN</a>
            </div>

            <div class="wh-line wh-space-between">
                <a href="#" class="wh-text wh-light filter-btn" data-filter="featured">NỔI BẬT</a>
                <a href="#" class="wh-text wh-light filter-btn" data-filter="identity">NHẬN DIỆN</a>
            </div>

            <div class="wh-line wh-right">
                <a href="#" class="wh-text wh-light filter-btn" data-filter="website">WEBSITE</a>
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
            <a href="{{ route('work.detail') }}" class="project-card" data-category="identity">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project1.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nhận diện</h3>
                    <p class="project-category">Brand identity</p>
                </div>
            </a>

            <a href="{{ route('work.detail') }}" class="project-card" data-category="website">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project2.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Website</h3>
                    <p class="project-category">UI/UX Design</p>
                </div>
            </a>

            <a href="{{ route('work.detail') }}" class="project-card" data-category="featured">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project3.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nổi bật</h3>
                    <p class="project-category">Brand identity - UI/UX - Social Stategy</p>
                </div>
            </a>

            <a href="{{ route('work.detail') }}" class="project-card" data-category="featured">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project4.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nổi bật 2</h3>
                    <p class="project-category">Social Stategy</p>
                </div>
            </a>

            <a href="{{ route('work.detail') }}" class="project-card" data-category="website">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project4.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Website </h3>
                    <p class="project-category">Web Development</p>
                </div>
            </a>

            <a href="{{ route('work.detail') }}" class="project-card" data-category="identity">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project1.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nhận diện 2</h3>
                    <p class="project-category">Brand identity</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="website">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project2.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Website 2</h3>
                    <p class="project-category">UI/UX Design</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="featured">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project3.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nổi bật 3</h3>
                    <p class="project-category">Brand identity - UI/UX</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="identity">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project4.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nhận diện 3</h3>
                    <p class="project-category">Brand identity</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="website">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project1.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Website 3</h3>
                    <p class="project-category">UI/UX Design</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="featured">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project2.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nổi bật 4</h3>
                    <p class="project-category">Social Strategy</p>
                </div>
            </a>
            <a href="{{ route('work.detail') }}" class="project-card" data-category="identity">
                <div class="project-img-wrap">
                    <img src="{{ asset('assets/images/project3.png') }}" alt="Tiên Phong" class="project-img">
                </div>
                <div class="project-info">
                    <h3 class="project-title">Nhận diện 4</h3>
                    <p class="project-category">Brand identity</p>
                </div>
            </a>

        </div>

        <div class="load-more-wrap">
            <a href="#" class="load-more-btn" id="loadMoreBtn" style="display: none;">XEM THÊM</a>
        </div>
    </section>

    <footer class="footer-section" id="footerSection">
        <div class="footer-sticky footer-cursor-zone" id="footerCursorZone">

            <video class="footer-bg-video" autoplay loop muted playsinline>
                <source src="{{ asset('assets/images/video_norm_ios.mp4') }}" type='video/mp4; codecs="hvc1"'>
                <source src="{{ asset('assets/images/video_norm_ios.mov') }}" type="video/quicktime">
                <source src="{{ asset('assets/images/video_norm.webm') }}" type="video/webm">
                Trình duyệt của bạn không hỗ trợ thẻ video.
            </video>
            <div class="footer-video-overlay"></div>
            <div class="container-fluid px-4 px-md-5 w-100" style="position: relative; z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="footer-text-wrap">
                            <div class="footer-line">
                                <span class="text-old">TỪ NHỮNG</span>
                                <span class="text-new">ĐẾN MỘT</span>
                            </div>
                            <div class="footer-line">
                                <span class="text-old">Ý TƯỞNG</span>
                                <span class="text-new">THƯƠNG HIỆU</span>
                            </div>
                            <div class="footer-line">
                                <span class="text-old">BAN ĐẦU</span>
                                <span class="text-new">ĐÁNG NHỚ</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-start text-md-end mt-5 mt-md-0 footer-real-btn-col">
                        <a href="#" class="footer-btn">
                            CÙNG BẮT ĐẦU <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <a href="#" class="footer-cursor-btn" id="footerCursorBtn">
        CÙNG BẮT ĐẦU <i class='bx bx-right-arrow-alt'></i>
    </a>
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
