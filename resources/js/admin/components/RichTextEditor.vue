<template>
  <div class="rich-editor-wrapper">
    <!-- Toolbar -->
    <div class="rich-editor-toolbar">
      <div class="toolbar-group">
        <!-- Block format: Normal / H2 / H3 / Quote -->
        <select class="toolbar-select" @mousedown="saveSelection"
          @change="formatBlock($event.target.value); $event.target.value = ''" title="Kiểu văn bản">
          <option value="" disabled selected>Kiểu chữ...</option>
          <option value="p">Đoạn văn (Thường)</option>
          <option value="h2">Tiêu đề lớn (H2)</option>
          <option value="h3">Tiêu đề phụ (H3)</option>
          <option value="blockquote">Khối trích dẫn</option>
        </select>

        <!-- Font Size -->
        <select class="toolbar-select toolbar-select-size" @mousedown="saveSelection"
          @change="applyFontSize($event.target.value); $event.target.value = ''" title="Kích thước chữ">
          <option value="" disabled selected>Cỡ chữ...</option>
          <option value="default">Mặc định</option>
          <option value="12">12px</option>
          <option value="14">14px</option>
          <option value="16">16px</option>
          <option value="18">18px</option>
          <option value="20">20px</option>
          <option value="24">24px</option>
          <option value="28">28px</option>
          <option value="32">32px</option>
          <option value="36">36px</option>
        </select>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Text formatting: Bold, Italic, Underline, Strikethrough + Color -->
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" @click="execCmd('bold')" title="In đậm (Ctrl+B)">
          <i class="bx bx-bold"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('italic')" title="In nghiêng (Ctrl+I)">
          <i class="bx bx-italic"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('underline')" title="Gạch chân (Ctrl+U)">
          <i class="bx bx-underline"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('strikeThrough')" title="Gạch ngang chữ">
          <i class="bx bx-strikethrough"></i>
        </button>

        <!-- Text Color Picker -->
        <div ref="colorMenuRef" class="toolbar-color-wrap">
          <button type="button" class="toolbar-btn toolbar-btn-color" :class="{ active: isColorMenuOpen }"
            @mousedown.prevent="toggleColorMenu" title="Màu chữ">
            <span class="color-btn-letter">A</span>
            <span class="color-btn-bar" :style="{ backgroundColor: activeColor }"></span>
          </button>

          <div v-if="isColorMenuOpen" class="color-picker-popover" @mousedown.stop>
            <div class="color-popover-header">
              <span>Màu chữ</span>
              <button type="button" class="btn-reset-color" @mousedown.prevent="applyTextColor('default')">
                Mặc định
              </button>
            </div>
            <div class="color-swatches-grid">
              <button v-for="c in presetColors" :key="c.value" type="button" class="color-swatch-btn"
                :class="{ active: activeColor.toLowerCase() === c.value.toLowerCase() }"
                :style="{ backgroundColor: c.value }" :title="c.label"
                @mousedown.prevent="selectPresetColor(c.value)"></button>
            </div>
            <label class="custom-color-row" @mousedown="saveSelection">
              <span class="custom-color-label">Chọn màu tùy chỉnh</span>
              <input type="color" class="custom-color-input" :value="activeColor"
                @input="applyTextColor($event.target.value)" @change="isColorMenuOpen = false" />
            </label>
          </div>
        </div>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Lists -->
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" @click="execCmd('insertUnorderedList')"
          title="Danh sách gạch đầu dòng">
          <i class="bx bx-list-ul"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('insertOrderedList')" title="Danh sách đánh số">
          <i class="bx bx-list-ol"></i>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Alignments -->
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" @click="execCmd('justifyLeft')" title="Căn lề trái">
          <i class="bx bx-align-left"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('justifyCenter')" title="Căn lề giữa">
          <i class="bx bx-align-middle"></i>
        </button>
        <button type="button" class="toolbar-btn" @click="execCmd('justifyRight')" title="Căn lề phải">
          <i class="bx bx-align-right"></i>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Insert: Link & Image -->
      <div class="toolbar-group">
        <button type="button" class="toolbar-btn" @click="insertLink" title="Chèn liên kết đường dẫn">
          <i class="bx bx-link"></i>
        </button>

        <button type="button" class="toolbar-btn btn-insert-image" :disabled="isUploadingImage"
          @click="triggerImageUpload" title="Chèn hình ảnh từ máy tính vào bài viết">
          <i class="bx" :class="isUploadingImage ? 'bx-loader-alt bx-spin' : 'bx-image-add'"></i>
          <span class="btn-img-text">{{ isUploadingImage ? 'Đang tải...' : 'Chèn ảnh' }}</span>
        </button>
        <input type="file" ref="imageInputRef" accept="image/*" style="display: none;" @change="handleImageSelected" />

        <button type="button" class="toolbar-btn" @click="execCmd('insertHorizontalRule')"
          title="Đường phân cách ngang">
          <i class="bx bx-minus"></i>
        </button>
      </div>

      <!-- Mode Switch: Visual vs Code -->
      <div class="toolbar-group toolbar-group-end ms-auto">
        <button type="button" class="toolbar-btn btn-mode-toggle" :class="{ active: isCodeMode }" @click="toggleMode"
          :title="isCodeMode ? 'Quay lại soạn thảo trực quan' : 'Xem và sửa mã HTML'">
          <i class="bx" :class="isCodeMode ? 'bx-edit' : 'bx-code-alt'"></i>
          <span>{{ isCodeMode ? 'Trực quan' : 'Mã HTML' }}</span>
        </button>
      </div>
    </div>

    <!-- Visual Editor Area (WYSIWYG) -->
    <div v-show="!isCodeMode" ref="editorRef" class="rich-editor-content" contenteditable="true"
      :data-placeholder="placeholder || 'Nhập nội dung bài viết tại đây'" @input="handleInput" @blur="handleBlur"
      @mouseup="saveSelection" @keyup="saveSelection" @paste="handlePaste"></div>

    <!-- Code Editor Area (Raw HTML mode) -->
    <textarea v-show="isCodeMode" ref="codeEditorRef" v-model="rawHtml" class="rich-editor-code" rows="14"
      @input="handleCodeInput" placeholder="Nhập mã HTML bài viết tại đây..."></textarea>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
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
const colorMenuRef = ref(null);
const isCodeMode = ref(false);
const rawHtml = ref('');
const isUploadingImage = ref(false);
const isColorMenuOpen = ref(false);
const activeColor = ref('#111111');
let isInternalChange = false;
let savedRange = null;

const presetColors = [
  { label: 'Đen', value: '#111111' },
  { label: 'Xám đậm', value: '#475569' },
  { label: 'Xám nhạt', value: '#94a3b8' },
  { label: 'Đỏ', value: '#dc2626' },
  { label: 'Cam', value: '#ea580c' },
  { label: 'Vàng đậm', value: '#ca8a04' },
  { label: 'Xanh lá', value: '#16a34a' },
  { label: 'Xanh dương', value: '#2563eb' },
  { label: 'Tím', value: '#7c3aed' },
  { label: 'Hồng', value: '#db2777' },
];

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

const closeColorMenuOutside = (e) => {
  if (isColorMenuOpen.value && colorMenuRef.value && !colorMenuRef.value.contains(e.target)) {
    isColorMenuOpen.value = false;
  }
};

onMounted(() => {
  updateEditorContent(props.modelValue);
  document.addEventListener('mousedown', closeColorMenuOutside);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', closeColorMenuOutside);
});

const saveSelection = () => {
  const sel = window.getSelection();
  if (!sel || sel.rangeCount === 0 || !editorRef.value) return;
  const range = sel.getRangeAt(0);
  if (editorRef.value.contains(range.commonAncestorContainer)) {
    savedRange = range.cloneRange();
  }
};

const restoreSelection = () => {
  if (!editorRef.value) return;
  editorRef.value.focus();
  if (savedRange) {
    const sel = window.getSelection();
    if (sel) {
      sel.removeAllRanges();
      sel.addRange(savedRange);
    }
  }
};

const cleanupEmptySpans = (root) => {
  if (!root) return;
  root.querySelectorAll('span').forEach((span) => {
    const styleAttr = span.getAttribute('style');
    if (styleAttr !== null && !styleAttr.trim()) {
      span.removeAttribute('style');
    }
    if (span.attributes.length === 0) {
      const fragment = document.createDocumentFragment();
      while (span.firstChild) {
        fragment.appendChild(span.firstChild);
      }
      span.parentNode?.replaceChild(fragment, span);
    }
  });
};

// Formatting commands
const execCmd = (command, value = null) => {
  if (isCodeMode.value) return;
  if (editorRef.value) {
    editorRef.value.focus();
  }
  document.execCommand(command, false, value);
  saveSelection();
  triggerChange();
};

const formatBlock = (tag) => {
  if (!tag || isCodeMode.value) return;
  restoreSelection();
  document.execCommand('formatBlock', false, tag);
  saveSelection();
  triggerChange();
};

const applyFontSize = (sizeVal) => {
  if (!sizeVal || isCodeMode.value || !editorRef.value) return;
  restoreSelection();

  const sel = window.getSelection();
  if (!sel || sel.rangeCount === 0) return;
  const range = sel.getRangeAt(0);

  if (sizeVal === 'default') {
    if (!range.collapsed) {
      document.execCommand('styleWithCSS', false, false);
      document.execCommand('fontSize', false, '7');
      editorRef.value.querySelectorAll('font[size="7"]').forEach((fontEl) => {
        fontEl.querySelectorAll('[style]').forEach((child) => {
          child.style.removeProperty('font-size');
        });
        const fragment = document.createDocumentFragment();
        while (fontEl.firstChild) {
          fragment.appendChild(fontEl.firstChild);
        }
        fontEl.parentNode.replaceChild(fragment, fontEl);
      });
      cleanupEmptySpans(editorRef.value);
      saveSelection();
      triggerChange();
    }
    return;
  }

  const pxValue = `${sizeVal}px`;
  if (range.collapsed) {
    const span = document.createElement('span');
    span.style.fontSize = pxValue;
    const zwsp = document.createTextNode('\u200B');
    span.appendChild(zwsp);
    range.insertNode(span);
    const newRange = document.createRange();
    newRange.setStart(zwsp, 1);
    newRange.collapse(true);
    sel.removeAllRanges();
    sel.addRange(newRange);
    savedRange = newRange.cloneRange();
    triggerChange();
    return;
  }

  document.execCommand('styleWithCSS', false, false);
  document.execCommand('fontSize', false, '7');
  const createdSpans = [];
  editorRef.value.querySelectorAll('font[size="7"]').forEach((fontEl) => {
    const span = document.createElement('span');
    span.style.fontSize = pxValue;
    while (fontEl.firstChild) {
      const child = fontEl.firstChild;
      if (child.nodeType === 1) {
        child.style.removeProperty('font-size');
        child.querySelectorAll?.('[style]').forEach((nested) => {
          nested.style.removeProperty('font-size');
        });
      }
      span.appendChild(child);
    }
    fontEl.parentNode.replaceChild(span, fontEl);
    createdSpans.push(span);
  });

  cleanupEmptySpans(editorRef.value);

  if (createdSpans.length > 0) {
    const newRange = document.createRange();
    newRange.setStartBefore(createdSpans[0]);
    newRange.setEndAfter(createdSpans[createdSpans.length - 1]);
    sel.removeAllRanges();
    sel.addRange(newRange);
    savedRange = newRange.cloneRange();
  }
  triggerChange();
};

const toggleColorMenu = () => {
  if (isCodeMode.value) return;
  saveSelection();
  isColorMenuOpen.value = !isColorMenuOpen.value;
};

const selectPresetColor = (color) => {
  applyTextColor(color);
  isColorMenuOpen.value = false;
};

const applyTextColor = (color) => {
  if (isCodeMode.value || !editorRef.value) return;
  restoreSelection();

  const sel = window.getSelection();
  if (!sel || sel.rangeCount === 0) return;

  if (color === 'default') {
    if (!sel.getRangeAt(0).collapsed) {
      document.execCommand('styleWithCSS', false, true);
      document.execCommand('foreColor', false, 'rgb(1, 2, 3)');
      editorRef.value.querySelectorAll('[style]').forEach((el) => {
        if (el.style.color === 'rgb(1, 2, 3)' || el.style.color === '#010203') {
          el.style.removeProperty('color');
        }
        el.querySelectorAll?.('[style]').forEach((nested) => {
          if (nested.style.color === 'rgb(1, 2, 3)' || nested.style.color === '#010203') {
            nested.style.removeProperty('color');
          }
        });
      });
      cleanupEmptySpans(editorRef.value);
    }
    activeColor.value = '#111111';
    isColorMenuOpen.value = false;
    saveSelection();
    triggerChange();
    return;
  }

  document.execCommand('styleWithCSS', false, true);
  document.execCommand('foreColor', false, color);
  activeColor.value = color;
  saveSelection();
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
  border-radius: 9px 9px 0 0;
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

.toolbar-select-size {
  min-width: 86px;
}

.toolbar-color-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.toolbar-btn-color {
  flex-direction: column;
  gap: 1px;
  padding: 2px 4px;
}

.toolbar-btn-color.active {
  background: #e2e8f0;
  color: #0f172a;
}

.color-btn-letter {
  font-size: 13px;
  font-weight: 700;
  line-height: 1;
}

.color-btn-bar {
  width: 15px;
  height: 3px;
  border-radius: 2px;
  border: 1px solid rgba(15, 23, 42, 0.15);
}

.color-picker-popover {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  z-index: 1200;
  width: 210px;
  padding: 10px 12px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.16), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
}

.color-popover-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 11.5px;
  font-weight: 700;
  color: #334155;
}

.btn-reset-color {
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #475569;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-reset-color:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.color-swatches-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 6px;
  margin-bottom: 10px;
}

.color-swatch-btn {
  width: 100%;
  height: 24px;
  border-radius: 5px;
  border: 1px solid rgba(15, 23, 42, 0.15);
  cursor: pointer;
  padding: 0;
  transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.color-swatch-btn:hover {
  transform: scale(1.08);
}

.color-swatch-btn.active {
  box-shadow: 0 0 0 2px #ffffff, 0 0 0 3.5px #0f172a;
}

.custom-color-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding-top: 8px;
  border-top: 1px solid #f1f5f9;
  cursor: pointer;
}

.custom-color-label {
  font-size: 11.5px;
  font-weight: 500;
  color: #475569;
}

.custom-color-input {
  width: 28px;
  height: 22px;
  padding: 0;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  background: transparent;
  cursor: pointer;
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
  border-radius: 0 0 9px 9px;
}
</style>
