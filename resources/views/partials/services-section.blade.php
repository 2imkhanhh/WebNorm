    <section class="services-intro-section">
        <div class="container-fluid {{ $introContainerClass ?? 'norm-container' }}">
            <div class="row services-intro-row">
                <div class="col-12 col-lg-2 mb-4 mb-lg-0">
                    <span class="si-label">• <span data-studio-key="services_label">{{ ltrim(\App\Helpers\NormHelper::val($pageContents['services_label'] ?? null, 'Dịch vụ'), '• ') }}</span></span>
                </div>

                <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                    <h2 class="si-statement reveal-text-multi" data-studio-key="services_statement">
                        @normVal($pageContents['services_statement'] ?? null, 'Chúng tôi giúp doanh nghiệp xây dựng thương hiệu rõ ràng, nhất quán để truyền tải giá trị khác biệt và tăng trưởng.')
                    </h2>
                </div>

                <div class="col-12 col-lg-3 d-flex justify-content-lg-end">
                    <p class="si-desc" data-studio-key="services_desc">
                        @normVal($pageContents['services_desc'] ?? null, 'Từ chiến lược đến trải nghiệm, chúng tôi định hình thương hiệu rõ nét.')
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="services-detail-section">
        <div class="container-fluid px-4">
            @foreach($services as $svc)
            @php
                $numStr = str_pad(rtrim($svc->number, '/'), 2, '0', STR_PAD_LEFT);
                $locale = app()->getLocale();
                $svcTitle = $svc->getTitle($locale);
                $listItems = $svc->getListItems($locale);
                $iconUrl = !empty($svc->icon)
                    ? (str_starts_with($svc->icon, 'http') ? $svc->icon : asset(ltrim($svc->icon, '/')))
                    : asset('assets/images/project1.png');
            @endphp
            <div class="service-item" id="service-{{ $numStr }}">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-block"></div>
                    <div class="col-12 col-lg-10">
                        <hr class="service-divider">
                        <div class="service-content-wrapper">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-7">
                                    <div class="d-flex align-items-end service-header-row">
                                        <div class="service-number-box">
                                            <span class="service-number">{{ $numStr }}/</span>
                                        </div>
                                        <h3 class="service-title">{{ $svcTitle }}</h3>
                                    </div>
                                    <div class="d-flex service-body-row">
                                        <div class="service-number-box"></div>
                                        <div class="service-text-box">
                                            <ul class="service-list">
                                                @foreach($listItems as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 mt-4 mt-md-0">
                                    <div class="service-image-viewport">
                                        <img src="{{ $iconUrl }}" alt="{{ $svcTitle }}" class="service-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

