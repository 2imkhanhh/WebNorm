<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Blog;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Article: Hiểu về tư duy thiết kế (design thinking)
        $designThinkingVi = <<<'HTML'
<p>Design Thinking là một cách tiếp cận vấn đề mang tính chiến lược, lấy con người làm trung tâm và hướng đến việc giải quyết những nhu cầu thực sự của người dùng. Trong bài viết này, chúng ta sẽ cùng tìm hiểu sâu về tư duy thiết kế là gì, vì sao tư duy thiết kế lại trở thành lợi thế cạnh tranh trong ngành công nghiệp sáng tạo, cũng như cách áp dụng phương pháp này để nâng tầm chất lượng dự án và tư duy làm nghề.</p>

<h3>1. Tư duy thiết kế (Design Thinking) là gì?</h3>

<p>Tư duy thiết kế, hay Design Thinking, là một phương pháp tư duy và giải quyết vấn đề lấy con người làm trung tâm. Thay vì bắt đầu từ giải pháp hoặc từ góc nhìn chủ quan của người làm sáng tạo, tư duy thiết kế bắt đầu từ việc thấu hiểu sâu sắc nhu cầu, mong muốn, hành vi và "nỗi đau" của người dùng, sau đó mới phát triển ý tưởng và giải pháp phù hợp.</p>

<p>Nói một cách đơn giản, tư duy thiết kế (Design Thinking) là cách tiếp cận vấn đề giống như một nhà thiết kế: quan sát, đặt câu hỏi, thấu cảm, thử nghiệm, sai, sửa và tối ưu liên tục. Phương pháp này không chỉ được sử dụng trong thiết kế đồ họa hay thiết kế sản phẩm, mà còn được ứng dụng rộng rãi trong kinh doanh, marketing, giáo dục, công nghệ và quản trị.</p>

<p>Khác với tư duy thiết kế truyền thống vốn tập trung nhiều vào hình thức và thẩm mỹ, tư duy Design Thinking nhấn mạnh vào việc giải quyết vấn đề cốt lõi. Một sản phẩm, một thương hiệu hay một chiến dịch truyền thông, dù có đẹp đến đâu, nếu không giải quyết đúng nhu cầu thật của người dùng thì vẫn có thể thất bại.</p>

<p>Ngày nay, nhiều tập đoàn lớn như Google, Apple, IDEO, Airbnb hay IBM đều coi tư duy thiết kế là nền tảng trong cách họ phát triển sản phẩm và xây dựng trải nghiệm khách hàng. Điều đó cho thấy tư duy thiết kế không còn là "đặc quyền" của giới designer, mà là kỹ năng cốt lõi của bất kỳ ai làm trong lĩnh vực sáng tạo và đổi mới.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/gotrail.jpg" alt="Gotrail Billboard" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. Vì sao tư duy thiết kế là lợi thế cạnh tranh?</h3>

<p>Trong bối cảnh thị trường bão hòa, nơi các sản phẩm và dịch vụ ngày càng tương đồng về tính năng lẫn giá cả, trải nghiệm người dùng và giá trị cảm xúc chính là yếu tố tạo nên sự khác biệt. Doanh nghiệp áp dụng tư duy thiết kế có khả năng đổi mới nhanh hơn, giảm thiểu rủi ro khi tung sản phẩm mới và xây dựng được lòng trung thành sâu sắc từ khách hàng.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/gallery1.jpg" alt="Design Thinking Workshop" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>3. Áp dụng tư duy thiết kế vào dự án sáng tạo</h3>

<p>Quy trình Design Thinking tiêu chuẩn gồm 5 giai đoạn: Thấu cảm (Empathize), Xác định vấn đề (Define), Lên ý tưởng (Ideate), Tạo mẫu thử (Prototype), và Kiểm tra (Test). Bằng cách lặp lại quy trình này một cách linh hoạt, đội ngũ sáng tạo có thể liên tục tối ưu hóa giải pháp, đảm bảo sản phẩm cuối cùng thực sự chạm đến nhu cầu của người dùng.</p>
HTML;

        $designThinkingEn = <<<'HTML'
<p>Design Thinking is a strategic, human-centered approach to problem solving that focuses on addressing real user needs. In this article, we explore what design thinking is, why it serves as a competitive advantage in the creative industry, and how to apply this methodology to elevate project quality and professional mindset.</p>

<h3>1. What is Design Thinking?</h3>

<p>Design thinking is a human-centric methodology for solving complex problems. Instead of starting with predefined solutions or subjective assumptions, design thinking begins with a deep empathy for users' needs, behaviors, and pain points, before developing tailored ideas and solutions.</p>

<p>Simply put, design thinking means approaching challenges the way a designer does: observing, questioning, empathizing, experimenting, failing fast, iterating, and optimizing continuously. This method is utilized not only in graphic and product design, but also widely across business, marketing, education, technology, and management.</p>

<p>Unlike traditional design mindsets centered primarily on aesthetics and visual form, Design Thinking emphasizes solving core underlying problems. A product, brand, or campaign—no matter how visually stunning—will ultimately struggle if it does not address genuine human needs.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/gotrail.jpg" alt="Gotrail Billboard" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. Why Design Thinking is a Competitive Advantage</h3>

<p>In saturated markets where products and services share similar features and pricing, user experience and emotional resonance become the true differentiators. Companies embracing design thinking innovate faster, mitigate risks during launches, and cultivate authentic customer loyalty.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/gallery1.jpg" alt="Design Thinking Workshop" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>3. Implementing Design Thinking in Creative Projects</h3>

<p>The standard design thinking framework encompasses five iterative stages: Empathize, Define, Ideate, Prototype, and Test. By cycling through these steps, creative teams continuously refine their solutions, guaranteeing that the final output resonates deeply with users.</p>
HTML;

        // 2. Article: Tái định vị thương hiệu kỷ nguyên số
        $repositioningVi = <<<'HTML'
<p>Trong kỷ nguyên số hóa với tốc độ biến chuyển chóng mặt, tái định vị thương hiệu không chỉ đơn thuần là thay đổi logo hay bộ màu nhận diện. Đó là một quyết định chiến lược nhằm tái thiết lập mối liên kết sâu sắc giữa thương hiệu và khách hàng mục tiêu thế hệ mới.</p>

<h3>1. Khi nào thương hiệu cần tái định vị?</h3>

<p>Thương hiệu cần xem xét tái định vị khi đối tượng khách hàng mục tiêu thay đổi hành vi, khi đối thủ cạnh tranh mới xuất hiện với mô hình vượt trội, hoặc khi tầm nhìn và danh mục sản phẩm của doanh nghiệp đã mở rộng vượt ra ngoài định vị ban đầu.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/project2.png" alt="Brand Strategy" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. Các nguyên tắc cốt lõi khi làm mới thương hiệu</h3>

<p>Một chiến dịch tái định vị thành công phải giữ lại được những giá trị di sản (Brand Heritage) cốt lõi đã làm nên niềm tin của khách hàng, đồng thời can đảm loại bỏ những yếu tố lỗi thời để đón đầu các xu hướng trải nghiệm hiện đại.</p>
HTML;

        $repositioningEn = <<<'HTML'
<p>In the fast-paced digital era, brand repositioning is far more than updating a logo or color palette. It is a strategic imperative to re-establish meaningful resonance with a new generation of consumers.</p>

<h3>1. When Does a Brand Need Repositioning?</h3>

<p>Brands should contemplate repositioning when target consumer behaviors shift, when disruptive competitors enter the market, or when the organization's vision and product portfolio outgrow its original identity.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/project2.png" alt="Brand Strategy" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. Core Principles for Brand Modernization</h3>

<p>A successful repositioning preserves the essential brand heritage that built customer trust, while boldly shedding obsolete elements to embrace modern user experiences.</p>
HTML;

        // 3. Article: Thiết kế tối giản và sức hút thương hiệu bền vững
        $minimalismVi = <<<'HTML'
<p>Thiết kế tối giản (Minimalism) không có nghĩa là loại bỏ tất cả, mà là giữ lại những gì tinh túy và giàu ý nghĩa nhất. Đây là triết lý giúp các thương hiệu hàng đầu thế giới giữ vững sức hút xuyên thời gian.</p>

<h3>1. Sức mạnh của sự tinh giản</h3>

<p>Khi người tiêu dùng bị quá tải bởi hàng nghìn thông điệp quảng cáo mỗi ngày, sự rõ ràng, mạch lạc và tinh gọn trong thiết kế tạo ra một "khoảng lặng" dễ chịu, giúp thương hiệu được ghi nhớ ngay lập tức.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/project3.png" alt="Minimalist Design" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. Từ triết lý đến trải nghiệm thực tế</h3>

<p>Áp dụng chủ nghĩa tối giản đòi hỏi sự chuẩn xác cao độ về tỷ lệ, phông chữ và khoảng trống (white space). Mỗi chi tiết còn lại đều phải mang một mục đích rõ ràng phục vụ cảm xúc người dùng.</p>
HTML;

        $minimalismEn = <<<'HTML'
<p>Minimalism does not mean having less; it means making room for what truly matters. It is the enduring philosophy that enables premier global brands to sustain timeless appeal.</p>

<h3>1. The Power of Simplicity</h3>

<p>In an environment where consumers are overwhelmed by thousands of marketing messages daily, visual clarity and conciseness offer a breath of fresh air that makes a brand instantly memorable.</p>

<div class="article-image-container my-4">
    <img src="/assets/images/project3.png" alt="Minimalist Design" class="article-fluid-img img-fluid w-100" style="border-radius: 8px;">
</div>

<h3>2. From Philosophy to Tangible Experience</h3>

<p>Practicing minimalism demands utmost precision in typography, proportions, and negative space. Every remaining element must purposefully serve the human experience.</p>
HTML;

        Blog::where('slug', 'hieu-ve-tu-duy-thiet-ke-design-thinking')
            ->update([
                'content' => ['vi' => $designThinkingVi, 'en' => $designThinkingEn],
            ]);

        Blog::where('slug', 'tai-dinh-vi-thuong-hieu-ky-nguyen-so')
            ->update([
                'content' => ['vi' => $repositioningVi, 'en' => $repositioningEn],
            ]);

        Blog::where('slug', 'thiet-ke-toi-gian-va-suc-hut-thuong-hieu')
            ->update([
                'content' => ['vi' => $minimalismVi, 'en' => $minimalismEn],
            ]);
    }

    public function down(): void
    {
        Blog::query()->update(['content' => null]);
    }
};

