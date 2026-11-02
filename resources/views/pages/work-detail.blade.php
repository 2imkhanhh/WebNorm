@extends('layouts.app')

@section('title', 'Chi tiết dự án | Brand Design Agency')
@section('body_class', 'work-detail-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/work-detail.css') }}">
@endpush

@section('content')
<section class="project-hero-section">
        <div class="hero-image-wrap">
            <img src="{{ asset('assets/images/gotrail.jpg') }}" alt="Gotrail Project" class="hero-image">
        </div>
    </section>

    <section class="project-overview-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-start overview-content-row">
                <div class="col-12 col-md-3">
                    <span class="label-side">• Tổng quan về dự án</span>
                </div>
                <div class="col-12 col-md-9 col-lg-9">
                    <div class="overview-text-block">
                        <p class="overview-text">
                            NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá
                            làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương
                            hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách
                            thương hiệu một cách rõ nét.
                        </p>
                        <p class="overview-text">
                            NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá
                            làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương
                            hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách
                            thương hiệu một cách rõ nét.
                        </p>
                    </div>
                    <div class="overview-image-wrapper">
                        <img src="{{ asset('assets/images/mockup.png') }}" alt="Project Overview Mockups" class="overview-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="project-gallery-section">
        <div class="container-fluid norm-container">
            <div class="row g-4 gallery-row">

                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work1.jpg') }}" alt="Gotrail Bottle" class="gallery-img">
                </div>
                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work2.jpg') }}" alt="Gotrail App" class="gallery-img">
                </div>

                <div class="col-12">
                    <img src="{{ asset('assets/images/work3.jpg') }}" alt="Gotrail Billboard" class="gallery-img">
                </div>

                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work4.jpg') }}" alt="Gotrail Wall Ad" class="gallery-img">
                </div>
                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work5.jpg') }}" alt="Gotrail Bus Stop" class="gallery-img">
                </div>

                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work6.jpg') }}" alt="Gotrail Cap" class="gallery-img">
                </div>
                <div class="col-12 col-md-6">
                    <img src="{{ asset('assets/images/work7.jpg') }}" alt="Gotrail Sign" class="gallery-img">
                </div>

            </div>
        </div>
    </section>

    <section class="project-credits-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-start overview-content-row">

                <div class="col-12 col-md-2 offset-md-5">
                    <span class="label-side text-nowrap">• Đội ngũ thực hiện</span>
                </div>

                <div class="col-12 col-md-5 col-lg-5">
                    <div class="credits-list">
                        <div class="credit-item">
                            <span class="credit-role">Account Manager</span>
                            <span class="credit-name">Sebastian Degenhart</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">Project Manager</span>
                            <span class="credit-name">Tijana Ostojić</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">Design Lead</span>
                            <span class="credit-name">Amadeus Malmin</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">Copywriter</span>
                            <span class="credit-name">Paula Espinosa V.</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">UX Designer</span>
                            <span class="credit-name">Bartek Papież</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">UX Designer Support</span>
                            <span class="credit-name">Marco Rinke</span>
                        </div>
                        <div class="credit-item">
                            <span class="credit-role">Developer</span>
                            <span class="credit-name">Luis Henrique Bizarro</span>
                        </div>
                    </div>
                </div>

            </div>
            <hr class="credit-divider" style="margin-top: 150px;">
        </div>
    </section>

    <section class="explore-section">
        <div class="container-fluid norm-container">
            <div class="row align-items-center">

                <div class="col-12 col-md-6">
                    <h2 class="explore-title">KHÁM PHÁ TIẾP?</h2>
                </div>

                <div class="col-12 col-md-6 mt-4 mt-md-0">
                    <div class="next-project-wrapper">
                        <img src="{{ asset('assets/images/work3.jpg') }}" alt="Next Project" class="next-project-img">

                        <a href="#" class="btn-next-project">
                            XEM DỰ ÁN <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

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
