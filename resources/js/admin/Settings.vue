<template>
  <Layout>
    <div class="sidebar-header">
      <h2 class="sidebar-title">Cài đặt Website</h2>
      <p class="sidebar-subtitle">Cấu hình thông tin thương hiệu và tên miền.</p>
    </div>
    <div class="sidebar-panel-content">
      <div class="panel-stat-card">
        <div class="stat-number">{{ systemInfo.version }}</div>
        <div class="stat-label">{{ systemInfo.name }}</div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from './Layout.vue';

const systemInfo = ref({
  name: 'Website Studio',
  version: '2.0',
});
</script>

<style src="./css/panels.css"></style>
