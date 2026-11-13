<footer class="footer-section" id="footerSection">
    <div class="footer-sticky footer-cursor-zone" id="footerCursorZone">

        <video class="footer-bg-video" autoplay loop muted playsinline>
            <source src="{{ asset('assets/images/video_norm_ios.mov') }}" type="video/quicktime">
            <source src="{{ asset('assets/images/video_norm.webm') }}" type="video/webm">
            {{ __('site.video_unsupported') }}
        </video>
        <div class="footer-video-overlay"></div>
        <div class="container-fluid px-4 px-md-5 w-100" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="footer-text-wrap">
                        <div class="footer-line">
                            <span class="text-old" data-studio-key="footer_line1_old">@normSetting('footer_line1_old', 'TỪ NHỮNG')</span>
                            <span class="text-new" data-studio-key="footer_line1_new">@normSetting('footer_line1_new', 'ĐẾN MỘT')</span>
                        </div>
                        <div class="footer-line">
                            <span class="text-old" data-studio-key="footer_line2_old">@normSetting('footer_line2_old', 'Ý TƯỞNG')</span>
                            <span class="text-new" data-studio-key="footer_line2_new">@normSetting('footer_line2_new', 'THƯƠNG HIỆU')</span>
                        </div>
                        <div class="footer-line">
                            <span class="text-old" data-studio-key="footer_line3_old">@normSetting('footer_line3_old', 'BAN ĐẦU')</span>
                            <span class="text-new" data-studio-key="footer_line3_new">@normSetting('footer_line3_new', 'ĐÁNG NHỚ')</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-start text-md-end mt-5 mt-md-0 footer-real-btn-col">
                    <a href="#" class="footer-btn nav-contact-trigger">
                        @normSetting('footer_btn_text', 'CÙNG BẮT ĐẦU') <i class='bx bx-right-arrow-alt'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="footer-cursor-btn nav-contact-trigger" id="footerCursorBtn">
    @normSetting('footer_btn_text', 'CÙNG BẮT ĐẦU') <i class='bx bx-right-arrow-alt'></i>
</a>

<section class="final-footer-section">
    <div class="container-fluid h-100">
        <div class="row h-100">

            <div class="col-12 col-md-6 d-flex flex-column justify-content-between final-left-col">
                <div class="contact-info">
                    <p data-studio-setting="hotline">@normSetting('hotline', '(+84)816 656718')</p>
                    <p data-studio-setting="email">@normSetting('email', 'info@norm.vn')</p>
                    <p data-studio-setting="address">{!! nl2br(e(\App\Helpers\NormHelper::setting('address', "No. 110 Nguyen Ngoc Nai street,\nPhuong Liet ward, Ha Noi city, Vietnam"))) !!}</p>
                </div>
                <div class="copyright">
                    <p data-studio-setting="copyright">@normSetting('copyright', '©2026 All Rights Reserved')</p>
                </div>
            </div>

            <div class="col-12 col-md-6 d-flex flex-column align-items-md-end final-right-col mt-5 mt-md-0">
                <div class="social-links-wrap">
                    <p class="label-side social-title">• {{ ltrim(\App\Helpers\NormHelper::setting('footer_social_title', 'Mạng xã hội'), '• ') }}</p>
                    <ul class="social-list">
                        <li><a href="{{ \App\Helpers\NormHelper::setting('social_behance', '#') }}" target="_blank">Behance</a></li>
                        <li><a href="{{ \App\Helpers\NormHelper::setting('social_linkedin', '#') }}" target="_blank">Linkedin</a></li>
                        <li><a href="{{ \App\Helpers\NormHelper::setting('social_instagram', '#') }}" target="_blank">Instagram</a></li>
                        <li><a href="{{ \App\Helpers\NormHelper::setting('social_facebook', '#') }}" target="_blank">Facebook</a></li>
                        <li><a href="{{ \App\Helpers\NormHelper::setting('social_dribbble', '#') }}" target="_blank">Dribbble</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="footer-dome">
            <img src="{{ asset('assets/images/footer-background.png') }}" alt="Footer Texture">
        </div>
    </div>
</section>
