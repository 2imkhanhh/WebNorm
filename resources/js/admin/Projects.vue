<template>
  <Layout>
    <div class="projects-workspace-view" :key="renderKey">
      <div class="projects-container">
        <!-- Header Card -->
        <div class="projects-header-card">
          <div class="projects-header-main">
            <div class="projects-title-area">
              <h1 class="projects-title">
                <span>Quản lý Dự án</span>
              </h1>
              <p class="projects-subtitle">Quản lý danh sách dự án, phân loại danh mục và toàn bộ nội dung chi tiết
                từng dự án.</p>
            </div>
            <div class="projects-header-actions">
              <div class="projects-header-search">
                <i class="bx bx-search search-icon"></i>
                <input v-model="searchQuery" type="text" class="projects-header-search-input" placeholder="Tìm kiếm" />
                <button v-if="searchQuery" type="button" class="btn-clear-header-search" @click="searchQuery = ''"
                  title="Xóa tìm kiếm">
                  <i class="bx bx-x"></i>
                </button>
              </div>
              <button type="button" class="btn-projects-manage-cat" @click="openCategoryModal"
                title="Quản lý danh mục dự án">
                <i class="bx bx-category"></i> Danh mục ({{ localCategories.length }})
              </button>
              <button type="button" class="btn-projects-create" @click="openCreateModal">
                <i class="bx bx-plus"></i> Thêm dự án mới
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

        <!-- Filter Bar -->
        <div class="projects-filter-bar">
          <div class="filter-pills-row">
            <button type="button" class="filter-tab-pill" :class="{ active: selectedFilterCat === 'all' }"
              @click="selectedFilterCat = 'all'">
              Tất cả ({{ localProjects.length }})
            </button>
            <button type="button" class="filter-tab-pill" :class="{ active: selectedFilterCat === 'featured' }"
              @click="selectedFilterCat = 'featured'">
              Nổi bật ({{ featuredCount }})
            </button>
            <button v-for="cat in localCategories" :key="cat.id" type="button" class="filter-tab-pill"
              :class="{ active: selectedFilterCat === cat.id }" @click="selectedFilterCat = cat.id">
              {{ getTrans(cat.name) }} ({{ getCategoryProjectCount(cat.id) }})
            </button>
          </div>
        </div>

        <!-- Projects Card Grid -->
        <div v-if="filteredProjects && filteredProjects.length > 0" class="projects-card-grid">
          <div v-for="p in paginatedProjects" :key="p.id" class="project-card-full">
            <div class="project-card-thumb-wrap">
              <img
                :src="p.thumbnail ? (p.thumbnail.startsWith('http') ? p.thumbnail : '/' + p.thumbnail.replace(/^\//, '')) : '/assets/images/project1.png'"
                :alt="getTrans(p.title)" loading="lazy" />
              <div v-if="p.is_featured" class="card-badges-top">
                <span class="card-badge-feat" title="Hiển thị tại trang chủ">Nổi bật</span>
              </div>
            </div>

            <div class="project-card-body">
              <h3 class="project-card-title">{{ getTrans(p.title) }}</h3>

              <div class="project-card-categories">
                <span v-for="catName in getProjectCategoryList(p)" :key="catName" class="project-cat-tag">
                  {{ catName }}
                </span>
                <span v-if="getProjectCategoryList(p).length === 0" class="project-cat-tag text-muted">
                  Chưa chọn danh mục
                </span>
              </div>
            </div>

            <div class="project-card-footer-actions">
              <a :href="'/work/' + p.slug" target="_blank" class="btn-card-preview-live"
                title="Xem trang chi tiết thực tế trên website">
                <i class="bx bx-link-external"></i> Xem chi tiết
              </a>
              <div class="card-action-btns-right d-flex gap-1">
                <button type="button" class="btn-action edit" @click="openEditModal(p)" title="Chỉnh sửa dự án">
                  <i class="bx bx-edit-alt"></i>
                </button>
                <button type="button" class="btn-action delete" @click="confirmDelete(p)" title="Xóa dự án">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination Controls (9 projects per page) -->
        <div v-if="totalPages > 1" class="projects-pagination-wrap">
          <div class="pagination-controls">
            <button type="button" class="btn-page-nav" :disabled="currentPage === 1" @click="setPage(currentPage - 1)"
              title="Trang trước">
              <i class="bx bx-chevron-left"></i>
            </button>

            <button v-for="page in totalPages" :key="page" type="button" class="btn-page-num"
              :class="{ active: currentPage === page }" @click="setPage(page)">
              {{ page }}
            </button>

            <button type="button" class="btn-page-nav" :disabled="currentPage === totalPages"
              @click="setPage(currentPage + 1)" title="Trang tiếp theo">
              <i class="bx bx-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredProjects || filteredProjects.length === 0"
          class="empty-state bg-white p-5 rounded-3 border text-center my-3">
          <i class="bx bx-folder-open empty-icon fs-1 text-muted"></i>
          <h4 class="mt-2 text-dark">Không tìm thấy dự án nào</h4>
          <p class="text-muted">Không có dự án nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm của bạn.</p>
          <button v-if="searchQuery || selectedFilterCat !== 'all'" class="btn btn-outline-secondary btn-sm mt-1"
            @click="resetFilters">
            Đặt lại bộ lọc
          </button>
        </div>
      </div>
    </div>

    <!-- Spacious Full Project Editor Modal (Tabs: Cơ bản & Chi tiết dự án) -->
    <div v-if="isModalOpen" class="studio-modal-backdrop" @click.self="isModalOpen = false">
      <div class="studio-modal-dialog-large">
        <div class="studio-modal-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-3">
            <h3 class="modal-title m-0">
              {{ editingItem ? 'Chỉnh sửa: ' + getTrans(editingItem.title) : 'Thêm dự án mới' }}
            </h3>
          </div>

          <div class="d-flex align-items-center gap-2">
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

            <button class="btn-close-modal" @click="isModalOpen = false">
              <i class="bx bx-x"></i>
            </button>
          </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="modal-nav-tabs">
          <button type="button" class="modal-nav-tab" :class="{ active: activeTab === 'basic' }"
            @click="activeTab = 'basic'">
            1. Thông tin chung
          </button>
          <button type="button" class="modal-nav-tab" :class="{ active: activeTab === 'detail' }"
            @click="activeTab = 'detail'">
            2. Chi tiết dự án
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="tab-pane-content">
          <!-- TAB 1: THÔNG TIN CHUNG (2 Cột khoa học) -->
          <div v-if="activeTab === 'basic'" class="project-tab-body">
            <!-- Cột trái: Thông tin chính -->
            <div class="project-form-main">
              <!-- Tên dự án -->
              <div class="form-group-custom">
                <label class="form-label-custom">
                  Tên dự án <span class="text-danger">*</span>
                </label>
                <input v-if="modalLang === 'vi'" v-model="form.title_vi" type="text" class="form-input-custom"
                  placeholder="Ví dụ: Tiên Phong Identity" @input="onTitleInput" required />
                <input v-else v-model="form.title_en" type="text" class="form-input-custom"
                  placeholder="E.g. Tien Phong Identity" @input="onTitleInput" required />
              </div>

              <!-- Đường dẫn tĩnh (Slug) -->
              <div class="form-group-custom">
                <label class="form-label-custom">
                  Đường dẫn tĩnh <span class="text-danger">*</span>
                </label>
                <div class="slug-input-wrap">
                  <span class="slug-prefix">/work/</span>
                  <input v-model="form.slug" type="text" class="slug-input-field" placeholder="tien-phong-identity"
                    @input="onSlugInput" required />
                </div>
              </div>

              <!-- Danh mục dự án (Pills) -->
              <div class="form-group-custom">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label-custom">
                    Danh mục dự án <span class="text-danger">*</span>
                  </label>
                  <button type="button" class="btn-manage-cat-link" @click="openCategoryModal">
                    <i class="bx bx-cog"></i> Quản lý danh mục
                  </button>
                </div>
                <div class="project-cat-pills">
                  <button v-for="cat in localCategories" :key="cat.id" type="button" class="cat-select-pill"
                    :class="{ active: form.category_ids.includes(cat.id) }" @click="toggleCategory(cat.id)">
                    <i class="bx" :class="form.category_ids.includes(cat.id) ? 'bx-check' : 'bx-plus'"></i>
                    <span>{{ getTrans(cat.name) }}</span>
                  </button>
                </div>
                <span v-if="!form.category_ids || form.category_ids.length === 0" class="field-hint text-danger mt-1">
                  * Vui lòng chọn ít nhất 1 danh mục cho dự án.
                </span>
              </div>

              <!-- Năm & Khách hàng -->
              <div class="form-row-2col">
                <div class="form-group-custom">
                  <label class="form-label-custom">Năm thực hiện</label>
                  <div class="calendar-picker-wrap">
                    <div class="calendar-input-box" @click="toggleCalendar">
                      <i class="bx bx-calendar calendar-icon"></i>
                      <span v-if="form.year" class="calendar-val">{{ formatYearDisplay(form.year) }}</span>
                      <span v-else class="calendar-placeholder">Chọn thời gian</span>
                      <button v-if="form.year" type="button" class="btn-clear-date" @click.stop="form.year = ''"
                        title="Xóa">
                        <i class="bx bx-x"></i>
                      </button>
                    </div>

                    <!-- Calendar Dropdown Popup -->
                    <div v-if="isCalendarOpen" class="calendar-dropdown" @click.stop>
                      <div class="calendar-header">
                        <button type="button" class="btn-cal-nav" @click="calYear--" title="Năm trước">
                          <i class="bx bx-chevron-left"></i>
                        </button>
                        <span class="cal-year-title">Năm {{ calYear }}</span>
                        <button type="button" class="btn-cal-nav" @click="calYear++" title="Năm sau">
                          <i class="bx bx-chevron-right"></i>
                        </button>
                      </div>

                      <div class="calendar-months-grid">
                        <button v-for="m in 12" :key="m" type="button" class="cal-month-btn"
                          :class="{ active: isMonthActive(m) }" @click="selectMonth(m)">
                          Tháng {{ m }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group-custom">
                  <label class="form-label-custom">Khách hàng</label>
                  <input v-model="form.client" type="text" class="form-input-custom"
                    placeholder="Tên khách hàng / Đối tác" />
                </div>
              </div>
            </div>

            <!-- Cột phải: Ảnh đại diện & Mô tả -->
            <div class="project-form-side">
              <!-- Thumbnail Upload -->
              <div class="form-group-custom">
                <label class="form-label-custom">Ảnh đại diện</label>

                <div class="project-thumb-box">
                  <img v-if="form.thumbnail"
                    :src="form.thumbnail.startsWith('http') ? form.thumbnail : '/' + form.thumbnail.replace(/^\//, '')"
                    alt="Thumbnail preview" class="project-thumb-img" />
                  <div v-else class="project-thumb-empty">
                    <i class="bx bx-image-add"></i>
                    <span>Chưa có ảnh đại diện</span>
                  </div>
                </div>

                <div class="thumb-action-row mt-2">
                  <button type="button" class="btn-upload-thumb" :disabled="isUploadingThumb"
                    @click="triggerThumbUpload">
                    <i class="bx" :class="isUploadingThumb ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                    <span>{{ isUploadingThumb ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                  </button>
                  <button v-if="form.thumbnail" type="button" class="btn-remove-thumb" @click="form.thumbnail = ''"
                    title="Xóa ảnh đại diện">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>

              <!-- Mô tả dự án / Giới thiệu đầu trang chi tiết -->
              <div class="form-group-custom">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label-custom m-0">Mô tả dự án</label>
                </div>
                <textarea v-if="modalLang === 'vi'" v-model="form.description_vi" class="form-textarea-custom" rows="3"
                  placeholder="Ví dụ: Thiết kế bộ nhận diện thương hiệu số toàn diện..."></textarea>
                <textarea v-else v-model="form.description_en" class="form-textarea-custom" rows="3"
                  placeholder="E.g. Comprehensive digital brand identity design..."></textarea>
              </div>

              <!-- Featured Checkbox Card -->
              <div class="project-featured-card">
                <label class="featured-checkbox-label">
                  <input v-model="form.is_featured" type="checkbox" class="featured-checkbox-input" />
                  <div class="featured-info">
                    <span class="featured-title">Dự án nổi bật</span>
                    <span class="featured-desc">Hiển thị tại Showcase trang chủ</span>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- TAB 2: NỘI DUNG TRANG CHI TIẾT (/work/{slug}) -->
          <div v-if="activeTab === 'detail'" class="project-tab-body-detail">
            <!-- 1. Hero Banner Section -->
            <div class="detail-card-section">
              <div class="detail-card-header">
                <div class="detail-card-title-wrap">
                  <span class="section-step-num">1</span>
                  <div>
                    <h4 class="detail-card-title">Ảnh bìa Hero Banner</h4>
                    <p class="detail-card-subtitle">Hiển thị đầu trang (Khuyến nghị 1920×1080px)
                    </p>
                  </div>
                </div>
                <button type="button" class="btn-detail-action" :disabled="isUploadingHero" @click="triggerHeroUpload">
                  <i class="bx" :class="isUploadingHero ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isUploadingHero ? 'Đang tải...' : 'Tải ảnh bìa' }}</span>
                </button>
              </div>

              <div v-if="form.hero_image" class="hero-preview-wrapper mt-2">
                <img
                  :src="form.hero_image.startsWith('http') ? form.hero_image : '/' + form.hero_image.replace(/^\//, '')"
                  alt="Hero banner preview" class="hero-preview-img" />
                <button type="button" class="btn-clear-preview" @click="form.hero_image = ''" title="Xóa ảnh bìa">
                  <i class="bx bx-x"></i>
                </button>
              </div>
              <div v-else class="hero-preview-empty mt-2">
                <i class="bx bx-image"></i>
                <span>Chưa có ảnh bìa</span>
              </div>
            </div>

            <!-- 2. Overview Section -->
            <div class="detail-card-section">
              <div class="detail-card-header">
                <div class="detail-card-title-wrap">
                  <span class="section-step-num">2</span>
                  <div>
                    <h4 class="detail-card-title">Tổng quan dự án</h4>
                    <p class="detail-card-subtitle">Tiêu đề, câu chuyện thương hiệu & ảnh tổng quan</p>
                  </div>
                </div>
              </div>

              <div class="form-row-2col mt-2">
                <div class="form-group-custom">
                  <label class="form-label-custom">Tiêu đề</label>
                  <div class="bullet-input-wrap">
                    <span class="bullet-prefix">•</span>
                    <input v-if="modalLang === 'vi'" :value="cleanDot(form.overview_title_vi)" type="text"
                      class="bullet-input-field" placeholder="Tổng quan về dự án"
                      @input="form.overview_title_vi = cleanDot($event.target.value)" />
                    <input v-else :value="cleanDot(form.overview_title_en)" type="text" class="bullet-input-field"
                      placeholder="Project Overview" @input="form.overview_title_en = cleanDot($event.target.value)" />
                  </div>
                </div>

                <div class="form-group-custom">
                  <label class="form-label-custom">Ảnh tổng quan</label>
                  <div class="d-flex gap-2">
                    <input v-model="form.overview_image" type="text" class="form-input-custom"
                      placeholder="/storage/uploads/... hoặc URL mockup" />
                    <button type="button" class="btn-upload-sm" :disabled="isUploadingOverview"
                      @click="triggerOverviewUpload" title="Tải ảnh Mockup">
                      <i class="bx" :class="isUploadingOverview ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="form-group-custom mt-2">
                <label class="form-label-custom">Nội dung giới thiệu chi tiết</label>
                <textarea v-if="modalLang === 'vi'" v-model="form.overview_text_vi" class="form-textarea-custom"
                  rows="3" placeholder="Giới thiệu ý tưởng, câu chuyện và giải pháp thương hiệu..."></textarea>
                <textarea v-else v-model="form.overview_text_en" class="form-textarea-custom" rows="3"
                  placeholder="Overview story and creative concept in English..."></textarea>
              </div>

              <div v-if="form.overview_image" class="mockup-preview-wrap mt-2">
                <img
                  :src="form.overview_image.startsWith('http') ? form.overview_image : '/' + form.overview_image.replace(/^\//, '')"
                  alt="Mockup preview" class="mockup-preview-img" />
                <button type="button" class="btn-clear-preview" @click="form.overview_image = ''"
                  title="Xóa ảnh mockup">
                  <i class="bx bx-x"></i>
                </button>
              </div>
            </div>

            <!-- 3. Gallery Images Section -->
            <div class="detail-card-section">
              <div class="detail-card-header">
                <div class="detail-card-title-wrap">
                  <span class="section-step-num">3</span>
                  <div>
                    <h4 class="detail-card-title">Bộ sưu tập ảnh ({{ form.gallery.length }} ảnh)</h4>
                    <p class="detail-card-subtitle">Ảnh hiển thị lớn ở bên dưới</p>
                  </div>
                </div>
                <button type="button" class="btn-detail-action" :disabled="isUploadingGallery"
                  @click="triggerGalleryUpload">
                  <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isUploadingGallery ? 'Đang tải ảnh...' : '+ Tải nhiều ảnh cùng lúc' }}</span>
                </button>
              </div>

              <div class="project-gallery-grid mt-2">
                <div v-for="(img, idx) in form.gallery" :key="idx" class="gallery-card-item">
                  <img :src="img.startsWith('http') ? img : '/' + img.replace(/^\//, '')" alt="Gallery item" />
                  <span class="gallery-num-badge">#{{ idx + 1 }}</span>
                  <button type="button" class="btn-del-gallery" @click="removeGalleryImage(idx)" title="Xóa ảnh này">
                    <i class="bx bx-x"></i>
                  </button>
                </div>

                <div class="gallery-add-card" @click="triggerGalleryUpload">
                  <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-plus-circle'"></i>
                  <span>{{ isUploadingGallery ? 'Đang tải...' : 'Thêm ảnh mới' }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Credits Section -->
            <div class="detail-card-section">
              <div class="detail-card-header">
                <div class="detail-card-title-wrap">
                  <span class="section-step-num">4</span>
                  <div>
                    <h4 class="detail-card-title">Đội ngũ thực hiện</h4>
                    <p class="detail-card-subtitle">Vai trò & tên các thành viên thực hiện dự án</p>
                  </div>
                </div>
                <button type="button" class="btn-detail-action" @click="addCreditRow">
                  <i class="bx bx-plus"></i> Thêm thành viên
                </button>
              </div>

              <div v-if="form.credits && form.credits.length > 0" class="credits-list-wrap mt-2">
                <div v-for="(item, idx) in form.credits" :key="idx" class="credit-input-row">
                  <span class="credit-index">#{{ idx + 1 }}</span>
                  <input v-model="item.role" type="text" class="form-input-custom credit-role"
                    placeholder="Vai trò (VD: Art Director, UI/UX Designer...)" />
                  <input v-model="item.name" type="text" class="form-input-custom credit-name"
                    placeholder="Họ và tên thành viên" />
                  <button type="button" class="btn-del-credit" @click="removeCreditRow(idx)" title="Xóa thành viên này">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>
              <div v-else class="text-muted small py-2 text-center"
                style="background: #ffffff; border-radius: 6px; border: 1px dashed #cbd5e1;">
                Chưa có thành viên nào. Nhấn "+ Thêm thành viên" để thêm vai trò và họ tên.
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="studio-modal-footer d-flex justify-content-end align-items-center gap-2 mt-3 pt-3 border-top">
            <button v-if="activeTab === 'basic'" type="button" class="btn-tab-switch" @click="activeTab = 'detail'">
              Tiếp tục sang Chi tiết dự án <i class="bx bx-right-arrow-alt"></i>
            </button>
            <button v-else type="button" class="btn-tab-switch" @click="activeTab = 'basic'">
              <i class="bx bx-left-arrow-alt"></i> Quay lại Thông tin chung
            </button>
            <button type="submit" class="btn-submit">
              {{ editingItem ? 'Áp dụng thay đổi' : 'Thêm vào danh sách' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Category Manager Modal (Full CRUD) -->
    <div v-if="isCategoryModalOpen" class="studio-modal-backdrop" @click.self="isCategoryModalOpen = false">
      <div class="studio-modal-dialog" style="max-width: 540px;">
        <div class="studio-modal-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <h3 class="modal-title m-0">Quản lý Danh mục Dự án</h3>
          </div>
          <button class="btn-close-modal" @click="isCategoryModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <div class="studio-modal-body">
          <!-- 1-Row Add / Edit Action Bar -->
          <form @submit.prevent="saveCategory" class="category-action-bar">
            <div class="category-input-wrap">
              <input ref="categoryInputRef" v-model="categoryInputName" type="text" class="category-input"
                :placeholder="editingCategory ? (currentLanguage === 'en' ? 'Sửa tên danh mục tiếng Anh...' : 'Sửa tên danh mục...') : (currentLanguage === 'en' ? 'Nhập tên danh mục tiếng Anh...' : 'Nhập tên danh mục mới...')"
                required />
            </div>
            <button type="submit" class="btn-save-cat" :disabled="isSavingCategory">
              <i class="bx"
                :class="isSavingCategory ? 'bx-loader-alt bx-spin' : (editingCategory ? 'bx-check' : 'bx-plus')"></i>
              <span>{{ isSavingCategory ? 'Đang lưu...' : (editingCategory ? 'Lưu' : 'Thêm') }}</span>
            </button>
            <button v-if="editingCategory" type="button" class="btn-cancel-edit-cat" @click="cancelEditCategory"
              title="Hủy chỉnh sửa">
              Hủy
            </button>
          </form>

          <!-- List of existing categories -->
          <div class="category-list-header">
            <span class="modal-label m-0 fw-semibold">Danh sách danh mục ({{ localCategories.length }})</span>
          </div>
          <div class="category-list-items">
            <div v-for="cat in localCategories" :key="cat.id" class="category-list-item"
              :class="{ 'active-editing': editingCategory && editingCategory.id === cat.id }">
              <div class="cat-item-left">
                <i class="bx bx-category-alt text-muted"></i>
                <span class="cat-name">{{ getTrans(cat.name) }}</span>
              </div>
              <div class="cat-item-right">
                <span class="cat-badge-count">{{ getCategoryProjectCount(cat.id) }} dự án</span>
                <div class="cat-item-actions">
                  <button type="button" class="btn-action edit" title="Sửa tên" @click="startEditCategory(cat)">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="btn-action delete" title="Xóa" @click="deleteCategory(cat)">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden native file inputs for uploads -->
    <input type="file" ref="thumbFileInputRef" style="display: none;" accept="image/*"
      @change="handleThumbUploadSelected" />
    <input type="file" ref="heroFileInputRef" style="display: none;" accept="image/*"
      @change="handleHeroUploadSelected" />
    <input type="file" ref="overviewFileInputRef" style="display: none;" accept="image/*"
      @change="handleOverviewUploadSelected" />
    <input type="file" ref="galleryFileInputRef" style="display: none;" accept="image/*" multiple
      @change="handleGalleryUploadSelected" />
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

const props = defineProps({
  projects: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
});

const localProjects = ref([]);
const localCategories = ref([]);
const deletedIds = ref([]);
const isModalOpen = ref(false);
const editingItem = ref(null);
const renderKey = ref(0);
const activeTab = ref('basic');
const modalLang = ref('vi');
const selectedFilterCat = ref('all');
const searchQuery = ref('');

const cleanDot = (s) => String(s || '').replace(/^[•\s\-\*]+/u, '').trim();

const form = reactive({
  title_vi: '',
  title_en: '',
  slug: '',
  category_ids: [],
  year: '',
  client: '',
  thumbnail: '',
  description_vi: '',
  description_en: '',
  is_featured: false,

  // Detail page fields
  hero_image: '',
  overview_title_vi: 'Tổng quan về dự án',
  overview_title_en: 'Project Overview',
  overview_text_vi: '',
  overview_text_en: '',
  overview_image: '',
  gallery: [],
  credits: [],
});

const isCalendarOpen = ref(false);
const calYear = ref(new Date().getFullYear());

const toggleCalendar = () => {
  if (!isCalendarOpen.value) {
    if (form.year) {
      if (form.year.includes('/')) {
        const parts = form.year.split('/');
        if (parts[1]) calYear.value = parseInt(parts[1], 10) || new Date().getFullYear();
      } else if (form.year.includes('-')) {
        const parts = form.year.split('-');
        if (parts[0]) calYear.value = parseInt(parts[0], 10) || new Date().getFullYear();
      } else {
        calYear.value = parseInt(form.year, 10) || new Date().getFullYear();
      }
    } else {
      calYear.value = new Date().getFullYear();
    }
    isCalendarOpen.value = true;
  } else {
    isCalendarOpen.value = false;
  }
};

const isMonthActive = (m) => {
  if (!form.year) return false;
  const mStr = String(m).padStart(2, '0');
  if (form.year.includes('/')) {
    const parts = form.year.split('/');
    return parts[0] === mStr && parts[1] === String(calYear.value);
  }
  if (form.year.includes('-')) {
    const parts = form.year.split('-');
    return parts[1] === mStr && parts[0] === String(calYear.value);
  }
  return false;
};

const selectMonth = (m) => {
  const mStr = String(m).padStart(2, '0');
  form.year = `${mStr}/${calYear.value}`;
  isCalendarOpen.value = false;
};

const formatYearDisplay = (val) => {
  if (!val) return '';
  if (val.includes('/')) {
    const parts = val.split('/');
    return `Tháng ${parts[0]} / ${parts[1]}`;
  }
  if (val.includes('-')) {
    const parts = val.split('-');
    return `Tháng ${parts[1]} / ${parts[0]}`;
  }
  return `Năm ${val}`;
};

const closeCalendarOnClickOutside = (e) => {
  const el = document.querySelector('.calendar-picker-wrap');
  if (el && !el.contains(e.target)) {
    isCalendarOpen.value = false;
  }
};

// Slug auto-generation logic
const isSlugManual = ref(false);

const slugify = (str) => {
  if (!str) return '';
  return str
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const onTitleInput = () => {
  if (!isSlugManual.value) {
    const src = (modalLang.value === 'en' ? form.title_en : form.title_vi) || form.title_vi || form.title_en;
    form.slug = slugify(src);
  }
};

const onSlugInput = () => {
  if (!form.slug || form.slug.trim() === '') {
    isSlugManual.value = false;
  } else {
    isSlugManual.value = true;
  }
};

watch(
  [() => form.title_vi, () => form.title_en, modalLang],
  () => {
    if (!isSlugManual.value) {
      const src = (modalLang.value === 'en' ? form.title_en : form.title_vi) || form.title_vi || form.title_en;
      if (src) {
        form.slug = slugify(src);
      }
    }
  }
);

watch(currentLanguage, (newLang) => {
  if (newLang) {
    modalLang.value = newLang;
  }
}, { immediate: true });

const syncFromProps = () => {
  localProjects.value = JSON.parse(JSON.stringify(props.projects || []));
  deletedIds.value = [];
  renderKey.value++;
  hasUnsavedChanges.value = false;
};

watch(() => props.projects, syncFromProps, { immediate: true });
watch(
  () => props.categories,
  (newVal) => {
    localCategories.value = JSON.parse(JSON.stringify(newVal || []));
  },
  { immediate: true }
);

const featuredCount = computed(() => {
  return localProjects.value.filter((p) => !!p.is_featured).length;
});

const filteredProjects = computed(() => {
  let list = localProjects.value;

  if (selectedFilterCat.value === 'featured') {
    list = list.filter((p) => !!p.is_featured);
  } else if (selectedFilterCat.value !== 'all') {
    const targetCatId = selectedFilterCat.value;
    list = list.filter((p) => {
      if (p.category_ids && Array.isArray(p.category_ids)) {
        return p.category_ids.includes(targetCatId);
      }
      if (p.categories && Array.isArray(p.categories)) {
        return p.categories.some((c) => c.id === targetCatId);
      }
      return false;
    });
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase();
    list = list.filter((p) => {
      const titleVi = (typeof p.title === 'object' ? p.title.vi : p.title || '').toLowerCase();
      const titleEn = (typeof p.title === 'object' ? p.title.en : '').toLowerCase();
      const slug = (p.slug || '').toLowerCase();
      const client = (p.client || '').toLowerCase();
      const year = String(p.year || '').toLowerCase();
      return titleVi.includes(q) || titleEn.includes(q) || slug.includes(q) || client.includes(q) || year.includes(q);
    });
  }

  return list;
});

const resetFilters = () => {
  selectedFilterCat.value = 'all';
  searchQuery.value = '';
  currentPage.value = 1;
};

// Pagination (9 projects per page)
const currentPage = ref(1);
const itemsPerPage = 9;

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(filteredProjects.value.length / itemsPerPage));
});

const paginatedProjects = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredProjects.value.slice(start, start + itemsPerPage);
});

const setPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

watch([selectedFilterCat, searchQuery], () => {
  currentPage.value = 1;
});

watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal) {
    currentPage.value = newTotal;
  }
});

const getTrans = (val) => {
  if (val && typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || '';
  }
  return val || '';
};

const getProjectCategoryList = (p) => {
  if (p.categories && Array.isArray(p.categories) && p.categories.length > 0) {
    return p.categories.map((c) => getTrans(c.name));
  }
  if (p.category_ids && Array.isArray(p.category_ids)) {
    return localCategories.value
      .filter((c) => p.category_ids.includes(c.id))
      .map((c) => getTrans(c.name));
  }
  const str = getTrans(p.category);
  return str ? str.split(' • ') : [];
};

const toggleCategory = (catId) => {
  const idx = form.category_ids.indexOf(catId);
  if (idx > -1) {
    form.category_ids.splice(idx, 1);
  } else {
    form.category_ids.push(catId);
  }
};

const openCreateModal = () => {
  editingItem.value = null;
  activeTab.value = 'basic';
  modalLang.value = currentLanguage.value || 'vi';
  form.title_vi = '';
  form.title_en = '';
  form.slug = '';
  isSlugManual.value = false;
  form.category_ids = [];
  form.year = '';
  calYear.value = new Date().getFullYear();
  isCalendarOpen.value = false;
  form.client = '';
  form.thumbnail = '/assets/images/project1.png';
  form.description_vi = '';
  form.description_en = '';
  form.is_featured = false;

  // Detail fields
  form.hero_image = '';
  form.overview_title_vi = 'Tổng quan về dự án';
  form.overview_title_en = 'Project Overview';
  form.overview_text_vi = '';
  form.overview_text_en = '';
  form.overview_image = '';
  form.gallery = [];
  form.credits = [
    { role: 'Account Manager', name: '' },
    { role: 'Design Lead', name: '' },
    { role: 'Developer', name: '' },
  ];

  isModalOpen.value = true;
};

const openEditModal = (p) => {
  editingItem.value = p;
  activeTab.value = 'basic';
  modalLang.value = currentLanguage.value || 'vi';
  form.title_vi = typeof p.title === 'object' ? (p.title.vi || '') : (p.title || '');
  form.title_en = typeof p.title === 'object' ? (p.title.en || '') : '';
  form.slug = p.slug || '';
  isSlugManual.value = true;

  let catIds = [];
  if (p.categories && Array.isArray(p.categories) && p.categories.length > 0) {
    catIds = p.categories.map((c) => c.id);
  } else if (p.category_ids && Array.isArray(p.category_ids)) {
    catIds = [...p.category_ids];
  } else {
    const catStr = getTrans(p.category).toLowerCase();
    localCategories.value.forEach((cat) => {
      const viName = (cat.name?.vi || '').toLowerCase();
      const enName = (cat.name?.en || '').toLowerCase();
      if ((viName && catStr.includes(viName)) || (enName && catStr.includes(enName))) {
        catIds.push(cat.id);
      }
    });
  }
  form.category_ids = catIds.length > 0 ? Array.from(new Set(catIds)) : (localCategories.value.length > 0 ? [localCategories.value[0].id] : []);

  form.year = p.year || '';
  calYear.value = new Date().getFullYear();
  isCalendarOpen.value = false;
  form.client = p.client || '';
  form.thumbnail = p.thumbnail || '';
  form.description_vi = typeof p.description === 'object' ? (p.description.vi || '') : (p.description || '');
  form.description_en = typeof p.description === 'object' ? (p.description.en || '') : '';
  form.is_featured = !!p.is_featured;

  // Detail fields from p.content & p.gallery
  let c = p.content || {};
  if (typeof c === 'string') {
    try { c = JSON.parse(c); } catch (e) { c = {}; }
  }
  let g = p.gallery || [];
  if (typeof g === 'string') {
    try { g = JSON.parse(g); } catch (e) { g = []; }
  }

  const cleanDot = (s) => String(s || '').replace(/^[•\s\-\*]+/u, '').trim();
  form.hero_image = c.hero_image || p.thumbnail || '/assets/images/gotrail.jpg';

  const rawVi = typeof c.overview_title === 'object' ? c.overview_title?.vi : c.overview_title;
  const rawEn = typeof c.overview_title === 'object' ? c.overview_title?.en : '';
  form.overview_title_vi = cleanDot(rawVi) || 'Tổng quan về dự án';
  form.overview_title_en = cleanDot(rawEn) || 'Project Overview';

  form.overview_text_vi = (typeof c.overview_text === 'object' ? c.overview_text?.vi : c.overview_text) || form.description_vi || 'NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.';
  form.overview_text_en = (typeof c.overview_text === 'object' ? c.overview_text?.en : '') || form.description_en || 'NORM Creative was born with a fresh, contemporary, youthful, and disruptive spirit. Taking culture as our core, combined with open design thinking.';
  form.overview_image = c.overview_image || '/assets/images/mockup.png';
  form.gallery = Array.isArray(g) && g.length > 0 ? [...g] : ['/assets/images/work1.jpg', '/assets/images/work2.jpg', '/assets/images/work3.jpg'];

  const hasCredits = Array.isArray(c.credits) && c.credits.some((cr) => cr && (cr.name || cr.role));
  form.credits = hasCredits
    ? JSON.parse(JSON.stringify(c.credits))
    : [
      { role: 'Account Manager', name: 'Sebastian Degenhart' },
      { role: 'Design Lead', name: 'Amadeus Malmin' },
      { role: 'Lead Designer', name: 'Kasia Bielska' },
      { role: 'Senior Designer', name: 'Mariusz Ciesla' },
    ];

  isModalOpen.value = true;
};

// Credit list management
const addCreditRow = () => {
  form.credits.push({ role: '', name: '' });
};

const removeCreditRow = (idx) => {
  form.credits.splice(idx, 1);
};

// Gallery management
const removeGalleryImage = (idx) => {
  form.gallery.splice(idx, 1);
};

// Native File Upload Handlers
const thumbFileInputRef = ref(null);
const heroFileInputRef = ref(null);
const overviewFileInputRef = ref(null);
const galleryFileInputRef = ref(null);

const isUploadingThumb = ref(false);
const isUploadingHero = ref(false);
const isUploadingOverview = ref(false);
const isUploadingGallery = ref(false);

const triggerThumbUpload = () => {
  if (thumbFileInputRef.value) {
    thumbFileInputRef.value.value = '';
    thumbFileInputRef.value.click();
  }
};

const triggerHeroUpload = () => {
  if (heroFileInputRef.value) {
    heroFileInputRef.value.value = '';
    heroFileInputRef.value.click();
  }
};

const triggerOverviewUpload = () => {
  if (overviewFileInputRef.value) {
    overviewFileInputRef.value.value = '';
    overviewFileInputRef.value.click();
  }
};

const triggerGalleryUpload = () => {
  if (galleryFileInputRef.value) {
    galleryFileInputRef.value.value = '';
    galleryFileInputRef.value.click();
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
    if (data && data.success && data.file_path) {
      return data.file_path.startsWith('/') ? data.file_path : '/' + data.file_path;
    }
  } catch (err) {
    console.error('Upload error:', err);
  }
  return null;
};

const handleThumbUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  isUploadingThumb.value = true;
  const path = await uploadSingleFile(file);
  if (path) form.thumbnail = path;
  isUploadingThumb.value = false;
};

const handleHeroUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  isUploadingHero.value = true;
  const path = await uploadSingleFile(file);
  if (path) form.hero_image = path;
  isUploadingHero.value = false;
};

const handleOverviewUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  isUploadingOverview.value = true;
  const path = await uploadSingleFile(file);
  if (path) form.overview_image = path;
  isUploadingOverview.value = false;
};

const handleGalleryUploadSelected = async (e) => {
  const files = e.target.files;
  if (!files || files.length === 0) return;
  isUploadingGallery.value = true;

  for (let i = 0; i < files.length; i++) {
    const path = await uploadSingleFile(files[i]);
    if (path) form.gallery.push(path);
  }
  isUploadingGallery.value = false;
};

// Category Manager CRUD logic
const isCategoryModalOpen = ref(false);
const editingCategory = ref(null);
const isSavingCategory = ref(false);
const categoryInputName = ref('');
const categoryInputRef = ref(null);

const getCategoryProjectCount = (catId) => {
  return localProjects.value.filter((p) => {
    if (p.category_ids && Array.isArray(p.category_ids)) {
      return p.category_ids.includes(catId);
    }
    if (p.categories && Array.isArray(p.categories)) {
      return p.categories.some((c) => c.id === catId);
    }
    return false;
  }).length;
};

const openCategoryModal = () => {
  cancelEditCategory();
  isCategoryModalOpen.value = true;
};

const startEditCategory = (cat) => {
  editingCategory.value = cat;
  categoryInputName.value = getTrans(cat.name);
  if (categoryInputRef.value) {
    categoryInputRef.value.focus();
  }
};

const cancelEditCategory = () => {
  editingCategory.value = null;
  categoryInputName.value = '';
};

// Auto update editing input if user changes language on header Topbar
watch(currentLanguage, () => {
  if (editingCategory.value) {
    categoryInputName.value = getTrans(editingCategory.value.name);
  }
});

const saveCategory = () => {
  const val = categoryInputName.value.trim();
  if (!val) {
    alert(currentLanguage.value === 'en' ? 'Please enter category name!' : 'Vui lòng nhập tên danh mục!');
    return;
  }

  isSavingCategory.value = true;
  const isEditing = !!editingCategory.value;
  const url = isEditing
    ? `/admin/project-categories/${editingCategory.value.id}`
    : '/admin/project-categories';

  const isEn = currentLanguage.value === 'en';
  const payload = isEn
    ? { name_en: val }
    : { name_vi: val };

  router[isEditing ? 'put' : 'post'](
    url,
    payload,
    {
      preserveScroll: true,
      onSuccess: () => {
        isSavingCategory.value = false;
        cancelEditCategory();
      },
      onError: (err) => {
        isSavingCategory.value = false;
        alert('Lỗi lưu danh mục: ' + JSON.stringify(err));
      },
    }
  );
};

const deleteCategory = (cat) => {
  if (
    !confirm(
      `Bạn có chắc chắn muốn xóa danh mục "${getTrans(cat.name)}" không?`
    )
  ) {
    return;
  }

  router.delete(`/admin/project-categories/${cat.id}`, {
    preserveScroll: true,
    onError: (err) => {
      alert('Lỗi khi xóa danh mục: ' + JSON.stringify(err));
    },
  });
};

// Submit modal: Only modifies local staging state
const submitForm = () => {
  const currentTitle = (modalLang.value === 'en' ? form.title_en : form.title_vi) || form.title_vi || form.title_en;
  if (!currentTitle || !currentTitle.trim()) {
    alert(modalLang.value === 'en' ? 'Vui lòng nhập tên dự án!' : 'Vui lòng nhập tên dự án!');
    return;
  }

  if (!form.slug || !form.slug.trim()) {
    alert('Vui lòng nhập đường dẫn tĩnh');
    return;
  }

  if (!form.category_ids || form.category_ids.length === 0) {
    alert('Vui lòng chọn ít nhất 1 danh mục cho dự án!');
    return;
  }

  const selectedCats = localCategories.value.filter((c) => form.category_ids.includes(c.id));
  const catVi = selectedCats.map((c) => c.name?.vi || c.name).join(' • ');
  const catEn = selectedCats.map((c) => c.name?.en || c.name?.vi || c.name).join(' • ');

  const cleanDot = (s) => String(s || '').replace(/^[•\s\-\*]+/u, '').trim();
  const viOverview = cleanDot(form.overview_title_vi || form.overview_title_en) || 'Tổng quan về dự án';
  const enOverview = cleanDot(form.overview_title_en || form.overview_title_vi) || 'Project Overview';

  const contentPayload = {
    hero_image: form.hero_image,
    overview_title: {
      vi: '• ' + viOverview,
      en: '• ' + enOverview,
    },
    overview_text: {
      vi: form.overview_text_vi || form.overview_text_en || '',
      en: form.overview_text_en || form.overview_text_vi || '',
    },
    overview_image: form.overview_image,
    credits: form.credits.filter((c) => (c.role && c.role.trim()) || (c.name && c.name.trim())),
  };

  const payloadData = {
    title: { vi: form.title_vi || form.title_en, en: form.title_en || form.title_vi },
    slug: form.slug,
    category_ids: [...form.category_ids],
    categories: selectedCats,
    category: { vi: catVi, en: catEn },
    year: form.year,
    client: form.client,
    thumbnail: form.thumbnail,
    description: { vi: form.description_vi || form.description_en, en: form.description_en || form.description_vi },
    is_featured: form.is_featured,
    gallery: [...form.gallery],
    content: contentPayload,
  };

  if (editingItem.value) {
    const idx = localProjects.value.findIndex((p) => p.id === editingItem.value.id);
    if (idx !== -1) {
      localProjects.value[idx] = {
        ...localProjects.value[idx],
        ...payloadData,
        _isModified: true,
      };
    }
  } else {
    localProjects.value.unshift({
      id: 'temp_' + Date.now(),
      ...payloadData,
      _isNew: true,
    });
  }

  hasUnsavedChanges.value = true;
  isModalOpen.value = false;
};

// Delete action
const confirmDelete = (p) => {
  if (confirm(`Bạn có chắc chắn muốn xóa dự án "${getTrans(p.title)}" không?\n(Lưu ý: Dữ liệu chỉ thực sự bị xóa khỏi database sau khi bạn bấm "Xuất bản")`)) {
    localProjects.value = localProjects.value.filter((item) => item.id !== p.id);
    if (typeof p.id === 'number' || (typeof p.id === 'string' && !p.id.startsWith('temp_'))) {
      deletedIds.value.push(p.id);
    }
    hasUnsavedChanges.value = true;
  }
};

// Revert back to database state
const revertChanges = () => {
  if (confirm('Hủy bỏ các thay đổi vừa làm trên màn hình và quay lại dữ liệu gốc trong database?')) {
    syncFromProps();
  }
};

// Publish batch changes to database
const publishProjects = () => {
  isSaving.value = true;
  router.post(
    '/admin/projects/publish',
    {
      items: localProjects.value,
      deleted_ids: deletedIds.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        isSaving.value = false;
        hasUnsavedChanges.value = false;
        alert('Đã xuất bản thay đổi dự án vào cơ sở dữ liệu thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        alert('Lỗi xuất bản: ' + JSON.stringify(err));
      },
    }
  );
};

onMounted(() => {
  onSaveHandler.value = publishProjects;
  onDraftHandler.value = publishProjects;
  document.addEventListener('click', closeCalendarOnClickOutside);
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
  document.removeEventListener('click', closeCalendarOnClickOutside);
});
</script>

<style src="./css/panels.css"></style>
<style src="./css/projects.css"></style>
