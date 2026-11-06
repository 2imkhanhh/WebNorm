<template>
  <Layout>
    <section class="overview-dashboard-view">
      <div class="overview-container">
        <!-- Top Hero Card (Soft Slate Surface) -->
        <div class="overview-hero-card">
          <div class="hero-card-content">
            <div class="hero-breadcrumbs">WEBSITE STUDIO / TỔNG QUAN</div>
            <h1 class="hero-brand-name">Brand Design Agency • Norm</h1>
            <p class="hero-tagline-text">Hệ thống quản trị cơ sở dữ liệu và nội dung website tập trung.</p>

            <div class="hero-badge-group">
              <span class="status-pill published">
                <span class="dot"></span>
                Đã kết nối Database MySQL
              </span>
              <span class="status-pill draft">
                {{ metrics.leads_count || 0 }} Liên hệ mới
              </span>
            </div>
          </div>

          <div class="hero-card-action">
            <a href="/" target="_blank" class="btn-open-site">
              <span>Mở website</span>
              <i class="bx bx-right-arrow-alt"></i>
            </a>
          </div>
        </div>

        <!-- Metric Cards Grid (6 Cards in Norm Monochrome Style) -->
        <div class="overview-metrics-grid">
          <Link href="/admin/pages" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-globe"></i>
            </div>
            <div class="metric-number">{{ metrics.pages_count || 4 }}</div>
            <div class="metric-title">Trang nội dung</div>
            <div class="metric-subtitle">Trang chủ, Dự án, Tin tức, Về chúng tôi</div>
          </Link>

          <Link href="/admin/projects" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-briefcase-alt"></i>
            </div>
            <div class="metric-number">{{ metrics.projects_count || 0 }}</div>
            <div class="metric-title">Dự án Showcase</div>
            <div class="metric-subtitle">Portfolio & tác phẩm thương hiệu</div>
          </Link>

          <Link href="/admin/services" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-layer"></i>
            </div>
            <div class="metric-number">{{ metrics.services_count || 0 }}</div>
            <div class="metric-title">Dịch vụ cốt lõi</div>
            <div class="metric-subtitle">Các gói dịch vụ doanh nghiệp</div>
          </Link>

          <Link href="/admin/reviews" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-star"></i>
            </div>
            <div class="metric-number">{{ metrics.reviews_count || 0 }}</div>
            <div class="metric-title">Đánh giá đối tác</div>
            <div class="metric-subtitle">Lời chứng thực khách hàng</div>
          </Link>

          <Link href="/admin/news" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-news"></i>
            </div>
            <div class="metric-number">{{ metrics.blogs_count || 0 }}</div>
            <div class="metric-title">Bài viết & Tin tức</div>
            <div class="metric-subtitle">Góc nhìn & chia sẻ chuyên sâu</div>
          </Link>

          <Link href="/admin/leads" class="metric-card">
            <div class="metric-icon-wrap">
              <i class="bx bx-envelope"></i>
            </div>
            <div class="metric-number">{{ metrics.leads_count || 0 }}</div>
            <div class="metric-title">Yêu cầu liên hệ</div>
            <div class="metric-subtitle">Khách hàng để lại thông tin</div>
          </Link>
        </div>

        <!-- Recent Contacts Section -->
        <div class="recent-leads-section" v-if="recentContacts && recentContacts.length > 0">
          <div class="leads-header d-flex justify-content-between align-items-center mb-3">
            <h3 class="leads-title m-0">Khách hàng liên hệ gần đây</h3>
            <Link href="/admin/leads" class="leads-view-all">Xem tất cả <i class="bx bx-right-arrow-alt"></i></Link>
          </div>
          <div class="leads-table-wrap">
            <table class="leads-table">
              <thead>
                <tr>
                  <th>Tên</th>
                  <th>Email</th>
                  <th>Số điện thoại</th>
                  <th>Dịch vụ quan tâm</th>
                  <th>Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="lead in recentContacts" :key="lead.id">
                  <td class="fw-medium">{{ lead.name }}</td>
                  <td>{{ lead.email }}</td>
                  <td>{{ lead.phone || '—' }}</td>
                  <td>{{ Array.isArray(lead.services) ? lead.services.join(', ') : (lead.services || '—') }}</td>
                  <td>
                    <span class="status-badge" :class="lead.status">{{ lead.status === 'new' ? 'Mới' : (lead.status === 'contacted' ? 'Đã liên hệ' : 'Hoàn thành') }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Layout from './Layout.vue';

defineProps({
  metrics: {
    type: Object,
    default: () => ({}),
  },
  recentContacts: {
    type: Array,
    default: () => [],
  },
});
</script>

<style src="./css/dashboard.css"></style>
