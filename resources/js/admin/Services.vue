<template>
  <Layout>
    <div class="services-workspace-view" :key="renderKey">
      <div class="services-container">
        <!-- Header Card -->
        <div class="services-header-card">
          <div class="services-header-main">
            <div class="services-title-area">
              <h1 class="services-title">
                <span>Quản lý Dịch vụ</span>
              </h1>
              <p class="services-subtitle">
                Quản lý danh sách các gói dịch vụ, danh sách nội dung và hình ảnh.
              </p>
            </div>
            <div class="services-header-actions">
              <div class="services-header-search">
                <i class="bx bx-search search-icon"></i>
                <input v-model="searchQuery" type="text" class="services-header-search-input"
                  placeholder="Tìm kiếm dịch vụ..." />
                <button v-if="searchQuery" type="button" class="btn-clear-header-search" @click="searchQuery = ''"
                  title="Xóa tìm kiếm">
                  <i class="bx bx-x"></i>
                </button>
              </div>
              <button type="button" class="btn-services-create" @click="openCreateModal">
                <i class="bx bx-plus"></i> Thêm dịch vụ mới
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

        <!-- Services Grid -->
        <div v-if="filteredServices && filteredServices.length > 0" class="services-card-grid">
          <div v-for="s in filteredServices" :key="s.id" class="service-card-full">
            <div class="service-card-header">
              <div class="service-card-meta">
                <div class="service-card-num-badge">{{ formatNumber(s.number) }}</div>
                <div class="service-card-title-box">
                  <h3 class="service-card-title">{{ getTrans(s.title) }}</h3>
                </div>
              </div>
            </div>

            <div class="service-card-body">
              <div class="service-card-media">
                <img :src="getImageUrl(s.icon)" :alt="getTrans(s.title)" loading="lazy" />
              </div>

              <div class="service-card-items-wrap">
                <ul class="service-card-bullet-list">
                  <li v-for="(item, idx) in getBulletList(s)" :key="idx">
                    <span class="bullet-dot">•</span>
                    <span>{{ item }}</span>
                  </li>
                  <li v-if="getBulletList(s).length === 0" class="text-muted fst-italic">
                    Chưa có hạng mục chi tiết
                  </li>
                </ul>
              </div>
            </div>

            <div class="service-card-footer">
              <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn-action move" :disabled="isFirst(s)" @click="moveUp(s)"
                  title="Di chuyển lên trên">
                  <i class="bx bx-chevron-up"></i>
                </button>
                <button type="button" class="btn-action move" :disabled="isLast(s)" @click="moveDown(s)"
                  title="Di chuyển xuống dưới">
                  <i class="bx bx-chevron-down"></i>
                </button>
                <button type="button" class="btn-action edit" @click="openEditModal(s)" title="Chỉnh sửa dịch vụ">
                  <i class="bx bx-edit-alt"></i>
                </button>
                <button type="button" class="btn-action delete" @click="confirmDelete(s)" title="Xóa dịch vụ">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="services-empty-state">
          <i class="bx bx-layer empty-icon"></i>
          <p v-if="searchQuery">Không tìm thấy dịch vụ nào phù hợp với từ khóa "{{ searchQuery }}"</p>
          <p v-else>Chưa có gói dịch vụ nào trong cơ sở dữ liệu.</p>
          <button v-if="searchQuery" type="button" class="btn-cancel" @click="searchQuery = ''">
            Xóa tìm kiếm
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Service) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog-service">
        <div class="studio-modal-header">
          <div class="modal-header-left">
            <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới' }}</h3>
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
          <div class="modal-form-group">
            <label class="modal-label">
              Tên dịch vụ *
            </label>
            <input v-if="modalLang === 'vi'" v-model="form.title_vi" type="text" class="modal-input"
              placeholder="Ví dụ: Thiết kế từ chiến lược" required />
            <input v-else v-model="form.title_en" type="text" class="modal-input" placeholder="E.g. Strategy-led Design"
              required />
          </div>

          <div class="modal-form-group">
            <label class="modal-label">
              Danh sách hạng mục
            </label>
            <span class="modal-help-text">Mỗi dòng tương ứng với 1 gạch đầu dòng hiển thị trên website.</span>
            <textarea v-if="modalLang === 'vi'" v-model="form.description_vi" class="modal-textarea" rows="6"
              placeholder=""></textarea>
            <textarea v-else v-model="form.description_en" class="modal-textarea" rows="6" placeholder=""></textarea>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Ảnh minh họa dịch vụ</label>
            <div class="service-thumb-row">
              <div class="service-thumb-preview">
                <img v-if="form.icon" :src="getImageUrl(form.icon)" alt="Preview" />
                <div v-else class="service-thumb-empty">
                  <i class="bx bx-image"></i>
                </div>
              </div>
              <div class="service-thumb-controls">
                <div class="service-thumb-btn-row">
                  <input type="file" ref="fileInputRef" accept="image/*" style="display: none"
                    @change="handleFileUpload" />
                  <button type="button" class="btn-upload-file" :disabled="isUploading" @click="triggerFileInput">
                    <i class="bx" :class="isUploading ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                    <span>{{ isUploading ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                  </button>
                  <button v-if="form.icon" type="button" class="btn-remove-file" @click="form.icon = ''"
                    title="Xóa ảnh">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>
            </div>
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
const searchQuery = ref('');
const modalLang = ref('vi');
const fileInputRef = ref(null);
const isUploading = ref(false);

const syncFromProps = () => {
  localServices.value = JSON.parse(JSON.stringify(props.services || []));
  deletedIds.value = [];
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => props.services, syncFromProps, { immediate: true });

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
  number: '01',
  title_vi: '',
  title_en: '',
  description_vi: '',
  description_en: '',
  icon: '',
});

const formatNumber = (num) => {
  if (!num) return '01';
  return String(num).replace(/[\/\s]+$/g, '').trim();
};

const getImageUrl = (icon) => {
  if (!icon) return '/assets/images/project1.png';
  if (icon.startsWith('http')) return icon;
  return '/' + icon.replace(/^\//, '');
};

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const getBulletList = (s) => {
  const lang = currentLanguage.value || 'vi';
  if (s.content && s.content[lang] && Array.isArray(s.content[lang]) && s.content[lang].length > 0) {
    return s.content[lang];
  }
  let desc = '';
  if (s.description && typeof s.description === 'object') {
    desc = s.description[lang] || s.description.vi || '';
  } else if (typeof s.description === 'string') {
    desc = s.description;
  }
  if (!desc) return [];
  return desc
    .split('\n')
    .map((line) => line.trim())
    .filter(Boolean);
};

const filteredServices = computed(() => {
  if (!searchQuery.value.trim()) return localServices.value;
  const q = searchQuery.value.toLowerCase().trim();
  return localServices.value.filter((s) => {
    const num = String(s.number || '').toLowerCase();
    const titleVi = (typeof s.title === 'object' ? s.title?.vi : s.title || '').toLowerCase();
    const titleEn = (typeof s.title === 'object' ? s.title?.en : '').toLowerCase();
    const descVi = (typeof s.description === 'object' ? s.description?.vi : s.description || '').toLowerCase();
    const descEn = (typeof s.description === 'object' ? s.description?.en : '').toLowerCase();
    return num.includes(q) || titleVi.includes(q) || titleEn.includes(q) || descVi.includes(q) || descEn.includes(q);
  });
});

const openCreateModal = () => {
  editingItem.value = null;
  modalLang.value = currentLanguage.value || 'vi';
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
  modalLang.value = currentLanguage.value || 'vi';
  form.number = String(s.number || '01').replace('/', '');
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
    console.error('Lỗi upload file:', err);
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
    form.icon = path;
  }
  isUploading.value = false;
  e.target.value = '';
};

const isFirst = (item) => {
  return localServices.value.findIndex((s) => s.id === item.id) === 0;
};

const isLast = (item) => {
  const idx = localServices.value.findIndex((s) => s.id === item.id);
  return idx === localServices.value.length - 1;
};

const reindexServices = () => {
  localServices.value.forEach((s, i) => {
    s.number = String(i + 1).padStart(2, '0');
  });
};

const moveUp = (item) => {
  const idx = localServices.value.findIndex((s) => s.id === item.id);
  if (idx > 0) {
    const itemToMove = localServices.value.splice(idx, 1)[0];
    localServices.value.splice(idx - 1, 0, itemToMove);
    reindexServices();
    hasUnsavedChanges.value = true;
  }
};

const moveDown = (item) => {
  const idx = localServices.value.findIndex((s) => s.id === item.id);
  if (idx !== -1 && idx < localServices.value.length - 1) {
    const itemToMove = localServices.value.splice(idx, 1)[0];
    localServices.value.splice(idx + 1, 0, itemToMove);
    reindexServices();
    hasUnsavedChanges.value = true;
  }
};

const submitForm = () => {
  const currentTitle = (modalLang.value === 'en' ? form.title_en : form.title_vi) || form.title_vi || form.title_en;
  if (!currentTitle || !currentTitle.trim()) {
    notifyWarning('Vui lòng nhập tên dịch vụ!');
    return;
  }

  const payloadData = {
    number: String(form.number || '01').replace(/[\/\s]+$/g, '').trim(),
    title: {
      vi: form.title_vi || form.title_en,
      en: form.title_en || form.title_vi,
    },
    description: {
      vi: form.description_vi || form.description_en,
      en: form.description_en || form.description_vi,
    },
    icon: form.icon,
  };

  if (editingItem.value) {
    const idx = localServices.value.findIndex((s) => s.id === editingItem.value.id);
    if (idx !== -1) {
      localServices.value[idx] = {
        ...localServices.value[idx],
        ...payloadData,
      };
    }
  } else {
    localServices.value.push({
      id: 'temp_' + Date.now(),
      ...payloadData,
    });
    reindexServices();
  }

  hasUnsavedChanges.value = true;
  isModalOpen.value = false;
};

const confirmDelete = async (s) => {
  const ok = await askConfirm({
    title: 'Xóa dịch vụ',
    message: `Bạn có chắc chắn muốn xóa dịch vụ "${getTrans(s.title)}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi cơ sở dữ liệu sau khi bạn bấm "Xuất bản")`,
    confirmText: 'Xóa dịch vụ',
    variant: 'danger',
  });
  if (ok) {
    localServices.value = localServices.value.filter((item) => item.id !== s.id);
    if (typeof s.id === 'number' || (typeof s.id === 'string' && !s.id.startsWith('temp_'))) {
      deletedIds.value.push(s.id);
    }
    reindexServices();
    hasUnsavedChanges.value = true;
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
        notifySuccess('Đã xuất bản dịch vụ vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        notifyError('Lỗi xuất bản: ' + extractErrorMessage(err));
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

<style src="./css/services.css"></style>
