<template>
  <section class="studio-preview" ref="previewAreaRef">
    <div
      class="preview-frame-container"
      :class="[currentDevice, { 'desktop-scaled': currentDevice === 'desktop' }]"
      :style="frameContainerStyle"
    >
      <iframe
        ref="iframeEl"
        :src="iframeSrc"
        class="live-iframe"
        @load="onIframeLoaded"
      ></iframe>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import {
  previewAreaRef as sharedPreviewArea,
  previewIframe,
  currentDevice,
  frameContainerStyle,
  selectedPageSlug,
  currentLanguage,
} from '../composables/useAdmin';

const iframeEl = ref(null);
const previewAreaRef = ref(null);

const iframeSrc = computed(() => {
  const slug = selectedPageSlug.value || '/';
  const lang = currentLanguage.value || 'vi';
  return slug + (slug.includes('?') ? '&' : '?') + `preview=draft&lang=${lang}`;
});

onMounted(() => {
  if (iframeEl.value) {
    previewIframe.value = iframeEl.value;
  }
  if (previewAreaRef.value) {
    sharedPreviewArea.value = previewAreaRef.value;
  }
});

watch(iframeEl, (newEl) => {
  if (newEl) {
    previewIframe.value = newEl;
  }
});

const onIframeLoaded = () => {
  if (iframeEl.value) {
    previewIframe.value = iframeEl.value;
  }
};
</script>

<style src="../css/preview.css"></style>
