<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Đánh giá khách hàng</h2>
          <p class="sidebar-subtitle">Lời nhận xét từ các đối tác và thương hiệu đồng hành.</p>
        </div>
        <button class="btn-create-item" @click="openCreateModal">
          <i class="bx bx-plus"></i> Thêm mới
        </button>
      </div>
    </div>

    <div class="sidebar-panel-content">
      <div class="panel-list-item" v-for="r in reviews" :key="r.id">
        <div class="item-thumb avatar">
          <img :src="r.avatar ? (r.avatar.startsWith('http') ? r.avatar : '/' + r.avatar.replace(/^\//, '')) : '/assets/images/avatar.png'" :alt="r.author" />
        </div>
        <div class="item-info">
          <div class="item-name d-flex align-items-center gap-2">
            <span>{{ r.author }}</span>
            <div class="review-stars">
              <i v-for="star in (r.rating || 5)" :key="star" class="bx bxs-star"></i>
            </div>
          </div>
          <div class="item-meta">{{ getTrans(r.role) }}</div>
          <div class="item-quote">"{{ getTrans(r.content) }}"</div>
        </div>
        <div class="item-actions">
          <button class="btn-action edit" title="Sửa nhận xét" @click="openEditModal(r)">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" title="Xóa nhận xét" @click="confirmDelete(r)">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>

      <div v-if="!reviews || reviews.length === 0" class="empty-state">
        <i class="bx bx-star empty-icon"></i>
        <p>Chưa có đánh giá nào trong cơ sở dữ liệu.</p>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Review) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog">
        <div class="studio-modal-header">
          <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa đánh giá' : 'Thêm đánh giá mới' }}</h3>
          <button class="btn-close-modal" @click="isModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="studio-modal-body">
          <div class="modal-form-row">
            <div class="modal-form-group col-7">
              <label class="modal-label">Tên người đánh giá *</label>
              <input v-model="form.author" type="text" class="modal-input" placeholder="Nguyễn Minh Anh" required />
            </div>
            <div class="modal-form-group col-5">
              <label class="modal-label">Số sao đánh giá (1-5)</label>
              <select v-model="form.rating" class="modal-input">
                <option :value="5">5 Sao ★★★★★</option>
                <option :value="4">4 Sao ★★★★☆</option>
                <option :value="3">3 Sao ★★★☆☆</option>
              </select>
            </div>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group col-6">
              <label class="modal-label">Chức vụ / Công ty (VI)</label>
              <input v-model="form.role_vi" type="text" class="modal-input" placeholder="Giám đốc Marketing" />
            </div>
            <div class="modal-form-group col-6">
              <label class="modal-label">Chức vụ / Công ty (EN)</label>
              <input v-model="form.role_en" type="text" class="modal-input" placeholder="Marketing Director" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Đường dẫn ảnh đại diện (Avatar)</label>
            <input v-model="form.avatar" type="text" class="modal-input" placeholder="/assets/images/avatar.png" />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Nội dung đánh giá (VI) *</label>
            <textarea v-model="form.content_vi" class="modal-textarea" rows="3" placeholder="Nhận xét của khách hàng..." required></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Nội dung đánh giá (EN)</label>
            <textarea v-model="form.content_en" class="modal-textarea" rows="3" placeholder="Client testimonial in English..."></textarea>
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
  reviews: {
    type: Array,
    default: () => [],
  },
});

const isModalOpen = ref(false);
const editingItem = ref(null);
const isSubmitting = ref(false);

const form = reactive({
  author: '',
  role_vi: '',
  role_en: '',
  rating: 5,
  avatar: '',
  content_vi: '',
  content_en: '',
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const openCreateModal = () => {
  editingItem.value = null;
  form.author = '';
  form.role_vi = 'Giám đốc Marketing';
  form.role_en = 'Marketing Director';
  form.rating = 5;
  form.avatar = '/assets/images/avatar.png';
  form.content_vi = '';
  form.content_en = '';
  isModalOpen.value = true;
};

const openEditModal = (r) => {
  editingItem.value = r;
  form.author = r.author || '';
  form.role_vi = typeof r.role === 'object' ? (r.role.vi || '') : (r.role || '');
  form.role_en = typeof r.role === 'object' ? (r.role.en || '') : '';
  form.rating = r.rating || 5;
  form.avatar = r.avatar || '';
  form.content_vi = typeof r.content === 'object' ? (r.content.vi || '') : (r.content || '');
  form.content_en = typeof r.content === 'object' ? (r.content.en || '') : '';
  isModalOpen.value = true;
};

const submitForm = () => {
  isSubmitting.value = true;
  const payload = {
    author: form.author,
    role: { vi: form.role_vi, en: form.role_en || form.role_vi },
    rating: form.rating,
    avatar: form.avatar,
    content: { vi: form.content_vi, en: form.content_en || form.content_vi },
  };

  if (editingItem.value) {
    router.put(`/admin/reviews/${editingItem.value.id}`, payload, {
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
    router.post('/admin/reviews', payload, {
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

const confirmDelete = (r) => {
  if (confirm(`Bạn có chắc chắn muốn xóa nhận xét của "${r.author}" không?`)) {
    router.delete(`/admin/reviews/${r.id}`, {
      preserveScroll: true,
    });
  }
};
</script>

<style src="./css/panels.css"></style>
