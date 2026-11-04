<template>
  <div class="studio-root">
    <!-- Topbar -->
    <header class="studio-topbar">
      <div class="topbar-left">
        <a href="/admin/studio" class="studio-brand">
          <span class="brand-badge">B</span>
          <span>Website Studio</span>
          <span class="pro-pill">VUE 3</span>
        </a>

        <!-- Page selector -->
        <div class="page-select-wrap">
          <i class="bx bx-file page-select-icon"></i>
          <select v-model="selectedPageSlug" class="page-select">
            <option v-for="page in pages" :key="page.slug" :value="page.slug">
              {{ page.name }}
            </option>
          </select>
          <i class="bx bx-chevron-down page-select-arrow"></i>
        </div>
      </div>

      <!-- Center: Device Switcher -->
      <div class="topbar-center">
        <button
          v-for="d in devices"
          :key="d.id"
          class="device-btn"
          :class="{ active: currentDevice === d.id }"
          @click="currentDevice = d.id"
        >
          <i :class="d.icon"></i> {{ d.label }}
        </button>
      </div>

      <!-- Right: Actions -->
      <div class="topbar-right">
        <div class="status-indicator">
          <span class="status-dot" :class="{ unsaved: hasUnsavedChanges }"></span>
          <span>{{ hasUnsavedChanges ? 'Có thay đổi chưa lưu' : 'Đã đồng bộ Live Preview' }}</span>
        </div>

        <button class="btn-draft" @click="resetCurrentPage">Khôi phục</button>
        <button class="btn-publish" :disabled="isSaving" @click="saveChanges">
          <span v-if="isSaving">Đang lưu...</span>
          <span v-else>Xuất bản</span>
        </button>

        <div class="user-dropdown">
          <span>{{ currentUser }}</span>
          <button class="btn-logout" title="Đăng xuất" @click="handleLogout">
            <i class="bx bx-log-out"></i>
          </button>
        </div>
      </div>
    </header>

    <!-- Workspace 2 Columns -->
    <main class="studio-workspace">
      <!-- Left Column: Controls (Vue) -->
      <aside class="studio-sidebar">
        <div class="sidebar-header">
          <h2 class="sidebar-title">Nội dung • {{ currentPageData.name }}</h2>
          <p class="sidebar-subtitle">Chỉnh sửa nội dung thời gian thực, bên phải sẽ cập nhật tức thì.</p>
        </div>

        <div class="sidebar-sections">
          <!-- Accordion Section Cards -->
          <div
            v-for="(section, sIndex) in currentPageData.sections"
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
              <span class="section-badge">Khối {{ sIndex + 1 }}</span>
            </div>

            <!-- Accordion Body -->
            <div v-show="openSectionId === section.id" class="section-body">
              <div
                v-for="field in section.fields"
                :key="field.key"
                class="field-group"
              >
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
                    <img :src="field.value.startsWith('http') ? field.value : '/' + field.value" alt="Preview" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- Right Column: Live Preview Iframe -->
      <section class="studio-preview">
        <div class="preview-frame-container" :class="currentDevice">
          <iframe
            ref="previewIframe"
            :src="iframeSrc"
            class="live-iframe"
            @load="onIframeLoaded"
          ></iframe>
        </div>
        <div class="preview-footer-note">
          <i class="bx bx-broadcast"></i>
          <span>Live Realtime Preview • Vue 3 syncs with Blade frame</span>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';

// Devices list
const devices = [
  { id: 'desktop', label: 'Desktop', icon: 'bx bx-desktop' },
  { id: 'tablet', label: 'Tablet', icon: 'bx bx-tab' },
  { id: 'mobile', label: 'Mobile', icon: 'bx bx-mobile-alt' },
];

const currentDevice = ref('desktop');
const currentUser = ref(window.__STUDIO_USER__ || 'Admin');
const isSaving = ref(false);
const hasUnsavedChanges = ref(false);
const previewIframe = ref(null);

// Pages & their editable sections
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

const selectedPageSlug = ref('/');
const openSectionId = ref('hero');

// Current page object
const currentPageData = computed(() => {
  return pages.value.find((p) => p.slug === selectedPageSlug.value) || pages.value[0];
});

// Iframe source URL
const iframeSrc = computed(() => {
  return selectedPageSlug.value;
});

// Toggle accordion
const toggleSection = (id) => {
  openSectionId.value = openSectionId.value === id ? null : id;
};

// Switch active accordion on page change
watch(selectedPageSlug, () => {
  nextTick(() => {
    if (currentPageData.value.sections && currentPageData.value.sections.length > 0) {
      openSectionId.value = currentPageData.value.sections[0].id;
    }
  });
});

// PostMessage Realtime sync to Blade Iframe
const onFieldChange = (sectionId, fieldKey, value) => {
  hasUnsavedChanges.value = true;
  if (!previewIframe.value || !previewIframe.value.contentWindow) return;

  // Send message into Blade Iframe
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

const onIframeLoaded = () => {
  // Can send initial handshake or sync if needed
};

// Reset page
const resetCurrentPage = () => {
  if (confirm('Bạn có muốn đặt lại thay đổi trang này?')) {
    hasUnsavedChanges.value = false;
    if (previewIframe.value) {
      previewIframe.value.src = selectedPageSlug.value;
    }
  }
};

// Save changes (Publish)
const saveChanges = () => {
  isSaving.value = true;
  setTimeout(() => {
    isSaving.value = false;
    hasUnsavedChanges.value = false;
    alert('Đã xuất bản cấu hình thành công!');
  }, 600);
};

// Logout
const handleLogout = () => {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/admin/logout';

  const tokenInput = document.createElement('input');
  tokenInput.type = 'hidden';
  tokenInput.name = '_token';
  const metaToken = document.querySelector('meta[name="csrf-token"]');
  tokenInput.value = metaToken ? metaToken.content : '';

  form.appendChild(tokenInput);
  document.body.appendChild(form);
  form.submit();
};
</script>

<style scoped>
.studio-root {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100vw;
  background-color: #f0f2f5;
  color: #1a1d20;
  overflow: hidden;
}

/* Topbar */
.studio-topbar {
  height: 56px;
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  z-index: 50;
  flex-shrink: 0;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.studio-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
  font-size: 15px;
  color: #111;
  text-decoration: none;
  padding-right: 14px;
  border-right: 1px solid #e2e8f0;
}

.brand-badge {
  background: #6366f1;
  color: #fff;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 800;
}

.pro-pill {
  background: #e0e7ff;
  color: #4338ca;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  border: 1px solid #c7d2fe;
}

.page-select-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.page-select {
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 6px 32px 6px 34px;
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  cursor: pointer;
  appearance: none;
  outline: none;
  transition: all 0.2s;
}

.page-select:focus {
  border-color: #6366f1;
  background: #fff;
}

.page-select-icon {
  position: absolute;
  left: 10px;
  color: #64748b;
  font-size: 16px;
  pointer-events: none;
}

.page-select-arrow {
  position: absolute;
  right: 10px;
  color: #64748b;
  font-size: 14px;
  pointer-events: none;
}

/* Center device switcher */
.topbar-center {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
}

.device-btn {
  background: transparent;
  border: none;
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.device-btn.active {
  background: #ffffff;
  color: #0f172a;
  font-weight: 600;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

/* Topbar right */
.topbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #10b981;
  font-weight: 500;
  margin-right: 6px;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10b981;
  transition: background 0.3s;
}

.status-dot.unsaved {
  background: #f59e0b;
}

.btn-draft {
  background: #fff;
  border: 1px solid #cbd5e1;
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-draft:hover {
  background: #f8fafc;
}

.btn-publish {
  background: #6366f1;
  border: none;
  padding: 7px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-publish:hover {
  background: #4f46e5;
}

.btn-publish:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.user-dropdown {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 8px;
  border-radius: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  font-size: 13px;
  color: #334155;
  margin-left: 6px;
}

.btn-logout {
  background: transparent;
  border: none;
  color: #ef4444;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  padding: 2px;
  transition: transform 0.2s;
}

.btn-logout:hover {
  transform: scale(1.1);
}

/* Workspace 2 Columns */
.studio-workspace {
  display: flex;
  flex: 1;
  height: calc(100vh - 56px);
  overflow: hidden;
}

/* Left Column */
.studio-sidebar {
  width: 440px;
  min-width: 400px;
  max-width: 500px;
  background: #ffffff;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.sidebar-header {
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f1f5f9;
}

.sidebar-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.sidebar-subtitle {
  font-size: 12px;
  color: #64748b;
}

.sidebar-sections {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Section Accordion Card */
.section-card {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  background: #ffffff;
  transition: all 0.2s;
  overflow: hidden;
}

.section-card.active {
  border-color: #6366f1;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08);
}

.section-header {
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  user-select: none;
  background: #fff;
}

.section-header:hover {
  background: #f8fafc;
}

.section-title-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13.5px;
  font-weight: 600;
  color: #1e293b;
}

.section-badge {
  background: #f1f5f9;
  color: #475569;
  font-size: 11px;
  padding: 2px 7px;
  border-radius: 12px;
  font-weight: 600;
}

.section-body {
  padding: 16px;
  background: #fcfdfe;
  border-top: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
}

.field-input,
.field-textarea {
  width: 100%;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 13px;
  color: #1e293b;
  outline: none;
  transition: border-color 0.2s;
}

.field-input:focus,
.field-textarea:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
}

.image-preview-thumb {
  margin-top: 6px;
  width: 80px;
  height: 50px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.image-preview-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Right Column: Live Preview */
.studio-preview {
  flex: 1;
  background: #cbd5e1;
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  position: relative;
}

.preview-frame-container {
  width: 100%;
  height: 100%;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  transition: width 0.3s ease, height 0.3s ease;
  position: relative;
}

.preview-frame-container.desktop {
  width: 100%;
  height: 100%;
}

.preview-frame-container.tablet {
  width: 768px;
  height: 95%;
  border-radius: 20px;
  border: 10px solid #1e293b;
}

.preview-frame-container.mobile {
  width: 375px;
  height: 92%;
  border-radius: 30px;
  border: 12px solid #1e293b;
}

.live-iframe {
  width: 100%;
  height: 100%;
  border: none;
  display: block;
}

.preview-footer-note {
  position: absolute;
  bottom: 8px;
  left: 20px;
  font-size: 11px;
  color: #475569;
  display: flex;
  align-items: center;
  gap: 6px;
  pointer-events: none;
}
</style>

