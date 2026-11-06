<template>
  <header class="studio-topbar">
    <div class="topbar-left">
      <a href="/admin/studio" class="studio-brand">
        <span class="brand-logo-badge">
          <img :src="logoUrl" alt="Norm Logo" class="brand-logo-img" />
        </span>
        <span>Website Studio</span>
      </a>

      <!-- Page selector (Show only when editing pages) -->
      <div v-if="currentNavTab === 'pages'" class="page-select-wrap">
        <i class="bx bx-file page-select-icon"></i>
        <select v-model="selectedPageSlug" class="page-select">
          <option v-for="page in pageList" :key="page.slug" :value="page.slug">
            {{ page.name }}
          </option>
        </select>
        <i class="bx bx-chevron-down page-select-arrow"></i>
      </div>
    </div>

    <!-- Center: Device Switcher (Show only when in Live Preview) -->
    <div class="topbar-center">
      <div v-if="currentNavTab === 'pages'" class="device-switch-group">
        <button
          v-for="d in devices"
          :key="d.id"
          class="device-btn"
          :class="{ active: currentDevice === d.id }"
          @click="setDevice(d.id)"
        >
          <i :class="d.icon"></i> {{ d.label }}
        </button>
      </div>
    </div>

    <!-- Right: Actions -->
    <div class="topbar-right">
      <div class="status-indicator">
        <span class="status-dot" :class="{ unsaved: hasUnsavedChanges }"></span>
        <span>{{ hasUnsavedChanges ? 'Có thay đổi chưa lưu' : 'Đã tải cấu hình' }}</span>
      </div>

      <button class="btn-draft" :disabled="isSavingDraft" @click="saveDraft">
        <span v-if="isSavingDraft">Đang lưu...</span>
        <span v-else>Lưu nháp</span>
      </button>
      <button class="btn-publish" :disabled="isSaving" @click="saveChanges">
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
            <a :href="selectedPageSlug" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
              <i class="bx bx-file-blank"></i>
              <span>Xem bản nháp</span>
            </a>

            <a href="/" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
              <i class="bx bx-globe"></i>
              <span>Mở website đã xuất bản</span>
            </a>

            <div class="dropdown-divider"></div>

            <button class="dropdown-item danger" @click="handleLogout">
              <i class="bx bx-log-out"></i>
              <span>Đăng xuất</span>
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import {
  currentNavTab,
  selectedPageSlug,
  devices,
  currentDevice,
  setDevice,
  hasUnsavedChanges,
  isSavingDraft,
  isSaving,
  saveDraft,
  saveChanges,
} from '../composables/useAdmin';

// Danh sách trang phục vụ dropdown chuyển trang
const pageList = [
  { name: 'Trang chủ', slug: '/' },
  { name: 'Về chúng tôi', slug: '/about-us' },
  { name: 'Dự án', slug: '/work' },
  { name: 'Bài viết / Tin tức', slug: '/news' },
];

// Logic quản lý thông tin User & Menu Dropdown
const currentUser = ref(window.__STUDIO_USER__ || 'Admin');
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

const handleLogout = () => {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/admin/logout';

  const tokenInput = document.createElement('input');
  tokenInput.type = 'hidden';
  tokenInput.name = '_token';
  const metaToken = document.querySelector('meta[name="csrf-token"]');
  tokenInput.value = metaToken ? metaToken.content : '';

  form.appendChild(tokenInput);
  document.body.appendChild(form);
  form.submit();
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style src="../css/topbar.css"></style>
