<template>
  <Layout>
    <div class="settings-workspace-view" :key="renderKey">
      <div class="settings-container">
        <!-- Header Card -->
        <div class="settings-header-card">
          <div class="settings-header-main">
            <div class="settings-title-area">
              <h1 class="settings-title">
                <i class="bx bx-cog"></i>
                <span>Cài đặt Website & Chân trang</span>
              </h1>
              <p class="settings-subtitle">
                Cấu hình hình ảnh thương hiệu, mô hình 3D, chân trang, liên hệ và mạng xã hội.
              </p>
            </div>
          </div>
        </div>

        <!-- Staging Alert Banner when settings have unsaved changes -->
        <div v-if="hasUnsavedChanges" class="staging-alert-banner">
          <div class="banner-info">
            <i class="bx bx-info-circle banner-icon"></i>
            <span>Có thay đổi chưa lưu</span>
          </div>
        </div>

        <form @submit.prevent="saveSettings" class="settings-form-layout">
          <div class="settings-card">
            <div class="settings-card-header">
              <h2 class="settings-card-title">
                <i class="bx bx-image-alt"></i> Hình ảnh logo & Mô hình 3D
              </h2>
              <p class="settings-card-desc">Cấu hình Logo hiển thị tại header và file 3D banner trang chủ.</p>
            </div>

            <div class="settings-grid-2">
              <div class="form-group-custom">
                <label>
                  <span>Logo chính</span>
                </label>
                <div class="settings-upload-row">
                  <div class="settings-preview-thumb">
                    <img v-if="form.logo" :src="getImageUrl(form.logo)" alt="Logo" />
                    <div v-else class="preview-empty-icon">
                      <i class="bx bx-image-alt"></i>
                    </div>
                  </div>
                  <div class="settings-btn-row">
                    <button type="button" class="btn-upload-file" :disabled="isUploadingLogo" @click="triggerLogoUpload"
                      title="Tải ảnh Logo từ máy tính">
                      <i class="bx" :class="isUploadingLogo ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                      <span>{{ isUploadingLogo ? 'Đang tải...' : 'Tải ảnh từ máy' }}</span>
                    </button>
                    <button v-if="form.logo" type="button" class="btn-remove-file" @click="form.logo = ''"
                      title="Xóa logo">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- 3D Model -->
              <div class="form-group-custom">
                <label>
                  <span>Mô hình 3D Hero (.glb)</span>
                </label>
                <div class="settings-upload-row">
                  <div class="settings-preview-thumb model-3d-box">
                    <i class="bx bx-cube" :class="{ 'has-file': form.model_3d }"></i>
                    <span v-if="form.model_3d" class="model-filename" :title="form.model_3d">{{
                      getFileName(form.model_3d) }}</span>
                    <span v-else class="model-filename text-muted">Chưa có tệp</span>
                  </div>
                  <div class="settings-btn-row">
                    <button type="button" class="btn-upload-file" :disabled="isUploading3D" @click="trigger3DUpload"
                      title="Tải tệp mô hình .glb từ máy tính (tối đa 50MB)">
                      <i class="bx" :class="isUploading3D ? 'bx-loader-alt bx-spin' : 'bx-upload'"></i>
                      <span>{{ isUploading3D ? 'Đang tải...' : 'Tải tệp 3D từ máy' }}</span>
                    </button>
                    <button v-if="form.model_3d" type="button" class="btn-remove-file" @click="form.model_3d = ''"
                      title="Xóa tệp 3D">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
                <span class="form-hint-custom">Mô hình 3D tại banner trang chủ.</span>
              </div>
            </div>
          </div>

          <div class="settings-card">
            <div class="settings-card-header">
              <h2 class="settings-card-title">
                <i class="bx bx-dock-bottom"></i> Cấu hình Chân trang
              </h2>
              <p class="settings-card-desc">Quản lý nút bấm, tiêu đề mạng xã hội và các dòng chữ chuyển động.
              </p>
            </div>

            <div class="settings-grid-3 mb-4">
              <div class="form-group-custom">
                <label>
                  <span>Nút hành động</span>
                  <span class="lang-pill vi">VI</span>
                </label>
                <input v-model="form.footer_btn_text_vi" type="text" class="form-control-custom"
                  placeholder="CÙNG BẮT ĐẦU" />
              </div>

              <div class="form-group-custom">
                <label>
                  <span>Nút hành động</span>
                  <span class="lang-pill en">EN</span>
                </label>
                <input v-model="form.footer_btn_text_en" type="text" class="form-control-custom"
                  placeholder="GET STARTED" />
              </div>

              <div class="form-group-custom">
                <label>
                  <span>Dòng bản quyền</span>
                </label>
                <input v-model="form.copyright" type="text" class="form-control-custom"
                  placeholder="©2026 All Rights Reserved" />
              </div>

              <!-- Tiêu đề Mạng xã hội VI -->
              <div class="form-group-custom">
                <label>
                  <span>Tiêu đề Mạng xã hội</span>
                  <span class="lang-pill vi">VI</span>
                </label>
                <input v-model="form.footer_social_title_vi" type="text" class="form-control-custom"
                  placeholder="Mạng xã hội" />
              </div>

              <!-- Tiêu đề Mạng xã hội EN -->
              <div class="form-group-custom">
                <label>
                  <span>Tiêu đề Mạng xã hội</span>
                  <span class="lang-pill en">EN</span>
                </label>
                <input v-model="form.footer_social_title_en" type="text" class="form-control-custom"
                  placeholder="Social Media" />
              </div>
            </div>

            <div class="mt-4">
              <label class="d-block mb-3" style="font-size: 13.5px; font-weight: 700; color: #0f172a;"> 3 dòng chữ lật
                chuyển động chân trang
              </label>

              <div class="d-flex flex-column gap-3">
                <!-- Dòng 1 -->
                <div class="words-row-box">
                  <div class="words-row-title">
                    <span>Dòng 1</span>
                  </div>
                  <div class="settings-grid-2">
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line1_old_vi" type="text" class="form-control-custom"
                        placeholder="TỪ NHỮNG" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line1_new_vi" type="text" class="form-control-custom"
                        placeholder="ĐẾN MỘT" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line1_old_en" type="text" class="form-control-custom"
                        placeholder="FROM AN" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line1_new_en" type="text" class="form-control-custom"
                        placeholder="INTO A" />
                    </div>
                  </div>
                </div>

                <!-- Dòng 2 -->
                <div class="words-row-box">
                  <div class="words-row-title">
                    <span>Dòng 2</span>
                  </div>
                  <div class="settings-grid-2">
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line2_old_vi" type="text" class="form-control-custom"
                        placeholder="Ý TƯỞNG" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line2_new_vi" type="text" class="form-control-custom"
                        placeholder="THƯƠNG HIỆU" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line2_old_en" type="text" class="form-control-custom"
                        placeholder="IDEA" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line2_new_en" type="text" class="form-control-custom"
                        placeholder="BRAND" />
                    </div>
                  </div>
                </div>

                <!-- Dòng 3 -->
                <div class="words-row-box">
                  <div class="words-row-title">
                    <span>Dòng 3</span>
                  </div>
                  <div class="settings-grid-2">
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line3_old_vi" type="text" class="form-control-custom"
                        placeholder="BAN ĐẦU" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill vi">VI</span></label>
                      <input v-model="form.footer_line3_new_vi" type="text" class="form-control-custom"
                        placeholder="ĐÁNG NHỚ" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ban đầu</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line3_old_en" type="text" class="form-control-custom"
                        placeholder="INITIAL" />
                    </div>
                    <div class="form-group-custom">
                      <label><span>Từ hiển thị ra sau</span> <span class="lang-pill en">EN</span></label>
                      <input v-model="form.footer_line3_new_en" type="text" class="form-control-custom"
                        placeholder="MEMORABLE" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Thông tin liên hệ -->
          <div class="settings-card">
            <div class="settings-card-header">
              <h2 class="settings-card-title">
                <i class="bx bx-phone-call"></i> Thông tin liên hệ
              </h2>
              <p class="settings-card-desc">Hiển thị tại chân trang và trang liên hệ.</p>
            </div>

            <div class="settings-grid-2 mb-3">
              <div class="form-group-custom">
                <label><span>Hotline điện thoại</span></label>
                <input v-model="form.hotline" type="text" class="form-control-custom" placeholder="(+84)816 656718" />
              </div>
              <div class="form-group-custom">
                <label><span>Email liên hệ</span></label>
                <input v-model="form.email" type="email" class="form-control-custom" placeholder="info@norm.vn" />
              </div>
            </div>

            <div class="settings-grid-2">
              <div class="form-group-custom">
                <label><span>Địa chỉ trụ sở</span> <span class="lang-pill vi">VI</span></label>
                <textarea v-model="form.address_vi" class="form-control-custom form-textarea-custom" rows="2"
                  placeholder="Số 110 Nguyễn Ngọc Nại, P. Phương Liệt, TP. Hà Nội"></textarea>
              </div>
              <div class="form-group-custom">
                <label><span>Địa chỉ trụ sở</span> <span class="lang-pill en">EN</span></label>
                <textarea v-model="form.address_en" class="form-control-custom form-textarea-custom" rows="2"
                  placeholder="No. 110 Nguyen Ngoc Nai street, Phuong Liet ward, Ha Noi city, Vietnam"></textarea>
              </div>
            </div>
          </div>

          <!-- 4. Mạng xã hội & Kênh truyền thông -->
          <div class="settings-card">
            <div class="settings-card-header">
              <h2 class="settings-card-title">
                <i class="bx bx-share-alt"></i> Mạng xã hội & Kênh truyền thông
              </h2>
              <p class="settings-card-desc">Liên kết mạng xã hội hiển thị tại chân trang.</p>
            </div>

            <div class="settings-grid-2">
              <div class="form-group-custom">
                <label><span>Facebook URL</span></label>
                <input v-model="form.social_facebook" type="text" class="form-control-custom"
                  placeholder="https://facebook.com/..." />
              </div>
              <div class="form-group-custom">
                <label><span>Instagram URL</span></label>
                <input v-model="form.social_instagram" type="text" class="form-control-custom"
                  placeholder="https://instagram.com/..." />
              </div>
              <div class="form-group-custom">
                <label><span>LinkedIn URL</span></label>
                <input v-model="form.social_linkedin" type="text" class="form-control-custom"
                  placeholder="https://linkedin.com/..." />
              </div>
              <div class="form-group-custom">
                <label><span>Behance URL</span></label>
                <input v-model="form.social_behance" type="text" class="form-control-custom"
                  placeholder="https://behance.net/..." />
              </div>
              <div class="form-group-custom">
                <label><span>Dribbble URL</span></label>
                <input v-model="form.social_dribbble" type="text" class="form-control-custom"
                  placeholder="https://dribbble.com/..." />
              </div>
            </div>
          </div>

          <!-- 3b. Cấu hình Email & SMTP -->
          <div class="settings-card">
            <div class="settings-card-header">
              <div>
                <h2 class="settings-card-title">
                  <i class="bx bx-envelope"></i> Cấu hình Email &amp; SMTP
                </h2>
              </div>
              <label class="mail-toggle-label">
                <input v-model="form.mail_enabled" type="checkbox" />
                <span>{{ form.mail_enabled ? 'Đang bật' : 'Đang tắt' }}</span>
              </label>
            </div>

            <div class="settings-grid-2 mb-3">
              <div class="form-group-custom">
                <label><span>Email nhận thông báo</span></label>
                <input v-model="form.mail_to_address" type="text" class="form-control-custom"
                  placeholder="info@norm.vn (nhiều email cách nhau bằng dấu phẩy)" />
                <span class="form-hint-custom">Nếu để trống sẽ lấy theo Email liên hệ ở mục trên.</span>
              </div>

              <div class="form-group-custom">
                <label><span>SMTP Host</span></label>
                <select v-model="selectedSmtpHostPreset" @change="handleSmtpHostPresetChange"
                  class="form-control-custom">
                  <option v-for="item in smtpHostPresets" :key="item.host" :value="item.host">
                    {{ item.label }}
                  </option>
                  <option value="custom">Khác (tự nhập)...</option>
                </select>
                <input v-if="selectedSmtpHostPreset === 'custom'" v-model="form.mail_host" type="text"
                  class="form-control-custom mt-2" placeholder="Nhập SMTP Host (vd: mail.domain.com)" />
              </div>
            </div>

            <div class="settings-grid-3 mb-3">
              <div class="form-group-custom">
                <label><span>SMTP Port</span></label>
                <input v-model="form.mail_port" type="number" class="form-control-custom" placeholder="587" />
              </div>

              <div class="form-group-custom">
                <label><span>Bảo mật</span></label>
                <select v-model="form.mail_encryption" class="form-control-custom">
                  <option value="tls">TLS / STARTTLS (cổng 587)</option>
                  <option value="ssl">SSL (cổng 465)</option>
                </select>
              </div>

              <div class="form-group-custom">
                <label><span>Tài khoản SMTP</span></label>
                <input v-model="form.mail_username" type="text" class="form-control-custom"
                  placeholder="your-email@gmail.com" autocomplete="off" />
              </div>
            </div>

            <div class="settings-grid-3 mb-3">
              <div class="form-group-custom">
                <label><span>Mật khẩu SMTP / App Password</span></label>
                <div class="mail-password-wrap">
                  <input v-model="form.mail_password" :type="showMailPassword ? 'text' : 'password'"
                    class="form-control-custom"
                    :placeholder="mailPasswordSet ? '•••••••• (đã lưu, để trống nếu giữ nguyên)' : 'Nhập mật khẩu hoặc App Password'"
                    autocomplete="new-password" />
                  <button type="button" class="btn-toggle-pass" @click="showMailPassword = !showMailPassword"
                    :title="showMailPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'">
                    <i class="bx" :class="showMailPassword ? 'bx-hide' : 'bx-show'"></i>
                  </button>
                </div>
              </div>

              <div class="form-group-custom">
                <label><span>Email gửi đi</span></label>
                <input v-model="form.mail_from_address" type="email" class="form-control-custom"
                  placeholder="noreply@norm.vn" />
              </div>

              <div class="form-group-custom">
                <label><span>Tên người gửi</span></label>
                <input v-model="form.mail_from_name" type="text" class="form-control-custom"
                  placeholder="Norm Studio" />
              </div>
            </div>

            <div class="mail-test-row">
              <button type="button" class="btn-test-mail" :disabled="isTestingMail" @click="sendTestMail">
                <i class="bx" :class="isTestingMail ? 'bx-loader-alt bx-spin' : 'bx-send'"></i>
                <span>{{ isTestingMail ? 'Đang gửi email thử...' : 'Gửi email thử nghiệm' }}</span>
              </button>
              <span v-if="testMailResult" class="mail-test-feedback"
                :class="testMailResult.success ? 'success' : 'error'">
                {{ testMailResult.message }}
              </span>
            </div>
          </div>

          <!-- Bottom Submit Bar -->
          <div class="settings-bottom-bar">
            <button type="submit" class="btn-save-settings-main" :disabled="isSaving">
              <i class="bx" :class="isSaving ? 'bx-loader-alt bx-spin' : 'bx-save'"></i>
              <span>{{ isSaving ? 'Đang lưu...' : 'Lưu' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Hidden native file input for 3D GLB upload -->
    <input type="file" ref="file3DInputRef" style="display: none;" accept=".glb" @change="handle3DUploadSelected" />

    <!-- Hidden native file input for Logo upload -->
    <input type="file" ref="fileLogoInputRef" style="display: none;" accept="image/*"
      @change="handleLogoUploadSelected" />
  </Layout>
</template>

<script setup>
import { reactive, ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import {
  hasUnsavedChanges,
  isSaving,
  onSaveHandler,
  onDraftHandler,
} from './composables/useAdmin';

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({}),
  },
});

const getVal = (key, lang = null) => {
  const val = props.settings[key];
  if (lang && val && typeof val === 'object') {
    return val[lang] || val.vi || '';
  }
  return val || '';
};

const getImageUrl = (url) => {
  if (!url) return '';
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  return '/' + url.replace(/^\/+/, '');
};

const getFileName = (url) => {
  if (!url) return '';
  return url.split('/').pop() || url;
};

const initFormData = () => ({
  logo: props.settings.logo || '/assets/images/logo.png',
  model_3d: props.settings.model_3d || '/storage/uploads/norm.glb',
  copyright: props.settings.copyright || '©2026 All Rights Reserved',

  // Footer Button & Social Title
  footer_btn_text_vi: getVal('footer_btn_text', 'vi') || 'CÙNG BẮT ĐẦU',
  footer_btn_text_en: getVal('footer_btn_text', 'en') || 'GET STARTED',
  footer_social_title_vi: getVal('footer_social_title', 'vi') || 'Mạng xã hội',
  footer_social_title_en: getVal('footer_social_title', 'en') || 'Social Media',

  // Footer animation words
  footer_line1_old_vi: getVal('footer_line1_old', 'vi') || 'TỪ NHỮNG',
  footer_line1_old_en: getVal('footer_line1_old', 'en') || 'FROM AN',
  footer_line1_new_vi: getVal('footer_line1_new', 'vi') || 'ĐẾN MỘT',
  footer_line1_new_en: getVal('footer_line1_new', 'en') || 'INTO A',

  footer_line2_old_vi: getVal('footer_line2_old', 'vi') || 'Ý TƯỞNG',
  footer_line2_old_en: getVal('footer_line2_old', 'en') || 'IDEA',
  footer_line2_new_vi: getVal('footer_line2_new', 'vi') || 'THƯƠNG HIỆU',
  footer_line2_new_en: getVal('footer_line2_new', 'en') || 'BRAND',

  footer_line3_old_vi: getVal('footer_line3_old', 'vi') || 'BAN ĐẦU',
  footer_line3_old_en: getVal('footer_line3_old', 'en') || 'INITIAL',
  footer_line3_new_vi: getVal('footer_line3_new', 'vi') || 'ĐÁNG NHỚ',
  footer_line3_new_en: getVal('footer_line3_new', 'en') || 'MEMORABLE',

  // Contact info
  hotline: props.settings.hotline || '(+84)816 656718',
  email: props.settings.email || 'info@norm.vn',
  address_vi: getVal('address', 'vi') || 'Số 110 Nguyễn Ngọc Nại, P. Phương Liệt, TP. Hà Nội',
  address_en: getVal('address', 'en') || 'No. 110 Nguyen Ngoc Nai street, Phuong Liet ward, Ha Noi city, Vietnam',

  // Mail / SMTP config
  mail_enabled: Boolean(props.settings.mail_enabled),
  mail_to_address: props.settings.mail_to_address || '',
  mail_host: props.settings.mail_host || 'smtp.gmail.com',
  mail_port: props.settings.mail_port || 587,
  mail_encryption: props.settings.mail_encryption || 'tls',
  mail_username: props.settings.mail_username || '',
  mail_password: '',
  mail_from_address: props.settings.mail_from_address || '',
  mail_from_name: props.settings.mail_from_name || 'Norm Studio',

  // Socials
  social_facebook: props.settings.social_facebook || 'https://facebook.com',
  social_instagram: props.settings.social_instagram || 'https://instagram.com',
  social_linkedin: props.settings.social_linkedin || 'https://linkedin.com',
  social_behance: props.settings.social_behance || 'https://behance.net',
  social_dribbble: props.settings.social_dribbble || 'https://dribbble.com',
});

const smtpHostPresets = [
  { host: 'smtp.gmail.com', label: 'smtp.gmail.com (Gmail)', port: 587, encryption: 'tls' },
  { host: 'smtp.office365.com', label: 'smtp.office365.com (Outlook / 365)', port: 587, encryption: 'tls' },
  { host: 'smtp.zoho.com', label: 'smtp.zoho.com (Zoho Mail)', port: 465, encryption: 'ssl' },
  { host: 'smtp.mail.yahoo.com', label: 'smtp.mail.yahoo.com (Yahoo Mail)', port: 465, encryption: 'ssl' },
  { host: 'sandbox.smtp.mailtrap.io', label: 'sandbox.smtp.mailtrap.io (Mailtrap)', port: 2525, encryption: 'tls' },
];

const form = reactive(initFormData());
const selectedSmtpHostPreset = ref(
  smtpHostPresets.some((p) => p.host === form.mail_host) ? form.mail_host : 'custom'
);

const handleSmtpHostPresetChange = () => {
  if (selectedSmtpHostPreset.value === 'custom') {
    if (smtpHostPresets.some((p) => p.host === form.mail_host)) {
      form.mail_host = '';
    }
    return;
  }

  const preset = smtpHostPresets.find((p) => p.host === selectedSmtpHostPreset.value);
  if (preset) {
    form.mail_host = preset.host;
    form.mail_port = preset.port;
    form.mail_encryption = preset.encryption;
  }
};

const mailPasswordSet = ref(Boolean(props.settings.mail_password_set));
const showMailPassword = ref(false);
const isTestingMail = ref(false);
const testMailResult = ref(null);

const renderKey = ref(0);
let isInitialized = false;
let isReverting = false;

watch(
  () => ({ ...form }),
  () => {
    if (isInitialized && !isReverting) {
      hasUnsavedChanges.value = true;
    }
  },
  { deep: true }
);

// Logo upload logic
const fileLogoInputRef = ref(null);
const isUploadingLogo = ref(false);

const triggerLogoUpload = () => {
  if (fileLogoInputRef.value) {
    fileLogoInputRef.value.value = '';
    fileLogoInputRef.value.click();
  }
};

const handleLogoUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  isUploadingLogo.value = true;
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
      const finalPath = data.file_path.startsWith('/') ? data.file_path : '/' + data.file_path;
      form.logo = finalPath;
      hasUnsavedChanges.value = true;
    } else {
      alert('Không thể tải logo lên: ' + (data.message || 'Lỗi không xác định'));
    }
  } catch (err) {
    alert('Có lỗi xảy ra khi tải logo: ' + err.message);
  } finally {
    isUploadingLogo.value = false;
  }
};

// 3D GLB upload logic
const file3DInputRef = ref(null);
const isUploading3D = ref(false);

const trigger3DUpload = () => {
  if (file3DInputRef.value) {
    file3DInputRef.value.value = '';
    file3DInputRef.value.click();
  }
};

const handle3DUploadSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  if (file.size > 50 * 1024 * 1024) {
    alert('Dung lượng tệp 3D không được vượt quá 50MB!');
    return;
  }

  isUploading3D.value = true;
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
      const finalPath = data.file_path.startsWith('/') ? data.file_path : '/' + data.file_path;
      form.model_3d = finalPath;
      hasUnsavedChanges.value = true;
    } else {
      alert('Không thể tải tệp lên: ' + (data.message || 'Lỗi không xác định'));
    }
  } catch (err) {
    alert('Có lỗi xảy ra khi tải tệp lên máy chủ: ' + err.message);
  } finally {
    isUploading3D.value = false;
  }
};

const revertSettings = () => {
  if (confirm('Hủy bỏ các thay đổi vừa chỉnh sửa và quay lại cài đặt gốc trong cơ sở dữ liệu?')) {
    isReverting = true;
    Object.assign(form, initFormData());
    renderKey.value++;
    hasUnsavedChanges.value = false;
    setTimeout(() => {
      isReverting = false;
      hasUnsavedChanges.value = false;
    }, 50);
  }
};

const saveSettings = () => {
  isSaving.value = true;
  const payload = {
    settings: {
      logo: form.logo,
      model_3d: form.model_3d,
      copyright: form.copyright,

      // Footer CTA & Social title
      footer_btn_text: { vi: form.footer_btn_text_vi, en: form.footer_btn_text_en },
      footer_social_title: { vi: form.footer_social_title_vi, en: form.footer_social_title_en },

      // Footer Animation Words
      footer_line1_old: { vi: form.footer_line1_old_vi, en: form.footer_line1_old_en },
      footer_line1_new: { vi: form.footer_line1_new_vi, en: form.footer_line1_new_en },
      footer_line2_old: { vi: form.footer_line2_old_vi, en: form.footer_line2_old_en },
      footer_line2_new: { vi: form.footer_line2_new_vi, en: form.footer_line2_new_en },
      footer_line3_old: { vi: form.footer_line3_old_vi, en: form.footer_line3_old_en },
      footer_line3_new: { vi: form.footer_line3_new_vi, en: form.footer_line3_new_en },

      // Contact info
      hotline: form.hotline,
      email: form.email,
      address: { vi: form.address_vi, en: form.address_en },

      // Mail / SMTP config
      mail_enabled: form.mail_enabled,
      mail_to_address: form.mail_to_address,
      mail_host: form.mail_host,
      mail_port: form.mail_port ? Number(form.mail_port) : 587,
      mail_encryption: form.mail_encryption,
      mail_username: form.mail_username,
      mail_password: form.mail_password,
      mail_from_address: form.mail_from_address,
      mail_from_name: form.mail_from_name,

      // Social URLs
      social_facebook: form.social_facebook,
      social_instagram: form.social_instagram,
      social_linkedin: form.social_linkedin,
      social_behance: form.social_behance,
      social_dribbble: form.social_dribbble,
    },
  };

  router.post('/admin/settings', payload, {
    preserveScroll: true,
    onSuccess: () => {
      if (form.mail_password) {
        isReverting = true;
        mailPasswordSet.value = true;
        form.mail_password = '';
        setTimeout(() => {
          isReverting = false;
          hasUnsavedChanges.value = false;
        }, 50);
      }
      isSaving.value = false;
      hasUnsavedChanges.value = false;
      alert('Đã xuất bản cài đặt website thành công vào cơ sở dữ liệu!');
    },
    onError: (err) => {
      isSaving.value = false;
      alert('Lỗi lưu cài đặt: ' + JSON.stringify(err));
    },
  });
};

const sendTestMail = async () => {
  isTestingMail.value = true;
  testMailResult.value = null;

  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  try {
    const response = await fetch('/admin/settings/test-mail', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({
        mail_host: form.mail_host,
        mail_port: form.mail_port ? Number(form.mail_port) : 587,
        mail_encryption: form.mail_encryption,
        mail_username: form.mail_username,
        mail_password: form.mail_password,
        mail_from_address: form.mail_from_address,
        mail_from_name: form.mail_from_name,
        mail_to_address: form.mail_to_address || form.email,
      }),
    });

    const data = await response.json();
    testMailResult.value = {
      success: Boolean(data.success),
      message: data.message || (data.success ? 'Gửi email thử thành công!' : 'Gửi email thử thất bại.'),
    };
  } catch (err) {
    testMailResult.value = {
      success: false,
      message: 'Lỗi kết nối: ' + err.message,
    };
  } finally {
    isTestingMail.value = false;
  }
};

onMounted(() => {
  isInitialized = true;
  onSaveHandler.value = saveSettings;
  onDraftHandler.value = saveSettings;
});

onUnmounted(() => {
  onSaveHandler.value = null;
  onDraftHandler.value = null;
  hasUnsavedChanges.value = false;
});
</script>

<style src="./css/settings.css"></style>
