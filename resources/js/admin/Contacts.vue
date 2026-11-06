<template>
  <Layout>
    <div class="sidebar-header">
      <h2 class="sidebar-title">Yêu cầu liên hệ / Báo giá</h2>
      <p class="sidebar-subtitle">Khách hàng gửi qua Contact Drawer.</p>
    </div>
    <div class="sidebar-panel-content">
      <div v-if="contacts.length === 0" class="panel-empty-state">
        <i class="bx bx-inbox"></i>
        <span>Chưa có liên hệ mới</span>
      </div>
      <div v-else class="panel-list-item" v-for="(c, idx) in contacts" :key="idx">
        <div class="item-avatar"><i class="bx bx-envelope"></i></div>
        <div class="item-info">
          <div class="item-name">{{ c.name }}</div>
          <div class="item-meta">{{ c.email }} • {{ c.phone }}</div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from './Layout.vue';

// Danh sách dữ liệu liên hệ khách hàng quản lý riêng trong module Contacts
const contacts = ref([]);
</script>

<style src="./css/panels.css"></style>
