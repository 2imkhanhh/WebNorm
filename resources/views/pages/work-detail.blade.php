@extends('layouts.app')

@section('title', ($project ? $project->getTitle() : 'Chi tiết dự án') . ' - NÔRM Creative')
@section('body_class', 'work-detail-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/work-detail.css') }}">
@endpush

@section('content')
<section class="project-hero-section">
    <div class="hero-image-wrap">
        <img src="{{ $project ? $project->getHeroImage() : asset('assets/images/gotrail.jpg') }}" alt="{{ $project ? $project->getTitle() : 'Gotrail Project' }}" class="hero-image">
    </div>
</section>

<section class="project-overview-section">
    <div class="container-fluid norm-container">
        <div class="row align-items-start overview-content-row">
            <div class="col-12 col-md-3">
                <span class="label-side">{{ $project ? $project->getOverviewTitle(app()->getLocale()) : '• Tổng quan về dự án' }}</span>
                @if($project && $project->categories && $project->categories->isNotEmpty())
                    <div class="mt-3 d-none d-md-block">
                        <span class="text-muted small d-block mb-1" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;">DANH MỤC</span>
                        <span class="fw-semibold text-dark">{{ $project->getCategory(app()->getLocale()) }}</span>
                    </div>
                @endif
                @if($project && $project->year)
                    <div class="mt-3 d-none d-md-block">
                        <span class="text-muted small d-block mb-1" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;">NĂM THỰC HIỆN</span>
                        <span class="fw-semibold text-dark">{{ $project->year }}</span>
                    </div>
                @endif
                @if($project && $project->client)
                    <div class="mt-3 d-none d-md-block">
                        <span class="text-muted small d-block mb-1" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;">KHÁCH HÀNG</span>
                        <span class="fw-semibold text-dark">{{ $project->client }}</span>
                    </div>
                @endif
            </div>
            <div class="col-12 col-md-9 col-lg-9">
                <div class="overview-text-block">
                    <p class="overview-text" style="white-space: pre-line;">{{ $project ? $project->getOverviewText(app()->getLocale()) : 'NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.' }}</p>
                </div>
                @php
                    $overviewImg = $project ? $project->getOverviewImage() : asset('assets/images/mockup.png');
                @endphp
                @if(!empty($overviewImg))
                <div class="overview-image-wrapper">
                    <img src="{{ $overviewImg }}" alt="{{ $project ? $project->getTitle() : 'Project Overview Mockups' }}" class="overview-img">
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="project-gallery-section">
    <div class="container-fluid norm-container">
        <div class="row g-4 gallery-row">
            @php
                $gallery = $project ? $project->getGallery() : [];
            @endphp
            @forelse($gallery as $idx => $imgUrl)
                @php
                    $isFull = ($idx % 5 === 2);
                @endphp
                <div class="{{ $isFull ? 'col-12' : 'col-12 col-md-6' }}">
                    <img src="{{ $imgUrl }}" alt="{{ $project ? $project->getTitle() : 'Gallery image' }} {{ $idx + 1 }}" class="gallery-img">
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">Chưa có ảnh trong bộ sưu tập.</div>
            @endforelse
        </div>
    </div>
</section>

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

@php
    $nextProject = ($otherProjects && $otherProjects->isNotEmpty()) ? $otherProjects->first() : null;
@endphp
@if($nextProject)
<section class="explore-section">
    <div class="container-fluid norm-container">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h2 class="explore-title">KHÁM PHÁ TIẾP?</h2>
                <div class="mt-2 text-muted">{{ $nextProject->getTitle() }} • {{ $nextProject->getCategory() }}</div>
            </div>
            <div class="col-12 col-md-6 mt-4 mt-md-0">
                <div class="next-project-wrapper">
                    <img src="{{ !empty($nextProject->thumbnail) ? (str_starts_with($nextProject->thumbnail, 'http') ? $nextProject->thumbnail : asset($nextProject->thumbnail)) : asset('assets/images/work3.jpg') }}" alt="{{ $nextProject->getTitle() }}" class="next-project-img">
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
