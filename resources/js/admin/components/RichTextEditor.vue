<template>
  <div class="rich-editor-wrapper">
    <!-- Toolbar -->
    <div class="rich-editor-toolbar">
      <div class="toolbar-group">
        <!-- Block format: Normal / H2 / H3 / Quote -->
        <select class="toolbar-select" @change="formatBlock($event.target.value); $event.target.value = ''" title="Kiểu văn bản">
          <option value="" disabled selected>Kiểu chữ...</option>
          <option value="p">Đoạn văn (Thường)</option>
          <option value="h2">Tiêu đề lớn (H2)</option>
          <option value="h3">Tiêu đề phụ (H3)</option>
          <option value="blockquote">Khối trích dẫn</option>
        </select>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Text formatting: Bold, Italic, Underline, Strikethrough -->
      <div class="toolbar-group">
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('bold')"
          title="In đậm (Ctrl+B)"
        >
          <i class="bx bx-bold"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('italic')"
          title="In nghiêng (Ctrl+I)"
        >
          <i class="bx bx-italic"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('underline')"
          title="Gạch chân (Ctrl+U)"
        >
          <i class="bx bx-underline"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('strikeThrough')"
          title="Gạch ngang chữ"
        >
          <i class="bx bx-strikethrough"></i>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Lists -->
      <div class="toolbar-group">
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('insertUnorderedList')"
          title="Danh sách gạch đầu dòng"
        >
          <i class="bx bx-list-ul"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('insertOrderedList')"
          title="Danh sách đánh số"
        >
          <i class="bx bx-list-ol"></i>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Alignments -->
      <div class="toolbar-group">
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('justifyLeft')"
          title="Căn lề trái"
        >
          <i class="bx bx-align-left"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('justifyCenter')"
          title="Căn lề giữa"
        >
          <i class="bx bx-align-middle"></i>
        </button>
        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('justifyRight')"
          title="Căn lề phải"
        >
          <i class="bx bx-align-right"></i>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Insert: Link & Image -->
      <div class="toolbar-group">
        <button
          type="button"
          class="toolbar-btn"
          @click="insertLink"
          title="Chèn liên kết đường dẫn"
        >
          <i class="bx bx-link"></i>
        </button>

        <button
          type="button"
          class="toolbar-btn btn-insert-image"
          :disabled="isUploadingImage"
          @click="triggerImageUpload"
          title="Chèn hình ảnh từ máy tính vào bài viết"
        >
          <i class="bx" :class="isUploadingImage ? 'bx-loader-alt bx-spin' : 'bx-image-add'"></i>
          <span class="btn-img-text">{{ isUploadingImage ? 'Đang tải...' : 'Chèn ảnh' }}</span>
        </button>
        <input
          type="file"
          ref="imageInputRef"
          accept="image/*"
          style="display: none;"
          @change="handleImageSelected"
        />

        <button
          type="button"
          class="toolbar-btn"
          @click="execCmd('insertHorizontalRule')"
          title="Đường phân cách ngang"
        >
          <i class="bx bx-minus"></i>
        </button>
      </div>

      <!-- Mode Switch: Visual vs Code -->
      <div class="toolbar-group toolbar-group-end ms-auto">
        <button
          type="button"
          class="toolbar-btn btn-mode-toggle"
          :class="{ active: isCodeMode }"
          @click="toggleMode"
          :title="isCodeMode ? 'Quay lại soạn thảo trực quan' : 'Xem và sửa mã HTML'"
        >
          <i class="bx" :class="isCodeMode ? 'bx-edit' : 'bx-code-alt'"></i>
          <span>{{ isCodeMode ? 'Trực quan' : 'Mã HTML' }}</span>
        </button>
      </div>
    </div>

    <!-- Visual Editor Area (WYSIWYG) -->
    <div
      v-show="!isCodeMode"
      ref="editorRef"
      class="rich-editor-content"
      contenteditable="true"
      :data-placeholder="placeholder || 'Nhập nội dung bài viết tại đây... (Nhấn Enter để xuống đoạn văn mới)'"
      @input="handleInput"
      @blur="handleBlur"
      @paste="handlePaste"
    ></div>

    <!-- Code Editor Area (Raw HTML mode) -->
    <textarea
      v-show="isCodeMode"
      ref="codeEditorRef"
      v-model="rawHtml"
      class="rich-editor-code"
      rows="14"
      @input="handleCodeInput"
      placeholder="Nhập mã HTML bài viết tại đây..."
    ></textarea>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import { notifyError, notifyWarning } from '../composables/useFeedback';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const editorRef = ref(null);
const codeEditorRef = ref(null);
const imageInputRef = ref(null);
const isCodeMode = ref(false);
const rawHtml = ref('');
const isUploadingImage = ref(false);
let isInternalChange = false;

// Sync from outside props
const updateEditorContent = (content) => {
  if (editorRef.value && editorRef.value.innerHTML !== content) {
    editorRef.value.innerHTML = content || '';
  }
  rawHtml.value = content || '';
};

watch(
  () => props.modelValue,
  (newVal) => {
    if (!isInternalChange) {
      updateEditorContent(newVal);
    }
    isInternalChange = false;
  }
);

onMounted(() => {
  updateEditorContent(props.modelValue);
});

// Formatting commands
const execCmd = (command, value = null) => {
  if (isCodeMode.value) return;
  if (editorRef.value) {
    editorRef.value.focus();
  }
  document.execCommand(command, false, value);
  triggerChange();
};

const formatBlock = (tag) => {
  if (!tag || isCodeMode.value) return;
  if (editorRef.value) {
    editorRef.value.focus();
  }
  document.execCommand('formatBlock', false, tag);
  triggerChange();
};

const insertLink = () => {
  if (isCodeMode.value) return;
  const url = prompt('Nhập đường dẫn liên kết (URL):', 'https://');
  if (url && url.trim()) {
    execCmd('createLink', url.trim());
  }
};

const triggerImageUpload = () => {
  if (isCodeMode.value) return;
  if (imageInputRef.value) {
    imageInputRef.value.click();
  }
};

const handleImageSelected = async (e) => {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  if (file.size > 50 * 1024 * 1024) {
    notifyWarning('Dung lượng ảnh không được vượt quá 50MB!');
    return;
  }

  isUploadingImage.value = true;
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
    const uploadedUrl = data.url || (data.path ? '/' + data.path.replace(/^\/+/, '') : null);

    if (uploadedUrl) {
      if (editorRef.value) {
        editorRef.value.focus();
      }
      // Insert image with standard article image styling
      const imageHtml = `
        <div class="article-image-container my-4" style="margin: 28px 0; border-radius: 8px; overflow: hidden;">
          <img src="${uploadedUrl}" alt="Hình ảnh bài viết" class="article-fluid-img img-fluid w-100" style="width: 100%; border-radius: 8px; display: block;" />
        </div>
        <p><br></p>
      `;
      document.execCommand('insertHTML', false, imageHtml);
      triggerChange();
    } else {
      notifyError('Không thể tải ảnh lên máy chủ, vui lòng thử lại!');
    }
  } catch (err) {
    console.error('Lỗi tải ảnh:', err);
    notifyError('Lỗi tải ảnh lên máy chủ!');
  } finally {
    isUploadingImage.value = false;
    e.target.value = '';
  }
};

const triggerChange = () => {
  if (!editorRef.value) return;
  isInternalChange = true;
  const html = editorRef.value.innerHTML;
  rawHtml.value = html;
  emit('update:modelValue', html);
};

const handleInput = () => {
  triggerChange();
};

const handleBlur = () => {
  triggerChange();
};

const handleCodeInput = () => {
  isInternalChange = true;
  if (editorRef.value) {
    editorRef.value.innerHTML = rawHtml.value;
  }
  emit('update:modelValue', rawHtml.value);
};

const toggleMode = () => {
  if (isCodeMode.value) {
    // Switch from code to visual
    if (editorRef.value) {
      editorRef.value.innerHTML = rawHtml.value;
    }
    isCodeMode.value = false;
    nextTick(() => {
      if (editorRef.value) {
        editorRef.value.focus();
      }
    });
  } else {
    // Switch from visual to code
    if (editorRef.value) {
      rawHtml.value = editorRef.value.innerHTML;
    }
    isCodeMode.value = true;
    nextTick(() => {
      if (codeEditorRef.value) {
        codeEditorRef.value.focus();
      }
    });
  }
};

// Clean paste formatting
const handlePaste = (e) => {
  // Let default paste happen, then trigger change
  setTimeout(() => {
    triggerChange();
  }, 10);
};
</script>

<style scoped>
.rich-editor-wrapper {
  display: flex;
  flex-direction: column;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  background: #ffffff;
  overflow: hidden;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.rich-editor-wrapper:focus-within {
  border-color: #0f172a;
  box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.06);
}

/* Toolbar */
.rich-editor-toolbar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 4px;
  padding: 6px 10px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.toolbar-group {
  display: flex;
  align-items: center;
  gap: 2px;
}

.toolbar-divider {
  width: 1px;
  height: 20px;
  background: #e2e8f0;
  margin: 0 4px;
}

.toolbar-select {
  height: 28px;
  padding: 0 8px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
  font-size: 12px;
  font-weight: 500;
  color: #334155;
  outline: none;
  cursor: pointer;
}

.toolbar-select:focus {
  border-color: #0f172a;
}

.toolbar-btn {
  width: 28px;
  height: 28px;
  border: 1px solid transparent;
  background: transparent;
  border-radius: 5px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
}

.toolbar-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.toolbar-btn:active {
  background: #cbd5e1;
}

.btn-insert-image {
  width: auto;
  padding: 0 8px;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  color: #2563eb;
  background: #eff6ff;
  border-color: #bfdbfe;
}

.btn-insert-image:hover {
  background: #dbeafe;
  color: #1d4ed8;
  border-color: #93c5fd;
}

.btn-insert-image:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-mode-toggle {
  width: auto;
  padding: 0 8px;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}

.btn-mode-toggle:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.btn-mode-toggle.active {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
}

/* Editor Body */
.rich-editor-content {
  min-height: 260px;
  max-height: 480px;
  overflow-y: auto;
  padding: 16px 20px;
  font-family: inherit;
  font-size: 14.5px;
  line-height: 1.7;
  color: #1e293b;
  outline: none;
}

.rich-editor-content:empty::before {
  content: attr(data-placeholder);
  color: #94a3b8;
  pointer-events: none;
}

/* Typography styles inside editor */
.rich-editor-content p {
  margin: 0 0 16px 0;
}

.rich-editor-content h2 {
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  margin: 28px 0 12px 0;
  line-height: 1.35;
}

.rich-editor-content h3 {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 24px 0 10px 0;
  line-height: 1.4;
}

.rich-editor-content blockquote {
  border-left: 3px solid #0f172a;
  padding-left: 14px;
  margin: 16px 0;
  color: #475569;
  font-style: italic;
  background: #f8fafc;
  padding: 10px 14px;
  border-radius: 0 6px 6px 0;
}

.rich-editor-content ul,
.rich-editor-content ol {
  padding-left: 24px;
  margin: 0 0 16px 0;
}

.rich-editor-content li {
  margin-bottom: 6px;
}

.rich-editor-content img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  display: block;
}

.rich-editor-content hr {
  border: none;
  border-top: 1px solid #e2e8f0;
  margin: 24px 0;
}

.rich-editor-content a {
  color: #2563eb;
  text-decoration: underline;
}

/* Raw HTML textarea */
.rich-editor-code {
  width: 100%;
  min-height: 260px;
  max-height: 480px;
  padding: 14px 18px;
  font-family: Consolas, Monaco, "Courier New", Courier, monospace;
  font-size: 13px;
  line-height: 1.5;
  color: #0f172a;
  background: #f8fafc;
  border: none;
  outline: none;
  resize: vertical;
  box-sizing: border-box;
}
</style>

