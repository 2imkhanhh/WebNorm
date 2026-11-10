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
        <div v-if="hasUnsavedChanges" class="staging-alert-banner shadow-sm">
          <div class="banner-info d-flex align-items-center gap-2">
            <i class="bx bx-info-circle text-warning fs-5"></i>
            <span>Bạn có thay đổi chưa lưu vào cơ sở dữ liệu. Nhấn "Xuất bản ngay" để áp dụng lên website.</span>
          </div>
          <div class="banner-buttons">
            <button type="button" class="btn-banner-revert" @click="revertChanges">Hủy thay đổi</button>
            <button type="button" class="btn-banner-publish" :disabled="isSaving" @click="publishProjects">
              {{ isSaving ? 'Đang lưu...' : 'Xuất bản ngay' }}
            </button>
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
              {{ getTrans(cat.name) }}
            </button>
          </div>
        </div>

        <!-- Projects Card Grid -->
        <div v-if="filteredProjects && filteredProjects.length > 0" class="projects-card-grid">
          <div v-for="p in filteredProjects" :key="p.id" class="project-card-full">
            <div class="project-card-thumb-wrap">
              <img
                :src="p.thumbnail ? (p.thumbnail.startsWith('http') ? p.thumbnail : '/' + p.thumbnail.replace(/^\//, '')) : '/assets/images/project1.png'"
                :alt="getTrans(p.title)" loading="lazy" />
              <div class="card-badges-top">
                <span v-if="p.is_featured" class="card-badge-feat" title="Hiển thị tại trang chủ">Nổi bật</span>
                <span v-if="p._isNew" class="card-badge-new" title="Chưa xuất bản vào database">Mới</span>
                <span v-else-if="p._isModified" class="card-badge-mod" title="Đã chỉnh sửa nhưng chưa xuất bản">Đã
                  sửa</span>
              </div>
              <div class="card-stats-overlay">
                <span title="Số lượng ảnh gallery"><i class="bx bx-images"></i> {{ (p.gallery && p.gallery.length) || 0
                  }} ảnh</span>
                <span title="Đội ngũ thực hiện"><i class="bx bx-group"></i> {{ (p.content && p.content.credits &&
                  p.content.credits.length) || 0 }}</span>
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

        <!-- Empty State -->
        <div v-else class="empty-state bg-white p-5 rounded-3 border text-center my-3">
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
            <a v-if="editingItem && form.slug" :href="'/work/' + form.slug" target="_blank"
              class="btn btn-sm btn-outline-primary py-1 px-2 d-inline-flex align-items-center gap-1"
              style="font-size: 11.5px;" title="Mở trang chi tiết trên website">
              <i class="bx bx-link-external"></i> Xem trang chi tiết
            </a>
          </div>
          <button class="btn-close-modal" @click="isModalOpen = false">
            <i class="bx bx-x"></i>
          </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="modal-nav-tabs">
          <button type="button" class="modal-nav-tab" :class="{ active: activeTab === 'basic' }"
            @click="activeTab = 'basic'">
            <i class="bx bx-info-circle"></i> 1. Thông tin cơ bản
          </button>
          <button type="button" class="modal-nav-tab" :class="{ active: activeTab === 'detail' }"
            @click="activeTab = 'detail'">
            <i class="bx bx-layout"></i> 2. Nội dung Trang Chi tiết (Hero, Gallery, Credits)
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="tab-pane-content">
          <!-- TAB 1: THÔNG TIN CƠ BẢN -->
          <div v-show="activeTab === 'basic'" class="d-flex flex-column gap-3">
            <div class="modal-form-row">
              <div class="modal-form-group col-6">
                <label class="modal-label">Tên dự án (Tiếng Việt) *</label>
                <input v-model="form.title_vi" type="text" class="modal-input" placeholder="Ví dụ: Tiên Phong Identity"
                  required />
              </div>
              <div class="modal-form-group col-6">
                <label class="modal-label">Tên dự án (Tiếng Anh)</label>
                <input v-model="form.title_en" type="text" class="modal-input"
                  placeholder="Ví dụ: Tien Phong Identity" />
              </div>
            </div>

            <div class="modal-form-row">
              <div class="modal-form-group col-6">
                <label class="modal-label">Đường dẫn tĩnh (Slug) *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted" style="font-size: 12px;">/work/</span>
                  <input v-model="form.slug" type="text" class="modal-input" placeholder="tien-phong-identity"
                    required />
                </div>
              </div>
              <div class="modal-form-group col-3">
                <label class="modal-label">Năm thực hiện</label>
                <input v-model="form.year" type="text" class="modal-input" placeholder="2026" />
              </div>
              <div class="modal-form-group col-3">
                <label class="modal-label">Khách hàng (Client)</label>
                <input v-model="form.client" type="text" class="modal-input" placeholder="Tên khách hàng" />
              </div>
            </div>

            <!-- Category Multi-Select Selector -->
            <div class="modal-form-group">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="modal-label m-0">Danh mục dự án (Chọn 1 hoặc nhiều danh mục) *</label>
                <button type="button" class="btn-quick-manage-cat" @click="openCategoryModal">
                  <i class="bx bx-cog"></i> Quản lý danh mục
                </button>
              </div>
              <div class="category-pills-wrap">
                <button v-for="cat in localCategories" :key="cat.id" type="button" class="category-pill"
                  :class="{ active: form.category_ids.includes(cat.id) }" @click="toggleCategory(cat.id)">
                  <i class="bx" :class="form.category_ids.includes(cat.id) ? 'bx-check' : 'bx-plus'"></i>
                  <span>{{ getTrans(cat.name) }}</span>
                </button>
              </div>
              <span v-if="form.category_ids.length === 0" class="field-hint text-danger mt-1">
                * Vui lòng chọn ít nhất 1 danh mục cho dự án.
              </span>
            </div>

            <!-- Thumbnail Upload -->
            <div class="modal-form-group">
              <label class="modal-label">Ảnh đại diện thẻ dự án (Thumbnail danh sách)</label>
              <div class="d-flex gap-2">
                <input v-model="form.thumbnail" type="text" class="modal-input"
                  placeholder="/storage/uploads/... hoặc URL ảnh" />
                <button type="button" class="btn-upload-trigger" :disabled="isUploadingThumb"
                  @click="triggerThumbUpload" title="Tải ảnh từ máy tính (PNG, JPG, WEBP...)">
                  <i class="bx" :class="isUploadingThumb ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isUploadingThumb ? 'Đang tải...' : 'Tải ảnh lên' }}</span>
                </button>
              </div>
              <div v-if="form.thumbnail" class="modal-img-preview mt-2" style="max-height: 120px;">
                <img :src="form.thumbnail.startsWith('http') ? form.thumbnail : '/' + form.thumbnail.replace(/^\//, '')"
                  alt="Preview" />
              </div>
            </div>

            <!-- Short Description -->
            <div class="modal-form-row">
              <div class="modal-form-group col-6">
                <label class="modal-label">Mô tả ngắn (VI)</label>
                <textarea v-model="form.description_vi" class="modal-textarea" rows="2"
                  placeholder="Tóm tắt ngắn về dự án..."></textarea>
              </div>
              <div class="modal-form-group col-6">
                <label class="modal-label">Mô tả ngắn (EN)</label>
                <textarea v-model="form.description_en" class="modal-textarea" rows="2"
                  placeholder="Short summary in English..."></textarea>
              </div>
            </div>

            <div class="modal-form-group">
              <label class="modal-checkbox-label">
                <input v-model="form.is_featured" type="checkbox" />
                <span>Đánh dấu là dự án nổi bật (Hiển thị tại Showcase trang chủ)</span>
              </label>
            </div>
          </div>

          <!-- TAB 2: NỘI DUNG TRANG CHI TIẾT (/work/{slug}) -->
          <div v-show="activeTab === 'detail'" class="d-flex flex-column gap-3">
            <!-- 1. Hero Banner Section -->
            <div class="detail-section-group">
              <div class="detail-section-header">
                <h4 class="detail-section-title">
                  <i class="bx bx-image-alt text-primary"></i> 1. Ảnh bìa Hero Banner lớn (Đầu trang chi tiết)
                </h4>
                <span class="detail-section-hint">Khuyến nghị ảnh kích thước 1920x1080px tràn viền</span>
              </div>
              <div class="d-flex gap-2">
                <input v-model="form.hero_image" type="text" class="modal-input"
                  placeholder="/storage/uploads/... hoặc URL ảnh bìa lớn" />
                <button type="button" class="btn-upload-trigger" :disabled="isUploadingHero" @click="triggerHeroUpload">
                  <i class="bx" :class="isUploadingHero ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                  <span>{{ isUploadingHero ? 'Đang tải...' : 'Tải ảnh bìa' }}</span>
                </button>
              </div>
              <div v-if="form.hero_image" class="editor-img-preview-box mt-1">
                <img
                  :src="form.hero_image.startsWith('http') ? form.hero_image : '/' + form.hero_image.replace(/^\//, '')"
                  alt="Hero Preview" />
              </div>
            </div>

            <!-- 2. Overview Section -->
            <div class="detail-section-group">
              <div class="detail-section-header">
                <h4 class="detail-section-title">
                  <i class="bx bx-paragraph text-primary"></i> 2. Phần Tổng quan dự án (Overview)
                </h4>
                <span class="detail-section-hint">Đoạn văn bản câu chuyện thương hiệu & Mockup tổng quan</span>
              </div>

              <div class="modal-form-row">
                <div class="modal-form-group col-6">
                  <label class="modal-label">Tiêu đề phụ bên trái (VI)</label>
                  <input v-model="form.overview_title_vi" type="text" class="modal-input"
                    placeholder="• Tổng quan về dự án" />
                </div>
                <div class="modal-form-group col-6">
                  <label class="modal-label">Tiêu đề phụ bên trái (EN)</label>
                  <input v-model="form.overview_title_en" type="text" class="modal-input"
                    placeholder="• Project Overview" />
                </div>
              </div>

              <div class="modal-form-row">
                <div class="modal-form-group col-6">
                  <label class="modal-label">Nội dung giới thiệu chi tiết (VI)</label>
                  <textarea v-model="form.overview_text_vi" class="modal-textarea" rows="4"
                    placeholder="Nhập 1 - 2 đoạn văn bản giới thiệu ý tưởng, giải pháp, câu chuyện của dự án..."></textarea>
                </div>
                <div class="modal-form-group col-6">
                  <label class="modal-label">Nội dung giới thiệu chi tiết (EN)</label>
                  <textarea v-model="form.overview_text_en" class="modal-textarea" rows="4"
                    placeholder="Overview story and creative concept in English..."></textarea>
                </div>
              </div>

              <div class="modal-form-group">
                <label class="modal-label">Ảnh Mockup Tổng quan (Bên dưới văn bản giới thiệu)</label>
                <div class="d-flex gap-2">
                  <input v-model="form.overview_image" type="text" class="modal-input"
                    placeholder="/storage/uploads/... hoặc URL ảnh mockup" />
                  <button type="button" class="btn-upload-trigger" :disabled="isUploadingOverview"
                    @click="triggerOverviewUpload">
                    <i class="bx" :class="isUploadingOverview ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                    <span>{{ isUploadingOverview ? 'Đang tải...' : 'Tải mockup' }}</span>
                  </button>
                </div>
                <div v-if="form.overview_image" class="editor-img-preview-box mt-1">
                  <img
                    :src="form.overview_image.startsWith('http') ? form.overview_image : '/' + form.overview_image.replace(/^\//, '')"
                    alt="Overview Mockup Preview" />
                </div>
              </div>
            </div>

            <!-- 3. Gallery Images Section -->
            <div class="detail-section-group">
              <div class="detail-section-header">
                <h4 class="detail-section-title">
                  <i class="bx bx-images text-primary"></i> 3. Bộ sưu tập ảnh Gallery ({{ form.gallery.length }} ảnh)
                </h4>
                <div class="d-flex gap-2 align-items-center">
                  <button type="button" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1"
                    :disabled="isUploadingGallery" @click="triggerGalleryUpload"
                    style="font-size: 11.5px; padding: 4px 10px;">
                    <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-cloud-upload'"></i>
                    <span>{{ isUploadingGallery ? 'Đang tải ảnh...' : '+ Tải nhiều ảnh từ máy' }}</span>
                  </button>
                </div>
              </div>

              <div class="editor-gallery-grid">
                <div v-for="(img, idx) in form.gallery" :key="idx" class="editor-gallery-item">
                  <img :src="img.startsWith('http') ? img : '/' + img.replace(/^\//, '')" alt="Gallery item" />
                  <span class="gallery-order-badge">#{{ idx + 1 }}</span>
                  <button type="button" class="btn-del-gallery-item" @click="removeGalleryImage(idx)"
                    title="Xóa ảnh này">
                    <i class="bx bx-x"></i>
                  </button>
                </div>

                <!-- Upload Card Trigger inside grid -->
                <div class="gallery-upload-trigger-card" @click="triggerGalleryUpload">
                  <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-plus'"></i>
                  <span>{{ isUploadingGallery ? 'Đang tải lên...' : 'Thêm ảnh mới' }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Credits Section -->
            <div class="detail-section-group">
              <div class="detail-section-header">
                <h4 class="detail-section-title">
                  <i class="bx bx-group text-primary"></i> 4. Đội ngũ thực hiện (Credits)
                </h4>
                <span class="detail-section-hint">Danh sách thành viên & vị trí thực hiện dự án</span>
              </div>

              <div class="credits-rows-list">
                <div v-for="(item, idx) in form.credits" :key="idx" class="credit-row-item">
                  <input v-model="item.role" type="text" class="modal-input" style="flex: 1;"
                    placeholder="Vai trò (VD: Design Lead, Account Manager, Developer...)" />
                  <input v-model="item.name" type="text" class="modal-input" style="flex: 1;"
                    placeholder="Họ và tên (VD: Amadeus Malmin, Sebastian...)" />
                  <button type="button" class="btn-del-credit-row" @click="removeCreditRow(idx)" title="Xóa dòng này">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>

              <button type="button" class="btn-add-credit-row" @click="addCreditRow">
                <i class="bx bx-plus"></i> Thêm thành viên
              </button>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="studio-modal-footer d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <button type="button" class="btn-cancel" @click="isModalOpen = false">Hủy</button>
            <div class="d-flex gap-2">
              <button v-if="activeTab === 'basic'" type="button" class="btn btn-outline-dark btn-sm px-3"
                @click="activeTab = 'detail'">
                Tiếp tục sang Chi tiết dự án <i class="bx bx-right-arrow-alt"></i>
              </button>
              <button type="submit" class="btn-submit">
                {{ editingItem ? 'Áp dụng thay đổi' : 'Thêm vào danh sách' }}
              </button>
            </div>
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
            <span class="modal-label m-0 fw-semibold">Danh sách danh mục</span>
          </div>
          <div class="category-list-items">
            <div v-for="cat in localCategories" :key="cat.id" class="category-list-item"
              :class="{ 'active-editing': editingCategory && editingCategory.id === cat.id }">
              <div class="cat-item-left">
                <i class="bx bx-category-alt text-muted"></i>
                <span class="cat-name">{{ getTrans(cat.name) }}</span>
              </div>
              <div class="cat-item-right">
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
const selectedFilterCat = ref('all');
const searchQuery = ref('');

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
};

const form = reactive({
  title_vi: '',
  title_en: '',
  slug: '',
  category_ids: [],
  year: '2026',
  client: '',
  thumbnail: '',
  description_vi: '',
  description_en: '',
  is_featured: false,

  // Detail page fields
  hero_image: '',
  overview_title_vi: '• Tổng quan về dự án',
  overview_title_en: '• Project Overview',
  overview_text_vi: '',
  overview_text_en: '',
  overview_image: '',
  gallery: [],
  credits: [],
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
  form.title_vi = '';
  form.title_en = '';
  form.slug = '';
  form.category_ids = localCategories.value.length > 0 ? [localCategories.value[0].id] : [];
  form.year = '2026';
  form.client = '';
  form.thumbnail = '/assets/images/project1.png';
  form.description_vi = '';
  form.description_en = '';
  form.is_featured = false;

  // Detail fields
  form.hero_image = '';
  form.overview_title_vi = '• Tổng quan về dự án';
  form.overview_title_en = '• Project Overview';
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
  form.title_vi = typeof p.title === 'object' ? (p.title.vi || '') : (p.title || '');
  form.title_en = typeof p.title === 'object' ? (p.title.en || '') : '';
  form.slug = p.slug || '';

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

  form.year = p.year || '2026';
  form.client = p.client || '';
  form.thumbnail = p.thumbnail || '';
  form.description_vi = typeof p.description === 'object' ? (p.description.vi || '') : (p.description || '');
  form.description_en = typeof p.description === 'object' ? (p.description.en || '') : '';
  form.is_featured = !!p.is_featured;

  // Detail fields from p.content & p.gallery
  const c = p.content || {};
  form.hero_image = c.hero_image || '';
  form.overview_title_vi = (typeof c.overview_title === 'object' ? c.overview_title.vi : c.overview_title) || '• Tổng quan về dự án';
  form.overview_title_en = (typeof c.overview_title === 'object' ? c.overview_title.en : '') || '• Project Overview';
  form.overview_text_vi = (typeof c.overview_text === 'object' ? c.overview_text.vi : c.overview_text) || form.description_vi || '';
  form.overview_text_en = (typeof c.overview_text === 'object' ? c.overview_text.en : '') || form.description_en || '';
  form.overview_image = c.overview_image || '';
  form.gallery = Array.isArray(p.gallery) ? [...p.gallery] : [];
  form.credits = Array.isArray(c.credits) && c.credits.length > 0
    ? JSON.parse(JSON.stringify(c.credits))
    : [
      { role: 'Account Manager', name: '' },
      { role: 'Design Lead', name: '' },
      { role: 'Developer', name: '' },
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

// Auto generate slug from Vietnamese title if empty
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

// Submit modal: Only modifies local staging state
const submitForm = () => {
  if (!form.category_ids || form.category_ids.length === 0) {
    alert('Vui lòng chọn ít nhất 1 danh mục cho dự án!');
    return;
  }

  const selectedCats = localCategories.value.filter((c) => form.category_ids.includes(c.id));
  const catVi = selectedCats.map((c) => c.name?.vi || c.name).join(' • ');
  const catEn = selectedCats.map((c) => c.name?.en || c.name?.vi || c.name).join(' • ');

  const contentPayload = {
    hero_image: form.hero_image,
    overview_title: { vi: form.overview_title_vi, en: form.overview_title_en || form.overview_title_vi },
    overview_text: { vi: form.overview_text_vi, en: form.overview_text_en || form.overview_text_vi },
    overview_image: form.overview_image,
    credits: form.credits.filter((c) => (c.role && c.role.trim()) || (c.name && c.name.trim())),
  };

  const payloadData = {
    title: { vi: form.title_vi, en: form.title_en || form.title_vi },
    slug: form.slug,
    category_ids: [...form.category_ids],
    categories: selectedCats,
    category: { vi: catVi, en: catEn },
    year: form.year,
    client: form.client,
    thumbnail: form.thumbnail,
    description: { vi: form.description_vi, en: form.description_en || form.description_vi },
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
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/panels.css"></style>
<style src="./css/projects.css"></style>
