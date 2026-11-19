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

    <AdminFeedback />
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Topbar from './components/Topbar.vue';
import LivePreview from './components/LivePreview.vue';
import AdminFeedback from './components/AdminFeedback.vue';
import { askConfirm } from './composables/useFeedback';
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

  let bypassLeaveGuard = false;

  const removeRouterGuard = router.on('before', (event) => {
    if (bypassLeaveGuard) {
      bypassLeaveGuard = false;
      return;
    }
    if (isSaving.value || isSavingDraft.value || isDiscardingDraft.value) {
      return;
    }
    const method = event.detail?.visit?.method;
    if (method && method.toLowerCase() !== 'get') {
      return;
    }

    if (hasUnsavedChanges.value) {
      event.preventDefault();
      const targetUrl = event.detail?.visit?.url?.href || event.detail?.visit?.url;
      askConfirm({
        title: 'Thay đổi chưa được lưu',
        message: 'Bạn đang có thay đổi chưa lưu. Nếu rời khỏi trang này, các thay đổi chưa lưu sẽ bị hủy. Bạn có chắc chắn muốn rời đi không?',
        confirmText: 'Rời khỏi trang',
        cancelText: 'Ở lại',
        variant: 'warning',
      }).then((confirmed) => {
        if (confirmed && targetUrl) {
          hasUnsavedChanges.value = false;
          bypassLeaveGuard = true;
          router.visit(targetUrl);
        }
      });
    }
  });

  const handleBeforeUnload = (e) => {
    if (hasUnsavedChanges.value) {
      e.preventDefault();
      e.returnValue = '';
    }
  };
  window.addEventListener('beforeunload', handleBeforeUnload);

  // Auto-logout after 30 minutes of inactivity
  const INACTIVITY_LIMIT_MS = 30 * 60 * 1000;
  const PING_INTERVAL_MS = 5 * 60 * 1000;
  const ACTIVITY_STORAGE_KEY = 'norm_admin_last_activity';
  const activityEvents = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'input', 'click'];

  let lastActivityAt = Date.now();
  let lastPingAt = Date.now();
  let hasActivitySincePing = false;
  let isAutoLoggingOut = false;

  try {
    localStorage.setItem(ACTIVITY_STORAGE_KEY, String(lastActivityAt));
  } catch { }

  const markActivity = () => {
    if (isAutoLoggingOut) return;
    const now = Date.now();
    hasActivitySincePing = true;
    if (now - lastActivityAt >= 1000) {
      lastActivityAt = now;
      try {
        localStorage.setItem(ACTIVITY_STORAGE_KEY, String(now));
      } catch { }
    }
  };

  const performAutoLogout = async () => {
    if (isAutoLoggingOut) return;
    isAutoLoggingOut = true;
    hasUnsavedChanges.value = false;
    try {
      localStorage.removeItem(ACTIVITY_STORAGE_KEY);
    } catch { }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
      await fetch('/admin/logout?timeout=1', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
        keepalive: true,
      });
    } catch { }

    window.location.href = '/admin/login?timeout=1';
  };

  const checkInactivity = async () => {
    if (isAutoLoggingOut) return;

    try {
      const stored = Number(localStorage.getItem(ACTIVITY_STORAGE_KEY));
      if (Number.isFinite(stored) && stored > lastActivityAt) {
        lastActivityAt = stored;
      }
    } catch { }

    const now = Date.now();
    if (now - lastActivityAt >= INACTIVITY_LIMIT_MS) {
      await performAutoLogout();
      return;
    }

    if (hasActivitySincePing && now - lastPingAt >= PING_INTERVAL_MS) {
      hasActivitySincePing = false;
      lastPingAt = now;
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const res = await fetch('/admin/ping', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
          },
        });
        if (res.status === 401 || res.status === 419) {
          await performAutoLogout();
        }
      } catch { }
    }
  };

  const handleStorageSync = (e) => {
    if (e.key === ACTIVITY_STORAGE_KEY && e.newValue) {
      const ts = Number(e.newValue);
      if (Number.isFinite(ts) && ts > lastActivityAt) {
        lastActivityAt = ts;
      }
    }
  };

  const handleVisibilityCheck = () => {
    if (document.visibilityState === 'visible') {
      checkInactivity();
    }
  };

  activityEvents.forEach((evt) => window.addEventListener(evt, markActivity, { passive: true, capture: true }));
  window.addEventListener('storage', handleStorageSync);
  window.addEventListener('focus', checkInactivity);
  document.addEventListener('visibilitychange', handleVisibilityCheck);
  const inactivityTimer = window.setInterval(checkInactivity, 15000);

  onUnmounted(() => {
    removeRouterGuard();
    window.removeEventListener('beforeunload', handleBeforeUnload);
    activityEvents.forEach((evt) => window.removeEventListener(evt, markActivity, { capture: true }));
    window.removeEventListener('storage', handleStorageSync);
    window.removeEventListener('focus', checkInactivity);
    document.removeEventListener('visibilitychange', handleVisibilityCheck);
    window.clearInterval(inactivityTimer);
  });
});
</script>

<style src="./css/layout.css"></style>
