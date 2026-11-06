<template>
  <Layout>
    <div class="sidebar-header">
      <h2 class="sidebar-title">{{ currentPageData.name }}</h2>
    </div>

    <div class="sidebar-sections">
      <!-- Accordion Section Cards -->
      <div
        v-for="section in currentPageData.sections"
        :key="section.id"
        class="section-card"
        :class="{ active: openSectionId === section.id }"
      >
        <div class="section-header" @click="toggleSection(section.id)">
          <div class="section-title-wrap">
            <i
              class="bx"
              :class="openSectionId === section.id ? 'bx-chevron-down' : 'bx-chevron-right'"
            ></i>
            <span>{{ section.title }}</span>
          </div>
        </div>

        <!-- Accordion Body -->
        <div v-show="openSectionId === section.id" class="section-body">
          <div v-for="field in section.fields" :key="field.key" class="field-group">
            <label class="field-label">{{ field.label }}</label>

            <!-- Text Input -->
            <input
              v-if="field.type === 'text'"
              v-model="field.value"
              type="text"
              class="field-input"
              @input="onFieldChange(section.id, field.key, field.value)"
            />

            <!-- Textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              v-model="field.value"
              class="field-textarea"
              :rows="field.rows || 3"
              @input="onFieldChange(section.id, field.key, field.value)"
            ></textarea>

            <!-- Image URL Input -->
            <div v-else-if="field.type === 'image'" class="image-field-wrap">
              <input
                v-model="field.value"
                type="text"
                class="field-input"
                placeholder="assets/images/..."
                @input="onFieldChange(section.id, field.key, field.value)"
              />
              <div v-if="field.value" class="image-preview-thumb">
                <img
                  :src="field.value.startsWith('http') ? field.value : '/' + field.value"
                  alt="Preview"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import Layout from './Layout.vue';
import { selectedPageSlug, hasUnsavedChanges, previewIframe } from './composables/useAdmin';

// Dữ liệu các trang và khối chỉnh sửa nội dung
const pages = ref([
  {
    name: 'Trang chủ',
    slug: '/',
    sections: [
      {
        id: 'hero',
        title: 'Hero & Tiêu đề chính',
        fields: [
          { key: 'page_title', label: 'Tiêu đề SEO / Title', type: 'text', value: 'Brand Design Agency' },
          { key: 'hero_desc', label: 'Mô tả ngắn trang chủ', type: 'textarea', value: 'Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.' },
        ],
      },
      {
        id: 'showcase',
        title: 'Khối dự án nổi bật',
        fields: [
          { key: 'showcase_label', label: 'Nhãn tiêu đề khối', type: 'text', value: 'DỰ ÁN NỔI BẬT' },
        ],
      },
      {
        id: 'testimonials',
        title: 'Khối Đánh giá khách hàng',
        fields: [
          { key: 'testi_title', label: 'Tiêu đề khối', type: 'text', value: 'ĐÁNH GIÁ CỦA ĐỐI TÁC' },
        ],
      },
      {
        id: 'footer_words',
        title: 'Chữ lật chân trang (Footer Animation)',
        fields: [
          { key: 'footer_line1_old', label: 'Dòng 1 (Trước)', type: 'text', value: 'TỪ NHỮNG' },
          { key: 'footer_line1_new', label: 'Dòng 1 (Sau)', type: 'text', value: 'ĐẾN MỘT' },
          { key: 'footer_line2_old', label: 'Dòng 2 (Trước)', type: 'text', value: 'Ý TƯỞNG' },
          { key: 'footer_line2_new', label: 'Dòng 2 (Sau)', type: 'text', value: 'THƯƠNG HIỆU' },
          { key: 'footer_line3_old', label: 'Dòng 3 (Trước)', type: 'text', value: 'BAN ĐẦU' },
          { key: 'footer_line3_new', label: 'Dòng 3 (Sau)', type: 'text', value: 'ĐÁNG NHỚ' },
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
          { key: 'about_heading', label: 'Tiêu đề giải pháp', type: 'text', value: 'GIẢI PHÁP THIẾT KẾ CHO' },
          { key: 'about_slogan', label: 'Slogan biến đổi', type: 'text', value: 'THƯƠNG HIỆU DOANH NGHIỆP' },
        ],
      },
      {
        id: 'about_workflow',
        title: 'Quy trình làm việc (Workflow)',
        fields: [
          { key: 'step1_title', label: 'Bước 1: Tên bước', type: 'text', value: 'Nghiên cứu & Định vị' },
          { key: 'step2_title', label: 'Bước 2: Tên bước', type: 'text', value: 'Xây dựng ý tưởng' },
          { key: 'step3_title', label: 'Bước 3: Tên bước', type: 'text', value: 'Thiết kế hoàn thiện' },
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
          { key: 'filter_all', label: 'Nút bộ lọc 1', type: 'text', value: 'TẤT CẢ DỰ ÁN' },
          { key: 'filter_featured', label: 'Nút bộ lọc 2', type: 'text', value: 'NỔI BẬT' },
          { key: 'filter_identity', label: 'Nút bộ lọc 3', type: 'text', value: 'NHẬN DIỆN' },
          { key: 'filter_website', label: 'Nút bộ lọc 4', type: 'text', value: 'WEBSITE' },
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
          { key: 'news_title1', label: 'Dòng tiêu đề trái', type: 'text', value: 'TIN TỨC &' },
          { key: 'news_title2', label: 'Dòng tiêu đề phải', type: 'text', value: 'GÓC NHÌN' },
        ],
      },
    ],
  },
]);

const openSectionId = ref(null);

const currentPageData = computed(() => {
  return pages.value.find((p) => p.slug === selectedPageSlug.value) || pages.value[0];
});

const toggleSection = (id) => {
  openSectionId.value = openSectionId.value === id ? null : id;
};

watch(selectedPageSlug, () => {
  openSectionId.value = null;
});

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
</script>

<style src="./css/pages.css"></style>
