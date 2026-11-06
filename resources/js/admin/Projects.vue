<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Quản lý Dự án (Work)</h2>
          <p class="sidebar-subtitle">Danh sách các dự án trong cơ sở dữ liệu.</p>
        </div>
        <button class="btn-create-item" @click="openCreateModal">
          <i class="bx bx-plus"></i> Thêm mới
        </button>
      </div>
    </div>

    <div class="sidebar-panel-content">
      <div class="panel-list-item" v-for="p in projects" :key="p.id">
        <div class="item-thumb">
          <img :src="p.thumbnail ? (p.thumbnail.startsWith('http') ? p.thumbnail : '/' + p.thumbnail.replace(/^\//, '')) : '/assets/images/project1.png'" :alt="getTrans(p.title)" />
        </div>
        <div class="item-info">
          <div class="item-name d-flex align-items-center gap-2">
            <span>{{ getTrans(p.title) }}</span>
            <span v-if="p.is_featured" class="badge-featured" title="Dự án nổi bật">Nổi bật</span>
          </div>
          <div class="item-meta">{{ getTrans(p.category) }} • {{ p.year || '2026' }}</div>
        </div>
        <div class="item-actions">
          <button class="btn-action edit" title="Sửa dự án" @click="openEditModal(p)">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" title="Xóa dự án" @click="confirmDelete(p)">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>

      <div v-if="!projects || projects.length === 0" class="empty-state">
        <i class="bx bx-folder-open empty-icon"></i>
        <p>Chưa có dự án nào. Hãy bấm "Thêm mới" để bắt đầu.</p>
      </div>
    </div>

    <!-- Modal Form (Add / Edit) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog">
        <div class="studio-modal-header">
          <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa dự án' : 'Thêm dự án mới' }}</h3>
          <button class="btn-close-modal" @click="isModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="studio-modal-body">
          <div class="modal-form-group">
            <label class="modal-label">Tên dự án (VI) *</label>
            <input v-model="form.title_vi" type="text" class="modal-input" placeholder="Ví dụ: Tiên Phong Identity" required />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tên dự án (EN)</label>
            <input v-model="form.title_en" type="text" class="modal-input" placeholder="Ví dụ: Tien Phong Identity" />
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Đường dẫn tĩnh (Slug) *</label>
              <input v-model="form.slug" type="text" class="modal-input" placeholder="tien-phong-identity" required />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Năm thực hiện</label>
              <input v-model="form.year" type="text" class="modal-input" placeholder="2026" />
            </div>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Danh mục (VI)</label>
              <input v-model="form.category_vi" type="text" class="modal-input" placeholder="Nhận diện thương hiệu" />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Danh mục (EN)</label>
              <input v-model="form.category_en" type="text" class="modal-input" placeholder="Brand identity" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn ảnh Thumbnail</label>
            <input v-model="form.thumbnail" type="text" class="modal-input" placeholder="/assets/images/project1.png hoặc URL ảnh" />
            <div v-if="form.thumbnail" class="modal-img-preview mt-2">
              <img :src="form.thumbnail.startsWith('http') ? form.thumbnail : '/' + form.thumbnail.replace(/^\//, '')" alt="Preview" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Mô tả ngắn (VI)</label>
            <textarea v-model="form.description_vi" class="modal-textarea" rows="2" placeholder="Tóm tắt về dự án..."></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-checkbox-label">
              <input v-model="form.is_featured" type="checkbox" />
              <span>Đánh dấu là dự án nổi bật (Hiển thị tại trang chủ)</span>
            </label>
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
import { ref, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import { currentLanguage } from './composables/useAdmin';

const props = defineProps({
  projects: {
    type: Array,
    default: () => [],
  },
});

const isModalOpen = ref(false);
const editingItem = ref(null);
const isSubmitting = ref(false);

const form = reactive({
  title_vi: '',
  title_en: '',
  slug: '',
  category_vi: '',
  category_en: '',
  year: '2026',
  client: '',
  thumbnail: '',
  description_vi: '',
  description_en: '',
  is_featured: false,
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const openCreateModal = () => {
  editingItem.value = null;
  form.title_vi = '';
  form.title_en = '';
  form.slug = '';
  form.category_vi = 'Brand identity';
  form.category_en = 'Brand identity';
  form.year = '2026';
  form.client = '';
  form.thumbnail = '/assets/images/project1.png';
  form.description_vi = '';
  form.description_en = '';
  form.is_featured = false;
  isModalOpen.value = true;
};

const openEditModal = (p) => {
  editingItem.value = p;
  form.title_vi = typeof p.title === 'object' ? (p.title.vi || '') : (p.title || '');
  form.title_en = typeof p.title === 'object' ? (p.title.en || '') : '';
  form.slug = p.slug || '';
  form.category_vi = typeof p.category === 'object' ? (p.category.vi || '') : (p.category || '');
  form.category_en = typeof p.category === 'object' ? (p.category.en || '') : '';
  form.year = p.year || '2026';
  form.client = p.client || '';
  form.thumbnail = p.thumbnail || '';
  form.description_vi = typeof p.description === 'object' ? (p.description.vi || '') : (p.description || '');
  form.description_en = typeof p.description === 'object' ? (p.description.en || '') : '';
  form.is_featured = !!p.is_featured;
  isModalOpen.value = true;
};

// Auto generate slug from Vietnamese title if empty
watch(
  () => form.title_vi,
  (newTitle) => {
    if (!editingItem.value && newTitle) {
      form.slug = newTitle
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[đĐ]/g, 'd')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }
  }
);

const submitForm = () => {
  isSubmitting.value = true;
  const payload = {
    title: { vi: form.title_vi, en: form.title_en || form.title_vi },
    slug: form.slug,
    category: { vi: form.category_vi, en: form.category_en || form.category_vi },
    year: form.year,
    client: form.client,
    thumbnail: form.thumbnail,
    description: { vi: form.description_vi, en: form.description_en || form.description_vi },
    is_featured: form.is_featured,
  };

  if (editingItem.value) {
    router.put(`/admin/projects/${editingItem.value.id}`, payload, {
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
    router.post('/admin/projects', payload, {
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

const confirmDelete = (p) => {
  if (confirm(`Bạn có chắc chắn muốn xóa dự án "${getTrans(p.title)}" không?`)) {
    router.delete(`/admin/projects/${p.id}`, {
      preserveScroll: true,
    });
  }
};
</script>

<style src="./css/panels.css"></style>
