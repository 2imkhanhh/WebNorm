<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">{{ currentPageData.name }}</h2>
        </div>
      </div>
    </div>

    <!-- Staging Alert Banner when changes are pending publish -->
    <div v-if="hasUnsavedChanges" class="staging-alert-banner mx-3 mt-2">
      <div class="banner-info">
        <span>Có thay đổi chưa lưu vào database</span>
      </div>
      <div class="banner-buttons">
        <button type="button" class="btn-banner-revert" @click="revertChanges">Hủy thay đổi</button>
        <button type="button" class="btn-banner-publish" :disabled="isSaving" @click="savePagesToDatabase(false)">
          {{ isSaving ? 'Đang lưu...' : 'Xuất bản ngay' }}
        </button>
      </div>
    </div>

    <div class="sidebar-sections" :key="renderKey + '_' + selectedPageSlug">
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
          <div v-for="field in section.fields" :key="field.key" class="field-group">
            <label class="field-label">{{ field.label }}</label>

            <!-- Text Input -->
            <input v-if="field.type === 'text'" :value="getFieldValue(field)" type="text" class="field-input"
              @input="updateField(section.id, field, $event.target.value)" />

            <!-- Textarea -->
            <textarea v-else-if="field.type === 'textarea'" :value="getFieldValue(field)" class="field-textarea"
              :rows="field.rows || 3" @input="updateField(section.id, field, $event.target.value)"></textarea>

            <!-- Image URL Input -->
            <div v-else-if="field.type === 'image'" class="image-field-wrap">
              <input :value="getFieldValue(field)" type="text" class="field-input" placeholder="assets/images/..."
                @input="updateField(section.id, field, $event.target.value)" />
              <div v-if="getFieldValue(field)" class="image-preview-thumb">
                <img
                  :src="getFieldValue(field).startsWith('http') ? getFieldValue(field) : '/' + getFieldValue(field).replace(/^\//, '')"
                  alt="Preview" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
  isSaving,
  isSavingDraft,
  onSaveHandler,
  onDraftHandler,
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
    sections: [
      {
        id: 'hero',
        title: 'Hero & Tiêu đề chính',
        fields: [
          { key: 'page_title', label: 'Tiêu đề SEO / Title', type: 'text', value: { vi: 'Brand Design Agency', en: 'Brand Design Agency' } },
          { key: 'hero_desc', label: 'Mô tả ngắn trang chủ', type: 'textarea', value: { vi: 'Chúng tôi giúp doanh nghiệp xây dựng bản sắc thương hiệu rõ ràng, nhất quán và phù hợp với định hướng kinh doanh.', en: 'We help businesses build clear, consistent brand identities.' } },
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
    sections: [
      {
        id: 'about_intro',
        title: 'Giới thiệu & Giải pháp',
        fields: [
          { key: 'about_heading', label: 'Tiêu đề giải pháp', type: 'text', value: { vi: 'GIẢI PHÁP THIẾT KẾ CHO', en: 'DESIGN SOLUTIONS FOR' } },
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

// Auto-open first section
watch(
  currentPageData,
  (newVal) => {
    if (newVal && newVal.sections && newVal.sections.length > 0 && !openSectionId.value) {
      openSectionId.value = newVal.sections[0].id;
    }
  },
  { immediate: true }
);

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
    { fields: flatFields },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        isSavingDraft.value = false;
        hasUnsavedChanges.value = false;
        // Update snapshot to freshly saved values
        originalPagesSnapshot.value = clone(pages.value);
        renderKey.value++;
        alert(isDraft ? 'Đã lưu bản nháp thành công!' : 'Đã xuất bản cấu hình thành công lên website!');
      },
      onError: (err) => {
        isSaving.value = false;
        isSavingDraft.value = false;
        alert('Có lỗi xảy ra khi lưu: ' + JSON.stringify(err));
      },
    }
  );
};

const revertChanges = () => {
  if (confirm('Hủy bỏ các thay đổi vừa chỉnh sửa và quay lại nội dung gốc?')) {
    // Restore strictly from pristine snapshot
    pages.value = clone(originalPagesSnapshot.value);
    renderKey.value++;
    hasUnsavedChanges.value = false;

    // Reset iframe to revert live preview changes
    if (previewIframe.value) {
      previewIframe.value.src = selectedPageSlug.value;
    }
  }
};

onMounted(() => {
  onSaveHandler.value = () => savePagesToDatabase(false);
  onDraftHandler.value = () => savePagesToDatabase(true);
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
});
</script>

<style src="./css/pages.css"></style>
