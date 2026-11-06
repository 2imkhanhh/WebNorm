<template>
  <div class="studio-root">
    <!-- Topbar -->
    <Topbar />

    <!-- Workspace Columns -->
    <main class="studio-workspace">
      <!-- 1. Leftmost Icon Navigation Sidebar -->
      <nav class="studio-nav-rail">
        <!-- Top overview item -->
        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'overview' }"
          @click="onSelectNavTab('overview')"
          title="Tổng quan"
        >
          <i class="bx bx-grid-alt"></i>
          <span>Tổng quan</span>
        </button>

        <div class="nav-rail-divider"></div>

        <div class="nav-rail-section-label">NỘI DUNG</div>

        <!-- Content nav items -->
        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'pages' }"
          @click="onSelectNavTab('pages')"
          title="Chỉnh sửa trang"
        >
          <i class="bx bx-edit-alt"></i>
          <span>Chỉnh sửa</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'projects' }"
          @click="onSelectNavTab('projects')"
          title="Dự án (Work)"
        >
          <i class="bx bx-briefcase-alt"></i>
          <span>Dự án</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'services' }"
          @click="onSelectNavTab('services')"
          title="Dịch vụ"
        >
          <i class="bx bx-layer"></i>
          <span>Dịch vụ</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'news' || currentNavTab === 'blogs' }"
          @click="onSelectNavTab('news')"
          title="Tin tức & Bài viết"
        >
          <i class="bx bx-news"></i>
          <span>Bài viết</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'reviews' }"
          @click="onSelectNavTab('reviews')"
          title="Đánh giá khách hàng"
        >
          <i class="bx bx-star"></i>
          <span>Reviews</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'leads' || currentNavTab === 'contacts' }"
          @click="onSelectNavTab('leads')"
          title="Khách hàng liên hệ"
        >
          <i class="bx bx-envelope"></i>
          <span>Liên hệ</span>
        </button>

        <button
          class="nav-rail-item"
          :class="{ active: currentNavTab === 'media' }"
          @click="onSelectNavTab('media')"
          title="Thư viện hình ảnh"
        >
          <i class="bx bx-image-alt"></i>
          <span>Thư viện</span>
        </button>
      </nav>

      <!-- 2. Controls Panel (Sub-Sidebar) -->
      <aside
        v-if="currentNavTab !== 'overview'"
        class="studio-sidebar"
        :style="{ width: sidebarWidth + 'px' }"
      >
        <Pages v-if="currentNavTab === 'pages'" />
        <Projects v-else-if="currentNavTab === 'projects'" />
        <Services v-else-if="currentNavTab === 'services'" />
        <Blogs v-else-if="currentNavTab === 'news' || currentNavTab === 'blogs'" />
        <Reviews v-else-if="currentNavTab === 'reviews'" />
        <Contacts v-else-if="currentNavTab === 'leads' || currentNavTab === 'contacts'" />
        <Media v-else-if="currentNavTab === 'media'" />
        <Settings v-else-if="currentNavTab === 'settings'" />
      </aside>

      <!-- Resizer Handle Bar -->
      <div
        v-if="currentNavTab !== 'overview'"
        class="resizer-bar"
        :class="{ dragging: isResizing }"
        @mousedown="startResize"
      >
        <div class="resizer-pill" title="Kéo để thay đổi độ rộng cột">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>

      <!-- Main Content Area: Overview or Live Preview -->
      <slot v-if="currentNavTab === 'overview'" name="overview" />
      <LivePreview v-else />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Topbar from './components/Topbar.vue';
import LivePreview from './components/LivePreview.vue';
import Pages from './Pages.vue';
import Projects from './Projects.vue';
import Services from './Services.vue';
import Blogs from './Blogs.vue';
import Reviews from './Reviews.vue';
import Contacts from './Contacts.vue';
import Media from './Media.vue';
import Settings from './Settings.vue';
import {
  currentNavTab,
  selectNavTab,
  calculateAutoFit,
  isAutoFit,
  previewIframe,
} from './composables/useAdmin';

// Logic kéo thay đổi độ rộng cột (Sidebar resizer) quản lý riêng trong Layout
const MIN_SIDEBAR_WIDTH = 260;
const sidebarWidth = ref(MIN_SIDEBAR_WIDTH);
const isResizing = ref(false);

const onSelectNavTab = (tabId) => {
  selectNavTab(tabId);
  if (tabId !== 'overview') {
    sidebarWidth.value = MIN_SIDEBAR_WIDTH;
  }
};

const startResize = (e) => {
  e.preventDefault();
  isResizing.value = true;
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';

  if (previewIframe.value) {
    previewIframe.value.style.pointerEvents = 'none';
  }

  window.addEventListener('mousemove', handleResize);
  window.addEventListener('mouseup', stopResize);
};

const handleResize = (e) => {
  if (!isResizing.value) return;
  const navRailWidth = 72;
  const maxWidth = Math.min(window.innerWidth * 0.65, 800);
  const currentSidebarWidth = e.clientX - navRailWidth;
  const newWidth = Math.max(MIN_SIDEBAR_WIDTH, Math.min(currentSidebarWidth, maxWidth));
  sidebarWidth.value = newWidth;
  if (isAutoFit.value) {
    calculateAutoFit();
  }
};

const stopResize = () => {
  isResizing.value = false;
  document.body.style.cursor = '';
  document.body.style.userSelect = '';

  if (previewIframe.value) {
    previewIframe.value.style.pointerEvents = '';
  }

  window.removeEventListener('mousemove', handleResize);
  window.removeEventListener('mouseup', stopResize);
};

onMounted(() => {
  calculateAutoFit();
  window.addEventListener('resize', () => {
    if (isAutoFit.value) calculateAutoFit();
  });
});
</script>

<style src="./css/layout.css"></style>
