<template>
  <header class="studio-topbar">
    <div class="topbar-left">
      <Link href="/admin/dashboard" class="studio-brand">
        <span class="brand-logo-badge">
          <img :src="logoUrl" alt="Norm Logo" class="brand-logo-img" />
        </span>
        <span>Website Studio</span>
      </Link>

      <!-- Page selector (Show only when editing pages) -->
      <div v-if="isPagesRoute" class="page-select-wrap">
        <i class="bx bx-file page-select-icon"></i>
        <select v-model="selectedPageSlug" class="page-select">
          <option v-for="p in pageList" :key="p.slug" :value="p.slug">
            {{ p.name }}
          </option>
        </select>
        <i class="bx bx-chevron-down page-select-arrow"></i>
      </div>

      <!-- Language selector -->
      <div class="lang-select-wrap">
        <i class="bx bx-globe lang-select-icon"></i>
        <select v-model="currentLanguage" class="lang-select" @change="onLanguageChange">
          <option value="vi">VI</option>
          <option value="en">EN</option>
        </select>
        <i class="bx bx-chevron-down lang-select-arrow"></i>
      </div>
    </div>

    <!-- Center: Device Switcher (Show only when in Pages editor) -->
    <div class="topbar-center">
      <div v-if="isPagesRoute" class="device-switch-group">
        <button v-for="d in devices" :key="d.id" class="device-btn" :class="{ active: currentDevice === d.id }"
          @click="setDevice(d.id)">
          <i :class="d.icon"></i> {{ d.label }}
        </button>
      </div>
    </div>

    <!-- Right: Actions -->
    <div class="topbar-right">
      <div class="status-indicator">
        <span class="status-dot"
          :class="{ unsaved: hasUnsavedChanges, draft: !hasUnsavedChanges && hasPendingDraft }"></span>
        <span v-if="hasUnsavedChanges" class="status-text-unsaved">Có thay đổi chưa lưu</span>
        <span v-else-if="hasPendingDraft" class="status-text-draft">Có bản nháp chưa xuất bản</span>
        <span v-else class="status-text-live">Đã xuất bản</span>
      </div>

      <button class="btn-draft" :disabled="isSavingDraft || isSaving" @click="saveDraft">
        <span v-if="isSavingDraft">Đang lưu...</span>
        <span v-else>Lưu nháp</span>
      </button>
      <button class="btn-publish" :disabled="isSaving || isSavingDraft" @click="saveChanges">
        <span v-if="isSaving">Đang lưu...</span>
        <span v-else>Xuất bản</span>
      </button>

      <div class="user-menu-wrapper" ref="userMenuRef">
        <div class="user-trigger" @click="toggleUserMenu">
          <div class="user-avatar">
            <i class="bx bx-user"></i>
          </div>
          <span class="user-name">{{ currentUser }}</span>
          <i class="bx bx-chevron-down user-arrow" :class="{ open: isUserMenuOpen }"></i>
        </div>

        <!-- Dropdown Menu -->
        <transition name="dropdown-fade">
          <div v-if="isUserMenuOpen" class="user-dropdown-popover">
            <a :href="previewDraftUrl" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
              <i class="bx bx-show"></i>
              <span>Xem bản nháp</span>
            </a>

            <a :href="selectedPageSlug" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
              <i class="bx bx-globe"></i>
              <span>Mở website đã xuất bản</span>
            </a>

            <div class="dropdown-divider"></div>

            <Link href="/admin/logout" method="post" as="button" class="dropdown-item danger">
              <i class="bx bx-log-out"></i>
              <span>Đăng xuất</span>
            </Link>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  selectedPageSlug,
  currentLanguage,
  previewIframe,
  devices,
  currentDevice,
  setDevice,
  hasUnsavedChanges,
  hasPendingDraft,
  isSavingDraft,
  isSaving,
  saveDraft,
  saveChanges,
} from '../composables/useAdmin';

const page = usePage();

const isPagesRoute = computed(() => {
  return page.component === 'Pages';
});

const previewDraftUrl = computed(() => {
  const slug = selectedPageSlug.value || '/';
  const lang = currentLanguage.value || 'vi';
  return slug + (slug.includes('?') ? '&' : '?') + `preview=draft&lang=${lang}`;
});

// Danh sách trang phục vụ dropdown chuyển trang
const pageList = [
  { name: 'Trang chủ', slug: '/' },
  { name: 'Về chúng tôi', slug: '/about-us' },
  { name: 'Dự án', slug: '/work' },
  { name: 'Bài viết / Tin tức', slug: '/news' },
];

// Logic quản lý thông tin User & Menu Dropdown
const currentUser = computed(() => {
  return page.props.auth?.user?.name || window.__STUDIO_USER__ || 'Admin';
});

const logoUrl = ref('/assets/images/logo.png');
const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const handleClickOutside = (e) => {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    isUserMenuOpen.value = false;
  }
};

const onLanguageChange = () => {
  if (previewIframe.value && previewIframe.value.contentWindow) {
    previewIframe.value.contentWindow.postMessage(
      {
        type: 'STUDIO_LANG_UPDATE',
        lang: currentLanguage.value,
      },
      '*'
    );
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style src="../css/topbar.css"></style>
