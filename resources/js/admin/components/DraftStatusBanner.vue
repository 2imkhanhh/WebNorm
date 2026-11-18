<template>
  <!-- 1. Staging Alert Banner when changes are pending in editor -->
  <div v-if="hasUnsavedChanges" class="staging-alert-banner">
    <div class="banner-info">
      <i class="bx bx-info-circle banner-icon"></i>
      <span>Có thay đổi chưa lưu</span>
    </div>
  </div>

  <!-- 2. Draft Status Banner when a draft exists in database and is unpublished -->
  <div v-else-if="hasDraft" class="draft-active-banner">
    <div class="banner-info">
      <i class="bx bx-file-blank banner-icon-draft"></i>
      <span>Có bản nháp chưa xuất bản</span>
    </div>
    <div class="banner-buttons">
      <a :href="resolvedPreviewUrl" target="_blank" class="btn-banner-view">
        <i class="bx bx-show"></i> Xem bản nháp
      </a>
      <button
        type="button"
        class="btn-banner-discard"
        :disabled="isDiscardingDraft || isSaving || isSavingDraft"
        @click="$emit('discard')"
      >
        <i class="bx bx-trash"></i>
        {{ isDiscardingDraft ? 'Đang xóa...' : 'Xóa bản nháp' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  currentLanguage,
  hasUnsavedChanges,
  isSaving,
  isSavingDraft,
  isDiscardingDraft,
} from '../composables/useAdmin';

const props = defineProps({
  hasDraft: {
    type: Boolean,
    default: false,
  },
  previewPath: {
    type: String,
    default: '/',
  },
});

defineEmits(['discard']);

const resolvedPreviewUrl = computed(() => {
  const slug = props.previewPath || '/';
  const lang = currentLanguage.value || 'vi';
  const targetSlug = lang === 'en' ? (slug === '/' ? '/en' : '/en' + slug) : slug;
  return targetSlug + (targetSlug.includes('?') ? '&' : '?') + 'preview=draft';
});
</script>

