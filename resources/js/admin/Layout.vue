<template>
  <div class="studio-root">
    <Topbar />

    <main class="studio-workspace">
      <nav class="studio-nav-rail">
        <Link href="/admin/dashboard" class="nav-rail-item" :class="{ active: isCurrent('Dashboard') }"
          title="Tổng quan">
          <i class="bx bx-grid-alt"></i>
          <span>Tổng quan</span>
        </Link>

        <div class="nav-rail-divider"></div>

        <div class="nav-rail-section-label">NỘI DUNG</div>

        <!-- Content nav items -->
        <Link href="/admin/pages" class="nav-rail-item" :class="{ active: isCurrent('Pages') }" title="Chỉnh sửa trang">
          <i class="bx bx-edit-alt"></i>
          <span>Chỉnh sửa</span>
        </Link>

        <Link href="/admin/projects" class="nav-rail-item" :class="{ active: isCurrent('Projects') }"
          title="Dự án (Work)">
          <i class="bx bx-briefcase-alt"></i>
          <span>Dự án</span>
        </Link>

        <Link href="/admin/services" class="nav-rail-item" :class="{ active: isCurrent('Services') }" title="Dịch vụ">
          <i class="bx bx-layer"></i>
          <span>Dịch vụ</span>
        </Link>

        <Link href="/admin/news" class="nav-rail-item" :class="{ active: isCurrent('Blogs') }"
          title="Tin tức & Bài viết">
          <i class="bx bx-news"></i>
          <span>Bài viết</span>
        </Link>

        <Link href="/admin/reviews" class="nav-rail-item" :class="{ active: isCurrent('Reviews') }"
          title="Đánh giá khách hàng">
          <i class="bx bx-star"></i>
          <span>Đánh giá</span>
        </Link>

        <Link href="/admin/leads" class="nav-rail-item" :class="{ active: isCurrent('Contacts') }"
          title="Khách hàng liên hệ">
          <i class="bx bx-envelope"></i>
          <span>Liên hệ</span>
        </Link>

        <Link href="/admin/settings" class="nav-rail-item" :class="{ active: isCurrent('Settings') }"
          title="Cài đặt website">
          <i class="bx bx-cog"></i>
          <span>Cài đặt</span>
        </Link>
      </nav>

      <!-- Main Content Area: Full Width (Dashboard, Projects) OR Studio Split View -->
      <slot v-if="isFullWidth" />

      <template v-else>
        <aside class="studio-sidebar" :style="{ width: sidebarWidth + 'px' }">
          <slot />
        </aside>

        <!-- Resizer Handle Bar -->
        <div class="resizer-bar" :class="{ dragging: isResizing }" @mousedown="startResize">
          <div class="resizer-pill" title="Kéo để thay đổi độ rộng cột">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
          </div>
        </div>

        <LivePreview />
      </template>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Topbar from './components/Topbar.vue';
import LivePreview from './components/LivePreview.vue';
import {
  calculateAutoFit,
  isAutoFit,
  previewIframe,
  hasUnsavedChanges,
  isSaving,
  isSavingDraft,
  isDiscardingDraft,
} from './composables/useAdmin';

const page = usePage();

const isCurrent = (componentName) => {
  return page.component === componentName;
};

const isDashboard = computed(() => {
  return page.component === 'Dashboard';
});

const isFullWidth = computed(() => {
  return page.component === 'Dashboard' || page.component === 'Projects' || page.component === 'Services' || page.component === 'Reviews' || page.component === 'Blogs' || page.component === 'Settings' || page.component === 'Contacts';
});

const MIN_SIDEBAR_WIDTH = 320;
const sidebarWidth = ref(MIN_SIDEBAR_WIDTH);
const isResizing = ref(false);

watch(
  () => page.component,
  (newComp) => {
    if (!isFullWidth.value) {
      sidebarWidth.value = MIN_SIDEBAR_WIDTH;
      nextTick(() => {
        if (isAutoFit.value) {
          calculateAutoFit();
        }
      });
    }
  }
);

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

  const removeRouterGuard = router.on('before', (event) => {
    // Không chặn khi đang thực hiện các request lưu (save, save draft, discard) hoặc request gửi dữ liệu POST/PUT/DELETE
    if (isSaving.value || isSavingDraft.value || isDiscardingDraft.value) {
      return;
    }
    const method = event.detail?.visit?.method;
    if (method && method.toLowerCase() !== 'get') {
      return;
    }

    if (hasUnsavedChanges.value) {
      if (!confirm('Bạn đang có thay đổi chưa lưu. Nếu rời khỏi trang này, các thay đổi chưa lưu sẽ bị hủy. Bạn có chắc chắn muốn rời đi không?')) {
        event.preventDefault();
      }
    }
  });

  const handleBeforeUnload = (e) => {
    if (hasUnsavedChanges.value) {
      e.preventDefault();
      e.returnValue = '';
    }
  };
  window.addEventListener('beforeunload', handleBeforeUnload);

  onUnmounted(() => {
    removeRouterGuard();
    window.removeEventListener('beforeunload', handleBeforeUnload);
  });
});
</script>

<style src="./css/layout.css"></style>
