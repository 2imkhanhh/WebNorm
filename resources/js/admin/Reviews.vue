<template>
  <Layout>
    <div class="reviews-workspace-view" :key="renderKey">
      <div class="reviews-container">
        <!-- Header Card -->
        <div class="reviews-header-card">
          <div class="reviews-header-main">
            <div class="reviews-title-area">
              <h1 class="reviews-title">
                <span>Quản lý Đánh giá</span>
              </h1>
              <p class="reviews-subtitle">
                Quản lý danh sách nhận xét, đối tác và thương hiệu hiển thị tại trang chủ.
              </p>
            </div>
            <div class="reviews-header-actions">
              <div class="reviews-header-search">
                <i class="bx bx-search search-icon"></i>
                <input v-model="searchQuery" type="text" class="reviews-header-search-input"
                  placeholder="Tìm kiếm đánh giá..." />
                <button v-if="searchQuery" type="button" class="btn-clear-header-search" @click="searchQuery = ''"
                  title="Xóa tìm kiếm">
                  <i class="bx bx-x"></i>
                </button>
              </div>
              <button type="button" class="btn-reviews-create" @click="openCreateModal">
                <i class="bx bx-plus"></i> Thêm đánh giá mới
              </button>
            </div>
          </div>
        </div>

        <!-- Staging Alert Banner when changes are pending publish -->
        <div v-if="hasUnsavedChanges" class="staging-alert-banner">
          <div class="banner-info">
            <i class="bx bx-info-circle banner-icon"></i>
            <span>Có thay đổi chưa lưu</span>
          </div>
        </div>

        <!-- Reviews Card Grid -->
        <div v-if="filteredReviews && filteredReviews.length > 0" class="reviews-card-grid">
          <div v-for="r in filteredReviews" :key="r.id" class="review-card-full">
            <div class="review-card-header">
              <div class="review-card-meta">
                <div class="review-card-avatar">
                  <img :src="getImageUrl(r.avatar)" :alt="r.author" loading="lazy" />
                </div>
                <div class="review-card-author-box">
                  <h3 class="review-card-author">{{ r.author }}</h3>
                  <div class="review-card-role">{{ getTrans(r.role) }}</div>
                </div>
              </div>

              <div class="review-card-stars">
                <i v-for="star in (r.rating || 5)" :key="star" class="bx bxs-star"></i>
              </div>
            </div>

            <div class="review-card-body">
              <p class="review-card-quote">“{{ getTrans(r.content) }}”</p>
            </div>

            <div class="service-card-footer">
              <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn-action move" :disabled="isFirst(r)" @click="moveUp(r)"
                  title="Di chuyển lên trên">
                  <i class="bx bx-chevron-up"></i>
                </button>
                <button type="button" class="btn-action move" :disabled="isLast(r)" @click="moveDown(r)"
                  title="Di chuyển xuống dưới">
                  <i class="bx bx-chevron-down"></i>
                </button>
                <button type="button" class="btn-action edit" @click="openEditModal(r)" title="Chỉnh sửa">
                  <i class="bx bx-edit-alt"></i>
                </button>
                <button type="button" class="btn-action delete" @click="confirmDelete(r)" title="Xóa">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="reviews-empty-state">
          <i class="bx bx-star empty-icon"></i>
          <p v-if="searchQuery">Không tìm thấy đánh giá nào phù hợp với từ khóa "{{ searchQuery }}"</p>
          <p v-else>Chưa có đánh giá nào trong cơ sở dữ liệu.</p>
          <button v-if="searchQuery" type="button" class="btn-cancel" @click="searchQuery = ''">
            Xóa tìm kiếm
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Review) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog-review">
        <div class="studio-modal-header">
          <div class="modal-header-left">
            <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa đánh giá' : 'Thêm đánh giá mới' }}</h3>
          </div>
          <div class="modal-header-right">
            <!-- Language Switcher in Modal -->
            <div class="modal-lang-switcher">
              <button type="button" class="btn-modal-lang" :class="{ active: modalLang === 'vi' }"
                @click="modalLang = 'vi'" title="Tiếng Việt">
                VI
              </button>
              <button type="button" class="btn-modal-lang" :class="{ active: modalLang === 'en' }"
                @click="modalLang = 'en'" title="English">
                EN
              </button>
            </div>
            <button type="button" class="btn-close-modal" @click="isModalOpen = false">
              <i class="bx bx-x"></i>
            </button>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="studio-modal-body">
          <div class="modal-form-row">
            <div class="modal-form-group">
              <label class="modal-label">Tên người đánh giá *</label>
              <input v-model="form.author" type="text" class="modal-input" placeholder="Ví dụ: Nguyễn Minh Anh"
                required />
            </div>
            <div class="modal-form-group">
              <label class="modal-label">Số sao đánh giá</label>
              <select v-model="form.rating" class="modal-input">
                <option :value="5">5 Sao</option>
                <option :value="4">4 Sao</option>
                <option :value="3">3 Sao</option>
                <option :value="2">2 Sao</option>
                <option :value="1">1 Sao</option>
              </select>
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">
              Chức vụ / Doanh nghiệp ({{ modalLang.toUpperCase() }})
            </label>
            <input v-if="modalLang === 'vi'" v-model="form.role_vi" type="text" class="modal-input"
              placeholder="Ví dụ: Giám đốc Marketing" />
            <input v-else v-model="form.role_en" type="text" class="modal-input"
              placeholder="E.g. Marketing Director" />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Ảnh đại diện (Avatar)</label>
            <div class="avatar-upload-row">
              <div class="avatar-preview-circle">
                <img v-if="form.avatar" :src="getImageUrl(form.avatar)" alt="Avatar" />
                <div v-else class="avatar-empty-icon">
                  <i class="bx bx-user"></i>
                </div>
              </div>
              <div class="avatar-btn-row">
                <input type="file" ref="fileInputRef" accept="image/*" style="display: none"
                  @change="handleFileUpload" />
                <button type="button" class="btn-upload-file" :disabled="isUploading" @click="triggerFileInput">
                  <i class="bx" :class="isUploading ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isUploading ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                </button>
                <button v-if="form.avatar" type="button" class="btn-remove-file" @click="form.avatar = ''"
                  title="Xóa ảnh">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">
              Nội dung nhận xét ({{ modalLang.toUpperCase() }}) *
            </label>
            <textarea v-if="modalLang === 'vi'" v-model="form.content_vi" class="modal-textarea" rows="4"
              placeholder="Nhập nội dung nhận xét của khách hàng..." required></textarea>
            <textarea v-else v-model="form.content_en" class="modal-textarea" rows="4"
              placeholder="Client testimonial in English..." required></textarea>
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
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import {
  currentLanguage,
  hasUnsavedChanges,
  isSaving,
  onSaveHandler,
  onDraftHandler,
} from './composables/useAdmin';
import {
  askConfirm,
  extractErrorMessage,
  notifyError,
  notifySuccess,
  notifyWarning,
} from './composables/useFeedback';

const props = defineProps({
  reviews: {
    type: Array,
    default: () => [],
  },
});

const localReviews = ref([]);
const deletedIds = ref([]);
const isModalOpen = ref(false);
const editingItem = ref(null);
const renderKey = ref(0);
const searchQuery = ref('');
const modalLang = ref('vi');
const fileInputRef = ref(null);
const isUploading = ref(false);

const syncFromProps = () => {
  localReviews.value = JSON.parse(JSON.stringify(props.reviews || []));
  deletedIds.value = [];
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => props.reviews, syncFromProps, { immediate: true });

watch(
  currentLanguage,
  (newLang) => {
    if (newLang) {
      modalLang.value = newLang;
    }
  },
  { immediate: true }
);

const form = reactive({
  author: '',
  role_vi: '',
  role_en: '',
  rating: 5,
  avatar: '',
  content_vi: '',
  content_en: '',
});

const getImageUrl = (avatar) => {
  if (!avatar) return '/assets/images/avatar.png';
  if (avatar.startsWith('http')) return avatar;
  return '/' + avatar.replace(/^\//, '');
};

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const filteredReviews = computed(() => {
  if (!searchQuery.value.trim()) return localReviews.value;
  const q = searchQuery.value.toLowerCase().trim();
  return localReviews.value.filter((r) => {
    const author = (r.author || '').toLowerCase();
    const roleVi = (typeof r.role === 'object' ? r.role?.vi : r.role || '').toLowerCase();
    const roleEn = (typeof r.role === 'object' ? r.role?.en : '').toLowerCase();
    const contentVi = (typeof r.content === 'object' ? r.content?.vi : r.content || '').toLowerCase();
    const contentEn = (typeof r.content === 'object' ? r.content?.en : '').toLowerCase();
    return author.includes(q) || roleVi.includes(q) || roleEn.includes(q) || contentVi.includes(q) || contentEn.includes(q);
  });
});

const isFirst = (item) => {
  return localReviews.value.findIndex((r) => r.id === item.id) === 0;
};

const isLast = (item) => {
  const idx = localReviews.value.findIndex((r) => r.id === item.id);
  return idx === localReviews.value.length - 1;
};

const moveUp = (item) => {
  const idx = localReviews.value.findIndex((r) => r.id === item.id);
  if (idx > 0) {
    const itemToMove = localReviews.value.splice(idx, 1)[0];
    localReviews.value.splice(idx - 1, 0, itemToMove);
    hasUnsavedChanges.value = true;
  }
};

const moveDown = (item) => {
  const idx = localReviews.value.findIndex((r) => r.id === item.id);
  if (idx !== -1 && idx < localReviews.value.length - 1) {
    const itemToMove = localReviews.value.splice(idx, 1)[0];
    localReviews.value.splice(idx + 1, 0, itemToMove);
    hasUnsavedChanges.value = true;
  }
};

const openCreateModal = () => {
  editingItem.value = null;
  modalLang.value = currentLanguage.value || 'vi';
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
  modalLang.value = currentLanguage.value || 'vi';
  form.author = r.author || '';
  form.role_vi = typeof r.role === 'object' ? (r.role.vi || '') : (r.role || '');
  form.role_en = typeof r.role === 'object' ? (r.role.en || '') : '';
  form.rating = r.rating || 5;
  form.avatar = r.avatar || '';
  form.content_vi = typeof r.content === 'object' ? (r.content.vi || '') : (r.content || '');
  form.content_en = typeof r.content === 'object' ? (r.content.en || '') : '';
  isModalOpen.value = true;
};

const triggerFileInput = () => {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
};

const uploadSingleFile = async (file) => {
  if (file.size > 50 * 1024 * 1024) {
    notifyWarning('Dung lượng tệp không được vượt quá 50MB!');
    return null;
  }
  const formData = new FormData();
  formData.append('file', file);
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  try {
    const res = await fetch('/admin/media/upload', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
    });
    const data = await res.json();
    if (data.url) return data.url;
    if (data.path) return data.path;
    return null;
  } catch (err) {
    console.error('Lỗi tải ảnh:', err);
    notifyError('Lỗi tải ảnh lên máy chủ!');
    return null;
  }
};

const handleFileUpload = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  isUploading.value = true;
  const path = await uploadSingleFile(file);
  if (path) {
    form.avatar = path;
  }
  isUploading.value = false;
  e.target.value = '';
};

const submitForm = () => {
  if (!form.author || !form.author.trim()) {
    notifyWarning('Vui lòng nhập tên người đánh giá!');
    return;
  }

  const currentContent = (modalLang.value === 'en' ? form.content_en : form.content_vi) || form.content_vi || form.content_en;
  if (!currentContent || !currentContent.trim()) {
    notifyWarning('Vui lòng nhập nội dung đánh giá!');
    return;
  }

  const payloadData = {
    author: form.author,
    role: {
      vi: form.role_vi || form.role_en,
      en: form.role_en || form.role_vi,
    },
    rating: form.rating,
    avatar: form.avatar,
    content: {
      vi: form.content_vi || form.content_en,
      en: form.content_en || form.content_vi,
    },
  };

  if (editingItem.value) {
    const idx = localReviews.value.findIndex((r) => r.id === editingItem.value.id);
    if (idx !== -1) {
      localReviews.value[idx] = {
        ...localReviews.value[idx],
        ...payloadData,
      };
    }
  } else {
    localReviews.value.push({
      id: 'temp_' + Date.now(),
      ...payloadData,
    });
  }

  hasUnsavedChanges.value = true;
  isModalOpen.value = false;
};

const confirmDelete = async (r) => {
  const ok = await askConfirm({
    title: 'Xóa đánh giá',
    message: `Bạn có chắc chắn muốn xóa nhận xét của "${r.author}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi cơ sở dữ liệu sau khi bạn bấm "Xuất bản")`,
    confirmText: 'Xóa đánh giá',
    variant: 'danger',
  });
  if (ok) {
    localReviews.value = localReviews.value.filter((item) => item.id !== r.id);
    if (typeof r.id === 'number' || (typeof r.id === 'string' && !r.id.startsWith('temp_'))) {
      deletedIds.value.push(r.id);
    }
    hasUnsavedChanges.value = true;
  }
};

const publishReviews = () => {
  isSaving.value = true;
  router.post(
    '/admin/reviews/publish',
    {
      items: localReviews.value,
      deleted_ids: deletedIds.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        hasUnsavedChanges.value = false;
        notifySuccess('Đã xuất bản đánh giá vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        notifyError('Lỗi xuất bản: ' + extractErrorMessage(err));
      },
    }
  );
};

onMounted(() => {
  onSaveHandler.value = publishReviews;
  onDraftHandler.value = publishReviews;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/reviews.css"></style>
