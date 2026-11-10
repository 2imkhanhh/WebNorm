<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Thư viện tệp (Media)</h2>
          <p class="sidebar-subtitle">Quản lý tải lên ảnh, mô hình 3D .glb và tư liệu website.</p>
        </div>
      </div>
    </div>

    <div class="sidebar-panel-content">
      <!-- Upload Dropzone -->
      <div
        class="media-upload-zone"
        :class="{ dragging: isDragging, uploading: isUploading }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        @click="triggerFileInput"
      >
        <input
          ref="fileInputRef"
          type="file"
          style="display: none;"
          accept="image/*,.glb,.mp4,.webm,.pdf"
          @change="handleFileSelect"
        />
        <div class="upload-zone-content">
          <i class="bx" :class="isUploading ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
          <span class="upload-title">{{ isUploading ? 'Đang tải tệp lên...' : 'Kéo thả tệp hoặc bấm để chọn ảnh / 3D model' }}</span>
          <span class="upload-hint">Hỗ trợ PNG, JPG, WEBP, GLB, MP4 (Tối đa 25MB)</span>
        </div>
      </div>

      <!-- Media Grid -->
      <div class="media-library-grid mt-4">
        <div class="media-item-card" v-for="m in media" :key="m.id">
          <div class="media-thumb-preview">
            <img
              v-if="m.file_type === 'image' || isImageUrl(m.file_path)"
              :src="m.file_path.startsWith('http') ? m.file_path : '/' + m.file_path.replace(/^\//, '')"
              :alt="m.name"
            />
            <div v-else class="media-file-icon">
              <i class="bx bx-cube" v-if="m.file_path.endsWith('.glb')"></i>
              <i class="bx bx-video" v-else-if="m.file_path.endsWith('.mp4') || m.file_path.endsWith('.webm')"></i>
              <i class="bx bx-file" v-else></i>
              <span>{{ getFileExt(m.file_path) }}</span>
            </div>
          </div>

          <div class="media-details">
            <div class="media-name" :title="m.name">{{ m.name }}</div>
            <div class="media-size">{{ formatSize(m.file_size) }}</div>
          </div>

          <div class="media-footer-actions">
            <button
              class="btn-copy-path"
              :class="{ copied: copiedId === m.id }"
              @click="copyPath(m)"
              title="Sao chép đường dẫn để dán vào trang"
            >
              <i class="bx" :class="copiedId === m.id ? 'bx-check' : 'bx-copy'"></i>
              <span>{{ copiedId === m.id ? 'Đã chép' : 'Chép link' }}</span>
            </button>
            <button class="btn-delete-media" title="Xóa tệp" @click="confirmDelete(m)">
              <i class="bx bx-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div v-if="!media || media.length === 0" class="empty-state">
        <i class="bx bx-images empty-icon"></i>
        <p>Thư viện hiện chưa có tệp tải lên nào.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';

defineProps({
  media: {
    type: Array,
    default: () => [],
  },
});

const fileInputRef = ref(null);
const isDragging = ref(false);
const isUploading = ref(false);
const copiedId = ref(null);

const triggerFileInput = () => {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
};

const handleFileSelect = (e) => {
  const file = e.target.files[0];
  if (file) uploadFile(file);
};

const handleFileDrop = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer.files[0];
  if (file) uploadFile(file);
};

const uploadFile = (file) => {
  isUploading.value = true;
  const formData = new FormData();
  formData.append('file', file);

  router.post('/admin/media/upload', formData, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      isUploading.value = false;
      if (fileInputRef.value) fileInputRef.value.value = '';
    },
    onError: (err) => {
      isUploading.value = false;
      alert('Tải lên thất bại: ' + JSON.stringify(err));
    },
  });
};

const isImageUrl = (path) => {
  return /\.(png|jpe?g|gif|svg|webp)$/i.test(path);
};

const getFileExt = (path) => {
  const parts = path.split('.');
  return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
};

const formatSize = (bytes) => {
  if (!bytes) return '';
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / 1048576).toFixed(1) + ' MB';
};

const copyPath = (m) => {
  const path = m.file_path;
  navigator.clipboard.writeText(path).then(() => {
    copiedId.value = m.id;
    setTimeout(() => {
      if (copiedId.value === m.id) copiedId.value = null;
    }, 2000);
  });
};

const confirmDelete = (m) => {
  if (confirm(`Bạn có chắc chắn muốn xóa tệp "${m.name}"?`)) {
    router.delete(`/admin/media/${m.id}`, {
      preserveScroll: true,
    });
  }
};
</script>

<style src="./css/panels.css"></style>
