<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Dịch vụ cốt lõi</h2>
          <p class="sidebar-subtitle">Các gói dịch vụ doanh nghiệp hiển thị tại trang chủ.</p>
        </div>
        <button class="btn-create-item" @click="openCreateModal">
          <i class="bx bx-plus"></i> Thêm mới
        </button>
      </div>
    </div>

    <!-- Staging Alert Banner when changes are pending publish -->
    <div v-if="hasUnsavedChanges" class="staging-alert-banner mx-3 mt-2">
      <div class="banner-info">
        <span>Có thay đổi chưa lưu</span>
      </div>
      <div class="banner-buttons">
        <button type="button" class="btn-banner-revert" @click="revertChanges">Hủy thay đổi</button>
        <button type="button" class="btn-banner-publish" :disabled="isSaving" @click="publishServices">
          {{ isSaving ? 'Đang lưu...' : 'Xuất bản ngay' }}
        </button>
      </div>
    </div>

    <div class="sidebar-panel-content" :key="renderKey">
      <div class="panel-list-item" v-for="s in localServices" :key="s.id">
        <div class="item-badge">{{ s.number }}</div>
        <div class="item-info">
          <div class="item-name d-flex align-items-center gap-2">
            <span>{{ getTrans(s.title) }}</span>
            <span v-if="s._isNew" class="badge-staging-new" title="Chưa xuất bản">Mới</span>
            <span v-else-if="s._isModified" class="badge-staging-modified" title="Đã chỉnh sửa">Đã sửa</span>
          </div>
          <div class="item-meta">{{ getTrans(s.description) }}</div>
        </div>
        <div class="item-actions">
          <button class="btn-action edit" title="Sửa dịch vụ" @click="openEditModal(s)">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" title="Xóa dịch vụ" @click="confirmDelete(s)">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>

      <div v-if="!localServices || localServices.length === 0" class="empty-state">
        <i class="bx bx-layer empty-icon"></i>
        <p>Chưa có dịch vụ nào trong cơ sở dữ liệu.</p>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Service) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog">
        <div class="studio-modal-header">
          <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới' }}</h3>
          <button class="btn-close-modal" @click="isModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="studio-modal-body">
          <div class="modal-form-group">
            <label class="modal-label">Số thứ tự hiển thị (Ví dụ: 01, 02, 03) *</label>
            <input v-model="form.number" type="text" class="modal-input" placeholder="01" required />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tên dịch vụ (VI) *</label>
            <input v-model="form.title_vi" type="text" class="modal-input" placeholder="Thiết kế từ chiến lược" required />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tên dịch vụ (EN)</label>
            <input v-model="form.title_en" type="text" class="modal-input" placeholder="Strategic Design" />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Danh sách dịch vụ / Bullet points (VI) — Mỗi dòng 1 mục</label>
            <textarea v-model="form.description_vi" class="modal-textarea" rows="5" placeholder="Định vị thương hiệu&#10;Câu chuyện thương hiệu&#10;Đặt tên thương hiệu..."></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Danh sách dịch vụ / Bullet points (EN) — Mỗi dòng 1 mục</label>
            <textarea v-model="form.description_en" class="modal-textarea" rows="5" placeholder="Brand positioning&#10;Brand story&#10;Brand naming..."></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn ảnh minh họa</label>
            <input v-model="form.icon" type="text" class="modal-input" placeholder="/assets/images/project1.png" />
          </div>

          <div class="studio-modal-footer">
            <button type="button" class="btn-cancel" @click="isModalOpen = false">Hủy</button>
            <button type="submit" class="btn-submit">
              {{ editingItem ? 'Áp dụng thay đổi' : 'Thêm vào danh sách' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import {
  currentLanguage,
  hasUnsavedChanges,
  isSaving,
  onSaveHandler,
  onDraftHandler,
} from './composables/useAdmin';

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
});

const localServices = ref([]);
const deletedIds = ref([]);
const isModalOpen = ref(false);
const editingItem = ref(null);
const renderKey = ref(0);

const syncFromProps = () => {
  localServices.value = JSON.parse(JSON.stringify(props.services || []));
  deletedIds.value = [];
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => props.services, syncFromProps, { immediate: true });

const form = reactive({
  number: '01',
  title_vi: '',
  title_en: '',
  description_vi: '',
  description_en: '',
  icon: '',
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const openCreateModal = () => {
  editingItem.value = null;
  form.number = String(localServices.value.length + 1).padStart(2, '0');
  form.title_vi = '';
  form.title_en = '';
  form.description_vi = '';
  form.description_en = '';
  form.icon = '/assets/images/project1.png';
  isModalOpen.value = true;
};

const openEditModal = (s) => {
  editingItem.value = s;
  form.number = s.number || '01';
  form.title_vi = typeof s.title === 'object' ? (s.title?.vi || '') : (s.title || '');
  form.title_en = typeof s.title === 'object' ? (s.title?.en || '') : '';

  let descVi = typeof s.description === 'object' ? (s.description?.vi || '') : (s.description || '');
  if (!descVi && s.content && s.content.vi && Array.isArray(s.content.vi)) {
    descVi = s.content.vi.join('\n');
  }
  let descEn = typeof s.description === 'object' ? (s.description?.en || '') : '';
  if (!descEn && s.content && s.content.en && Array.isArray(s.content.en)) {
    descEn = s.content.en.join('\n');
  }

  form.description_vi = descVi;
  form.description_en = descEn;
  form.icon = s.icon || '';
  isModalOpen.value = true;
};

const submitForm = () => {
  const payloadData = {
    number: form.number,
    title: { vi: form.title_vi, en: form.title_en || form.title_vi },
    description: { vi: form.description_vi, en: form.description_en || form.description_vi },
    icon: form.icon,
  };

  if (editingItem.value) {
    const idx = localServices.value.findIndex((s) => s.id === editingItem.value.id);
    if (idx !== -1) {
      localServices.value[idx] = {
        ...localServices.value[idx],
        ...payloadData,
        _isModified: true,
      };
    }
  } else {
    localServices.value.push({
      id: 'temp_' + Date.now(),
      ...payloadData,
      _isNew: true,
    });
  }

  hasUnsavedChanges.value = true;
  isModalOpen.value = false;
};

const confirmDelete = (s) => {
  if (confirm(`Bạn có chắc chắn muốn xóa dịch vụ "${getTrans(s.title)}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi database sau khi bạn bấm "Xuất bản")`)) {
    localServices.value = localServices.value.filter((item) => item.id !== s.id);
    if (typeof s.id === 'number' || (typeof s.id === 'string' && !s.id.startsWith('temp_'))) {
      deletedIds.value.push(s.id);
    }
    hasUnsavedChanges.value = true;
  }
};

const revertChanges = () => {
  if (confirm('Hủy bỏ các thay đổi vừa làm trên màn hình và quay lại dữ liệu gốc trong database?')) {
    syncFromProps();
  }
};

const publishServices = () => {
  isSaving.value = true;
  router.post(
    '/admin/services/publish',
    {
      items: localServices.value,
      deleted_ids: deletedIds.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        hasUnsavedChanges.value = false;
        alert('Đã xuất bản dịch vụ vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        alert('Lỗi xuất bản: ' + JSON.stringify(err));
      },
    }
  );
};

onMounted(() => {
  onSaveHandler.value = publishServices;
  onDraftHandler.value = publishServices;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/panels.css"></style>
