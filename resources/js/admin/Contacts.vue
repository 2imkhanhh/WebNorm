<template>
  <Layout>
    <div class="contacts-workspace-view">
      <div class="contacts-container">
        <div class="contacts-header-card">
          <div>
            <h2 class="contacts-title">Liên hệ</h2>
            <p class="contacts-subtitle">Các yêu cầu gửi từ form liên hệ.</p>
          </div>
          <div class="contacts-count">
            Tổng: {{ contacts.length }}
          </div>
        </div>

        <div class="contacts-table-card" v-if="contacts && contacts.length > 0">
          <div class="contacts-table-scroll">
            <table class="contacts-table">
              <thead>
                <tr>
                  <th style="width: 12%;">Khách hàng</th>
                  <th style="width: 15%;">Email</th>
                  <th style="width: 10%;">SĐT</th>
                  <th style="width: 15%;">Dịch vụ</th>
                  <th style="width: 10%;">Ngân sách</th>
                  <th style="width: 18%;">Nội dung</th>
                  <th style="width: 10%;">Thời gian</th>
                  <th style="width: 10%;">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="c in contacts" :key="c.id">
                  <td>
                    <div class="lead-name">{{ c.name }}</div>
                  </td>

                  <td>
                    <a v-if="c.email" :href="'mailto:' + c.email" class="lead-link">{{ c.email }}</a>
                    <span v-else class="lead-muted">-</span>
                  </td>

                  <td class="lead-phone-cell">
                    <a v-if="c.phone" :href="'tel:' + c.phone" class="lead-link">{{ c.phone }}</a>
                    <span v-else class="lead-muted">-</span>
                  </td>

                  <td>
                    <div v-if="formatServices(c.services)" class="lead-services-text">
                      {{ formatServices(c.services) }}
                    </div>
                    <span v-else class="lead-muted">-</span>
                  </td>

                  <td>
                    <div v-if="c.budget" class="lead-budget-text">
                      {{ c.budget }}
                    </div>
                    <span v-else class="lead-muted">-</span>
                  </td>

                  <td>
                    <p v-if="c.message" class="lead-message-text">{{ c.message }}</p>
                    <span v-else class="lead-muted">-</span>
                  </td>

                  <td class="lead-date-cell">
                    {{ formatDate(c.created_at) }}
                  </td>

                  <td>
                    <div class="lead-actions-cell">
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
                      <button type="button" class="btn-lead-delete" title="Xóa liên hệ" @click="confirmDelete(c)">
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="empty-state contacts-table-card">
          <i class="bx bx-envelope-open empty-icon"></i>
          <p>Chưa có yêu cầu liên hệ nào.</p>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import Layout from './Layout.vue';
import { askConfirm, notifySuccess } from './composables/useFeedback';

defineProps({
  contacts: {
    type: Array,
    default: () => [],
  },
});

const formatServices = (services) => {
  if (!services) return '';
  if (Array.isArray(services)) return services.filter(Boolean).join(', ');
  return String(services);
};

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

const confirmDelete = async (contact) => {
  const ok = await askConfirm({
    title: 'Xóa liên hệ',
    message: `Bạn có chắc chắn muốn xóa liên hệ từ "${contact.name}" không?`,
    confirmText: 'Xóa',
    variant: 'danger',
  });
  if (ok) {
    router.delete(`/admin/leads/${contact.id}`, {
      preserveScroll: true,
      onSuccess: () => notifySuccess('Đã xóa thông tin liên hệ.'),
    });
  }
};
</script>

<style src="./css/panels.css"></style>
