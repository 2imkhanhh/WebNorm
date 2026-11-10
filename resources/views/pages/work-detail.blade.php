@extends('layouts.app')

@php
    $locale = request('lang', request('locale', session('locale', app()->getLocale() ?: 'vi')));
    if ($locale !== 'en') {
        $locale = 'vi';
    }
@endphp

@section('title', ($project ? $project->getTitle('vi') : 'Chi tiết dự án') . ' | Brand Design Agency')
@section('body_class', 'work-detail-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/work-detail.css') }}">
@endpush

@section('content')
    <!-- 1. Intro Section (Slogan, Description, Meta: Khách hàng, Dịch vụ, Thời gian) -->
    <section class="project-intro-section">
        <div class="container-fluid norm-container">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1 class="intro-slogan">
                        {{ $project ? ($project->getTitle('vi') ?: $project->getSlogan('vi')) : 'Chắc ở đây nó sẽ là 1 câu quote hay slogan gì đó của thương hiệu em ạ' }}
                    </h1>
                </div>

                <div class="w-100 d-none d-md-block"></div>

                <div class="col-12 col-md-6 offset-md-6">
                    <div class="intro-right-content">
                        <p class="intro-desc">
                            {{ $project && !empty($project->getDescription('vi')) ? $project->getDescription('vi') : ($project && !empty($project->getDescription('en')) ? $project->getDescription('en') : 'Thiết kế bộ nhận diện thương hiệu số toàn diện.') }}
                        </p>

                        <div class="intro-meta">
                            <div class="meta-item">
                                <span class="meta-label">Khách hàng</span>
                                <span class="meta-value">{{ ($project && !empty($project->client)) ? $project->client : 'Tiên Phong Co Ltd' }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Dịch vụ</span>
                                <span class="meta-value">{!! ($project && !empty($project->getCategory('vi'))) ? nl2br(e($project->getCategory('vi'))) : 'Branding Identity /<br>Web & App Design' !!}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Thời gian</span>
                                <span class="meta-value">{{ ($project && !empty($project->year)) ? $project->year : 'Tháng 1/2025' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Hero Banner Section -->
    <section class="project-hero-section">
        <div class="hero-image-wrap">
            <img src="{{ $project ? $project->getHeroImage() : asset('assets/images/gotrail.jpg') }}" alt="{{ $project ? $project->getTitle('vi') : 'Gotrail Project' }}" class="hero-image">
        </div>
    </section>

    <!-- 3. Overview Section (Label-side + Overview Text paragraphs + Mockup image) -->
    <section class="project-overview-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-start overview-content-row">
                <div class="col-12 col-md-3">
                    <span class="label-side">{{ $project ? $project->getOverviewTitle('vi') : '• Tổng quan về dự án' }}</span>
                </div>
                <div class="col-12 col-md-9 col-lg-9">
                    <div class="overview-text-block">
                        @php
                            $overviewText = $project ? $project->getOverviewText('vi') : null;
                        @endphp
                        @if($overviewText)
                            @foreach(explode("\n\n", str_replace("\r\n", "\n", $overviewText)) as $para)
                                @if(trim($para))
                                    <p class="overview-text" style="white-space: pre-line;">{{ trim($para) }}</p>
                                @endif
                            @endforeach
                        @else
                            <p class="overview-text">
                                NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.
                            </p>
                        @endif
                    </div>
                    @php
                        $overviewImg = $project ? $project->getOverviewImage() : asset('assets/images/mockup.png');
                    @endphp
                    @if(!empty($overviewImg))
                    <div class="overview-image-wrapper">
                        <img src="{{ $overviewImg }}" alt="{{ $project ? $project->getTitle('vi') : 'Project Overview Mockups' }}" class="overview-img">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Gallery Section -->
    <section class="project-gallery-section">
        <div class="container-fluid norm-container">
            <div class="row g-4 gallery-row">
                @php
                    $gallery = $project ? $project->getGallery() : [];
                @endphp
                @forelse($gallery as $idx => $imgUrl)
                    @php
                        // Pattern from raw template: items 0, 1 are col-md-6; item 2 is col-12; items 3, 4 are col-md-6; items 5, 6 are col-md-6
                        $isFull = ($idx % 5 === 2);
                    @endphp
                    <div class="{{ $isFull ? 'col-12' : 'col-12 col-md-6' }}">
                        <img src="{{ $imgUrl }}" alt="{{ $project ? $project->getTitle('vi') : 'Gallery image' }} {{ $idx + 1 }}" class="gallery-img">
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">Chưa có ảnh trong bộ sưu tập.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 5. Credits Section -->
    @php
        $credits = $project ? $project->getCredits() : [];
    @endphp
    @if(!empty($credits))
    <section class="project-credits-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-start overview-content-row">
                <div class="col-12 col-md-2 offset-md-5">
                    <span class="label-side text-nowrap">• Đội ngũ thực hiện</span>
                </div>
                <div class="col-12 col-md-5 col-lg-5">
                    <div class="credits-list">
                        @foreach($credits as $credit)
                            @if(!empty($credit['role']) || !empty($credit['name']))
                            <div class="credit-item">
                                <span class="credit-role">{{ $credit['role'] ?? '' }}</span>
                                <span class="credit-name">{{ $credit['name'] ?? '' }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <hr class="credit-divider" style="margin-top: 150px;">
        </div>
    </section>
    @endif

    <!-- 6. Explore Next Project Section -->
    @php
        $nextProject = ($otherProjects && $otherProjects->isNotEmpty()) ? $otherProjects->first() : null;
    @endphp
    @if($nextProject)
    <section class="explore-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h2 class="explore-title">KHÁM PHÁ TIẾP?</h2>
                </div>
                <div class="col-12 col-md-6 mt-4 mt-md-0">
                    <div class="next-project-wrapper">
                        <img src="{{ !empty($nextProject->thumbnail) ? (str_starts_with($nextProject->thumbnail, 'http') ? $nextProject->thumbnail : asset($nextProject->thumbnail)) : asset('assets/images/work3.jpg') }}" alt="{{ $nextProject->getTitle('vi') }}" class="next-project-img">

                        <a href="{{ route('work.detail', $nextProject->slug) }}" class="btn-next-project">
                            XEM DỰ ÁN <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.querySelector('.next-project-wrapper');
        const cursorBtn = document.querySelector('.btn-next-project');

        if (!wrapper || !cursorBtn) return;

        wrapper.addEventListener('mousemove', (e) => {
            if (window.innerWidth > 1024) { 
                const rect = wrapper.getBoundingClientRect();
                const x = e.clientX - rect.left - cursorBtn.offsetWidth / 2;
                const y = e.clientY - rect.top - cursorBtn.offsetHeight / 2;
                cursorBtn.style.left = x + 'px';
                cursorBtn.style.top = y + 'px';
            }
        });

        wrapper.addEventListener('mouseleave', () => {
            if (window.innerWidth > 1024) { 
                cursorBtn.style.left = '50%';
                cursorBtn.style.top = '50%';
            }
        });

        wrapper.addEventListener('click', function (e) {
            if (window.innerWidth <= 1024) { 
                if (e.target.closest('.btn-next-project')) return;
                this.classList.toggle('show-button');
            }
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 1024) { 
                if (!wrapper.contains(e.target)) {
                    wrapper.classList.remove('show-button');
                }
            }
        });
    });
</script>
@endpush
