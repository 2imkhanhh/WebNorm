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

    <div class="sidebar-panel-content">
      <div class="panel-list-item" v-for="s in services" :key="s.id">
        <div class="item-badge">{{ s.number }}</div>
        <div class="item-info">
          <div class="item-name">{{ getTrans(s.title) }}</div>
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

      <div v-if="!services || services.length === 0" class="empty-state">
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
            <label class="modal-label">Mô tả tóm tắt (VI)</label>
            <textarea v-model="form.description_vi" class="modal-textarea" rows="2" placeholder="Định hình nền tảng bản sắc thương hiệu vững chắc..."></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Mô tả tóm tắt (EN)</label>
            <textarea v-model="form.description_en" class="modal-textarea" rows="2" placeholder="Building solid foundation for brand identity..."></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn ảnh minh họa</label>
            <input v-model="form.icon" type="text" class="modal-input" placeholder="/assets/images/project1.png" />
          </div>

          <div class="studio-modal-footer">
            <button type="button" class="btn-cancel" @click="isModalOpen = false">Hủy</button>
            <button type="submit" class="btn-submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Đang lưu...' : (editingItem ? 'Cập nhật' : 'Thêm mới') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import { currentLanguage } from './composables/useAdmin';

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
});

const isModalOpen = ref(false);
const editingItem = ref(null);
const isSubmitting = ref(false);

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
  form.number = String(props.services.length + 1).padStart(2, '0');
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
  form.title_vi = typeof s.title === 'object' ? (s.title.vi || '') : (s.title || '');
  form.title_en = typeof s.title === 'object' ? (s.title.en || '') : '';
  form.description_vi = typeof s.description === 'object' ? (s.description.vi || '') : (s.description || '');
  form.description_en = typeof s.description === 'object' ? (s.description.en || '') : '';
  form.icon = s.icon || '';
  isModalOpen.value = true;
};

const submitForm = () => {
  isSubmitting.value = true;
  const payload = {
    number: form.number,
    title: { vi: form.title_vi, en: form.title_en || form.title_vi },
    description: { vi: form.description_vi, en: form.description_en || form.description_vi },
    icon: form.icon,
  };

  if (editingItem.value) {
    router.put(`/admin/services/${editingItem.value.id}`, payload, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false;
        isModalOpen.value = false;
      },
      onError: () => {
        isSubmitting.value = false;
      },
    });
  } else {
    router.post('/admin/services', payload, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false;
        isModalOpen.value = false;
      },
      onError: () => {
        isSubmitting.value = false;
      },
    });
  }
};

const confirmDelete = (s) => {
  if (confirm(`Bạn có chắc chắn muốn xóa dịch vụ "${getTrans(s.title)}" không?`)) {
    router.delete(`/admin/services/${s.id}`, {
      preserveScroll: true,
    });
  }
};
</script>

<style src="./css/panels.css"></style>
