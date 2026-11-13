@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', ($project ? $project->getTitle($locale) : __('site.title_work_detail')) . ' | Brand Design Agency')
@section('description', $project && $project->getDescription($locale) ? \Illuminate\Support\Str::limit(strip_tags($project->getDescription($locale)), 160) : __('site.meta_description_default'))
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
                        {{ $project ? $project->getSlogan($locale) : '' }}
                    </h1>
                </div>

                <div class="w-100 d-none d-md-block"></div>

                <div class="col-12 col-md-6 offset-md-6">
                    <div class="intro-right-content">
                        <p class="intro-desc">
                            {{ $project && !empty($project->getDescription($locale)) ? $project->getDescription($locale) : __('site.work_default_desc') }}
                        </p>

                        <div class="intro-meta">
                            <div class="meta-item">
                                <span class="meta-label">{{ __('site.work_client_label') }}</span>
                                <span class="meta-value">{{ ($project && !empty($project->client)) ? $project->client : 'Tiên Phong Co Ltd' }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">{{ __('site.work_services_label') }}</span>
                                <span class="meta-value">{!! ($project && !empty($project->getCategory($locale))) ? nl2br(e($project->getCategory($locale))) : 'Branding Identity /<br>Web & App Design' !!}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">{{ __('site.work_year_label') }}</span>
                                <span class="meta-value">{{ ($project && !empty($project->year)) ? $project->year : '01/2025' }}</span>
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
            <img src="{{ $project ? $project->getHeroImage() : asset('assets/images/gotrail.jpg') }}" alt="{{ $project ? $project->getTitle($locale) : 'Gotrail Project' }}" class="hero-image">
        </div>
    </section>

    <!-- 3. Overview Section (Label-side + Overview Text paragraphs + Mockup image) -->
    <section class="project-overview-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-start overview-content-row">
                <div class="col-12 col-md-3">
                    <span class="label-side">{{ $project ? $project->getOverviewTitle($locale) : '• ' . __('site.work_overview_label') }}</span>
                </div>
                <div class="col-12 col-md-9 col-lg-9">
                    <div class="overview-text-block">
                        @php
                            $overviewText = $project ? $project->getOverviewText($locale) : null;
                        @endphp
                        @if($overviewText)
                            @foreach(explode("\n\n", str_replace("\r\n", "\n", $overviewText)) as $para)
                                @if(trim($para))
                                    <p class="overview-text" style="white-space: pre-line;">{{ trim($para) }}</p>
                                @endif
                            @endforeach
                        @else
                            <p class="overview-text">
                                {{ $project ? $project->getOverviewText($locale) : '' }}
                            </p>
                        @endif
                    </div>
                    @php
                        $overviewImg = $project ? $project->getOverviewImage() : asset('assets/images/mockup.png');
                    @endphp
                    @if(!empty($overviewImg))
                    <div class="overview-image-wrapper">
                        <img src="{{ $overviewImg }}" alt="{{ $project ? $project->getTitle($locale) : 'Project Overview Mockups' }}" class="overview-img">
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
                        <img src="{{ $imgUrl }}" alt="{{ $project ? $project->getTitle($locale) : 'Gallery image' }} {{ $idx + 1 }}" class="gallery-img">
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">{{ __('site.work_gallery_empty') }}</div>
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
                    <span class="label-side text-nowrap">{{ __('site.work_credits_label') }}</span>
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
                    <h2 class="explore-title">{{ __('site.work_explore_next') }}</h2>
                </div>
                <div class="col-12 col-md-6 mt-4 mt-md-0">
                    <div class="next-project-wrapper">
                        <img src="{{ !empty($nextProject->thumbnail) ? (str_starts_with($nextProject->thumbnail, 'http') ? $nextProject->thumbnail : asset($nextProject->thumbnail)) : asset('assets/images/work3.jpg') }}" alt="{{ $nextProject->getTitle($locale) }}" class="next-project-img">

                        <a href="{{ \App\Helpers\NormHelper::localizedRoute('work.detail', $nextProject->slug) }}" class="btn-next-project">
                            {{ __('site.work_view_project') }} <i class='bx bx-right-arrow-alt'></i>
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
