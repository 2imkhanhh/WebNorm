<template>
  <Layout>
    <div class="sidebar-header">
      <div class="d-flex justify-content-between align-items-center w-100">
        <div>
          <h2 class="sidebar-title">Khách hàng liên hệ (Leads)</h2>
          <p class="sidebar-subtitle">Các yêu cầu gửi từ Form liên hệ trên website.</p>
        </div>
        <div class="badge-total-leads">
          Tổng: {{ contacts.length }}
        </div>
      </div>
    </div>

    <div class="sidebar-panel-content">
      <div class="lead-card-item" v-for="c in contacts" :key="c.id">
        <div class="lead-card-header d-flex justify-content-between align-items-center">
          <div class="lead-customer-info">
            <h4 class="lead-name">{{ c.name }}</h4>
            <div class="lead-contact-links">
              <a :href="'mailto:' + c.email" class="lead-link" title="Gửi email">
                <i class="bx bx-envelope"></i> {{ c.email }}
              </a>
              <a v-if="c.phone" :href="'tel:' + c.phone" class="lead-link" title="Gọi điện">
                <i class="bx bx-phone"></i> {{ c.phone }}
              </a>
            </div>
          </div>
          <div class="lead-status-controls">
            <select
              :value="c.status || 'new'"
              class="lead-status-select"
              :class="c.status || 'new'"
              @change="updateLeadStatus(c, $event.target.value)"
            >
              <option value="new">Mới</option>
              <option value="contacted">Đã liên hệ</option>
              <option value="completed">Hoàn thành</option>
            </select>
            <button class="btn-action delete ms-2" title="Xóa liên hệ" @click="confirmDelete(c)">
              <i class="bx bx-trash"></i>
            </button>
          </div>
        </div>

        <div class="lead-meta-row mt-2" v-if="c.services || c.budget">
          <div class="lead-tags" v-if="c.services">
            <span class="lead-tag" v-for="(svc, sIdx) in (Array.isArray(c.services) ? c.services : [c.services])" :key="sIdx">
              {{ svc }}
            </span>
          </div>
          <span class="lead-budget-badge" v-if="c.budget">
            Ngân sách: {{ c.budget }}
          </span>
        </div>

        <div class="lead-message-box mt-2" v-if="c.message">
          <p class="lead-message-text">{{ c.message }}</p>
        </div>

        <div class="lead-card-footer mt-2 d-flex justify-content-between align-items-center">
          <span class="lead-date-text">{{ formatDate(c.created_at) }}</span>
        </div>
      </div>

      <div v-if="!contacts || contacts.length === 0" class="empty-state">
        <i class="bx bx-envelope-open empty-icon"></i>
        <p>Chưa có yêu cầu liên hệ nào.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';

defineProps({
  contacts: {
    type: Array,
    default: () => [],
  },
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleString('vi-VN');
};

const updateLeadStatus = (contact, newStatus) => {
  router.patch(
    `/admin/leads/${contact.id}`,
    { status: newStatus },
    {
      preserveScroll: true,
    }
  );
};

const confirmDelete = (contact) => {
  if (confirm(`Bạn có chắc chắn muốn xóa liên hệ từ "${contact.name}" không?`)) {
    router.delete(`/admin/leads/${contact.id}`, {
      preserveScroll: true,
    });
  }
};
</script>

<style src="./css/panels.css"></style>
