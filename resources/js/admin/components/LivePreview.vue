<template>
  <section class="studio-preview" ref="previewAreaRef">
    <div
      class="preview-frame-container"
      :class="[currentDevice, { 'desktop-scaled': currentDevice === 'desktop' }]"
      :style="frameContainerStyle"
    >
      <iframe
        ref="previewIframe"
        :src="selectedPageSlug"
        class="live-iframe"
        @load="onIframeLoaded"
      ></iframe>
    </div>
  </section>
</template>

<script setup>
import {
  previewAreaRef,
  previewIframe,
  currentDevice,
  frameContainerStyle,
  selectedPageSlug,
} from '../composables/useAdmin';

const onIframeLoaded = () => {
  // Hook xử lý khi iframe load hoàn tất (nếu cần bắt tín hiệu)
};
</script>

<style src="../css/preview.css"></style>
