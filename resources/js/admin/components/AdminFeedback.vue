<template>
  <!-- Confirm Dialog Modal -->
  <Transition name="feedback-fade">
    <div
      v-if="confirmState.open"
      class="admin-confirm-overlay"
      @click.self="resolveConfirm(false)"
    >
      <div class="admin-confirm-dialog" role="dialog" aria-modal="true">
        <div class="admin-confirm-body">
          <div class="admin-confirm-icon" :class="`variant-${confirmState.variant}`">
            <i
              class="bx"
              :class="
                confirmState.variant === 'danger'
                  ? 'bx-trash'
                  : confirmState.variant === 'warning'
                    ? 'bx-error'
                    : 'bx-help-circle'
              "
            ></i>
          </div>
          <div class="admin-confirm-text">
            <h3 class="admin-confirm-title">{{ confirmState.title }}</h3>
            <p class="admin-confirm-message">{{ confirmState.message }}</p>
          </div>
        </div>
        <div class="admin-confirm-footer">
          <button
            type="button"
            class="admin-confirm-btn admin-confirm-btn-cancel"
            @click="resolveConfirm(false)"
          >
            {{ confirmState.cancelText }}
          </button>
          <button
            type="button"
            class="admin-confirm-btn"
            :class="`admin-confirm-btn-${confirmState.variant}`"
            @click="resolveConfirm(true)"
          >
            {{ confirmState.confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Transition>

  <!-- Bottom-Right Toast Stack -->
  <div class="admin-toast-container" aria-live="polite" aria-atomic="true">
    <TransitionGroup name="toast-slide">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="admin-toast-item"
        :class="`toast-${toast.type}`"
        role="status"
      >
        <div class="admin-toast-icon">
          <i
            class="bx"
            :class="{
              'bxs-check-circle': toast.type === 'success',
              'bxs-x-circle': toast.type === 'error',
              'bxs-error': toast.type === 'warning',
              'bxs-info-circle': toast.type === 'info',
            }"
          ></i>
        </div>
        <div class="admin-toast-content">
          <div v-if="toast.title" class="admin-toast-title">{{ toast.title }}</div>
          <div class="admin-toast-msg">{{ toast.message }}</div>
        </div>
        <button
          type="button"
          class="admin-toast-close"
          aria-label="Đóng thông báo"
          @click="dismissToast(toast.id)"
        >
          <i class="bx bx-x"></i>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import {
  toasts,
  dismissToast,
  confirmState,
  resolveConfirm,
} from '../composables/useFeedback';

const handleKeydown = (e) => {
  if (e.key === 'Escape' && confirmState.open) {
    resolveConfirm(false);
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
/* Confirm Dialog */
.admin-confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 99998;
  background: rgba(15, 23, 42, 0.48);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.admin-confirm-dialog {
  width: 100%;
  max-width: 440px;
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.25);
  overflow: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.admin-confirm-body {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 22px 22px 18px;
}

.admin-confirm-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.admin-confirm-icon.variant-danger {
  background: #fef2f2;
  color: #dc2626;
}

.admin-confirm-icon.variant-warning {
  background: #fef3c7;
  color: #d97706;
}

.admin-confirm-icon.variant-primary {
  background: #f1f5f9;
  color: #0f172a;
}

.admin-confirm-text {
  flex: 1;
  min-width: 0;
}

.admin-confirm-title {
  margin: 0 0 6px;
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.35;
}

.admin-confirm-message {
  margin: 0;
  font-size: 13.5px;
  color: #475569;
  line-height: 1.55;
  white-space: pre-line;
  word-break: break-word;
}

.admin-confirm-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 22px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.admin-confirm-btn {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all 0.15s ease;
  font-family: inherit;
}

.admin-confirm-btn-cancel {
  background: #ffffff;
  border-color: #cbd5e1;
  color: #334155;
}

.admin-confirm-btn-cancel:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
}

.admin-confirm-btn-danger {
  background: #dc2626;
  color: #ffffff;
}

.admin-confirm-btn-danger:hover {
  background: #b91c1c;
}

.admin-confirm-btn-warning {
  background: #d97706;
  color: #ffffff;
}

.admin-confirm-btn-warning:hover {
  background: #b45309;
}

.admin-confirm-btn-primary {
  background: #0f172a;
  color: #ffffff;
}

.admin-confirm-btn-primary:hover {
  background: #1e293b;
}

/* Bottom-Right Toasts */
.admin-toast-container {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 99999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 380px;
  width: calc(100vw - 40px);
  pointer-events: none;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.admin-toast-item {
  pointer-events: auto;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 13px 14px;
  border-radius: 12px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.18), 0 4px 10px -2px rgba(15, 23, 42, 0.06);
  border-left-width: 4px;
}

.admin-toast-item.toast-success {
  border-left-color: #16a34a;
}

.admin-toast-item.toast-error {
  border-left-color: #dc2626;
}

.admin-toast-item.toast-warning {
  border-left-color: #d97706;
}

.admin-toast-item.toast-info {
  border-left-color: #0f172a;
}

.admin-toast-icon {
  font-size: 20px;
  line-height: 1;
  margin-top: 1px;
  flex-shrink: 0;
}

.toast-success .admin-toast-icon {
  color: #16a34a;
}

.toast-error .admin-toast-icon {
  color: #dc2626;
}

.toast-warning .admin-toast-icon {
  color: #d97706;
}

.toast-info .admin-toast-icon {
  color: #0f172a;
}

.admin-toast-content {
  flex: 1;
  min-width: 0;
}

.admin-toast-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.3;
  margin-bottom: 2px;
}

.admin-toast-msg {
  font-size: 12.5px;
  color: #475569;
  line-height: 1.45;
  word-break: break-word;
}

.admin-toast-close {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 2px;
  font-size: 18px;
  line-height: 1;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.15s, color 0.15s;
}

.admin-toast-close:hover {
  background: #f1f5f9;
  color: #0f172a;
}

/* Transitions */
.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(24px);
}

.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(24px);
}

.feedback-fade-enter-active,
.feedback-fade-leave-active {
  transition: opacity 0.18s ease;
}

.feedback-fade-enter-from,
.feedback-fade-leave-to {
  opacity: 0;
}
</style>

