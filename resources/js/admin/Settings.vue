<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Cài đặt Website</h2>
          <p class="sidebar-subtitle">Cấu hình thông tin thương hiệu, hotline, 3D model và mạng xã hội.</p>
        </div>
      </div>
    </div>

    <!-- Staging Alert Banner when settings have unsaved changes -->
    <div v-if="hasUnsavedChanges" class="staging-alert-banner mx-3 mt-2">
      <div class="banner-info">
        <span>Có thay đổi chưa lưu vào database</span>
      </div>
      <div class="banner-buttons">
        <button type="button" class="btn-banner-revert" @click="revertSettings">Hủy thay đổi</button>
        <button type="button" class="btn-banner-publish" :disabled="isSaving" @click="saveSettings">
          {{ isSaving ? 'Đang lưu...' : 'Xuất bản ngay' }}
        </button>
      </div>
    </div>

    <div class="sidebar-panel-content">
      <form @submit.prevent="saveSettings" class="settings-form" :key="renderKey">
        <!-- 1. Thông tin chung & Logo -->
        <div class="settings-card">
          <div class="settings-card-title">
            <i class="bx bx-store-alt"></i> Thông tin thương hiệu
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tên website / Thương hiệu (VI)</label>
            <input v-model="form.site_name_vi" type="text" class="modal-input" placeholder="Brand Design Agency • Norm" />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tên website / Thương hiệu (EN)</label>
            <input v-model="form.site_name_en" type="text" class="modal-input" placeholder="Brand Design Agency • Norm" />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn Logo chính</label>
            <input v-model="form.logo" type="text" class="modal-input" placeholder="/assets/images/logo.png" />
            <div v-if="form.logo" class="logo-preview-box mt-2">
              <img :src="form.logo.startsWith('http') ? form.logo : '/' + form.logo.replace(/^\//, '')" alt="Logo preview" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn Mô hình 3D Hero (.glb)</label>
            <input v-model="form.model_3d" type="text" class="modal-input" placeholder="/assets/images/norm.glb" />
            <span class="field-hint">Mô hình 3D tương tác tại Banner trang chủ.</span>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Dòng bản quyền Footer</label>
            <input v-model="form.copyright" type="text" class="modal-input" placeholder="©2026 All Rights Reserved" />
          </div>
        </div>

        <!-- 2. Thông tin liên hệ -->
        <div class="settings-card mt-3">
          <div class="settings-card-title">
            <i class="bx bx-phone-call"></i> Thông tin liên hệ
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Hotline</label>
              <input v-model="form.hotline" type="text" class="modal-input" placeholder="(+84)816 656718" />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Email</label>
              <input v-model="form.email" type="email" class="modal-input" placeholder="info@norm.vn" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Địa chỉ trụ sở (VI)</label>
            <textarea v-model="form.address_vi" class="modal-textarea" rows="2" placeholder="Số 110 Nguyễn Ngọc Nại, P. Phương Liệt, TP. Hà Nội"></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Địa chỉ trụ sở (EN)</label>
            <textarea v-model="form.address_en" class="modal-textarea" rows="2" placeholder="No. 110 Nguyen Ngoc Nai street, Phuong Liet ward, Ha Noi city, Vietnam"></textarea>
          </div>
        </div>

        <!-- 3. Liên kết Mạng xã hội -->
        <div class="settings-card mt-3">
          <div class="settings-card-title">
            <i class="bx bx-share-alt"></i> Mạng xã hội & Kênh truyền thông
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Facebook URL</label>
              <input v-model="form.social_facebook" type="text" class="modal-input" placeholder="https://facebook.com/..." />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Instagram URL</label>
              <input v-model="form.social_instagram" type="text" class="modal-input" placeholder="https://instagram.com/..." />
            </div>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">LinkedIn URL</label>
              <input v-model="form.social_linkedin" type="text" class="modal-input" placeholder="https://linkedin.com/..." />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Behance URL</label>
              <input v-model="form.social_behance" type="text" class="modal-input" placeholder="https://behance.net/..." />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Dribbble URL</label>
            <input v-model="form.social_dribbble" type="text" class="modal-input" placeholder="https://dribbble.com/..." />
          </div>
        </div>

        <div class="settings-submit-bar mt-4">
          <button type="submit" class="btn-save-settings" :disabled="isSaving">
            <i class="bx" :class="isSaving ? 'bx-loader-alt bx-spin' : 'bx-save'"></i>
            <span>{{ isSaving ? 'Đang lưu cài đặt...' : 'Lưu toàn bộ cài đặt' }}</span>
          </button>
        </div>
      </form>
    </div>
  </Layout>
</template>

<script setup>
import { reactive, ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import {
  hasUnsavedChanges,
  isSaving,
  onSaveHandler,
  onDraftHandler,
} from './composables/useAdmin';

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({}),
  },
});

const getVal = (key, lang = null) => {
  const val = props.settings[key];
  if (lang && val && typeof val === 'object') {
    return val[lang] || val.vi || '';
  }
  return val || '';
};

const initFormData = () => ({
  site_name_vi: getVal('site_name', 'vi') || 'Brand Design Agency • Norm',
  site_name_en: getVal('site_name', 'en') || 'Brand Design Agency • Norm',
  logo: props.settings.logo || '/assets/images/logo.png',
  model_3d: props.settings.model_3d || '/assets/images/norm.glb',
  copyright: props.settings.copyright || '©2026 All Rights Reserved',
  hotline: props.settings.hotline || '(+84)816 656718',
  email: props.settings.email || 'info@norm.vn',
  address_vi: getVal('address', 'vi') || 'Số 110 Nguyễn Ngọc Nại, P. Phương Liệt, TP. Hà Nội',
  address_en: getVal('address', 'en') || 'No. 110 Nguyen Ngoc Nai street, Phuong Liet ward, Ha Noi city, Vietnam',
  social_facebook: props.settings.social_facebook || 'https://facebook.com',
  social_instagram: props.settings.social_instagram || 'https://instagram.com',
  social_linkedin: props.settings.social_linkedin || 'https://linkedin.com',
  social_behance: props.settings.social_behance || 'https://behance.net',
  social_dribbble: props.settings.social_dribbble || 'https://dribbble.com',
});

const form = reactive(initFormData());

const renderKey = ref(0);
let isInitialized = false;
let isReverting = false;

watch(
  () => ({ ...form }),
  () => {
    if (isInitialized && !isReverting) {
      hasUnsavedChanges.value = true;
    }
  },
  { deep: true }
);

const revertSettings = () => {
  if (confirm('Hủy bỏ các thay đổi vừa chỉnh sửa và quay lại cài đặt gốc trong database?')) {
    isReverting = true;
    Object.assign(form, initFormData());
    renderKey.value++;
    hasUnsavedChanges.value = false;
    setTimeout(() => {
      isReverting = false;
      hasUnsavedChanges.value = false;
    }, 50);
  }
};

const saveSettings = () => {
  isSaving.value = true;
  const payload = {
    settings: {
      site_name: { vi: form.site_name_vi, en: form.site_name_en },
      logo: form.logo,
      model_3d: form.model_3d,
      copyright: form.copyright,
      hotline: form.hotline,
      email: form.email,
      address: { vi: form.address_vi, en: form.address_en },
      social_facebook: form.social_facebook,
      social_instagram: form.social_instagram,
      social_linkedin: form.social_linkedin,
      social_behance: form.social_behance,
      social_dribbble: form.social_dribbble,
    },
  };

  router.post('/admin/settings', payload, {
    preserveScroll: true,
    onSuccess: () => {
      isSaving.value = false;
      hasUnsavedChanges.value = false;
      alert('Đã xuất bản cài đặt website thành công vào cơ sở dữ liệu!');
    },
    onError: (err) => {
      isSaving.value = false;
      alert('Lỗi lưu cài đặt: ' + JSON.stringify(err));
    },
  });
};

onMounted(() => {
  isInitialized = true;
  onSaveHandler.value = saveSettings;
  onDraftHandler.value = saveSettings;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/panels.css"></style>
