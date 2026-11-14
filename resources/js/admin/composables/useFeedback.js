import { reactive, ref } from 'vue';

export const toasts = ref([]);

const toastTimers = new Map();
let toastCounter = 0;

export const dismissToast = (id) => {
  const timer = toastTimers.get(id);
  if (timer) {
    clearTimeout(timer);
    toastTimers.delete(id);
  }
  toasts.value = toasts.value.filter((t) => t.id !== id);
};

export const notify = (messageOrOpts, type = 'info', duration = null) => {
  const opts =
    typeof messageOrOpts === 'object' && messageOrOpts !== null
      ? messageOrOpts
      : { message: String(messageOrOpts ?? ''), type, duration };

  const toastType = opts.type || 'info';
  const toastDuration =
    opts.duration ?? (toastType === 'error' ? 5000 : toastType === 'warning' ? 4200 : 3500);

  const id = ++toastCounter;
  const defaultTitles = {
    success: 'Thành công',
    error: 'Có lỗi xảy ra',
    warning: 'Lưu ý',
    info: 'Thông báo',
  };

  const item = {
    id,
    type: toastType,
    title: opts.title ?? defaultTitles[toastType] ?? 'Thông báo',
    message: opts.message || '',
  };

  toasts.value.push(item);
  if (toasts.value.length > 5) {
    const oldest = toasts.value[0];
    if (oldest) dismissToast(oldest.id);
  }

  if (toastDuration > 0) {
    const timer = setTimeout(() => {
      dismissToast(id);
    }, toastDuration);
    toastTimers.set(id, timer);
  }

  return id;
};

export const notifySuccess = (message, title = 'Thành công') =>
  notify({ type: 'success', title, message });

export const notifyError = (message, title = 'Có lỗi xảy ra') =>
  notify({ type: 'error', title, message });

export const notifyWarning = (message, title = 'Cảnh báo') =>
  notify({ type: 'warning', title, message });

export const notifyInfo = (message, title = 'Thông báo') =>
  notify({ type: 'info', title, message });

export const extractErrorMessage = (err, fallback = 'Không thể thực hiện thao tác.') => {
  if (!err) return fallback;
  if (typeof err === 'string') return err;
  if (err.message && typeof err.message === 'string') return err.message;
  if (typeof err === 'object') {
    const values = Object.values(err).flat();
    const firstMsg = values.find((v) => typeof v === 'string' && v.trim() !== '');
    if (firstMsg) return firstMsg;
  }
  return fallback;
};

export const confirmState = reactive({
  open: false,
  title: 'Xác nhận thao tác',
  message: '',
  confirmText: 'Xác nhận',
  cancelText: 'Hủy',
  variant: 'danger', // 'danger' | 'warning' | 'primary'
});

let pendingConfirmResolver = null;

export const resolveConfirm = (result) => {
  confirmState.open = false;
  if (pendingConfirmResolver) {
    const resolver = pendingConfirmResolver;
    pendingConfirmResolver = null;
    resolver(!!result);
  }
};

export const askConfirm = (optionsOrMessage) => {
  if (pendingConfirmResolver) {
    pendingConfirmResolver(false);
    pendingConfirmResolver = null;
  }

  const opts =
    typeof optionsOrMessage === 'string'
      ? { message: optionsOrMessage }
      : optionsOrMessage || {};

  confirmState.title = opts.title || 'Xác nhận thao tác';
  confirmState.message = opts.message || '';
  confirmState.confirmText = opts.confirmText || 'Xác nhận';
  confirmState.cancelText = opts.cancelText || 'Hủy';
  confirmState.variant = opts.variant || 'danger';
  confirmState.open = true;

  return new Promise((resolve) => {
    pendingConfirmResolver = resolve;
  });
};

