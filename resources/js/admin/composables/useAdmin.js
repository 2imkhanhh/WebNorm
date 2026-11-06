import { ref, computed, nextTick } from 'vue';

// 1. Device preview state
export const devices = [
  { id: 'desktop', label: 'Desktop', icon: 'bx bx-desktop' },
  { id: 'tablet', label: 'Tablet', icon: 'bx bx-tab' },
  { id: 'mobile', label: 'Mobile', icon: 'bx bx-mobile-alt' },
];
export const currentDevice = ref('desktop');

export const deviceSpecs = {
  desktop: { width: 1440, height: 780 },
  tablet: { width: 768, height: 1024 },
  mobile: { width: 375, height: 812 },
};

// 2. Page selection & Iframe preview reference
export const selectedPageSlug = ref('/');
export const currentLanguage = ref('vi');
export const previewIframe = ref(null);
export const previewAreaRef = ref(null);

// 3. Save & Changes state
export const hasUnsavedChanges = ref(false);
export const isSaving = ref(false);
export const isSavingDraft = ref(false);

export const saveDraft = () => {
  isSavingDraft.value = true;
  setTimeout(() => {
    isSavingDraft.value = false;
    hasUnsavedChanges.value = false;
    alert('Đã lưu bản nháp thành công!');
  }, 500);
};

export const saveChanges = () => {
  isSaving.value = true;
  setTimeout(() => {
    isSaving.value = false;
    hasUnsavedChanges.value = false;
    alert('Đã xuất bản cấu hình thành công!');
  }, 600);
};

// 4. Scaling & Viewport calculation
export const zoomLevel = ref(0.85);
export const isAutoFit = ref(true);

export const calculateAutoFit = () => {
  if (!previewAreaRef.value) return;
  const spec = deviceSpecs[currentDevice.value] || deviceSpecs.desktop;
  const availableWidth = previewAreaRef.value.clientWidth - 48;
  const availableHeight = previewAreaRef.value.clientHeight - 48;
  const scaleX = availableWidth / spec.width;
  const scaleY = availableHeight / spec.height;
  const fitScale = Math.min(1, Math.max(0.4, Math.min(scaleX, scaleY)));
  zoomLevel.value = Math.round(fitScale * 100) / 100;
};

export const setDevice = (device) => {
  currentDevice.value = device;
  isAutoFit.value = true;
  nextTick(() => {
    calculateAutoFit();
  });
};

export const frameContainerStyle = computed(() => {
  const spec = deviceSpecs[currentDevice.value] || deviceSpecs.desktop;
  const scale = zoomLevel.value;
  const devWidth = spec.width;
  const devHeight = spec.height;
  const diffH = devHeight * (1 - scale);
  const diffW = devWidth * (1 - scale);
  return {
    width: `${devWidth}px`,
    height: `${devHeight}px`,
    transform: `scale(${scale})`,
    transformOrigin: 'center center',
    marginTop: `-${diffH / 2}px`,
    marginBottom: `-${diffH / 2}px`,
    marginLeft: `-${diffW / 2}px`,
    marginRight: `-${diffW / 2}px`,
  };
});
