<div class="contact-overlay" id="contactOverlay"></div>
<div class="contact-sidebar" id="contactSidebar" data-lenis-prevent>
    <div class="contact-header">
        <span class="contact-close" id="contactClose">Close</span>
    </div>
    <div class="contact-content">
        <h2 class="contact-title">Chúng tôi có thể giúp bạn điều gì?</h2>
        <div class="contact-options group-1" id="contactServiceOptions">
            @if(isset($globalServices) && count($globalServices) > 0)
                @foreach($globalServices as $svc)
                    <button type="button" class="contact-pill" data-value="{{ $svc->getTitle() }}">{{ $svc->getTitle() }}</button>
                @endforeach
            @else
                <button type="button" class="contact-pill" data-value="Chiến lược thương hiệu">Chiến lược thương hiệu</button>
                <button type="button" class="contact-pill" data-value="Nhận diện thương hiệu">Nhận diện thương hiệu</button>
                <button type="button" class="contact-pill" data-value="Thiết kế website">Thiết kế website</button>
                <button type="button" class="contact-pill" data-value="Thiết kế sản phẩm">Thiết kế sản phẩm</button>
            @endif
            <button type="button" class="contact-pill" data-value="Khác">Khác</button>
        </div>

        <h2 class="contact-title">Ngân sách dự kiến của bạn là bao nhiêu?</h2>
        <div class="contact-options group-2" id="contactBudgetOptions">
            <button type="button" class="contact-pill" data-value="Dưới 50 triệu">Dưới 50 triệu</button>
            <button type="button" class="contact-pill" data-value="50 - 100 triệu">50 - 100 triệu</button>
            <button type="button" class="contact-pill" data-value="100 - 200 triệu">100 - 200 triệu</button>
            <button type="button" class="contact-pill" data-value="Trên 200 triệu">Trên 200 triệu</button>
        </div>

        <h2 class="contact-title">Thông tin</h2>
        <form class="contact-form" id="contactForm" action="{{ route('contact.submit') }}" method="POST">
            @csrf
            <div class="contact-form-row">
                <input type="text" name="name" id="contactName" placeholder="Tên của bạn" class="contact-input" required>
                <input type="email" name="email" id="contactEmail" placeholder="Email" class="contact-input" required>
            </div>
            <input type="tel" inputmode="tel" name="phone" id="contactPhone" placeholder="Số điện thoại" class="contact-input mb-3" style="width: 100%;" maxlength="20">
            <textarea name="message" id="contactMessage" placeholder="Hãy chia sẻ ý tưởng, mục tiêu hoặc mong muốn của bạn cho dự án này..." class="contact-textarea" rows="5" required></textarea>
            
            <div id="contactFormFeedback" style="display: none; padding: 12px; margin-bottom: 12px; border-radius: 4px; font-size: 14px;"></div>

            <button type="submit" class="contact-submit" id="contactSubmitBtn">
                GỬI THÔNG TIN <i class='bx bx-right-arrow-alt'></i>
            </button>
        </form>
        <div class="contact-footer">
            <div class="contact-footer-left">
                <div class="contact-info-text">
                    <p>@normSetting('hotline', '(+84)816 656718')</p>
                    <p>@normSetting('email', 'info@norm.vn')</p>
                    <p class="contact-address">{!! nl2br(e(\App\Helpers\NormHelper::setting('address', "No. 110 Nguyen Ngoc Nai street,\nPhuong Liet ward, Ha Noi city, Vietnam"))) !!}</p>
                </div>
                <p class="contact-copyright">@normSetting('copyright', '©2026 All Rights Reserved')</p>
            </div>

            <div class="contact-footer-right">
                <span class="label-side contact-social-title">• Mạng xã hội</span>
                <ul class="contact-social-list">
                    <li><a href="{{ \App\Helpers\NormHelper::setting('social_behance', '#') }}" target="_blank">Behance</a></li>
                    <li><a href="{{ \App\Helpers\NormHelper::setting('social_linkedin', '#') }}" target="_blank">Linkedin</a></li>
                    <li><a href="{{ \App\Helpers\NormHelper::setting('social_instagram', '#') }}" target="_blank">Instagram</a></li>
                    <li><a href="{{ \App\Helpers\NormHelper::setting('social_facebook', '#') }}" target="_blank">Facebook</a></li>
                    <li><a href="{{ \App\Helpers\NormHelper::setting('social_dribbble', '#') }}" target="_blank">Dribbble</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    const feedbackBox = document.getElementById('contactFormFeedback');
    const submitBtn = document.getElementById('contactSubmitBtn');
    const phoneInput = document.getElementById('contactPhone');
    const phoneRegex = /^(?=(?:.*\d){8,15})[0-9+\-\.\s()]+$/;

    if (phoneInput) {
        phoneInput.addEventListener('input', () => {
            phoneInput.value = phoneInput.value.replace(/[^0-9+\-\.\s()]/g, '');
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            feedbackBox.style.display = 'none';

            const phoneVal = phoneInput ? phoneInput.value.trim() : '';
            if (phoneVal && !phoneRegex.test(phoneVal)) {
                feedbackBox.style.display = 'block';
                feedbackBox.style.background = '#fee2e2';
                feedbackBox.style.color = '#991b1b';
                feedbackBox.textContent = 'Số điện thoại không hợp lệ (chỉ nhập số, từ 8 đến 15 chữ số).';
                phoneInput.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'ĐANG GỬI...';

            const selectedServices = Array.from(document.querySelectorAll('#contactServiceOptions .contact-pill.active'))
                .map(p => p.dataset.value || p.textContent.trim());
            const activeBudget = document.querySelector('#contactBudgetOptions .contact-pill.active');
            const selectedBudget = activeBudget ? (activeBudget.dataset.value || activeBudget.textContent.trim()) : '';

            const payload = {
                name: document.getElementById('contactName').value,
                email: document.getElementById('contactEmail').value,
                phone: phoneVal,
                message: document.getElementById('contactMessage').value,
                services: selectedServices,
                budget: selectedBudget,
                _token: document.querySelector('input[name="_token"]').value
            };

            try {
                const res = await fetch(contactForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': payload._token
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (res.ok) {
                    feedbackBox.style.display = 'block';
                    feedbackBox.style.background = '#dcfce7';
                    feedbackBox.style.color = '#166534';
                    feedbackBox.textContent = data.message || 'Cảm ơn bạn! Yêu cầu đã được gửi thành công.';
                    contactForm.reset();
                    document.querySelectorAll('.contact-pill.active').forEach(p => p.classList.remove('active'));
                } else {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                    feedbackBox.style.display = 'block';
                    feedbackBox.style.background = '#fee2e2';
                    feedbackBox.style.color = '#991b1b';
                    feedbackBox.textContent = firstError || data.message || 'Có lỗi xảy ra, vui lòng thử lại sau.';
                }
            } catch (err) {
                feedbackBox.style.display = 'block';
                feedbackBox.style.background = '#fee2e2';
                feedbackBox.style.color = '#991b1b';
                feedbackBox.textContent = 'Không thể kết nối máy chủ. Vui lòng thử lại sau.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `GỬI THÔNG TIN <i class='bx bx-right-arrow-alt'></i>`;
            }
        });
    }
});
</script>
