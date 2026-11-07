@extends('layouts.app')

@section('title', 'Về chúng tôi')
@section('body_class', 'about-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-us.css') }}?v={{ filemtime(public_path('assets/css/about-us.css')) }}">
    <style>
        /* Đảm bảo Body là block để position: sticky hoạt động chuẩn trên Chrome/Edge */
        body.about-page {
            display: block !important;
            background: #FFFFFF !important;
            min-height: auto !important;
        }

        /* Section wrapper */
        .solution-section-wrapper {
            height: 150vh !important;
            margin-top: 5% !important;
            position: relative !important;
        }

        /* Sticky zone: Đặt overflow: visible và padding-top để tuyệt đối không cắt dấu hỏi trên chữ Ả */
        .solution-sticky-zone {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 20vh !important;
            width: 100% !important;
            z-index: 2 !important;
            overflow: visible !important;
            padding-top: 25px !important;
            padding-bottom: 25px !important;
        }

        .solution-sticky-zone .testimonial-title-container {
            overflow: visible !important;
            padding-top: 10px !important;
        }

        /* Font size & spacing chuẩn khớp 100% template tĩnh (Ảnh 2) */
        .solution-sticky-zone .testimonial-title {
            font-size: 8vw !important;
            line-height: 1 !important;
            margin-top: 20px !important;
            margin-bottom: 0 !important;
            letter-spacing: -0.02em !important;
            color: #111111 !important;
            text-transform: uppercase !important;
            font-weight: 500 !important;
        }

        /* Dòng 1: GIẢI PHÁP - Khớp vị trí padding-left: 12vw và margin-left: 2% của file tĩnh */
        .solution-sticky-zone .solution-text-1 {
            margin-left: 2% !important;
            padding-left: 12vw !important;
            margin-top: 20px !important;
            text-align: left !important;
            overflow: visible !important;
        }

        /* Dòng 2: THIẾT KẾ CHO - Canh phải trọn chiều rộng block khớp Ảnh 2 */
        .solution-sticky-zone .solution-right-group {
            width: 100% !important;
            text-align: right !important;
            padding-right: 13% !important;
            margin: 0 !important;
        }
        .solution-sticky-zone .solution-right-group .testimonial-title {
            display: block !important;
            width: 100% !important;
            text-align: right !important;
            margin-top: 20px !important;
        }

        /* Dòng 3: THƯƠNG HIỆU / DOANH NGHIỆP - Canh phải khớp lề dòng 2 */
        .solution-sticky-zone .brand-line {
            position: relative !important;
            overflow: hidden !important;
            height: 1.2em !important;
            line-height: 1.2em !important;
            display: block !important;
            width: fit-content !important;
            margin-left: auto !important;
            margin-right: 13% !important;
            padding-right: 1em !important;
            text-align: left !important;
            margin-top: 20px !important;
            margin-bottom: 0 !important;
        }

        .solution-sticky-zone .brand-line .text-old {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            white-space: nowrap !important;
            height: 100% !important;
            color: #BDBDBD !important;
        }

        .solution-sticky-zone .brand-line .text-new {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            white-space: nowrap !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            width: 100% !important;
            height: 100% !important;
            color: #BDBDBD !important;
            background-color: #ffffff !important;
            -webkit-clip-path: inset(100% 0 0 0);
            clip-path: inset(100% 0 0 0);
        }

        /* Section intro bên dưới: Nằm dưới và che phủ khi cuộn qua */
        .about-intro-section {
            position: relative !important;
            z-index: 3 !important;
            background-color: #ffffff !important;
        }

        /* Responsive Tablet (768px - 1024px) */
        @media (max-width: 1024px) and (min-width: 768px) {
            .solution-section-wrapper {
                height: 140vh !important;
                margin-top: 15vw !important;
            }
            .solution-sticky-zone {
                top: 20vh !important;
                padding-top: 20px !important;
            }
            .solution-sticky-zone .testimonial-title {
                font-size: 9vw !important;
                line-height: 1 !important;
            }
            .solution-sticky-zone .solution-text-1 {
                padding-left: 6vw !important;
                margin-left: 2% !important;
            }
            .solution-sticky-zone .solution-right-group {
                padding-right: 4% !important;
                margin-top: 1.5vw !important;
            }
            .solution-sticky-zone .solution-right-group .testimonial-title {
                display: block !important;
                width: 100% !important;
                text-align: right !important;
                margin-top: 1.5vw !important;
            }
            .solution-sticky-zone .brand-line {
                margin-right: 4% !important;
                padding-right: 0.8em !important;
                margin-top: 1.5vw !important;
            }
        }

        /* Responsive Mobile (<= 767px) */
        @media (max-width: 767px) {
            .solution-section-wrapper {
                margin-top: 20vw !important;
                height: 120vh !important;
            }
            .solution-sticky-zone {
                top: 15vh !important;
                padding-top: 15px !important;
            }
            .solution-sticky-zone .testimonial-title {
                font-size: 11vw !important;
                line-height: 1.1 !important;
            }
            .solution-sticky-zone .solution-text-1 {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
            .solution-sticky-zone .solution-right-group {
                padding-right: 3% !important;
                margin-top: 4vw !important;
            }
            .solution-sticky-zone .solution-right-group .testimonial-title {
                display: block !important;
                width: 100% !important;
                text-align: right !important;
                margin-top: 4vw !important;
            }
            .solution-sticky-zone .brand-line {
                padding-right: 0.8em !important;
                margin-top: 4vw !important;
                margin-right: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <section class="about-hero-gallery">
        <div class="gallery-track" id="galleryTrack">
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery1.jpg') }}" alt="Gallery 1"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery2.jpg') }}" alt="Gallery 2"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery3.jpg') }}" alt="Gallery 3"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery4.jpg') }}" alt="Gallery 4"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery5.jpg') }}" alt="Gallery 5"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery6.jpg') }}" alt="Gallery 6"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery7.jpg') }}" alt="Gallery 7"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery8.jpg') }}" alt="Gallery 8"></div>
            <div class="gallery-item"><img src="{{ asset('assets/images/gallery9.jpg') }}" alt="Gallery 9"></div>
        </div>
    </section>

    <section class="solution-section-wrapper" id="solutionSection">
        <div class="solution-sticky-zone">
            <div class="container-fluid px-4 px-md-5">
                <div class="row">
                    <div class="col-12 testimonial-title-container">

                        <h2 class="testimonial-title text-left scroll-slide-left solution-text-1" data-studio-key="about_heading_1">@normVal($pageContents['about_heading_1'] ?? null, 'GIẢI PHÁP')</h2>

                        <div class="solution-right-group">
                            <h2 class="testimonial-title scroll-slide-right" data-studio-key="about_heading_2">@normVal($pageContents['about_heading_2'] ?? null, 'THIẾT KẾ CHO')</h2>
                        </div>

                        <h2 class="testimonial-title brand-line scroll-slide-up">
                            <span class="text-old">THƯƠNG HIỆU</span>
                            <span class="text-new">DOANH NGHIỆP</span>
                        </h2>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-intro-section">
        <div class="container-fluid px-4 px-md-5">
            <div class="row align-items-start about-content-row">

                <div class="col-md-3">
                    <span class="label-side">• Về chúng tôi</span>
                </div>

                <div class="col-lg-7 col-md-9 custom-nudge">

                    <div class="row text-paragraphs-row">
                        <div class="col-md-6 about-text-left">
                            <p class="desc-text m-0">
                                NÔRM Creative là agency sáng tạo thế hệ mới, chuyên sâu trong lĩnh vực thiết kế nhận
                                diện thương hiệu, tư vấn chiến lược bài bản, toàn diện và các giải pháp hình ảnh chuyên
                                nghiệp cho doanh nghiệp.
                            </p>
                        </div>
                        <div class="col-md-6 about-text-right mt-4 mt-md-0">
                            <p class="desc-text m-0">
                                NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn
                                hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính
                                thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa
                                tính cách thương hiệu một cách rõ nét.
                            </p>
                        </div>
                    </div>

                    <div class="team-image-wrapper">
                        <img src="{{ asset('assets/images/team-photo.jpg') }}" alt="Team" class="team-img">
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="workflow-section" id="workflowSection">
        <div class="workflow-sticky-zone">
            <div class="container-fluid px-4 px-md-5">
                <div class="row align-items-start workflow-content-row">

                    <div class="col-12 col-lg-3" style="position: relative; z-index: 1;">
                        <span class="label-side d-block sync-gap">• Quy trình làm việc</span>
                        <p class="workflow-slogan m-0">
                            We create innovative and thought-provoking environments that foster deeper human
                            experiences.
                        </p>
                    </div>

                    <div class="col-12 col-lg-7 custom-nudge workflow-steps-container"
                        style="position: relative; z-index: 10;">

                        <div class="row flex-nowrap workflow-slider-track" id="workflowSliderTrack">
                            <div class="col-12 workflow-step is-active">
                                <div class="step-top">
                                    <span class="step-number d-block sync-gap">01/</span>
                                    <h3 class="step-title m-0">Trao đổi & tìm hiểu</h3>
                                </div>
                                <p class="step-desc">
                                    Tổ chức buổi trao đổi trực tuyến để thu thập thông tin chuyên sâu về thương hiệu,
                                    xác định tầm nhìn và kỳ vọng chung.
                                </p>
                                <div class="step-image-wrapper">
                                    <img src="{{ asset('assets/images/gallery1.jpg') }}" alt="Bước 1" class="img-fluid">
                                </div>
                            </div>

                            <div class="col-12 workflow-step">
                                <div class="step-top">
                                    <span class="step-number d-block sync-gap">02/</span>
                                    <h3 class="step-title m-0">Khởi tạo & ký kết</h3>
                                </div>
                                <p class="step-desc">
                                    Bắt khách phải ký
                                </p>
                                <div class="step-image-wrapper">
                                    <img src="{{ asset('assets/images/gallery2.jpg') }}" alt="Bước 2" class="img-fluid">
                                </div>
                            </div>

                            <div class="col-12 workflow-step">
                                <div class="step-top">
                                    <span class="step-number d-block sync-gap">03/</span>
                                    <h3 class="step-title m-0">Thiết kế & hoàn thiện</h3>
                                </div>
                                <p class="step-desc">
                                    Triển khai thiết kế dựa trên các định hướng đã thống nhất và hoàn thiện sản phẩm
                                    cuối cùng.
                                </p>
                                <div class="step-image-wrapper">
                                    <img src="{{ asset('assets/images/gallery3.jpg') }}" alt="Bước 3" class="img-fluid">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="services-intro-section">
        <div class="container-fluid px-4">
            <div class="row services-intro-row">

                <div class="col-12 col-lg-2 mb-4 mb-lg-0">
                    <span class="si-label">• Dịch vụ</span>
                </div>

                <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                    <h2 class="si-statement reveal-text-multi">
                        Chúng tôi giúp doanh nghiệp xây dựng thương hiệu rõ ràng, nhất quán để truyền tải giá trị khác
                        biệt và tăng trưởng.
                    </h2>
                </div>

                <div class="col-12 col-lg-3 d-flex justify-content-lg-end">
                    <p class="si-desc">
                        Từ chiến lược đến trải nghiệm, chúng tôi định hình thương hiệu rõ nét.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section class="services-detail-section">
        <div class="container-fluid px-4">

            <div class="service-item" id="service-01">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-block"></div>
                    <div class="col-12 col-lg-10">
                        <hr class="service-divider">
                        <div class="service-content-wrapper">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-7">
                                    <div class="d-flex align-items-end service-header-row">
                                        <div class="service-number-box">
                                            <span class="service-number">01/</span>
                                        </div>
                                        <h3 class="service-title">Thiết kế từ chiến lược</h3>
                                    </div>
                                    <div class="d-flex service-body-row">
                                        <div class="service-number-box"></div>
                                        <div class="service-text-box">
                                            <ul class="service-list">
                                                <li>Định vị thương hiệu</li>
                                                <li>Câu chuyện thương hiệu</li>
                                                <li>Đặt tên thương hiệu</li>
                                                <li>Bản sắc thương hiệu</li>
                                                <li>Thông điệp thương hiệu</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 mt-4 mt-md-0">
                                    <div class="service-image-viewport">
                                        <img src="{{ asset('assets/images/project1.png') }}" alt="Dịch vụ 1"
                                            class="service-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-item" id="service-02">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-block"></div>
                    <div class="col-12 col-lg-10">
                        <hr class="service-divider">
                        <div class="service-content-wrapper">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-7">
                                    <div class="d-flex align-items-end service-header-row">
                                        <div class="service-number-box">
                                            <span class="service-number">02/</span>
                                        </div>
                                        <h3 class="service-title">Thiết kế nhận diện thương hiệu</h3>
                                    </div>
                                    <div class="d-flex service-body-row">
                                        <div class="service-number-box"></div>
                                        <div class="service-text-box">
                                            <ul class="service-list">
                                                <li>Thiết kế logo</li>
                                                <li>Hệ thống nhận diện thương hiệu</li>
                                                <li>Bộ nhận diện ứng dụng</li>
                                                <li>Cẩm nang thương hiệu</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 mt-4 mt-md-0">
                                    <div class="service-image-viewport">
                                        <img src="{{ asset('assets/images/project2.png') }}" alt="Dịch vụ 2"
                                            class="service-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-item" id="service-03">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-block"></div>
                    <div class="col-12 col-lg-10">
                        <hr class="service-divider">
                        <div class="service-content-wrapper">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-7">
                                    <div class="d-flex align-items-end service-header-row">
                                        <div class="service-number-box">
                                            <span class="service-number">03/</span>
                                        </div>
                                        <h3 class="service-title">Thiết kế không gian số</h3>
                                    </div>
                                    <div class="d-flex service-body-row">
                                        <div class="service-number-box"></div>
                                        <div class="service-text-box">
                                            <ul class="service-list">
                                                <li>Thiết kế UI/UX</li>
                                                <li>Thiết kế Website</li>
                                                <li>Ứng dụng di động</li>
                                                <li>Tối ưu trải nghiệm</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 mt-4 mt-md-0">
                                    <div class="service-image-viewport">
                                        <img src="{{ asset('assets/images/project3.png') }}" alt="Dịch vụ 3"
                                            class="service-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-item" id="service-04">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-block"></div>
                    <div class="col-12 col-lg-10">
                        <hr class="service-divider">
                        <div class="service-content-wrapper">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-7">
                                    <div class="d-flex align-items-end service-header-row">
                                        <div class="service-number-box">
                                            <span class="service-number">04/</span>
                                        </div>
                                        <h3 class="service-title">Sản xuất nội dung</h3>
                                    </div>
                                    <div class="d-flex service-body-row">
                                        <div class="service-number-box"></div>
                                        <div class="service-text-box">
                                            <ul class="service-list">
                                                <li>Chụp ảnh doanh nghiệp</li>
                                                <li>Sản xuất Video quảng cáo</li>
                                                <li>Nội dung truyền thông</li>
                                                <li>Quản trị Fanpage</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 mt-4 mt-md-0">
                                    <div class="service-image-viewport">
                                        <img src="{{ asset('assets/images/project4.png') }}" alt="Dịch vụ 4"
                                            class="service-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="members-section">
        <div class="container-fluid px-4 px-md-5">
            <div class="row">
                <div class="col-12 testimonial-title-container">
                    <h2 class="testimonial-title text-left scroll-slide-left">GẶP GỠ</h2>
                    <h2 class="testimonial-title text-right scroll-slide-right">THÀNH VIÊN</h2>
                </div>
            </div>

            <div class="members-grid-container mt-5">

                <div class="row member-row">
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team1.png') }}" alt="Manh Tho">
                            <h4 class="member-name">Mạnh Thọ</h4>
                            <p class="member-role">CEO - CO founder</p>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-md-block"></div>
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team2.png') }}" alt="Loan Ng">
                            <h4 class="member-name">Loan Ng</h4>
                            <p class="member-role">Marketing Executive</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team3.png') }}" alt="Thanh Vien 3">
                            <h4 class="member-name">Yen Nguyen</h4>
                            <p class="member-role">UI/UX designer</p>
                        </div>
                    </div>
                </div>

                <div class="row member-row">
                    <div class="col-md-3 d-none d-md-block"></div>
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team4.png') }}" alt="Duw">
                            <h4 class="member-name">Duw</h4>
                            <p class="member-role">Junior designer</p>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-md-block"></div>
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team3.png') }}" alt="Yen Nguyen">
                            <h4 class="member-name">Yen Nguyen</h4>
                            <p class="member-role">UI/UX designer</p>
                        </div>
                    </div>
                </div>

                <div class="row member-row">
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team1.png') }}" alt="Member 5">
                            <h4 class="member-name">Mạnh Thọ</h4>
                            <p class="member-role">CEO - CO founder</p>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-md-block"></div>
                    <div class="col-6 col-md-3 member-item">
                        <div class="member-card">
                            <img src="{{ asset('assets/images/team2.png') }}" alt="Member 6">
                            <h4 class="member-name">Loan Ng</h4>
                            <p class="member-role">Marketing Executive</p>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-md-block"></div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('galleryTrack');
            if (track) {
                const originalItems = Array.from(track.children);
                const totalItems = originalItems.length;
                originalItems.forEach((item, index) => item.style.setProperty('--i', index + 1));
                originalItems.forEach((item, index) => {
                    const clone = item.cloneNode(true);
                    clone.style.setProperty('--i', totalItems + index + 1);
                    clone.setAttribute('aria-hidden', 'true');
                    track.appendChild(clone);
                });
            }

            const galleryItems = document.querySelectorAll('.gallery-item');

            galleryItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (item.classList.contains('is-active')) {
                        item.classList.remove('is-active');
                    } else {
                        galleryItems.forEach(el => el.classList.remove('is-active'));
                        item.classList.add('is-active');
                    }
                });
            });

            document.addEventListener('click', () => {
                galleryItems.forEach(item => item.classList.remove('is-active'));
            });

            let hasScrolled = false;
            const animatedTexts = document.querySelectorAll(
                '.scroll-slide-left, .scroll-slide-right, .scroll-slide-up');

            function initTextObserver() {
                if (animatedTexts.length === 0) return;
                const textObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                        } else {
                            entry.target.classList.remove('is-visible');
                        }
                    });
                }, {
                    threshold: 0.2
                });
                animatedTexts.forEach(text => textObserver.observe(text));
            }

            window.addEventListener('scroll', () => {
                if (!hasScrolled) {
                    hasScrolled = true;
                    initTextObserver();
                }
            }, {
                once: false,
                passive: true
            });

            const solutionSection = document.getElementById('solutionSection');
            const brandLine = document.querySelector('.brand-line');

            if (solutionSection && brandLine) {
                const isMobile = window.innerWidth <= 765;
                const snapDelay = isMobile ? 200 : 150;

                let targetSolutionProgress = 0;
                let currentSolutionProgress = 0;
                let solutionSnapTimeout;
                let solutionLastScrollY = window.scrollY;

                const textNew = brandLine.querySelector('.text-new');
                if (textNew) {
                    textNew.style.webkitClipPath = 'inset(100% 0 0 0)';
                    textNew.style.clipPath = 'inset(100% 0 0 0)';
                }

                function renderSolutionSmooth() {
                    currentSolutionProgress += (targetSolutionProgress - currentSolutionProgress) * 0.05;

                    if (textNew) {
                        let progress = currentSolutionProgress;
                        if (progress < 0) progress = 0;
                        if (progress > 1) progress = 1;

                        const insetTop = (1 - progress) * 100;
                        textNew.style.webkitClipPath = `inset(${insetTop}% 0 0 0)`;
                        textNew.style.clipPath = `inset(${insetTop}% 0 0 0)`;
                    }
                    requestAnimationFrame(renderSolutionSmooth);
                }

                requestAnimationFrame(renderSolutionSmooth);

                window.addEventListener('scroll', () => {
                    const sectionTop = solutionSection.offsetTop;
                    const scrollY = window.scrollY;
                    const windowHeight = window.innerHeight;

                    const scrollDirection = scrollY > solutionLastScrollY ? 'down' : 'up';
                    solutionLastScrollY = scrollY;

                    let scrolledInside = scrollY - sectionTop;
                    if (scrolledInside < 0) scrolledInside = 0;

                    const maxScroll = solutionSection.offsetHeight - windowHeight;
                    if (scrolledInside > maxScroll) scrolledInside = maxScroll;

                    if (maxScroll > 0) {
                        targetSolutionProgress = scrolledInside / maxScroll;
                    }

                    clearTimeout(solutionSnapTimeout);

                    if (scrollY > sectionTop && scrollY < sectionTop + maxScroll) {
                        solutionSnapTimeout = setTimeout(() => {
                            let snapTargetY;
                            if (scrollDirection === 'down') {
                                snapTargetY = targetSolutionProgress >= 0.1 ? sectionTop +
                                    maxScroll : sectionTop;
                            } else {
                                snapTargetY = targetSolutionProgress <= 0.9 ? sectionTop :
                                    sectionTop + maxScroll;
                            }
                            if (Math.abs(scrollY - snapTargetY) > 5) {
                                if (typeof lenis !== 'undefined') {
                                    lenis.scrollTo(snapTargetY, {
                                        duration: 1.0
                                    });
                                } else {
                                    window.scrollTo({
                                        top: snapTargetY,
                                        behavior: 'smooth'
                                    });
                                }
                            }
                        }, snapDelay);
                    }
                }, {
                    passive: true
                });
            }

            const workflowSection = document.getElementById('workflowSection');
            const workflowSlogan = document.querySelector('.workflow-slogan');
            const workflowTrack = document.getElementById('workflowSliderTrack');

            if (workflowSection && workflowSlogan && workflowTrack) {
                const steps = workflowTrack.querySelectorAll('.workflow-step');

                let isDesktop = window.innerWidth > 1024;
                let targetWfProgress = 0;
                let currentWfProgress = 0;

                function calculateActiveWorkflowStep() {
                    if (isDesktop) return;

                    let activeItem = null;
                    let minDistance = Infinity;
                    const viewportCenter = window.innerHeight / 2;

                    steps.forEach(step => {
                        const rect = step.getBoundingClientRect();
                        const itemCenter = rect.top + rect.height / 2;
                        const distance = Math.abs(viewportCenter - itemCenter);

                        if (distance < minDistance) {
                            minDistance = distance;
                            activeItem = step;
                        }
                    });

                    steps.forEach(step => step.classList.remove('is-active'));
                    if (activeItem) activeItem.classList.add('is-active');
                }

                window.addEventListener('resize', () => {
                    isDesktop = window.innerWidth > 1024;
                    if (!isDesktop) {
                        workflowSlogan.style.transform = '';
                        workflowSlogan.style.opacity = '';
                        workflowTrack.style.transform = '';
                        calculateActiveWorkflowStep();
                    }
                }, {
                    passive: true
                });

                function renderWorkflowSmooth() {
                    if (isDesktop) {
                        currentWfProgress += (targetWfProgress - currentWfProgress) * 0.08;

                        const sloganMove = currentWfProgress * -200;
                        workflowSlogan.style.transform = `translateX(${sloganMove}%)`;
                        workflowSlogan.style.opacity = 1 - (currentWfProgress * 5);

                        const maxTrackScroll = workflowTrack.scrollWidth - (window.innerWidth * 0.25);
                        const trackMove = currentWfProgress * -maxTrackScroll;
                        workflowTrack.style.transform = `translateX(${trackMove}px)`;

                        if (steps.length > 0) {
                            let activeIndex = Math.round(currentWfProgress * (steps.length - 1));
                            steps.forEach((step, index) => {
                                step.classList.toggle('is-active', index === activeIndex);
                            });
                        }
                    }
                    requestAnimationFrame(renderWorkflowSmooth);
                }

                requestAnimationFrame(renderWorkflowSmooth);
                let isTicking = false;

                window.addEventListener('scroll', () => {
                    if (!isDesktop) {
                        calculateActiveWorkflowStep();
                        return;
                    }
                    if (!isTicking) {
                        window.requestAnimationFrame(() => {
                            const sectionTop = workflowSection.offsetTop;
                            const windowHeight = window.innerHeight;
                            const scrollY = window.scrollY;
                            const maxScroll = workflowSection.offsetHeight - windowHeight;
                            let scrolledInside = Math.max(0, Math.min(scrollY - sectionTop,
                                maxScroll));
                            if (maxScroll > 0) {
                                targetWfProgress = scrolledInside / maxScroll;
                            }
                            isTicking = false;
                        });
                        isTicking = true;
                    }
                }, {
                    passive: true
                });

                calculateActiveWorkflowStep();
            }
        });
    </script>
@endpush
