<template>
  <Layout>
    <div class="blogs-workspace-view" :key="renderKey">
      <div class="blogs-container">
        <!-- Header Card -->
        <div class="blogs-header-card">
          <div class="blogs-header-main">
            <div class="blogs-title-area">
              <h1 class="blogs-title">
                <span>Quản lý Bài viết & Tin tức</span>
              </h1>
              <p class="blogs-subtitle">
                Quản lý các bài viết, chia sẻ thương hiệu hiển thị trên trang Bài viết.
              </p>
            </div>
            <div class="blogs-header-actions">
              <div class="blogs-header-search">
                <i class="bx bx-search search-icon"></i>
                <input v-model="searchQuery" type="text" class="blogs-header-search-input"
                  placeholder="Tìm kiếm bài viết..." />
                <button v-if="searchQuery" type="button" class="btn-clear-header-search" @click="searchQuery = ''"
                  title="Xóa tìm kiếm">
                  <i class="bx bx-x"></i>
                </button>
              </div>
              <button type="button" class="btn-blogs-create" @click="openCreateModal">
                <i class="bx bx-plus"></i> Thêm bài viết mới
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

        <!-- Blogs Card Grid -->
        <div v-if="filteredBlogs && filteredBlogs.length > 0" class="blogs-card-grid">
          <div v-for="b in filteredBlogs" :key="b.id" class="blog-card-full">
            <div class="blog-card-media">
              <img :src="getImageUrl(b.thumbnail)" :alt="getTrans(b.title)" loading="lazy" />
              <!-- Only featured badge like Projects tab -->
              <div v-if="b.is_featured" class="card-badges-top">
                <span class="card-badge-feat" title="Hiển thị tại trang chủ">Nổi bật</span>
              </div>
            </div>

            <div class="blog-card-content">
              <div class="blog-card-meta">
                <span class="blog-card-date">{{ formatDate(b.published_at) }}</span>
              </div>

              <h3 class="blog-card-title">{{ getTrans(b.title) }}</h3>

              <p class="blog-card-excerpt">
                {{ getTrans(b.excerpt) || 'Không có mô tả tóm tắt.' }}
              </p>

              <div class="blog-card-footer">
                <div class="blog-card-actions">
                  <button type="button" class="btn-blog-action edit" @click="openEditModal(b)" title="Chỉnh sửa">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="btn-blog-action delete" @click="confirmDelete(b)" title="Xóa">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="blogs-empty-state">
          <i class="bx bx-news empty-icon"></i>
          <p v-if="searchQuery">Không tìm thấy bài viết nào phù hợp với từ khóa "{{ searchQuery }}"</p>
          <p v-else>Chưa có bài viết nào trong cơ sở dữ liệu.</p>
          <button v-if="searchQuery" type="button" class="btn-cancel" @click="searchQuery = ''">
            Xóa tìm kiếm
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form (Add / Edit Blog) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog-blog">
        <div class="studio-modal-header">
          <div class="modal-header-left">
            <h3 class="modal-title">{{ editingItem ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới' }}</h3>
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
            <label class="modal-label">Tiêu đề bài viết *</label>
            <input v-if="modalLang === 'vi'" v-model="form.title_vi" type="text" class="modal-input"
              placeholder="Nhập tiêu đề bài viết" required />
            <input v-else v-model="form.title_en" type="text" class="modal-input" placeholder="Enter article title"
              required />
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group">
              <label class="modal-label">Đường dẫn tĩnh *</label>
              <input v-model="form.slug" type="text" class="modal-input" placeholder="duong-dan-bai-viet" required />
            </div>
            <div class="modal-form-group">
              <label class="modal-label">Tên tác giả</label>
              <input v-model="form.author" type="text" class="modal-input" placeholder="Nguyễn Minh Anh" />
            </div>
          </div>

          <div class="modal-form-group">
            <label class="modal-label">Vị trí / Chức vụ tác giả</label>
            <input v-if="modalLang === 'vi'" v-model="form.author_role_vi" type="text" class="modal-input"
              placeholder="Giám đốc Marketing" />
            <input v-else v-model="form.author_role_en" type="text" class="modal-input"
              placeholder="E.g. Marketing Director" />
          </div>

          <!-- Featured Checkbox Card -->
          <div class="blog-featured-card">
            <label class="featured-checkbox-label">
              <input v-model="form.is_featured" type="checkbox" class="featured-checkbox-input" />
              <div class="featured-info">
                <span class="featured-title">Bài viết nổi bật</span>
                <span class="featured-desc">Hiển thị trên Trang chủ</span>
              </div>
            </label>
          </div>

          <!-- Thumbnail Upload -->
          <div class="modal-form-group">
            <label class="modal-label">Ảnh bìa bài viết</label>
            <div class="blog-upload-row">
              <div class="blog-preview-thumb">
                <img v-if="form.thumbnail" :src="getImageUrl(form.thumbnail)" alt="Thumbnail" />
                <div v-else class="blog-preview-empty-icon">
                  <i class="bx bx-image-alt"></i>
                </div>
              </div>
              <div class="blog-btn-row">
                <input type="file" ref="fileInputRef" accept="image/*" style="display: none"
                  @change="handleFileUpload" />
                <button type="button" class="btn-upload-file" :disabled="isUploading" @click="triggerFileInput">
                  <i class="bx" :class="isUploading ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isUploading ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                </button>
                <button v-if="form.thumbnail" type="button" class="btn-remove-file" @click="form.thumbnail = ''"
                  title="Xóa ảnh">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Excerpt -->
          <div class="modal-form-group">
            <label class="modal-label">
              Tóm tắt ngắn
            </label>
            <textarea v-if="modalLang === 'vi'" v-model="form.excerpt_vi" class="modal-textarea" rows="3"
              placeholder="Nhập đoạn giới thiệu ngắn về bài viết..."></textarea>
            <textarea v-else v-model="form.excerpt_en" class="modal-textarea" rows="3"
              placeholder="Short excerpt in English..."></textarea>
          </div>

          <!-- Content / Full Article Body with Visual Rich Text Editor -->
          <div class="modal-form-group">
            <div class="modal-label-row">
              <label class="modal-label">
                Nội dung chi tiết bài viết
              </label>
            </div>
            <RichTextEditor v-if="modalLang === 'vi'" v-model="form.content_vi"
              placeholder="Nhập nội dung bài viết tiếng Việt tại đây... (Nhấn Enter để xuống đoạn văn mới)" />
            <RichTextEditor v-else v-model="form.content_en"
              placeholder="Write detailed article content in English here... (Press Enter for new paragraph)" />
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
import RichTextEditor from './components/RichTextEditor.vue';
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
const searchQuery = ref('');
const modalLang = ref('vi');
const fileInputRef = ref(null);
const isUploading = ref(false);

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
  thumbnail: '',
  excerpt_vi: '',
  excerpt_en: '',
  content_vi: '',
  content_en: '',
  author: '',
  author_role_vi: '',
  author_role_en: '',
  status: 'published',
  is_featured: false,
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || val.en || '';
  }
  return val || '';
};

const getImageUrl = (url) => {
  if (!url) return '/assets/images/project1.png';
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  return '/' + url.replace(/^\/+/, '');
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  return `${day} Tháng ${month} ${d.getFullYear()}`;
};

const filteredBlogs = computed(() => {
  if (!searchQuery.value || !searchQuery.value.trim()) {
    return localBlogs.value;
  }
  const q = searchQuery.value.toLowerCase().trim();
  return localBlogs.value.filter((b) => {
    const titleVi = typeof b.title === 'object' ? (b.title.vi || '') : (b.title || '');
    const titleEn = typeof b.title === 'object' ? (b.title.en || '') : '';
    const slug = b.slug || '';
    return (
      titleVi.toLowerCase().includes(q) ||
      titleEn.toLowerCase().includes(q) ||
      slug.toLowerCase().includes(q)
    );
  });
});

const triggerFileInput = () => {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
};

const uploadSingleFile = async (file) => {
  if (file.size > 50 * 1024 * 1024) {
    alert('Dung lượng tệp không được vượt quá 50MB!');
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
    alert('Lỗi tải ảnh lên máy chủ!');
    return null;
  }
};

const handleFileUpload = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  isUploading.value = true;
  const path = await uploadSingleFile(file);
  if (path) {
    form.thumbnail = path;
  }
  isUploading.value = false;
  e.target.value = '';
};

const openCreateModal = () => {
  editingItem.value = null;
  modalLang.value = currentLanguage.value || 'vi';
  form.title_vi = '';
  form.title_en = '';
  form.slug = '';
  form.thumbnail = '/assets/images/project1.png';
  form.excerpt_vi = '';
  form.excerpt_en = '';
  form.content_vi = '';
  form.content_en = '';
  form.author = '';
  form.author_role_vi = '';
  form.author_role_en = '';
  form.status = 'published';
  form.is_featured = false;
  isModalOpen.value = true;
};

const openEditModal = (b) => {
  editingItem.value = b;
  modalLang.value = currentLanguage.value || 'vi';
  form.title_vi = typeof b.title === 'object' ? (b.title.vi || '') : (b.title || '');
  form.title_en = typeof b.title === 'object' ? (b.title.en || '') : '';
  form.slug = b.slug || '';
  form.thumbnail = b.thumbnail || '';
  form.excerpt_vi = typeof b.excerpt === 'object' ? (b.excerpt.vi || '') : (b.excerpt || '');
  form.excerpt_en = typeof b.excerpt === 'object' ? (b.excerpt.en || '') : '';
  form.content_vi = typeof b.content === 'object' ? (b.content?.vi || '') : (b.content || '');
  form.content_en = typeof b.content === 'object' ? (b.content?.en || '') : '';
  form.author = b.author || '';
  form.author_role_vi = typeof b.author_role === 'object' ? (b.author_role?.vi || '') : (b.author_role || '');
  form.author_role_en = typeof b.author_role === 'object' ? (b.author_role?.en || '') : '';
  form.status = b.status || 'published';
  form.is_featured = !!b.is_featured;
  isModalOpen.value = true;
};

// Auto generate slug from Vietnamese title
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
  const currentTitle = form.title_vi || form.title_en;
  if (!currentTitle || !currentTitle.trim()) {
    alert('Vui lòng nhập tiêu đề bài viết!');
    return;
  }
  if (!form.slug || !form.slug.trim()) {
    alert('Vui lòng nhập đường dẫn tĩnh (Slug)!');
    return;
  }

  const payloadData = {
    title: {
      vi: form.title_vi || form.title_en,
      en: form.title_en || form.title_vi,
    },
    slug: form.slug.trim(),
    category: null,
    thumbnail: form.thumbnail || '/assets/images/project1.png',
    excerpt: {
      vi: form.excerpt_vi || form.excerpt_en || '',
      en: form.excerpt_en || form.excerpt_vi || '',
    },
    content: {
      vi: form.content_vi || form.content_en || '',
      en: form.content_en || form.content_vi || '',
    },
    author: form.author ? form.author.trim() : '',
    author_role: {
      vi: form.author_role_vi ? form.author_role_vi.trim() : (form.author_role_en ? form.author_role_en.trim() : ''),
      en: form.author_role_en ? form.author_role_en.trim() : (form.author_role_vi ? form.author_role_vi.trim() : ''),
    },
    status: form.status || 'published',
    is_featured: !!form.is_featured,
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
  if (
    confirm(
      `Bạn có chắc chắn muốn xóa bài viết "${getTrans(b.title)}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi cơ sở dữ liệu sau khi bạn bấm "Xuất bản")`
    )
  ) {
    localBlogs.value = localBlogs.value.filter((item) => item.id !== b.id);
    if (typeof b.id === 'number' || (typeof b.id === 'string' && !b.id.startsWith('temp_'))) {
      deletedIds.value.push(b.id);
    }
    hasUnsavedChanges.value = true;
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

<style src="./css/blogs.css"></style>
