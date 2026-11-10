<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">{{ currentPageData.name }}</h2>
        </div>
      </div>
    </div>

    <div class="sidebar-sections" :key="renderKey + '_' + selectedPageSlug">
      <!-- 1. Staging Alert Banner when changes are pending in editor -->
      <div v-if="hasUnsavedChanges" class="staging-alert-banner">
        <div class="banner-info">
          <i class="bx bx-info-circle banner-icon"></i>
          <span>Có thay đổi chưa lưu</span>
        </div>
      </div>

      <!-- 2. Draft Status Banner when a draft exists in database and is unpublished -->
      <div v-else-if="currentPageData && currentPageData.has_draft" class="draft-active-banner">
        <div class="banner-info">
          <i class="bx bx-file-blank banner-icon-draft"></i>
          <span>Có bản nháp chưa xuất bản</span>
        </div>
        <div class="banner-buttons">
          <a :href="previewDraftUrl" target="_blank" class="btn-banner-view">
            <i class="bx bx-show"></i> Xem bản nháp
          </a>
          <button type="button" class="btn-banner-discard" :disabled="isDiscardingDraft || isSaving"
            @click="handleDiscardDraft">
            <i class="bx bx-trash"></i>
            {{ isDiscardingDraft ? 'Đang xóa...' : 'Xóa bản nháp' }}
          </button>
        </div>
      </div>

      <!-- Accordion Section Cards -->
      <div v-for="section in currentPageData.sections" :key="section.id" class="section-card"
        :class="{ active: openSectionId === section.id }">
        <div class="section-header" @click="toggleSection(section.id)">
          <div class="section-title-wrap">
            <i class="bx" :class="openSectionId === section.id ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
            <span>{{ section.title }}</span>
          </div>
          <span class="fields-count">{{ section.fields.length }}</span>
        </div>

        <!-- Accordion Body -->
        <div v-show="openSectionId === section.id" class="section-body">
          <div v-for="field in section.fields" :key="field.key" class="field-group"
            :class="{ 'field-compact': field.key && field.key.startsWith('hero_tagline_') }">
            <label class="field-label">{{ field.label }}</label>

            <!-- Text Input -->
            <input v-if="field.type === 'text'" :value="getFieldValue(field)" type="text" class="field-input"
              @input="updateField(section.id, field, $event.target.value)" />

            <!-- Textarea -->
            <textarea v-else-if="field.type === 'textarea'" :value="getFieldValue(field)" class="field-textarea"
              :rows="field.rows || (field.key === 'hero_desc' ? 4 : 3)"
              @input="updateField(section.id, field, $event.target.value)"></textarea>

            <!-- Image URL Input -->
            <div v-else-if="field.type === 'image'" class="image-field-wrap">
              <div class="media-input-group">
                <input :value="getFieldValue(field)" type="text" class="field-input"
                  placeholder="assets/images/... hoặc storage/uploads/..."
                  @input="updateField(section.id, field, $event.target.value)" />
                <button type="button" class="btn-upload-trigger" :disabled="isUploadingMedia"
                  @click="triggerMediaUpload(section.id, field, 'image/*')"
                  title="Tải ảnh lên từ máy tính (PNG, JPG, WEBP... tối đa 40MB)">
                  <i class="bx" :class="isFieldUploading(field) ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isFieldUploading(field) ? 'Đang tải...' : 'Tải file lên' }}</span>
                </button>
              </div>
              <div v-if="getFieldValue(field)" class="image-preview-thumb mt-2">
                <img
                  :src="getFieldValue(field).startsWith('http') ? getFieldValue(field) : '/' + getFieldValue(field).replace(/^\//, '')"
                  alt="Preview" />
              </div>
            </div>

            <!-- Video / Media URL Input -->
            <div v-else-if="field.type === 'video'" class="video-field-wrap">
              <div class="media-input-group">
                <input :value="getFieldValue(field)" type="text" class="field-input"
                  placeholder="storage/uploads/... hoặc assets/images/..."
                  @input="updateField(section.id, field, $event.target.value)" />
                <button type="button" class="btn-upload-trigger" :disabled="isUploadingMedia"
                  @click="triggerMediaUpload(section.id, field, 'video/mp4,video/webm,video/quicktime,image/*')"
                  title="Tải tệp lên từ máy tính (Video MP4/WEBM hoặc Ảnh PNG/JPG/WEBP, tối đa 40MB)">
                  <i class="bx" :class="isFieldUploading(field) ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isFieldUploading(field) ? 'Đang tải...' : 'Tải file lên' }}</span>
                </button>
              </div>
              <div v-if="getFieldValue(field)" class="media-preview-thumb mt-2">
                <video v-if="isVideoUrl(getFieldValue(field))"
                  :src="getFieldValue(field).startsWith('http') ? getFieldValue(field) : '/' + getFieldValue(field).replace(/^\//, '')"
                  controls muted playsinline class="video-preview-player"></video>
                <div v-else class="image-preview-thumb">
                  <img
                    :src="getFieldValue(field).startsWith('http') ? getFieldValue(field) : '/' + getFieldValue(field).replace(/^\//, '')"
                    alt="Preview" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden native file input for media upload -->
    <input type="file" ref="mediaFileInputRef" style="display: none;" :accept="mediaUploadAccept"
      @change="handleMediaUploadSelected" />
  </Layout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import {
  selectedPageSlug,
  currentLanguage,
  hasUnsavedChanges,
  hasPendingDraft,
  isSaving,
  isSavingDraft,
  isDiscardingDraft,
  onSaveHandler,
  onDraftHandler,
  onDiscardDraftHandler,
  previewIframe,
} from './composables/useAdmin';

const props = defineProps({
  initialPages: {
    type: Array,
    default: () => [],
  },
});

// Fallback pages data
const defaultPages = [
  {
    name: 'Trang chủ',
    slug: '/',
    has_draft: false,
    draft_updated_at: null,
    sections: [
      {
        id: 'hero',
        title: 'Hero & Tiêu đề chính',
        fields: [
          { key: 'hero_tagline_1', label: 'Banner Tagline 1', type: 'text', value: { vi: 'Brand Design Agency', en: 'Brand Design Agency' } },
          { key: 'hero_tagline_2', label: 'Banner Tagline 2', type: 'text', value: { vi: 'Based In Vietnam', en: 'Based In Vietnam' } },
          { key: 'hero_tagline_3', label: 'Banner Tagline 3', type: 'text', value: { vi: 'Culture Your Brand', en: 'Culture Your Brand' } },
          { key: 'hero_heading', label: 'Tiêu đề lớn Hero', type: 'textarea', value: { vi: "NHẬN DIỆN\nCHIẾN LƯỢC\nVĂN HOÁ", en: "IDENTITY\nSTRATEGY\nCULTURE" } },
          { key: 'hero_desc', label: 'Mô tả ngắn trang chủ', type: 'textarea', value: { vi: 'Chúng tôi giúp doanh nghiệp xây dựng bản sắc thương hiệu rõ ràng, nhất quán và phù hợp với định hướng kinh doanh.', en: 'We help businesses build clear, consistent brand identities.' } },
          { key: 'hero_video', label: 'Tệp giới thiệu', type: 'video', value: { vi: 'storage/uploads/main-feature.mp4', en: 'storage/uploads/main-feature.mp4' } },
        ],
      },
      {
        id: 'intro_about',
        title: 'Khối Giới thiệu',
        fields: [
          { key: 'about_label', label: 'Nhãn tiêu đề (Label)', type: 'text', value: { vi: '• Về chúng tôi', en: '• About us' } },
          { key: 'about_desc', label: 'Nội dung mô tả', type: 'textarea', rows: 4, value: { vi: 'Có rất nhiều lý do tại sao xây dựng thương hiệu và tiếp thị lại quan trọng. Vô số sách viết về chủ đề này, nhưng tất cả đều quy về một điều đơn giản: tại sao mọi người nên quan tâm đến sự tồn tại của bạn? Chúng tôi tạo ra những trải nghiệm khiến mọi người chú ý và quan tâm.', en: 'There are countless reasons why branding and marketing matter. Endless books have been written on this topic, but it all comes down to one simple question: why should anyone care about your existence? We create experiences that make people notice and care.' } },
          { key: 'about_btn_text', label: 'Chữ trên nút bấm', type: 'text', value: { vi: 'ĐỌC THÊM', en: 'READ MORE' } },
          { key: 'about_btn_url', label: 'Đường dẫn liên kết nút', type: 'text', value: { vi: '/about-us', en: '/about-us' } },
        ],
      },
      {
        id: 'showcase',
        title: 'Khối dự án nổi bật',
        fields: [
          { key: 'showcase_label', label: 'Nhãn tiêu đề khối', type: 'text', value: { vi: 'DỰ ÁN NỔI BẬT', en: 'FEATURED PROJECTS' } },
        ],
      },
      {
        id: 'footer_words',
        title: 'Chữ lật chân trang (Footer Animation)',
        fields: [
          { key: 'footer_line1_old', label: 'Dòng 1 (Trước)', type: 'text', value: { vi: 'TỪ NHỮNG', en: 'FROM AN' } },
          { key: 'footer_line1_new', label: 'Dòng 1 (Sau)', type: 'text', value: { vi: 'ĐẾN MỘT', en: 'INTO A' } },
          { key: 'footer_line2_old', label: 'Dòng 2 (Trước)', type: 'text', value: { vi: 'Ý TƯỞNG', en: 'IDEA' } },
          { key: 'footer_line2_new', label: 'Dòng 2 (Sau)', type: 'text', value: { vi: 'THƯƠNG HIỆU', en: 'BRAND' } },
          { key: 'footer_line3_old', label: 'Dòng 3 (Trước)', type: 'text', value: { vi: 'BAN ĐẦU', en: 'INITIAL' } },
          { key: 'footer_line3_new', label: 'Dòng 3 (Sau)', type: 'text', value: { vi: 'ĐÁNG NHỚ', en: 'MEMORABLE' } },
        ],
      },
    ],
  },
  {
    name: 'Về chúng tôi',
    slug: '/about-us',
    has_draft: false,
    draft_updated_at: null,
    sections: [
      {
        id: 'about_intro',
        title: 'Giới thiệu & Giải pháp',
        fields: [
          { key: 'about_heading_1', label: 'Tiêu đề dòng 1 (trái)', type: 'text', value: { vi: 'GIẢI PHÁP', en: 'DESIGN SOLUTIONS' } },
          { key: 'about_heading_2', label: 'Tiêu đề dòng 2 (phải)', type: 'text', value: { vi: 'THIẾT KẾ CHO', en: 'FOR' } },
          { key: 'about_slogan', label: 'Slogan biến đổi', type: 'text', value: { vi: 'THƯƠNG HIỆU DOANH NGHIỆP', en: 'ENTERPRISE BRANDS' } },
        ],
      },
      {
        id: 'about_workflow',
        title: 'Quy trình làm việc (Workflow)',
        fields: [
          { key: 'step1_title', label: 'Bước 1: Tên bước', type: 'text', value: { vi: 'Nghiên cứu & Định vị', en: 'Research & Positioning' } },
          { key: 'step2_title', label: 'Bước 2: Tên bước', type: 'text', value: { vi: 'Xây dựng ý tưởng', en: 'Concept Ideation' } },
          { key: 'step3_title', label: 'Bước 3: Tên bước', type: 'text', value: { vi: 'Thiết kế hoàn thiện', en: 'Final Delivery' } },
        ],
      },
    ],
  },
  {
    name: 'Dự án',
    slug: '/work',
    has_draft: false,
    draft_updated_at: null,
    sections: [
      {
        id: 'work_hero',
        title: 'Banner Dự án & Bộ lọc',
        fields: [
          { key: 'filter_all', label: 'Nút bộ lọc 1', type: 'text', value: { vi: 'TẤT CẢ DỰ ÁN', en: 'ALL PROJECTS' } },
          { key: 'filter_featured', label: 'Nút bộ lọc 2', type: 'text', value: { vi: 'NỔI BẬT', en: 'FEATURED' } },
          { key: 'filter_identity', label: 'Nút bộ lọc 3', type: 'text', value: { vi: 'NHẬN DIỆN', en: 'IDENTITY' } },
          { key: 'filter_website', label: 'Nút bộ lọc 4', type: 'text', value: { vi: 'WEBSITE', en: 'WEBSITE' } },
        ],
      },
    ],
  },
  {
    name: 'Bài viết / Tin tức',
    slug: '/news',
    has_draft: false,
    draft_updated_at: null,
    sections: [
      {
        id: 'news_header',
        title: 'Tiêu đề trang Tin tức',
        fields: [
          { key: 'news_title1', label: 'Dòng tiêu đề trái', type: 'text', value: { vi: 'TIN TỨC &', en: 'NEWS &' } },
          { key: 'news_title2', label: 'Dòng tiêu đề phải', type: 'text', value: { vi: 'GÓC NHÌN', en: 'PERSPECTIVES' } },
        ],
      },
    ],
  },
];

const clone = (data) => JSON.parse(JSON.stringify(data || []));
const renderKey = ref(0);

const getSourcePages = () => {
  return props.initialPages && props.initialPages.length > 0 ? props.initialPages : defaultPages;
};

// Pristine snapshot directly from database - NEVER mutated
const originalPagesSnapshot = ref(clone(getSourcePages()));
// Reactive working copy that user modifies in UI
const pages = ref(clone(originalPagesSnapshot.value));

// If initialPages changes from server, update ref and snapshot
watch(
  () => props.initialPages,
  (newVal) => {
    if (newVal && newVal.length > 0) {
      originalPagesSnapshot.value = clone(newVal);
      pages.value = clone(newVal);
      renderKey.value++;
      hasUnsavedChanges.value = false;
    }
  },
  { deep: true }
);

const openSectionId = ref(null);

const currentPageData = computed(() => {
  return pages.value.find((p) => p.slug === selectedPageSlug.value) || pages.value[0];
});

// Sync hasPendingDraft to useAdmin state
watch(
  () => [currentPageData.value?.has_draft, currentPageData.value?.draft_updated_at],
  () => {
    hasPendingDraft.value = !!currentPageData.value?.has_draft;
  },
  { immediate: true }
);

const previewDraftUrl = computed(() => {
  const slug = selectedPageSlug.value || '/';
  return slug + (slug.includes('?') ? '&' : '?') + 'preview=draft';
});

const formattedDraftTime = computed(() => {
  const page = currentPageData.value;
  if (!page || !page.draft_updated_at) return '';
  try {
    const d = new Date(page.draft_updated_at);
    return (
      d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) +
      ' - ' +
      d.toLocaleDateString('vi-VN')
    );
  } catch (e) {
    return '';
  }
});

const toggleSection = (id) => {
  openSectionId.value = openSectionId.value === id ? null : id;
};

watch(selectedPageSlug, () => {
  openSectionId.value = null;
});

// Helper to get bilingual value
const getFieldValue = (field) => {
  if (field.value && typeof field.value === 'object') {
    return field.value[currentLanguage.value] ?? field.value['vi'] ?? '';
  }
  return field.value ?? '';
};

// Media / Video / Image file upload logic
const mediaFileInputRef = ref(null);
const mediaUploadAccept = ref('video/mp4,video/webm,video/quicktime,image/*');
const isUploadingMedia = ref(false);
const activeUploadTarget = ref({ sectionId: null, field: null });

const isFieldUploading = (field) => {
  return isUploadingMedia.value && activeUploadTarget.value.field?.key === field?.key;
};

const isVideoUrl = (url) => {
  if (!url) return false;
  const clean = url.split('?')[0].toLowerCase();
  return (
    clean.endsWith('.mp4') ||
    clean.endsWith('.webm') ||
    clean.endsWith('.mov') ||
    clean.endsWith('.avi')
  );
};

const triggerMediaUpload = (sectionId, field, acceptType = 'video/mp4,video/webm,video/quicktime,image/*') => {
  activeUploadTarget.value = { sectionId, field };
  mediaUploadAccept.value = acceptType;
  if (mediaFileInputRef.value) {
    mediaFileInputRef.value.value = '';
    mediaFileInputRef.value.click();
  }
};

const handleMediaUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  if (file.size > 40 * 1024 * 1024) {
    alert('Dung lượng tệp không được vượt quá 40MB!');
    return;
  }

  isUploadingMedia.value = true;
  const formData = new FormData();
  formData.append('file', file);

  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  try {
    const response = await fetch('/admin/media/upload', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
    });

    const data = await response.json();
    if (data && data.success && data.file_path) {
      const { sectionId, field } = activeUploadTarget.value;
      if (sectionId && field) {
        updateField(sectionId, field, data.file_path);
      }
    } else {
      alert('Không thể tải tệp lên: ' + (data.message || 'Lỗi không xác định'));
    }
  } catch (err) {
    alert('Có lỗi xảy ra khi tải tệp lên máy chủ: ' + err.message);
  } finally {
    isUploadingMedia.value = false;
  }
};

// Update field value
const updateField = (sectionId, field, newVal) => {
  if (!field.value || typeof field.value !== 'object') {
    field.value = { vi: field.value || '', en: field.value || '' };
  }
  field.value[currentLanguage.value] = newVal;
  onFieldChange(sectionId, field.key, newVal);
};

// Đồng bộ thay đổi tức thời sang Iframe qua postMessage
const onFieldChange = (sectionId, fieldKey, value) => {
  hasUnsavedChanges.value = true;
  if (!previewIframe.value || !previewIframe.value.contentWindow) return;

  previewIframe.value.contentWindow.postMessage(
    {
      type: 'STUDIO_LIVE_UPDATE',
      sectionId,
      fieldKey,
      value,
    },
    '*'
  );
};

// Save logic to backend MySQL database via Inertia
const savePagesToDatabase = (isDraft = false) => {
  if (isDraft) {
    isSavingDraft.value = true;
  } else {
    isSaving.value = true;
  }

  const flatFields = [];
  pages.value.forEach((p) => {
    if (p.sections) {
      p.sections.forEach((s) => {
        if (s.fields) {
          s.fields.forEach((f) => {
            flatFields.push({
              page_slug: p.slug,
              section_id: s.id,
              key: f.key,
              value: f.value,
            });
          });
        }
      });
    }
  });

  router.post(
    '/admin/pages/update',
    {
      fields: flatFields,
      action: isDraft ? 'draft' : 'publish',
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        isSavingDraft.value = false;
        hasUnsavedChanges.value = false;

        const targetPage = pages.value.find((p) => p.slug === selectedPageSlug.value);
        if (targetPage) {
          if (isDraft) {
            targetPage.has_draft = true;
            targetPage.draft_updated_at = new Date().toISOString();
            hasPendingDraft.value = true;
          } else {
            targetPage.has_draft = false;
            targetPage.draft_updated_at = null;
            hasPendingDraft.value = false;
          }
        }

        // Update snapshot to freshly saved values
        originalPagesSnapshot.value = clone(pages.value);
        renderKey.value++;

        // Reload preview iframe to fetch freshly saved draft or published state
        if (previewIframe.value) {
          const baseSlug = selectedPageSlug.value || '/';
          previewIframe.value.src =
            baseSlug + (baseSlug.includes('?') ? '&' : '?') + 'preview=draft&t=' + Date.now();
        }

        alert(isDraft ? 'Đã lưu bản nháp thành công!' : 'Đã xuất bản thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        isSavingDraft.value = false;
        alert('Có lỗi xảy ra khi lưu: ' + JSON.stringify(err));
      },
    }
  );
};

const handleDiscardDraft = () => {
  if (!confirm('Bạn có chắc chắn muốn xóa bản nháp này không?')) {
    return;
  }

  isDiscardingDraft.value = true;

  router.post(
    '/admin/pages/discard-draft',
    { page_slug: selectedPageSlug.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        isDiscardingDraft.value = false;
        hasUnsavedChanges.value = false;

        const targetPage = pages.value.find((p) => p.slug === selectedPageSlug.value);
        if (targetPage) {
          targetPage.has_draft = false;
          targetPage.draft_updated_at = null;
          hasPendingDraft.value = false;

          // Revert working value back to published_value
          if (targetPage.sections) {
            targetPage.sections.forEach((s) => {
              if (s.fields) {
                s.fields.forEach((f) => {
                  if (f.published_value !== undefined) {
                    f.value = clone(f.published_value);
                  }
                });
              }
            });
          }
        }

        originalPagesSnapshot.value = clone(pages.value);
        renderKey.value++;

        // Reload preview iframe
        if (previewIframe.value) {
          const baseSlug = selectedPageSlug.value || '/';
          previewIframe.value.src =
            baseSlug + (baseSlug.includes('?') ? '&' : '?') + 'preview=draft&t=' + Date.now();
        }

        alert('Đã xóa bản nháp thành công!');
      },
      onError: (err) => {
        isDiscardingDraft.value = false;
        alert('Có lỗi xảy ra khi xóa bản nháp: ' + JSON.stringify(err));
      },
    }
  );
};

const revertChanges = () => {
  if (confirm('Hủy bỏ các thay đổi vừa chỉnh sửa và quay lại nội dung đã lưu gần nhất?')) {
    // Restore strictly from pristine snapshot
    pages.value = clone(originalPagesSnapshot.value);
    renderKey.value++;
    hasUnsavedChanges.value = false;

    // Reset iframe to revert live preview changes
    if (previewIframe.value) {
      const baseSlug = selectedPageSlug.value || '/';
      previewIframe.value.src =
        baseSlug + (baseSlug.includes('?') ? '&' : '?') + 'preview=draft&t=' + Date.now();
    }
  }
};

onMounted(() => {
  onSaveHandler.value = () => savePagesToDatabase(false);
  onDraftHandler.value = () => savePagesToDatabase(true);
  onDiscardDraftHandler.value = handleDiscardDraft;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  onDiscardDraftHandler.value = null;
  hasPendingDraft.value = false;
});
</script>

<style src="./css/pages.css"></style>
