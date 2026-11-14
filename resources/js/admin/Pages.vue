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
            <label v-if="field.type !== 'gallery' && field.type !== 'repeater'" class="field-label">{{ field.label
            }}</label>

            <!-- Text Input with fixed bullet dot -->
            <div
              v-if="field.type === 'text' && (field.prefix === '•' || field.key === 'services_label' || field.key === 'about_label' || field.key === 'blog_label' || field.key === 'story_label' || (field.key && field.key.endsWith('_label')))"
              class="field-input-prefix-wrap">
              <span class="field-prefix-dot">•</span>
              <input :value="cleanDot(getFieldValue(field))" type="text" class="field-input field-input-with-prefix"
                @input="updateField(section.id, field, cleanDot($event.target.value))" />
            </div>

            <!-- Standard Text Input -->
            <input v-else-if="field.type === 'text'" :value="getFieldValue(field)" type="text" class="field-input"
              @input="updateField(section.id, field, $event.target.value)" />

            <!-- Textarea -->
            <textarea v-else-if="field.type === 'textarea'" :value="getFieldValue(field)" class="field-textarea"
              :rows="field.rows || (field.key === 'hero_desc' ? 4 : 3)"
              @input="updateField(section.id, field, $event.target.value)"></textarea>

            <!-- Link Picker (Predefined Pages + Custom URL) -->
            <div v-else-if="field.type === 'link' || field.type === 'url'" class="link-field-wrap">
              <select class="field-select" :value="getLinkSelectValue(field)"
                @change="handleLinkSelectChange(section.id, field, $event.target.value)">
                <option value="/about-us">Về chúng tôi (/about-us)</option>
                <option value="/work">Dự án (/work)</option>
                <option value="/news">Bài viết / Tin tức (/news)</option>
                <option value="/">Trang chủ (/)</option>
                <option value="__custom__">Đường dẫn tùy chỉnh khác...</option>
              </select>
              <div v-if="isCustomLink(field)" class="custom-link-box">
                <input :value="getFieldValue(field)" type="text" class="field-input"
                  placeholder="https://... hoặc /duong-dan"
                  @input="updateField(section.id, field, $event.target.value)" />
                <span class="field-hint" style="font-size: 11px; color: #64748b;">
                  Điền link ngoài (https://...) hoặc đường dẫn tùy ý.
                </span>
              </div>
            </div>

            <!-- Image Field (Clean Upload Row without raw path input) -->
            <div v-else-if="field.type === 'image'" class="pages-image-upload-row">
              <div class="pages-image-thumb" @click="triggerMediaUpload(section.id, field, 'image/*')"
                title="Nhấn để đổi ảnh">
                <img v-if="getFieldValue(field)"
                  :src="getFieldValue(field).startsWith('http') ? getFieldValue(field) : '/' + getFieldValue(field).replace(/^\//, '')"
                  alt="Preview" />
                <div v-else class="preview-empty-icon">
                  <i class="bx bx-image-alt"></i>
                </div>
              </div>
              <div class="pages-image-btn-wrap">
                <button type="button" class="btn-upload-file" :disabled="isUploadingMedia"
                  @click="triggerMediaUpload(section.id, field, 'image/*')" title="Tải ảnh từ máy tính">
                  <i class="bx" :class="isFieldUploading(field) ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isFieldUploading(field) ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                </button>
              </div>
            </div>

            <!-- Video / Media URL Input -->
            <div v-else-if="field.type === 'video'" class="video-field-wrap">
              <div class="media-input-group">
                <input :value="getFieldValue(field)" type="text" class="field-input"
                  placeholder="storage/uploads/... hoặc assets/images/..."
                  @input="updateField(section.id, field, $event.target.value)" />
                <button type="button" class="btn-upload-file" :disabled="isUploadingMedia"
                  @click="triggerMediaUpload(section.id, field, 'video/mp4,video/webm,video/quicktime,image/*')"
                  title="Tải tệp lên từ máy tính (Video MP4/WEBM hoặc Ảnh PNG/JPG/WEBP, tối đa 40MB)">
                  <i class="bx" :class="isFieldUploading(field) ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isFieldUploading(field) ? 'Đang tải...' : 'Tải file từ máy' }}</span>
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

            <!-- Gallery Multi-Image Field -->
            <div v-else-if="field.type === 'gallery'" class="pages-gallery-wrap">
              <div class="pages-gallery-top-bar">
                <button type="button" class="btn-upload-file" :disabled="isUploadingGallery"
                  @click="triggerGalleryUpload(section.id, field)" title="Tải ảnh từ máy tính">
                  <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isUploadingGallery ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                </button>
              </div>

              <div class="pages-gallery-grid mt-2">
                <div v-for="(img, idx) in getGalleryItems(field)" :key="idx" class="pages-gallery-item">
                  <img :src="img.startsWith('http') ? img : '/' + img.replace(/^\//, '')" alt="Gallery item"
                    loading="lazy" />
                  <span class="gallery-num-badge">#{{ idx + 1 }}</span>
                  <button type="button" class="btn-del-gallery" @click="removeGalleryImage(section.id, field, idx)"
                    title="Xóa ảnh này">
                    <i class="bx bx-x"></i>
                  </button>
                </div>

                <div class="pages-gallery-add-card" @click="triggerGalleryUpload(section.id, field)">
                  <i class="bx" :class="isUploadingGallery ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                  <span>{{ isUploadingGallery ? 'Đang tải...' : 'Thêm ảnh mới' }}</span>
                </div>
              </div>
            </div>

            <!-- Repeater / List Field (Workflow Steps & Team Members CRUD) -->
            <div v-else-if="field.type === 'repeater' || field.key === 'workflow_steps'" class="workflow-repeater-wrap">
              <label class="field-label" style="margin-bottom: 2px;">{{ getRepeaterConfig(field).sectionLabel }}</label>

              <div class="workflow-step-cards-list">
                <div v-for="(step, sIdx) in getRepeaterItems(field)" :key="step.id || sIdx" class="workflow-step-card"
                  @click="onStepFocus(field, sIdx)">
                  <div class="workflow-step-card-header">
                    <div class="step-card-left">
                      <span class="step-badge">{{ getRepeaterConfig(field).badgePrefix ? `${getRepeaterConfig(field).badgePrefix} ${String(sIdx + 1).padStart(2, '0')}` : String(sIdx + 1).padStart(2, '0') }}</span>
                      <span v-if="getRepeaterConfig(field).showHeaderTitle !== false" class="step-card-title-preview" :title="getRepeaterPreviewTitle(field, step)">
                        {{ getRepeaterPreviewTitle(field, step) || '(Chưa nhập nội dung)' }}
                      </span>
                    </div>
                    <div class="step-card-actions">
                      <button type="button" class="btn-step-action" :disabled="sIdx === 0"
                        @click.stop="moveRepeaterStep(section.id, field, sIdx, -1)" title="Di chuyển lên">
                        <i class="bx bx-chevron-up"></i>
                      </button>
                      <button type="button" class="btn-step-action" :disabled="sIdx === getRepeaterItems(field).length - 1"
                        @click.stop="moveRepeaterStep(section.id, field, sIdx, 1)" title="Di chuyển xuống">
                        <i class="bx bx-chevron-down"></i>
                      </button>
                      <button type="button" class="btn-step-action btn-step-del"
                        @click.stop="removeRepeaterStep(section.id, field, sIdx)" title="Xóa mục này">
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </div>

                  <div class="workflow-step-card-body">
                    <div class="step-field-inner">
                      <label class="step-field-label">{{ getRepeaterConfig(field).primaryLabel }}</label>
                      <input type="text" class="field-input"
                        :value="getRepeaterItemField(step, getRepeaterConfig(field).primaryKey)"
                        :placeholder="currentLanguage === 'en' ? getRepeaterConfig(field).primaryPlaceholderEn : getRepeaterConfig(field).primaryPlaceholderVi"
                        @focus="onStepFocus(field, sIdx)"
                        @input="updateRepeaterItemField(section.id, field, sIdx, getRepeaterConfig(field).primaryKey, $event.target.value)" />
                    </div>

                    <div class="step-field-inner">
                      <label class="step-field-label">{{ getRepeaterConfig(field).secondaryLabel }}</label>
                      <input v-if="getRepeaterConfig(field).secondaryType === 'input'" type="text" class="field-input"
                        :value="getRepeaterItemField(step, getRepeaterConfig(field).secondaryKey)"
                        :placeholder="currentLanguage === 'en' ? getRepeaterConfig(field).secondaryPlaceholderEn : getRepeaterConfig(field).secondaryPlaceholderVi"
                        @focus="onStepFocus(field, sIdx)"
                        @input="updateRepeaterItemField(section.id, field, sIdx, getRepeaterConfig(field).secondaryKey, $event.target.value)" />
                      <textarea v-else class="field-textarea" rows="3"
                        :value="getRepeaterItemField(step, getRepeaterConfig(field).secondaryKey)"
                        :placeholder="currentLanguage === 'en' ? getRepeaterConfig(field).secondaryPlaceholderEn : getRepeaterConfig(field).secondaryPlaceholderVi"
                        @focus="onStepFocus(field, sIdx)"
                        @input="updateRepeaterItemField(section.id, field, sIdx, getRepeaterConfig(field).secondaryKey, $event.target.value)"></textarea>
                    </div>

                    <div class="step-field-inner">
                      <label class="step-field-label">{{ getRepeaterConfig(field).imageLabel }}</label>
                      <div class="pages-image-upload-row">
                        <div class="pages-image-thumb" @click="triggerStepImageUpload(section.id, field, sIdx)"
                          title="Nhấn để đổi ảnh">
                          <img v-if="getStepImage(step)"
                            :src="getStepImage(step).startsWith('http') ? getStepImage(step) : '/' + getStepImage(step).replace(/^\//, '')"
                            alt="Item image" />
                          <div v-else class="preview-empty-icon">
                            <i class="bx bx-image-alt"></i>
                          </div>
                        </div>
                        <div class="pages-image-btn-wrap">
                          <button type="button" class="btn-upload-file" :disabled="isStepUploading(field, sIdx)"
                            @click="triggerStepImageUpload(section.id, field, sIdx)" title="Tải ảnh từ máy tính">
                            <i class="bx" :class="isStepUploading(field, sIdx) ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                            <span>{{ isStepUploading(field, sIdx) ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <button type="button" class="btn-add-step" @click="addRepeaterStep(section.id, field)">
                <i class="bx bx-plus-circle"></i>
                <span>{{ getRepeaterConfig(field).addButtonText }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden native file input for media upload -->
    <input type="file" ref="mediaFileInputRef" style="display: none;" :accept="mediaUploadAccept"
      @change="handleMediaUploadSelected" />

    <!-- Hidden native file input for gallery multi-upload -->
    <input type="file" ref="galleryFileInputRef" style="display: none;" multiple accept="image/*"
      @change="handleGalleryUploadSelected" />

    <!-- Hidden native file input for step image upload -->
    <input type="file" ref="stepFileInputRef" style="display: none;" accept="image/*"
      @change="handleStepImageUploadSelected" />
  </Layout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue';
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
import {
  askConfirm,
  extractErrorMessage,
  notifyError,
  notifySuccess,
  notifyWarning,
} from './composables/useFeedback';

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
          { key: 'about_label', label: 'Nhãn tiêu đề', type: 'text', prefix: '•', value: { vi: 'Về chúng tôi', en: 'About us' } },
          { key: 'about_desc', label: 'Nội dung mô tả', type: 'textarea', rows: 4, value: { vi: 'Có rất nhiều lý do tại sao xây dựng thương hiệu và tiếp thị lại quan trọng. Vô số sách viết về chủ đề này, nhưng tất cả đều quy về một điều đơn giản: tại sao mọi người nên quan tâm đến sự tồn tại của bạn? Chúng tôi tạo ra những trải nghiệm khiến mọi người chú ý và quan tâm.', en: 'There are countless reasons why branding and marketing matter. Endless books have been written on this topic, but it all comes down to one simple question: why should anyone care about your existence? We create experiences that make people notice and care.' } },
          { key: 'about_btn_text', label: 'Chữ trên nút bấm', type: 'text', value: { vi: 'ĐỌC THÊM', en: 'READ MORE' } },
          { key: 'about_btn_url', label: 'Đường dẫn liên kết nút', type: 'link', value: { vi: '/about-us', en: '/about-us' } },
        ],
      },
      {
        id: 'intro_services',
        title: 'Khối Giới thiệu Dịch vụ',
        fields: [
          { key: 'services_label', label: 'Nhãn tiêu đề', type: 'text', prefix: '•', value: { vi: 'Dịch vụ', en: 'Services' } },
          { key: 'services_statement', label: 'Tuyên ngôn dịch vụ (Statement)', type: 'textarea', rows: 3, value: { vi: 'Chúng tôi giúp doanh nghiệp xây dựng thương hiệu rõ ràng, nhất quán để truyền tải giá trị khác biệt và tăng trưởng.', en: 'We help businesses build clear, consistent brands to convey distinct value and drive growth.' } },
          { key: 'services_desc', label: 'Mô tả ngắn bên phải', type: 'textarea', rows: 3, value: { vi: 'Từ chiến lược đến trải nghiệm, chúng tôi định hình thương hiệu rõ nét.', en: 'From strategy to experience, we shape distinct brands.' } },
        ],
      },
      {
        id: 'intro_testimonials',
        title: 'Khối Trải nghiệm khách hàng',
        fields: [
          { key: 'testimonial_title_1', label: 'Dòng tiêu đề 1', type: 'text', value: { vi: 'TRẢI NGHIỆM', en: 'CLIENT' } },
          { key: 'testimonial_title_2', label: 'Dòng tiêu đề 2', type: 'text', value: { vi: 'KHÁCH HÀNG', en: 'EXPERIENCES' } },
        ],
      },
      {
        id: 'intro_blog',
        title: 'Khối Giới thiệu Blog',
        fields: [
          { key: 'blog_label', label: 'Nhãn tiêu đề', type: 'text', prefix: '•', value: { vi: 'Blog', en: 'Blog' } },
          { key: 'blog_statement', label: 'Giới thiệu bài viết', type: 'textarea', rows: 3, value: { vi: 'Chia sẻ góc nhìn về thương hiệu, thiết kế và chiến lược giúp doanh nghiệp phát triển bền vững.', en: 'Sharing perspectives on branding, design, and strategy to help businesses grow sustainably.' } },
          { key: 'blog_btn_text', label: 'Nút xem thêm', type: 'text', value: { vi: 'XEM THÊM BÀI VIẾT', en: 'VIEW ALL ARTICLES' } },
          { key: 'blog_btn_url', label: 'Đường dẫn liên kết nút', type: 'link', value: { vi: '/news', en: '/news' } },
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
        id: 'about_hero_gallery',
        title: 'Bộ sưu tập ảnh chuyển động đầu trang',
        fields: [
          {
            key: 'hero_gallery',
            label: 'Danh sách ảnh chuyển động',
            type: 'gallery',
            value: [
              '/assets/images/gallery1.jpg',
              '/assets/images/gallery2.jpg',
              '/assets/images/gallery3.jpg',
              '/assets/images/gallery4.jpg',
              '/assets/images/gallery5.jpg',
              '/assets/images/gallery6.jpg',
              '/assets/images/gallery7.jpg',
              '/assets/images/gallery8.jpg',
              '/assets/images/gallery9.jpg',
            ],
          },
        ],
      },
      {
        id: 'about_intro',
        title: 'Giới thiệu',
        fields: [
          { key: 'about_heading_1', label: 'Tiêu đề dòng 1', type: 'text', value: { vi: 'GIẢI PHÁP', en: 'DESIGN SOLUTIONS' } },
          { key: 'about_heading_2', label: 'Tiêu đề dòng 2', type: 'text', value: { vi: 'THIẾT KẾ CHO', en: 'FOR' } },
          { key: 'about_slogan_old', label: 'Tiêu đề dòng 3 (Trước)', type: 'text', value: { vi: 'THƯƠNG HIỆU', en: 'ENTERPRISE' } },
          { key: 'about_slogan_new', label: 'Tiêu đề dòng 3 (Sau)', type: 'text', value: { vi: 'DOANH NGHIỆP', en: 'BRANDS' } },
        ],
      },
      {
        id: 'about_story',
        title: 'Về chúng tôi',
        fields: [
          { key: 'story_label', label: 'Nhãn tiêu đề', type: 'text', prefix: '•', value: { vi: 'Về chúng tôi', en: 'About us' } },
          { key: 'story_desc_left', label: 'Nội dung đoạn 1', type: 'textarea', rows: 4, value: { vi: 'NÔRM Creative là agency sáng tạo thế hệ mới, chuyên sâu trong lĩnh vực thiết kế nhận diện thương hiệu, tư vấn chiến lược bài bản, toàn diện và các giải pháp hình ảnh chuyên nghiệp cho doanh nghiệp.', en: 'NÔRM Creative is a next-generation creative agency specializing in brand identity design, comprehensive strategy consulting, and professional visual solutions for businesses.' } },
          { key: 'story_desc_right', label: 'Nội dung đoạn 2', type: 'textarea', rows: 4, value: { vi: 'NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.', en: 'NÔRM Creative was born with a fresh, modern, youthful, and disruptive spirit. Taking culture as the core combined with open design thinking, we weave brand stories and personality into every design product, breathing life, forging identity, and distinctly portraying brand character.' } },
          { key: 'story_image', label: 'Hình ảnh giới thiệu', type: 'image', value: { vi: '/assets/images/team-photo.jpg', en: '/assets/images/team-photo.jpg' } },
        ],
      },
      {
        id: 'about_workflow',
        title: 'Quy trình làm việc',
        fields: [
          { key: 'workflow_label', label: 'Nhãn tiêu đề', type: 'text', prefix: '•', value: { vi: 'Quy trình làm việc', en: 'Workflow' } },
          { key: 'workflow_slogan', label: 'Mô tả / Slogan', type: 'textarea', rows: 3, value: { vi: 'Chúng tôi tạo ra những không gian sáng tạo và giàu tính gợi mở, thúc đẩy những trải nghiệm sâu sắc hơn của con người.', en: 'We create innovative and thought-provoking environments that foster deeper human experiences.' } },
          {
            key: 'workflow_steps',
            label: 'Danh sách các bước quy trình',
            type: 'repeater',
            value: [
              {
                id: 1,
                title: { vi: 'Trao đổi & tìm hiểu', en: 'Discovery & Consultation' },
                desc: { vi: 'Tổ chức buổi trao đổi trực tuyến để thu thập thông tin chuyên sâu về thương hiệu, xác định tầm nhìn và kỳ vọng chung.', en: 'Organize an online session to gather in-depth brand insights, defining shared vision and expectations.' },
                image: '/assets/images/gallery1.jpg',
              },
              {
                id: 2,
                title: { vi: 'Khởi tạo & ký kết', en: 'Initiation & Agreement' },
                desc: { vi: 'Thống nhất phương án hợp tác, ký kết hợp đồng và xác lập các mốc thời gian triển khai cụ thể.', en: 'Finalize the scope of collaboration, sign the contract, and establish clear delivery milestones.' },
                image: '/assets/images/gallery2.jpg',
              },
              {
                id: 3,
                title: { vi: 'Thiết kế & hoàn thiện', en: 'Design & Execution' },
                desc: { vi: 'Triển khai thiết kế dựa trên các định hướng đã thống nhất và hoàn thiện sản phẩm cuối cùng.', en: 'Execute creative design based on agreed directions and deliver the finalized outputs.' },
                image: '/assets/images/gallery3.jpg',
              },
            ],
          },
        ],
      },
      {
        id: 'about_members',
        title: 'Thành viên',
        fields: [
          { key: 'members_heading_1', label: 'Tiêu đề dòng 1', type: 'text', value: { vi: 'GẶP GỠ', en: 'MEET' } },
          { key: 'members_heading_2', label: 'Tiêu đề dòng 2', type: 'text', value: { vi: 'THÀNH VIÊN', en: 'THE TEAM' } },
          {
            key: 'members_list',
            label: 'Danh sách thành viên',
            type: 'repeater',
            value: [
              { id: 1, name: { vi: 'Mạnh Thọ', en: 'Manh Tho' }, role: { vi: 'CEO - CO founder', en: 'CEO - CO founder' }, image: '/assets/images/team1.png' },
              { id: 2, name: { vi: 'Loan Ng', en: 'Loan Ng' }, role: { vi: 'Marketing Executive', en: 'Marketing Executive' }, image: '/assets/images/team2.png' },
              { id: 3, name: { vi: 'Yen Nguyen', en: 'Yen Nguyen' }, role: { vi: 'UI/UX designer', en: 'UI/UX designer' }, image: '/assets/images/team3.png' },
              { id: 4, name: { vi: 'Duw', en: 'Duw' }, role: { vi: 'Junior designer', en: 'Junior designer' }, image: '/assets/images/team4.png' },
              { id: 5, name: { vi: 'Yen Nguyen', en: 'Yen Nguyen' }, role: { vi: 'UI/UX designer', en: 'UI/UX designer' }, image: '/assets/images/team3.png' },
              { id: 6, name: { vi: 'Mạnh Thọ', en: 'Manh Tho' }, role: { vi: 'CEO - CO founder', en: 'CEO - CO founder' }, image: '/assets/images/team1.png' },
              { id: 7, name: { vi: 'Loan Ng', en: 'Loan Ng' }, role: { vi: 'Marketing Executive', en: 'Marketing Executive' }, image: '/assets/images/team2.png' },
            ],
          },
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
          { key: 'filter_all', label: 'Nhãn nút "Tất cả dự án"', type: 'text', value: { vi: 'TẤT CẢ DỰ ÁN', en: 'ALL PROJECTS' } },
          { key: 'filter_featured', label: 'Nhãn nút "Nổi bật"', type: 'text', value: { vi: 'NỔI BẬT', en: 'FEATURED' } },
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
  if (openSectionId.value && previewIframe.value && previewIframe.value.contentWindow) {
    previewIframe.value.contentWindow.postMessage(
      {
        type: 'STUDIO_SCROLL_TO_SECTION',
        sectionId: id,
      },
      '*'
    );
  }
};

watch(selectedPageSlug, () => {
  openSectionId.value = null;
});

// Helper to strip leading dot/bullet
const cleanDot = (s) => String(s || '').replace(/^[•\s\-\*]+/u, '').trimStart();

// Helper to get bilingual value
const getFieldValue = (field) => {
  if (!field) return '';
  if (Array.isArray(field.value)) return field.value;
  if (field.value && typeof field.value === 'object') {
    return field.value[currentLanguage.value] ?? field.value['vi'] ?? '';
  }
  return field.value ?? '';
};

// Link Picker logic (Predefined Pages + Custom URL)
const internalPages = [
  { label: 'Về chúng tôi (/about-us)', value: '/about-us' },
  { label: 'Dự án (/work)', value: '/work' },
  { label: 'Bài viết / Tin tức (/news)', value: '/news' },
  { label: 'Trang chủ (/)', value: '/' },
];

const customLinkMap = reactive({});

const getLinkSelectValue = (field) => {
  const currentVal = (getFieldValue(field) || '').trim();
  if (customLinkMap[field.key]) {
    return '__custom__';
  }
  const match = internalPages.find((p) => p.value === currentVal);
  if (match) {
    return match.value;
  }
  return '__custom__';
};

const isCustomLink = (field) => {
  return getLinkSelectValue(field) === '__custom__';
};

const handleLinkSelectChange = (sectionId, field, selectedVal) => {
  if (selectedVal === '__custom__') {
    customLinkMap[field.key] = true;
  } else {
    customLinkMap[field.key] = false;
    updateField(sectionId, field, selectedVal);
  }
};

// Single media upload worker
const uploadSingleMediaFile = async (file) => {
  if (file.size > 40 * 1024 * 1024) {
    notifyWarning(`Dung lượng tệp "${file.name}" không được vượt quá 40MB!`);
    return null;
  }
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
      return data.file_path;
    } else {
      notifyError('Không thể tải tệp lên: ' + (data?.message || 'Lỗi không xác định'));
      return null;
    }
  } catch (err) {
    notifyError('Có lỗi xảy ra khi tải tệp lên máy chủ: ' + err.message);
    return null;
  }
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

  isUploadingMedia.value = true;
  try {
    const uploadedPath = await uploadSingleMediaFile(file);
    if (uploadedPath) {
      const { sectionId, field } = activeUploadTarget.value;
      if (sectionId && field) {
        updateField(sectionId, field, uploadedPath);
      }
    }
  } finally {
    isUploadingMedia.value = false;
  }
};

// Gallery Multi-Image upload logic
const galleryFileInputRef = ref(null);
const isUploadingGallery = ref(false);
const activeGalleryTarget = ref({ sectionId: null, field: null });

const getGalleryItems = (field) => {
  if (!field) return [];
  let val = field.value;
  if (val && typeof val === 'object' && !Array.isArray(val)) {
    val = val[currentLanguage.value] || val.vi || val;
  }
  if (typeof val === 'string') {
    try {
      val = JSON.parse(val);
    } catch (e) {
      val = [];
    }
  }
  return Array.isArray(val) ? val : [];
};

const triggerGalleryUpload = (sectionId, field) => {
  activeGalleryTarget.value = { sectionId, field };
  if (galleryFileInputRef.value) {
    galleryFileInputRef.value.value = '';
    galleryFileInputRef.value.click();
  }
};

const handleGalleryUploadSelected = async (e) => {
  const files = e.target.files;
  if (!files || files.length === 0) return;
  const { sectionId, field } = activeGalleryTarget.value;
  if (!sectionId || !field) return;

  isUploadingGallery.value = true;
  const currentItems = [...getGalleryItems(field)];

  for (let i = 0; i < files.length; i++) {
    const path = await uploadSingleMediaFile(files[i]);
    if (path) {
      currentItems.push(path);
    }
  }

  setGalleryItems(sectionId, field, currentItems);
  isUploadingGallery.value = false;
};

const removeGalleryImage = (sectionId, field, idx) => {
  const currentItems = [...getGalleryItems(field)];
  currentItems.splice(idx, 1);
  setGalleryItems(sectionId, field, currentItems);
};

const setGalleryItems = (sectionId, field, items) => {
  field.value = items;
  hasUnsavedChanges.value = true;
  onFieldChange(sectionId, field.key, items);
};

// Repeater helpers (Workflow Steps & Team Members)
const stepFileInputRef = ref(null);
const isUploadingStepImage = ref(false);
const activeStepUploadTarget = ref({ sectionId: null, field: null, stepIdx: null });

const repeaterConfigs = {
  members_list: {
    sectionLabel: 'Danh sách thành viên',
    badgePrefix: '',
    showHeaderTitle: false,
    primaryKey: 'name',
    primaryLabel: 'Họ tên thành viên',
    primaryPlaceholderVi: 'Ví dụ: Mạnh Thọ...',
    primaryPlaceholderEn: 'E.g. Manh Tho...',
    secondaryKey: 'role',
    secondaryLabel: 'Vai trò / Chức vụ',
    secondaryType: 'input',
    secondaryPlaceholderVi: 'Ví dụ: CEO - CO founder...',
    secondaryPlaceholderEn: 'E.g. CEO - CO founder...',
    imageLabel: 'Ảnh đại diện',
    addButtonText: 'Thêm thành viên',
    minItems: 0,
    minMessage: '',
    confirmDeleteLabel: 'Thành viên',
    defaultImage: (nextNum) => `/assets/images/team${((nextNum - 1) % 4) + 1}.png`,
    createItem: (nextNum) => ({
      id: Date.now(),
      name: { vi: `Thành viên ${nextNum}`, en: `Member ${nextNum}` },
      role: { vi: 'Vị trí / Chức vụ', en: 'Role / Position' },
      image: `/assets/images/team${((nextNum - 1) % 4) + 1}.png`,
    }),
  },
  workflow_steps: {
    sectionLabel: 'Các bước quy trình',
    badgePrefix: 'Bước',
    primaryKey: 'title',
    primaryLabel: 'Tiêu đề bước',
    primaryPlaceholderVi: 'Ví dụ: Trao đổi & tìm hiểu...',
    primaryPlaceholderEn: 'Nhập tiêu đề tiếng Anh...',
    secondaryKey: 'desc',
    secondaryLabel: 'Mô tả chi tiết',
    secondaryType: 'textarea',
    secondaryPlaceholderVi: 'Nội dung mô tả chi tiết của bước này...',
    secondaryPlaceholderEn: 'Nhập mô tả tiếng Anh...',
    imageLabel: 'Hình ảnh minh họa',
    addButtonText: 'Thêm bước',
    minItems: 1,
    minMessage: 'Quy trình phải có ít nhất 1 bước!',
    confirmDeleteLabel: 'Bước',
    createItem: (nextNum) => ({
      id: Date.now(),
      title: { vi: `Bước ${nextNum}: Tiêu đề mới`, en: `Step ${nextNum}: New title` },
      desc: { vi: 'Nội dung mô tả cho bước mới...', en: 'Description for new step...' },
      image: `/assets/images/gallery${((nextNum - 1) % 9) + 1}.jpg`,
    }),
  },
};

const getRepeaterConfig = (field) => {
  return repeaterConfigs[field?.key] || repeaterConfigs.workflow_steps;
};

const getRepeaterItems = (field) => {
  if (!field) return [];
  let val = field.value;
  if (typeof val === 'string') {
    try {
      val = JSON.parse(val);
    } catch (e) {
      val = [];
    }
  }
  return Array.isArray(val) ? val : [];
};

const getRepeaterItemField = (item, propKey) => {
  if (!item || !item[propKey]) return '';
  if (typeof item[propKey] === 'object') {
    return item[propKey][currentLanguage.value] !== undefined ? item[propKey][currentLanguage.value] : '';
  }
  return currentLanguage.value === 'vi' ? item[propKey] : '';
};

const getRepeaterPreviewTitle = (field, item) => {
  const key = getRepeaterConfig(field).primaryKey;
  const val = item?.[key];
  if (!val) return '';
  if (typeof val === 'object') {
    return val[currentLanguage.value] || val.vi || val.en || '';
  }
  return val;
};

const getStepImage = (step) => {
  if (!step) return '';
  let img = step.image || '';
  if (img && typeof img === 'object') {
    img = img[currentLanguage.value] || img.vi || '';
  }
  return img;
};

const updateRepeaterItemField = (sectionId, field, stepIdx, propKey, newVal) => {
  const items = [...getRepeaterItems(field)];
  if (!items[stepIdx]) return;
  const currentItem = { ...items[stepIdx] };
  let fieldObj = currentItem[propKey];
  if (!fieldObj || typeof fieldObj !== 'object') {
    fieldObj = { vi: fieldObj || '', en: fieldObj || '' };
  } else {
    fieldObj = { ...fieldObj };
  }
  fieldObj[currentLanguage.value] = newVal;
  currentItem[propKey] = fieldObj;
  items[stepIdx] = currentItem;
  field.value = items;
  onFieldChange(sectionId, field.key, items);
};

const onStepFocus = (field, stepIdx) => {
  if (!previewIframe.value || !previewIframe.value.contentWindow) return;
  if (field?.key === 'workflow_steps') {
    previewIframe.value.contentWindow.postMessage(
      {
        type: 'STUDIO_SCROLL_TO_WORKFLOW_STEP',
        stepIndex: stepIdx,
      },
      '*'
    );
  }
};

const addRepeaterStep = (sectionId, field) => {
  const cfg = getRepeaterConfig(field);
  const items = [...getRepeaterItems(field)];
  const nextNum = items.length + 1;
  items.push(cfg.createItem(nextNum));
  field.value = items;
  onFieldChange(sectionId, field.key, items);
  setTimeout(() => onStepFocus(field, items.length - 1), 80);
};

const removeRepeaterStep = async (sectionId, field, stepIdx) => {
  const cfg = getRepeaterConfig(field);
  const items = [...getRepeaterItems(field)];
  if (cfg.minItems > 0 && items.length <= cfg.minItems) {
    notifyWarning(cfg.minMessage);
    return;
  }
  const ok = await askConfirm({
    title: `Xóa ${cfg.confirmDeleteLabel}`,
    message: `Bạn có chắc muốn xóa ${cfg.confirmDeleteLabel} ${stepIdx + 1}?`,
    confirmText: 'Xóa',
    variant: 'danger',
  });
  if (!ok) return;
  items.splice(stepIdx, 1);
  field.value = items;
  onFieldChange(sectionId, field.key, items);
  setTimeout(() => onStepFocus(field, Math.max(0, stepIdx - 1)), 80);
};

const moveRepeaterStep = (sectionId, field, stepIdx, direction) => {
  const items = [...getRepeaterItems(field)];
  const targetIdx = stepIdx + direction;
  if (targetIdx < 0 || targetIdx >= items.length) return;
  const temp = items[stepIdx];
  items[stepIdx] = items[targetIdx];
  items[targetIdx] = temp;
  field.value = items;
  onFieldChange(sectionId, field.key, items);
};

const isStepUploading = (field, stepIdx) => {
  return (
    isUploadingStepImage.value &&
    activeStepUploadTarget.value.field?.key === field?.key &&
    activeStepUploadTarget.value.stepIdx === stepIdx
  );
};

const triggerStepImageUpload = (sectionId, field, stepIdx) => {
  activeStepUploadTarget.value = { sectionId, field, stepIdx };
  if (stepFileInputRef.value) {
    stepFileInputRef.value.value = '';
    stepFileInputRef.value.click();
  }
};

const handleStepImageUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  isUploadingStepImage.value = true;
  try {
    const uploadedPath = await uploadSingleMediaFile(file);
    if (uploadedPath) {
      const { sectionId, field, stepIdx } = activeStepUploadTarget.value;
      if (sectionId && field && stepIdx !== null) {
        const items = [...getRepeaterItems(field)];
        if (items[stepIdx]) {
          const currentStep = { ...items[stepIdx] };
          currentStep.image = uploadedPath;
          items[stepIdx] = currentStep;
          field.value = items;
          onFieldChange(sectionId, field.key, items);
          onStepFocus(field, stepIdx);
        }
      }
    }
  } finally {
    isUploadingStepImage.value = false;
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

// Đồng bộ thay đổi tức thời sang Iframe qua postMessage (không tự đánh dấu chưa lưu)
const syncPreviewField = (sectionId, fieldKey, value) => {
  if (!previewIframe.value || !previewIframe.value.contentWindow) return;

  let cleanValue = value;
  try {
    cleanValue = JSON.parse(JSON.stringify(value));
  } catch (e) {
    console.warn('Could not serialize value for postMessage', e);
  }

  previewIframe.value.contentWindow.postMessage(
    {
      type: 'STUDIO_LIVE_UPDATE',
      sectionId,
      fieldKey,
      value: cleanValue,
      lang: currentLanguage.value,
    },
    '*'
  );
};

// Đánh dấu có thay đổi chưa lưu khi người dùng chỉnh sửa nội dung và đồng bộ sang Iframe
const onFieldChange = (sectionId, fieldKey, value) => {
  hasUnsavedChanges.value = true;
  syncPreviewField(sectionId, fieldKey, value);
};

// Đồng bộ toàn bộ nội dung preview khi chuyển tab ngôn ngữ (VI <-> EN)
watch(currentLanguage, (newLang) => {
  if (previewIframe.value && previewIframe.value.contentWindow) {
    previewIframe.value.contentWindow.postMessage(
      {
        type: 'STUDIO_LANG_UPDATE',
        lang: newLang,
      },
      '*'
    );
  }

  if (currentPageData.value && currentPageData.value.sections) {
    currentPageData.value.sections.forEach((sec) => {
      if (sec.fields) {
        sec.fields.forEach((f) => {
          if (f.type === 'repeater' || f.key === 'workflow_steps') {
            syncPreviewField(sec.id, f.key, f.value);
          } else if (f.type !== 'gallery') {
            let val = f.value;
            if (val && typeof val === 'object') {
              val = val[newLang] ?? val.vi ?? '';
            }
            syncPreviewField(sec.id, f.key, val);
          }
        });
      }
    });
  }
});

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

        notifySuccess(isDraft ? 'Đã lưu bản nháp thành công!' : 'Đã xuất bản thành công!');
      },
      onError: (err) => {
        isSaving.value = false;
        isSavingDraft.value = false;
        notifyError('Có lỗi xảy ra khi lưu: ' + extractErrorMessage(err));
      },
    }
  );
};

const handleDiscardDraft = async () => {
  const ok = await askConfirm({
    title: 'Xóa bản nháp',
    message: 'Bạn có chắc chắn muốn xóa bản nháp này không?',
    confirmText: 'Xóa bản nháp',
    variant: 'danger',
  });
  if (!ok) {
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

        notifySuccess('Đã xóa bản nháp thành công!');
      },
      onError: (err) => {
        isDiscardingDraft.value = false;
        notifyError('Có lỗi xảy ra khi xóa bản nháp: ' + extractErrorMessage(err));
      },
    }
  );
};

const revertChanges = async () => {
  const ok = await askConfirm({
    title: 'Hủy thay đổi chưa lưu',
    message: 'Hủy bỏ các thay đổi vừa chỉnh sửa và quay lại nội dung đã lưu gần nhất?',
    confirmText: 'Hủy thay đổi',
    variant: 'warning',
  });
  if (ok) {
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
