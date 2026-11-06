<template>
  <Layout>
    <template #overview>
      <section class="overview-dashboard-view">
        <div class="overview-container">
          <!-- Top Hero Card (Soft Slate Surface) -->
          <div class="overview-hero-card">
            <div class="hero-card-content">
              <div class="hero-breadcrumbs">WEBSITE STUDIO / TỔNG QUAN</div>
              <h1 class="hero-brand-name">Brand Design Agency • Norm</h1>
              <p class="hero-tagline-text">Mọi thứ cần để giữ website chỉn chu, trong một nơi.</p>

              <div class="hero-badge-group">
                <span class="status-pill published">
                  <span class="dot"></span>
                  Đã có bản xuất bản
                </span>
                <span class="status-pill draft">Chưa có bản nháp</span>
              </div>
            </div>

            <div class="hero-card-action">
              <a href="/" target="_blank" class="btn-open-site">
                <span>Mở website</span>
                <i class="bx bx-right-arrow-alt"></i>
              </a>
            </div>
          </div>

          <!-- Bottom Metric Cards Grid (4 Cards in Norm Style) -->
          <div class="overview-metrics-grid">
            <div class="metric-card" @click="goToTab('pages')">
              <div class="metric-icon-wrap">
                <i class="bx bx-globe"></i>
              </div>
              <div class="metric-number">4</div>
              <div class="metric-title">Trang nội dung</div>
              <div class="metric-subtitle">Trang chủ, Dự án, Tin tức, Về chúng tôi</div>
            </div>

            <div class="metric-card" @click="goToTab('projects')">
              <div class="metric-icon-wrap">
                <i class="bx bx-briefcase-alt"></i>
              </div>
              <div class="metric-number">6</div>
              <div class="metric-title">Dự án Showcase</div>
              <div class="metric-subtitle">Dự án đang bật hiển thị</div>
            </div>

            <div class="metric-card" @click="goToTab('services')">
              <div class="metric-icon-wrap">
                <i class="bx bx-layer"></i>
              </div>
              <div class="metric-number">3</div>
              <div class="metric-title">Dịch vụ cốt lõi</div>
              <div class="metric-subtitle">Gói dịch vụ đang kích hoạt</div>
            </div>

            <div class="metric-card" @click="goToTab('reviews')">
              <div class="metric-icon-wrap">
                <i class="bx bx-star"></i>
              </div>
              <div class="metric-number">2</div>
              <div class="metric-title">Đánh giá đối tác</div>
              <div class="metric-subtitle">Lời chứng thực khách hàng</div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </Layout>
</template>

<script setup>
import Layout from './Layout.vue';
import { selectNavTab } from './composables/useAdmin';

const goToTab = (tabId) => {
  selectNavTab(tabId);
};
</script>

<style src="./css/dashboard.css"></style>
