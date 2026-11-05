<template>
  <div class="studio-root">
    <!-- Topbar -->
    <header class="studio-topbar">
      <div class="topbar-left">
        <a href="/admin/studio" class="studio-brand">
          <span class="brand-logo-badge">
            <img :src="logoUrl" alt="Norm Logo" class="brand-logo-img" />
          </span>
          <span>Website Studio</span>
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
        <div class="device-switch-group">
          <button v-for="d in devices" :key="d.id" class="device-btn" :class="{ active: currentDevice === d.id }"
            @click="setDevice(d.id)">
            <i :class="d.icon"></i> {{ d.label }}
          </button>
        </div>
      </div>

      <!-- Right: Actions -->
      <div class="topbar-right">
        <div class="status-indicator">
          <span class="status-dot" :class="{ unsaved: hasUnsavedChanges }"></span>
          <span>{{ hasUnsavedChanges ? 'Có thay đổi chưa lưu' : 'Đã tải cấu hình' }}</span>
        </div>

        <button class="btn-draft" :disabled="isSavingDraft" @click="saveDraft">
          <span v-if="isSavingDraft">Đang lưu...</span>
          <span v-else>Lưu nháp</span>
        </button>
        <button class="btn-publish" :disabled="isSaving" @click="saveChanges">
          <span v-if="isSaving">Đang lưu...</span>
          <span v-else>Xuất bản</span>
        </button>

        <div class="user-menu-wrapper" ref="userMenuRef">
          <div class="user-trigger" @click="toggleUserMenu">
            <div class="user-avatar">
              <i class="bx bx-user"></i>
            </div>
            <span class="user-name">{{ currentUser }}</span>
            <i class="bx bx-chevron-down user-arrow" :class="{ open: isUserMenuOpen }"></i>
          </div>

          <!-- Dropdown Menu -->
          <transition name="dropdown-fade">
            <div v-if="isUserMenuOpen" class="user-dropdown-popover">
              <a :href="selectedPageSlug" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
                <i class="bx bx-file-blank"></i>
                <span>Xem bản nháp</span>
              </a>

              <a href="/" target="_blank" class="dropdown-item" @click="isUserMenuOpen = false">
                <i class="bx bx-globe"></i>
                <span>Mở website đã xuất bản</span>
              </a>

              <div class="dropdown-divider"></div>

              <button class="dropdown-item danger" @click="handleLogout">
                <i class="bx bx-log-out"></i>
                <span>Đăng xuất</span>
              </button>
            </div>
          </transition>
        </div>
      </div>
    </header>

    <!-- Workspace Columns -->
    <main class="studio-workspace">
      <!-- 1. Leftmost Icon Navigation Sidebar -->
      <nav class="studio-nav-rail">
        <!-- Top overview item -->
        <button class="nav-rail-item" :class="{ active: currentNavTab === 'overview' }"
          @click="selectNavTab('overview')" title="Tổng quan">
          <i class="bx bx-grid-alt"></i>
          <span>Tổng quan</span>
        </button>

        <div class="nav-rail-divider"></div>

        <div class="nav-rail-section-label">NỘI DUNG</div>

        <!-- Content nav items -->
        <button class="nav-rail-item" :class="{ active: currentNavTab === 'pages' }"
          @click="selectNavTab('pages')" title="Chỉnh sửa trang">
          <i class="bx bx-edit-alt"></i>
          <span>Chỉnh sửa</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'projects' }"
          @click="selectNavTab('projects')" title="Dự án (Work)">
          <i class="bx bx-briefcase-alt"></i>
          <span>Dự án</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'services' }"
          @click="selectNavTab('services')" title="Dịch vụ">
          <i class="bx bx-layer"></i>
          <span>Dịch vụ</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'news' }"
          @click="selectNavTab('news')" title="Tin tức & Bài viết">
          <i class="bx bx-news"></i>
          <span>Bài viết</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'reviews' }"
          @click="selectNavTab('reviews')" title="Đánh giá khách hàng">
          <i class="bx bx-star"></i>
          <span>Reviews</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'leads' }"
          @click="selectNavTab('leads')" title="Khách hàng liên hệ">
          <i class="bx bx-envelope"></i>
          <span>Liên hệ</span>
        </button>

        <button class="nav-rail-item" :class="{ active: currentNavTab === 'media' }"
          @click="selectNavTab('media')" title="Thư viện hình ảnh">
          <i class="bx bx-image-alt"></i>
          <span>Thư viện</span>
        </button>
      </nav>

      <!-- 2. Controls Panel (Sub-Sidebar) -->
      <aside class="studio-sidebar" :style="{ width: sidebarWidth + 'px' }">
        <!-- Tab: Chỉnh sửa trang trực quan (Current Studio) -->
        <template v-if="currentNavTab === 'pages'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Nội dung • {{ currentPageData.name }}</h2>
            <p class="sidebar-subtitle">Chỉnh sửa nội dung thời gian thực, bên phải sẽ cập nhật tức thì.</p>
          </div>

          <div class="sidebar-sections">
            <!-- Accordion Section Cards -->
            <div v-for="section in currentPageData.sections" :key="section.id" class="section-card"
              :class="{ active: openSectionId === section.id }">
              <div class="section-header" @click="toggleSection(section.id)">
                <div class="section-title-wrap">
                  <i class="bx" :class="openSectionId === section.id ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
                  <span>{{ section.title }}</span>
                </div>
              </div>

              <!-- Accordion Body -->
              <div v-show="openSectionId === section.id" class="section-body">
                <div v-for="field in section.fields" :key="field.key" class="field-group">
                  <label class="field-label">{{ field.label }}</label>

                  <!-- Text Input -->
                  <input v-if="field.type === 'text'" v-model="field.value" type="text" class="field-input"
                    @input="onFieldChange(section.id, field.key, field.value)" />

                  <!-- Textarea -->
                  <textarea v-else-if="field.type === 'textarea'" v-model="field.value" class="field-textarea"
                    :rows="field.rows || 3" @input="onFieldChange(section.id, field.key, field.value)"></textarea>

                  <!-- Image URL Input -->
                  <div v-else-if="field.type === 'image'" class="image-field-wrap">
                    <input v-model="field.value" type="text" class="field-input" placeholder="assets/images/..."
                      @input="onFieldChange(section.id, field.key, field.value)" />
                    <div v-if="field.value" class="image-preview-thumb">
                      <img :src="field.value.startsWith('http') ? field.value : '/' + field.value" alt="Preview" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <!-- Tab: Tổng quan -->
        <template v-else-if="currentNavTab === 'overview'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Tổng quan hệ thống</h2>
            <p class="sidebar-subtitle">Thống kê dữ liệu và trạng thái trang web Norm.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-stat-card">
              <div class="stat-number">4</div>
              <div class="stat-label">Trang chính hoạt động</div>
            </div>
            <div class="panel-stat-card">
              <div class="stat-number">6</div>
              <div class="stat-label">Dự án trong Portfolio</div>
            </div>
            <div class="panel-stat-card">
              <div class="stat-number">3</div>
              <div class="stat-label">Gói dịch vụ cốt lõi</div>
            </div>
            <div class="panel-stat-card">
              <div class="stat-number">2</div>
              <div class="stat-label">Đánh giá đối tác</div>
            </div>
          </div>
        </template>

        <!-- Tab: Dự án (Portfolio) -->
        <template v-else-if="currentNavTab === 'projects'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Quản lý Dự án (Work)</h2>
            <p class="sidebar-subtitle">Danh sách các dự án hiển thị tại trang Portfolio.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-list-item" v-for="(p, idx) in demoProjects" :key="idx">
              <div class="item-thumb"><img :src="p.image" alt=""></div>
              <div class="item-info">
                <div class="item-name">{{ p.name }}</div>
                <div class="item-meta">{{ p.category }}</div>
              </div>
            </div>
          </div>
        </template>

        <!-- Tab: Dịch vụ -->
        <template v-else-if="currentNavTab === 'services'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Dịch vụ cốt lõi</h2>
            <p class="sidebar-subtitle">Các gói dịch vụ hiển thị tại trang chủ.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-list-item" v-for="(s, idx) in demoServices" :key="idx">
              <div class="item-badge">{{ s.number }}</div>
              <div class="item-info">
                <div class="item-name">{{ s.title }}</div>
                <div class="item-meta">{{ s.desc }}</div>
              </div>
            </div>
          </div>
        </template>

        <!-- Tab: Tin tức -->
        <template v-else-if="currentNavTab === 'news'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Bài viết & Tin tức</h2>
            <p class="sidebar-subtitle">Các bài viết chia sẻ góc nhìn thương hiệu.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-list-item" v-for="(n, idx) in demoNews" :key="idx">
              <div class="item-thumb"><img :src="n.image" alt=""></div>
              <div class="item-info">
                <div class="item-name">{{ n.title }}</div>
                <div class="item-meta">{{ n.date }}</div>
              </div>
            </div>
          </div>
        </template>

        <!-- Tab: Đánh giá -->
        <template v-else-if="currentNavTab === 'reviews'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Đánh giá của đối tác</h2>
            <p class="sidebar-subtitle">Slider nhận xét của khách hàng tại trang chủ.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-list-item" v-for="(r, idx) in demoReviews" :key="idx">
              <div class="item-avatar"><i class="bx bx-user"></i></div>
              <div class="item-info">
                <div class="item-name">{{ r.author }}</div>
                <div class="item-meta">{{ r.role }}</div>
              </div>
            </div>
          </div>
        </template>

        <!-- Tab: Liên hệ -->
        <template v-else-if="currentNavTab === 'leads'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Yêu cầu liên hệ / Báo giá</h2>
            <p class="sidebar-subtitle">Khách hàng gửi qua Contact Drawer.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="panel-empty-state">
              <i class="bx bx-inbox"></i>
              <span>Chưa có liên hệ mới</span>
            </div>
          </div>
        </template>

        <!-- Tab: Thư viện -->
        <template v-else-if="currentNavTab === 'media'">
          <div class="sidebar-header">
            <h2 class="sidebar-title">Thư viện tệp</h2>
            <p class="sidebar-subtitle">Ảnh banner, mô hình 3D norm.glb và tư liệu.</p>
          </div>
          <div class="sidebar-panel-content">
            <div class="media-grid">
              <div class="media-card" v-for="(m, mIdx) in demoMedia" :key="mIdx">
                <img :src="m.url" :alt="m.name">
                <span>{{ m.name }}</span>
              </div>
            </div>
          </div>
        </template>
      </aside>

      <!-- Resizer Handle Bar -->
      <div class="resizer-bar" :class="{ dragging: isResizing }" @mousedown="startResize">
        <div class="resizer-pill" title="Kéo để thay đổi độ rộng cột">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>

      <!-- Right Column: Live Preview Iframe -->
      <section class="studio-preview" ref="previewAreaRef">
        <div class="preview-frame-container" :class="[currentDevice, { 'desktop-scaled': currentDevice === 'desktop' }]"
          :style="frameContainerStyle">
          <iframe ref="previewIframe" :src="iframeSrc" class="live-iframe" @load="onIframeLoaded"></iframe>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';

// Devices list
const devices = [
  { id: 'desktop', label: 'Desktop', icon: 'bx bx-desktop' },
  { id: 'tablet', label: 'Tablet', icon: 'bx bx-tab' },
  { id: 'mobile', label: 'Mobile', icon: 'bx bx-mobile-alt' },
];

// Active Left Navigation Rail Tab
const currentNavTab = ref('pages');
const selectNavTab = (tabId) => {
  currentNavTab.value = tabId;
};

// Demo datasets for quick reference
const demoProjects = ref([
  { name: 'Tiên Phong Identity', category: 'Brand identity', image: '/assets/images/project1.png' },
  { name: 'Norm Digital Experience', category: 'UI/UX Design', image: '/assets/images/project2.png' },
  { name: 'Heritage Brand Strategy', category: 'Packaging', image: '/assets/images/project3.png' },
  { name: 'E-commerce Platform', category: 'Web Development', image: '/assets/images/project4.png' },
]);

const demoServices = ref([
  { number: '01', title: 'Thiết kế từ chiến lược', desc: 'Định hình nền tảng bản sắc thương hiệu vững chắc.' },
  { number: '02', title: 'Hệ thống nhận diện toàn diện', desc: 'Thiết kế hệ sinh thái nhận diện đồng bộ và khác biệt.' },
  { number: '03', title: 'Trải nghiệm số & Tương tác', desc: 'Website và sản phẩm số tối ưu trải nghiệm người dùng.' },
]);

const demoNews = ref([
  { title: 'Tái định vị thương hiệu kỷ nguyên số', date: '24/10/2026', image: '/assets/images/project1.png' },
  { title: 'Thiết kế tối giản và sức hút thương hiệu', date: '18/10/2026', image: '/assets/images/project2.png' },
]);

const demoReviews = ref([
  { author: 'Nguyễn Minh Anh', role: 'Giám đốc Marketing', text: 'Rất hài lòng với giải pháp nhận diện chuyên nghiệp của Norm.' },
  { author: 'Trần Văn B', role: 'CEO Founder', text: 'Quy trình bài bản, sáng tạo và hiểu thị trường sâu sắc.' },
]);

const demoMedia = ref([
  { name: 'banner.png', url: '/assets/images/banner.png' },
  { name: 'project1.png', url: '/assets/images/project1.png' },
  { name: 'project2.png', url: '/assets/images/project2.png' },
  { name: 'project3.png', url: '/assets/images/project3.png' },
]);

const currentDevice = ref('desktop');
const currentUser = ref(window.__STUDIO_USER__ || 'Admin');
const logoUrl = ref('/assets/images/logo.png');
const isSaving = ref(false);
const isSavingDraft = ref(false);
const hasUnsavedChanges = ref(false);
const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const handleClickOutside = (e) => {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    isUserMenuOpen.value = false;
  }
};
const previewIframe = ref(null);
const previewAreaRef = ref(null);

// Zoom & Viewport Scaling
const zoomLevel = ref(0.85); // Default scaled so 1440px desktop fits perfectly
const isAutoFit = ref(true);

const setDevice = (device) => {
  currentDevice.value = device;
  if (device === 'desktop') {
    calculateAutoFit();
  } else {
    zoomLevel.value = 1;
  }
};

const calculateAutoFit = () => {
  if (currentDevice.value !== 'desktop' || !previewAreaRef.value) return;
  // Available width in preview area minus 48px padding
  const availableWidth = previewAreaRef.value.clientWidth - 48;
  const standardDesktopWidth = 1440;
  // Scale down so 1440px desktop fits into available preview width (keep minimum legible 0.5)
  const fitScale = Math.min(1, Math.max(0.5, availableWidth / standardDesktopWidth));
  zoomLevel.value = Math.round(fitScale * 100) / 100;
};

const zoomIn = () => {
  isAutoFit.value = false;
  zoomLevel.value = Math.min(1.5, Math.round((zoomLevel.value + 0.05) * 100) / 100);
};

const zoomOut = () => {
  isAutoFit.value = false;
  zoomLevel.value = Math.max(0.4, Math.round((zoomLevel.value - 0.05) * 100) / 100);
};

const zoomFit = () => {
  isAutoFit.value = true;
  calculateAutoFit();
};

const frameContainerStyle = computed(() => {
  if (currentDevice.value === 'desktop') {
    const scale = zoomLevel.value;
    const desktopWidth = 1440;
    // Standard viewport height scaled, container expands as user scrolls inside iframe
    const desktopHeight = 900;
    return {
      width: `${desktopWidth}px`,
      height: `${desktopHeight}px`,
      transform: `scale(${scale})`,
      transformOrigin: 'top center',
      marginBottom: `-${desktopHeight * (1 - scale)}px`,
      marginRight: `-${desktopWidth * (1 - scale) / 2}px`,
      marginLeft: `-${desktopWidth * (1 - scale) / 2}px`,
    };
  }
  return {};
});

// Resizable sidebar width
const sidebarWidth = ref(440);
const isResizing = ref(false);

const startResize = (e) => {
  e.preventDefault();
  isResizing.value = true;
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';

  // Prevent iframe from swallowing mousemove events while dragging
  if (previewIframe.value) {
    previewIframe.value.style.pointerEvents = 'none';
  }

  window.addEventListener('mousemove', handleResize);
  window.addEventListener('mouseup', stopResize);
};

const handleResize = (e) => {
  if (!isResizing.value) return;
  // Constrain width between 320px and 750px (or 60% of viewport)
  const maxWidth = Math.min(window.innerWidth * 0.65, 800);
  const newWidth = Math.max(320, Math.min(e.clientX, maxWidth));
  sidebarWidth.value = newWidth;
  if (isAutoFit.value) {
    calculateAutoFit();
  }
};

const stopResize = () => {
  isResizing.value = false;
  document.body.style.cursor = '';
  document.body.style.userSelect = '';

  if (previewIframe.value) {
    previewIframe.value.style.pointerEvents = '';
  }

  window.removeEventListener('mousemove', handleResize);
  window.removeEventListener('mouseup', stopResize);
};

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
const openSectionId = ref(null);

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

// Reset active accordion on page change
watch(selectedPageSlug, () => {
  openSectionId.value = null;
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

// Save Draft
const saveDraft = () => {
  isSavingDraft.value = true;
  setTimeout(() => {
    isSavingDraft.value = false;
    hasUnsavedChanges.value = false;
    alert('Đã lưu bản nháp thành công!');
  }, 500);
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

onMounted(() => {
  calculateAutoFit();
  window.addEventListener('resize', () => {
    if (isAutoFit.value) calculateAutoFit();
  });
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
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
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.studio-root input,
.studio-root textarea,
.studio-root select,
.studio-root button {
  font-family: inherit;
}

/* Topbar */
.studio-topbar {
  height: 56px;
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px 0 20px;
  z-index: 100;
  flex-shrink: 0;
  position: relative;
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


.brand-logo-badge {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}


.brand-logo-img {
  width: 20px;
  height: auto;
  display: block;
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

/* Center device switcher & Zoom controls */
.topbar-center {
  display: flex;
  align-items: center;
  gap: 12px;
}

.device-switch-group {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
}

.zoom-controls {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 3px 6px;
  gap: 4px;
}

.zoom-btn {
  background: transparent;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 15px;
}

.zoom-btn:hover {
  background: #ffffff;
  color: #0f172a;
}

.zoom-text {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  min-width: 38px;
  text-align: center;
  user-select: none;
}

.zoom-reset-btn {
  background: #e2e8f0;
  border: none;
  border-radius: 4px;
  padding: 2px 7px;
  font-size: 11px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
}

.zoom-reset-btn:hover {
  background: #6366f1;
  color: #ffffff;
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
  background: #ffffff;
  border: 1px solid #d1d5db;
  padding: 7px 15px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-draft:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
  color: #111827;
}

.btn-publish {
  background: #111827;
  border: 1px solid #111827;
  padding: 7px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-publish:hover {
  background: #23272f;
  border-color: #23272f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15);
}

.btn-publish:active {
  transform: translateY(0);
}

.btn-publish:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* User Profile Trigger & Dropdown Menu */
.user-menu-wrapper {
  position: relative;
  margin-left: 6px;
}

.user-trigger {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 3px 8px 3px 4px;
  border-radius: 9999px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
}

.user-trigger:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.user-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
  transition: all 0.15s ease;
}

.user-trigger:hover .user-avatar {
  background: #e2e8f0;
  color: #0f172a;
}

.user-name {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  letter-spacing: -0.01em;
}

.user-arrow {
  font-size: 12px;
  color: #94a3b8;
  transition: transform 0.2s ease, color 0.15s ease;
}

.user-arrow.open {
  transform: rotate(180deg);
  color: #334155;
}

/* Dropdown popover */
.user-dropdown-popover {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  width: max-content;
  min-width: 200px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.12), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
  padding: 6px;
  z-index: 1000;
  overflow: hidden;
}

.dropdown-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 4px 0;
}

.dropdown-item {
  width: 100%;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 8px;
  background: transparent;
  border: none;
  font-size: 13.5px;
  font-weight: 500;
  color: #1e293b;
  text-decoration: none;
  cursor: pointer;
  text-align: left;
  white-space: nowrap;
  transition: background 0.15s, color 0.15s;
}

.dropdown-item i {
  font-size: 17px;
  color: #475569;
  transition: color 0.15s;
  flex-shrink: 0;
}

.dropdown-item:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.dropdown-item:hover i {
  color: #0f172a;
}

.dropdown-item.danger {
  color: #dc2626;
}

.dropdown-item.danger i {
  color: #dc2626;
}

.dropdown-item.danger:hover {
  background: #fef2f2;
  color: #b91c1c;
}

.dropdown-item.danger:hover i {
  color: #b91c1c;
}

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}

/* Workspace Columns */
.studio-workspace {
  display: flex;
  flex: 1;
  height: calc(100vh - 56px);
  overflow: hidden;
}

/* 1. Leftmost Navigation Rail (Icon Sidebar) */
.studio-nav-rail {
  width: 72px;
  background: #fafafa;
  border-right: 1px solid #eaedf1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px 0;
  gap: 3px;
  flex-shrink: 0;
  z-index: 30;
  overflow-y: auto;
}

.nav-rail-item {
  width: 58px;
  height: 54px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
  padding: 0;
}

.nav-rail-item i {
  font-size: 20px;
  color: #64748b;
  transition: all 0.15s ease;
}

.nav-rail-item span {
  font-size: 10.5px;
  font-weight: 500;
  color: #64748b;
  letter-spacing: -0.01em;
  white-space: nowrap;
  transition: color 0.15s ease;
}

.nav-rail-item:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.nav-rail-item:hover i {
  color: #1e293b;
}

.nav-rail-item:hover span {
  color: #1e293b;
}

.nav-rail-item.active {
  background: #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
}

.nav-rail-item.active i {
  color: #0f172a;
  transform: scale(1.05);
}

.nav-rail-item.active span {
  font-weight: 700;
  color: #0f172a;
}

.nav-rail-divider {
  width: 44px;
  height: 1px;
  background: #e2e8f0;
  margin: 8px 0 6px;
}

.nav-rail-section-label {
  font-size: 9.5px;
  font-weight: 700;
  color: #94a3b8;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin: 0 0 4px;
  user-select: none;
}

/* 2. Controls Panel (Sub-Sidebar) */
.studio-sidebar {
  min-width: 320px;
  max-width: 800px;
  background: #ffffff;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  flex-shrink: 0;
}

/* Panel Content for non-editor tabs */
.sidebar-panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.panel-stat-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.panel-stat-card .stat-number {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  min-width: 40px;
}

.panel-stat-card .stat-label {
  font-size: 13px;
  font-weight: 500;
  color: #475569;
}

.panel-list-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  transition: all 0.15s ease;
}

.panel-list-item:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.item-thumb {
  width: 44px;
  height: 44px;
  border-radius: 6px;
  overflow: hidden;
  background: #f1f5f9;
  flex-shrink: 0;
}

.item-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-badge {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #0f172a;
  color: #FDFF8E;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.item-info {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-meta {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

.panel-empty-state {
  text-align: center;
  padding: 48px 16px;
  color: #94a3b8;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.panel-empty-state i {
  font-size: 36px;
  color: #cbd5e1;
}

.panel-empty-state span {
  font-size: 13px;
}

.media-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.media-card {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
}

.media-card img {
  width: 100%;
  height: 90px;
  object-fit: cover;
}

.media-card span {
  padding: 6px 8px;
  font-size: 11px;
  color: #475569;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Resizer Handle Bar */
.resizer-bar {
  width: 14px;
  margin-left: -7px;
  margin-right: -7px;
  z-index: 40;
  cursor: col-resize;
  display: flex;
  align-items: center;
  justify-content: center;
  user-select: none;
  position: relative;
  transition: background-color 0.15s;
}

.resizer-bar:hover,
.resizer-bar.dragging {
  background-color: rgba(15, 23, 42, 0.05);
}

.resizer-pill {
  width: 16px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
  transition: all 0.15s ease;
}

.resizer-bar:hover .resizer-pill,
.resizer-bar.dragging .resizer-pill {
  border-color: #0f172a;
  background: #ffffff;
  box-shadow: 0 3px 10px rgba(15, 23, 42, 0.12);
  transform: scale(1.05);
}

.resizer-pill .dot {
  width: 3px;
  height: 3px;
  background-color: #94a3b8;
  border-radius: 50%;
  transition: background-color 0.15s;
}

.resizer-bar:hover .resizer-pill .dot,
.resizer-bar.dragging .resizer-pill .dot {
  background-color: #0f172a;
}

.sidebar-header {
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f1f5f9;
}

.sidebar-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.02em;
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
  transition: border-color 0.15s, box-shadow 0.15s;
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
  align-items: flex-start;
  justify-content: center;
  overflow: auto;
  position: relative;
}

.preview-frame-container {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  transition: transform 0.2s ease-out;
  position: relative;
}

.preview-frame-container.desktop {
  /* Dynamic scale computed via frameContainerStyle */
  max-width: none;
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
