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

        <!-- Unsaved / Draft Status Banner -->
        <DraftStatusBanner :has-draft="hasDraft" preview-path="/news" @discard="handleDiscardDraft" />

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
    <div v-if="isModalOpen" class="studio-modal-backdrop">
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
              <label class="modal-label">Ngày đăng bài viết</label>
              <div ref="datePickerWrapRef" class="blog-calendar-picker-wrap">
                <div class="blog-calendar-input-box" @click="toggleDatePicker">
                  <i class="bx bx-calendar blog-calendar-icon"></i>
                  <span v-if="form.published_at" class="blog-calendar-val">
                    {{ formatDateInputDisplay(form.published_at) }}
                  </span>
                  <span v-else class="blog-calendar-placeholder">Chọn ngày / tháng / năm</span>
                  <button v-if="form.published_at" type="button" class="btn-clear-blog-date"
                    @click.stop="clearPublishedDate" title="Xóa ngày">
                    <i class="bx bx-x"></i>
                  </button>
                </div>

                <div v-if="isDatePickerOpen" class="blog-calendar-dropdown" @click.stop>
                  <div class="blog-calendar-header">
                    <button type="button" class="btn-blog-cal-nav" @click="prevMonth" title="Tháng trước">
                      <i class="bx bx-chevron-left"></i>
                    </button>

                    <div class="blog-cal-selectors">
                      <select v-model.number="pickerMonth" class="blog-cal-select" aria-label="Chọn tháng">
                        <option v-for="m in 12" :key="m" :value="m">Tháng {{ String(m).padStart(2, '0') }}</option>
                      </select>
                      <select v-model.number="pickerYear" class="blog-cal-select blog-cal-select-year"
                        aria-label="Chọn năm">
                        <option v-for="y in pickerYears" :key="y" :value="y">{{ y }}</option>
                      </select>
                    </div>

                    <button type="button" class="btn-blog-cal-nav" @click="nextMonth" title="Tháng sau">
                      <i class="bx bx-chevron-right"></i>
                    </button>
                  </div>

                  <div class="blog-calendar-weekdays">
                    <span v-for="wd in ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" :key="wd"
                      class="blog-cal-weekday">{{ wd }}</span>
                  </div>

                  <div class="blog-calendar-days-grid">
                    <button v-for="cell in calendarDays" :key="cell.key" type="button" class="blog-cal-day-btn"
                      :class="{
                        'is-outside': !cell.isCurrentMonth,
                        'is-today': cell.isoDate === todayIsoDate,
                        'is-selected': cell.isoDate === form.published_at,
                      }" @click="selectCalendarDay(cell)">
                      {{ cell.day }}
                    </button>
                  </div>

                  <div class="blog-calendar-footer">
                    <button type="button" class="btn-blog-cal-quick" @click="clearPublishedDate">Xóa ngày</button>
                    <button type="button" class="btn-blog-cal-quick primary" @click="selectToday">Hôm nay</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-form-row">
            <div class="modal-form-group">
              <label class="modal-label">Tên tác giả</label>
              <input v-model="form.author" type="text" class="modal-input" placeholder="Nguyễn Minh Anh" />
            </div>
            <div class="modal-form-group">
              <label class="modal-label">Vị trí / Chức vụ tác giả</label>
              <input v-if="modalLang === 'vi'" v-model="form.author_role_vi" type="text" class="modal-input"
                placeholder="Giám đốc Marketing" />
              <input v-else v-model="form.author_role_en" type="text" class="modal-input"
                placeholder="E.g. Marketing Director" />
            </div>
          </div>

          <!-- Author Avatar Upload -->
          <div class="modal-form-group">
            <label class="modal-label">Ảnh đại diện tác giả</label>
            <div class="blog-upload-row">
              <div class="blog-preview-avatar">
                <img v-if="form.author_avatar" :src="getImageUrl(form.author_avatar)" alt="Author Avatar" />
                <div v-else class="blog-preview-empty-icon">
                  <i class="bx bx-user"></i>
                </div>
              </div>
              <div class="blog-btn-row">
                <input type="file" ref="authorAvatarInputRef" accept="image/*" style="display: none"
                  @change="handleAuthorAvatarUpload" />
                <button type="button" class="btn-upload-file" :disabled="isUploadingAvatar"
                  @click="triggerAuthorAvatarInput">
                  <i class="bx" :class="isUploadingAvatar ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isUploadingAvatar ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                </button>
                <button v-if="form.author_avatar" type="button" class="btn-remove-file"
                  @click="form.author_avatar = ''" title="Xóa ảnh tác giả">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
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
              placeholder="Nhập nội dung bài viết tại đây" />
            <RichTextEditor v-else v-model="form.content_en" placeholder="Write detailed article content in here" />
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
import DraftStatusBanner from './components/DraftStatusBanner.vue';
import {
  currentLanguage,
  hasUnsavedChanges,
  hasPendingDraft,
  isSaving,
  isSavingDraft,
  isDiscardingDraft,
  onSaveHandler,
  onDraftHandler,
  onDiscardDraftHandler,
} from './composables/useAdmin';
import {
  askConfirm,
  extractErrorMessage,
  notifyError,
  notifySuccess,
  notifyWarning,
} from './composables/useFeedback';

const props = defineProps({
  blogs: {
    type: Array,
    default: () => [],
  },
  deletedIds: {
    type: Array,
    default: () => [],
  },
  hasDraft: {
    type: Boolean,
    default: false,
  },
  draftUpdatedAt: {
    type: String,
    default: null,
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
const authorAvatarInputRef = ref(null);
const datePickerWrapRef = ref(null);
const isUploading = ref(false);
const isUploadingAvatar = ref(false);
const isDatePickerOpen = ref(false);
const pickerMonth = ref(new Date().getMonth() + 1);
const pickerYear = ref(new Date().getFullYear());

const syncFromProps = () => {
  localBlogs.value = JSON.parse(JSON.stringify(props.blogs || []));
  deletedIds.value = JSON.parse(JSON.stringify(props.deletedIds || []));
  hasPendingDraft.value = !!props.hasDraft;
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => [props.blogs, props.deletedIds, props.hasDraft], syncFromProps, { immediate: true });

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
  author_avatar: '',
  published_at: '',
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
  if (Number.isNaN(d.getTime())) return '';
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  return `${day} Tháng ${month} ${d.getFullYear()}`;
};

const toDateInputValue = (dateStr) => {
  if (!dateStr) return '';
  if (typeof dateStr === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateStr.trim())) {
    return dateStr.trim();
  }
  const d = new Date(dateStr);
  if (Number.isNaN(d.getTime())) return '';
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const getTodayDateInput = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const todayIsoDate = computed(() => getTodayDateInput());

const formatDateInputDisplay = (isoDate) => {
  if (!isoDate || !/^\d{4}-\d{2}-\d{2}$/.test(isoDate)) return isoDate || '';
  const [y, m, d] = isoDate.split('-');
  return `${d} Tháng ${m}, ${y}`;
};

const pickerYears = computed(() => {
  const currentYear = new Date().getFullYear();
  const minYear = Math.min(2015, pickerYear.value - 5);
  const maxYear = Math.max(currentYear + 5, pickerYear.value + 5);
  const list = [];
  for (let y = minYear; y <= maxYear; y++) {
    list.push(y);
  }
  return list;
});

const formatIsoDate = (year, month, day) => {
  return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
};

const calendarDays = computed(() => {
  const year = pickerYear.value;
  const month = pickerMonth.value;
  const firstDay = new Date(year, month - 1, 1);
  const startOffset = (firstDay.getDay() + 6) % 7; // Monday = 0
  const daysInMonth = new Date(year, month, 0).getDate();
  const daysInPrevMonth = new Date(year, month - 1, 0).getDate();

  const prevMonthNum = month === 1 ? 12 : month - 1;
  const prevMonthYear = month === 1 ? year - 1 : year;
  const nextMonthNum = month === 12 ? 1 : month + 1;
  const nextMonthYear = month === 12 ? year + 1 : year;

  const cells = [];

  for (let i = startOffset - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    const isoDate = formatIsoDate(prevMonthYear, prevMonthNum, day);
    cells.push({
      key: `prev-${isoDate}`,
      day,
      month: prevMonthNum,
      year: prevMonthYear,
      isCurrentMonth: false,
      isoDate,
    });
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const isoDate = formatIsoDate(year, month, day);
    cells.push({
      key: `curr-${isoDate}`,
      day,
      month,
      year,
      isCurrentMonth: true,
      isoDate,
    });
  }

  const remaining = 42 - cells.length;
  for (let day = 1; day <= remaining; day++) {
    const isoDate = formatIsoDate(nextMonthYear, nextMonthNum, day);
    cells.push({
      key: `next-${isoDate}`,
      day,
      month: nextMonthNum,
      year: nextMonthYear,
      isCurrentMonth: false,
      isoDate,
    });
  }

  return cells;
});

const toggleDatePicker = () => {
  if (!isDatePickerOpen.value) {
    if (form.published_at && /^\d{4}-\d{2}-\d{2}$/.test(form.published_at)) {
      const [y, m] = form.published_at.split('-').map((n) => parseInt(n, 10));
      if (y && m) {
        pickerYear.value = y;
        pickerMonth.value = m;
      }
    } else {
      const now = new Date();
      pickerYear.value = now.getFullYear();
      pickerMonth.value = now.getMonth() + 1;
    }
    isDatePickerOpen.value = true;
  } else {
    isDatePickerOpen.value = false;
  }
};

const prevMonth = () => {
  if (pickerMonth.value === 1) {
    pickerMonth.value = 12;
    pickerYear.value--;
  } else {
    pickerMonth.value--;
  }
};

const nextMonth = () => {
  if (pickerMonth.value === 12) {
    pickerMonth.value = 1;
    pickerYear.value++;
  } else {
    pickerMonth.value++;
  }
};

const selectCalendarDay = (cell) => {
  form.published_at = cell.isoDate;
  pickerMonth.value = cell.month;
  pickerYear.value = cell.year;
  isDatePickerOpen.value = false;
};

const selectToday = () => {
  const today = getTodayDateInput();
  form.published_at = today;
  const [y, m] = today.split('-').map((n) => parseInt(n, 10));
  pickerYear.value = y;
  pickerMonth.value = m;
  isDatePickerOpen.value = false;
};

const clearPublishedDate = () => {
  form.published_at = '';
  isDatePickerOpen.value = false;
};

const closeDatePickerOnClickOutside = (e) => {
  if (isDatePickerOpen.value && datePickerWrapRef.value && !datePickerWrapRef.value.contains(e.target)) {
    isDatePickerOpen.value = false;
  }
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

const triggerAuthorAvatarInput = () => {
  if (authorAvatarInputRef.value) {
    authorAvatarInputRef.value.click();
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

const uploadImageToField = async (e, field, loadingRef) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  loadingRef.value = true;
  const path = await uploadSingleFile(file);
  if (path) {
    form[field] = path;
  }
  loadingRef.value = false;
  e.target.value = '';
};

const handleFileUpload = (e) => uploadImageToField(e, 'thumbnail', isUploading);
const handleAuthorAvatarUpload = (e) => uploadImageToField(e, 'author_avatar', isUploadingAvatar);

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
  form.author_avatar = '';
  form.published_at = getTodayDateInput();
  form.status = 'published';
  form.is_featured = false;
  isDatePickerOpen.value = false;
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
  form.author_avatar = b.author_avatar || '';
  form.published_at = toDateInputValue(b.published_at) || getTodayDateInput();
  form.status = b.status || 'published';
  form.is_featured = !!b.is_featured;
  isDatePickerOpen.value = false;
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
    notifyWarning('Vui lòng nhập tiêu đề bài viết!');
    return;
  }
  if (!form.slug || !form.slug.trim()) {
    notifyWarning('Vui lòng nhập đường dẫn tĩnh (Slug)!');
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
    author_avatar: form.author_avatar || null,
    status: form.status || 'published',
    is_featured: !!form.is_featured,
    published_at:
      editingItem.value &&
      editingItem.value.published_at &&
      form.published_at === toDateInputValue(editingItem.value.published_at)
        ? editingItem.value.published_at
        : form.published_at
          ? `${form.published_at}T12:00:00.000Z`
          : new Date().toISOString(),
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

const confirmDelete = async (b) => {
  const ok = await askConfirm({
    title: 'Xóa bài viết',
    message: `Bạn có chắc chắn muốn xóa bài viết "${getTrans(b.title)}" không?`,
    confirmText: 'Xóa bài viết',
    variant: 'danger',
  });
  if (ok) {
    localBlogs.value = localBlogs.value.filter((item) => item.id !== b.id);
    if (typeof b.id === 'number' || (typeof b.id === 'string' && !b.id.startsWith('temp_'))) {
      deletedIds.value.push(b.id);
    }
    hasUnsavedChanges.value = true;
  }
};

const saveDraftBlogs = () => {
  isSavingDraft.value = true;
  router.post(
    '/admin/news/draft',
    {
      items: localBlogs.value,
      deleted_ids: deletedIds.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSavingDraft.value = false;
        hasUnsavedChanges.value = false;
        hasPendingDraft.value = true;
        notifySuccess('Đã lưu bản nháp bài viết thành công!');
      },
      onError: (err) => {
        isSavingDraft.value = false;
        notifyError('Lỗi lưu bản nháp: ' + extractErrorMessage(err));
      },
    }
  );
};

const handleDiscardDraft = async () => {
  const ok = await askConfirm({
    title: 'Xóa bản nháp',
    message: 'Bạn có chắc chắn muốn xóa bản nháp bài viết này và quay về dữ liệu đã xuất bản không?',
    confirmText: 'Xóa bản nháp',
    variant: 'danger',
  });
  if (!ok) return;

  isDiscardingDraft.value = true;
  router.post(
    '/admin/news/discard-draft',
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        isDiscardingDraft.value = false;
        hasUnsavedChanges.value = false;
        hasPendingDraft.value = false;
        notifySuccess('Đã xóa bản nháp bài viết thành công!');
      },
      onError: (err) => {
        isDiscardingDraft.value = false;
        notifyError('Có lỗi xảy ra khi xóa bản nháp: ' + extractErrorMessage(err));
      },
    }
  );
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
        hasPendingDraft.value = false;
        notifySuccess('Đã xuất bản bài viết vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        notifyError('Lỗi xuất bản: ' + extractErrorMessage(err));
      },
    }
  );
};

onMounted(() => {
  onSaveHandler.value = publishBlogs;
  onDraftHandler.value = saveDraftBlogs;
  onDiscardDraftHandler.value = handleDiscardDraft;
  document.addEventListener('click', closeDatePickerOnClickOutside);
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  onDiscardDraftHandler.value = null;
  hasUnsavedChanges.value = false;
  hasPendingDraft.value = false;
  document.removeEventListener('click', closeDatePickerOnClickOutside);
});
</script>

<style src="./css/blogs.css"></style>
