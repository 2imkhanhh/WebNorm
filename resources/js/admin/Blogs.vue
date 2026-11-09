<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Tin tức & Bài viết</h2>
          <p class="sidebar-subtitle">Quản lý các bài viết trên trang Góc nhìn / Tin tức.</p>
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
        <button type="button" class="btn-banner-publish" :disabled="isSaving" @click="publishBlogs">
          {{ isSaving ? 'Đang lưu...' : 'Xuất bản ngay' }}
        </button>
      </div>
    </div>

    <div class="sidebar-panel-content" :key="renderKey">
      <div class="panel-list-item" v-for="b in localBlogs" :key="b.id">
        <div class="item-thumb">
          <img :src="b.thumbnail ? (b.thumbnail.startsWith('http') ? b.thumbnail : '/' + b.thumbnail.replace(/^\//, '')) : '/assets/images/news1.png'" :alt="getTrans(b.title)" />
        </div>
        <div class="item-info">
          <div class="item-name d-flex align-items-center gap-2">
            <span>{{ getTrans(b.title) }}</span>
            <span v-if="b._isNew" class="badge-staging-new" title="Chưa xuất bản">Mới</span>
            <span v-else-if="b._isModified" class="badge-staging-modified" title="Đã chỉnh sửa">Đã sửa</span>
          </div>
          <div class="item-meta">
            {{ getTrans(b.category) }} • {{ formatDate(b.published_at) }}
            <span class="badge-status-dot" :class="b.status">{{ b.status === 'published' ? 'Đã xuất bản' : 'Bản nháp' }}</span>
          </div>
        </div>
        <div class="item-actions">
          <button class="btn-action edit" title="Sửa bài viết" @click="openEditModal(b)">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" title="Xóa bài viết" @click="confirmDelete(b)">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>

      <div v-if="!localBlogs || localBlogs.length === 0" class="empty-state">
        <i class="bx bx-news empty-icon"></i>
        <p>Chưa có bài viết nào trong cơ sở dữ liệu.</p>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Blog) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog">
        <div class="studio-modal-header">
          <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới' }}</h3>
          <button class="btn-close-modal" @click="isModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="studio-modal-body">
          <div class="modal-form-group">
            <label class="modal-label">Tiêu đề bài viết (VI) *</label>
            <input v-model="form.title_vi" type="text" class="modal-input" placeholder="Tiêu đề bài viết tiếng Việt" required />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tiêu đề bài viết (EN)</label>
            <input v-model="form.title_en" type="text" class="modal-input" placeholder="English article title" />
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Đường dẫn tĩnh (Slug) *</label>
              <input v-model="form.slug" type="text" class="modal-input" placeholder="tieu-de-bai-viet" required />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Trạng thái</label>
              <select v-model="form.status" class="modal-input">
                <option value="published">Xuất bản (Hiển thị ngay)</option>
                <option value="draft">Bản nháp</option>
              </select>
            </div>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Danh mục (VI)</label>
              <input v-model="form.category_vi" type="text" class="modal-input" placeholder="Góc nhìn sáng tạo" />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Tác giả</label>
              <input v-model="form.author" type="text" class="modal-input" placeholder="Norm Editorial" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn ảnh bìa (Thumbnail)</label>
            <input v-model="form.thumbnail" type="text" class="modal-input" placeholder="/assets/images/news1.png" />
            <div v-if="form.thumbnail" class="modal-img-preview mt-2">
              <img :src="form.thumbnail.startsWith('http') ? form.thumbnail : '/' + form.thumbnail.replace(/^\//, '')" alt="Preview" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Tóm tắt ngắn (VI)</label>
            <textarea v-model="form.excerpt_vi" class="modal-textarea" rows="2" placeholder="Tóm tắt ngắn về bài viết..."></textarea>
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
  blogs: {
    type: Array,
    default: () => [],
  },
});

const localBlogs = ref([]);
const deletedIds = ref([]);
const isModalOpen = ref(false);
const editingItem = ref(null);
const renderKey = ref(0);

const syncFromProps = () => {
  localBlogs.value = JSON.parse(JSON.stringify(props.blogs || []));
  deletedIds.value = [];
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => props.blogs, syncFromProps, { immediate: true });

const form = reactive({
  title_vi: '',
  title_en: '',
  slug: '',
  category_vi: '',
  category_en: '',
  thumbnail: '',
  excerpt_vi: '',
  excerpt_en: '',
  author: 'Norm Creative',
  status: 'published',
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN');
};

const openCreateModal = () => {
  editingItem.value = null;
  form.title_vi = '';
  form.title_en = '';
  form.slug = '';
  form.category_vi = 'Góc nhìn sáng tạo';
  form.category_en = 'Creative Perspectives';
  form.thumbnail = '/assets/images/news1.png';
  form.excerpt_vi = '';
  form.excerpt_en = '';
  form.author = 'Norm Creative';
  form.status = 'published';
  isModalOpen.value = true;
};

const openEditModal = (b) => {
  editingItem.value = b;
  form.title_vi = typeof b.title === 'object' ? (b.title.vi || '') : (b.title || '');
  form.title_en = typeof b.title === 'object' ? (b.title.en || '') : '';
  form.slug = b.slug || '';
  form.category_vi = typeof b.category === 'object' ? (b.category.vi || '') : (b.category || '');
  form.category_en = typeof b.category === 'object' ? (b.category.en || '') : '';
  form.thumbnail = b.thumbnail || '';
  form.excerpt_vi = typeof b.excerpt === 'object' ? (b.excerpt.vi || '') : (b.excerpt || '');
  form.excerpt_en = typeof b.excerpt === 'object' ? (b.excerpt.en || '') : '';
  form.author = b.author || 'Norm Creative';
  form.status = b.status || 'published';
  isModalOpen.value = true;
};

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
  const payloadData = {
    title: { vi: form.title_vi, en: form.title_en || form.title_vi },
    slug: form.slug,
    category: { vi: form.category_vi, en: form.category_en || form.category_vi },
    thumbnail: form.thumbnail,
    excerpt: { vi: form.excerpt_vi, en: form.excerpt_en || form.excerpt_vi },
    author: form.author,
    status: form.status,
    published_at: editingItem.value ? editingItem.value.published_at : new Date().toISOString(),
  };

  if (editingItem.value) {
    const idx = localBlogs.value.findIndex((b) => b.id === editingItem.value.id);
    if (idx !== -1) {
      localBlogs.value[idx] = {
        ...localBlogs.value[idx],
        ...payloadData,
        _isModified: true,
      };
    }
  } else {
    localBlogs.value.unshift({
      id: 'temp_' + Date.now(),
      ...payloadData,
      _isNew: true,
    });
  }

  hasUnsavedChanges.value = true;
  isModalOpen.value = false;
};

const confirmDelete = (b) => {
  if (confirm(`Bạn có chắc chắn muốn xóa bài viết "${getTrans(b.title)}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi database sau khi bạn bấm "Xuất bản")`)) {
    localBlogs.value = localBlogs.value.filter((item) => item.id !== b.id);
    if (typeof b.id === 'number' || (typeof b.id === 'string' && !b.id.startsWith('temp_'))) {
      deletedIds.value.push(b.id);
    }
    hasUnsavedChanges.value = true;
  }
};

const revertChanges = () => {
  if (confirm('Hủy bỏ các thay đổi vừa làm trên màn hình và quay lại dữ liệu gốc trong database?')) {
    syncFromProps();
  }
};

const publishBlogs = () => {
  isSaving.value = true;
  router.post(
    '/admin/news/publish',
    {
      items: localBlogs.value,
      deleted_ids: deletedIds.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        hasUnsavedChanges.value = false;
        alert('Đã xuất bản bài viết vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        alert('Lỗi xuất bản: ' + JSON.stringify(err));
      },
    }
  );
};

onMounted(() => {
  onSaveHandler.value = publishBlogs;
  onDraftHandler.value = publishBlogs;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/panels.css"></style>
