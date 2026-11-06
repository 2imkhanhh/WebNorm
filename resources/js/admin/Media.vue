<template>
  <div class="sidebar-header">
    <h2 class="sidebar-title">Thư viện tệp</h2>
    <p class="sidebar-subtitle">Ảnh banner, mô hình 3D norm.glb và tư liệu.</p>
  </div>
  <div class="sidebar-panel-content">
    <div class="media-grid">
      <div class="media-card" v-for="(m, mIdx) in mediaList" :key="mIdx">
        <img :src="m.url" :alt="m.name" />
        <span>{{ m.name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Danh sách tệp hình ảnh & assets quản lý riêng trong module Media
const mediaList = ref([
  { name: 'banner.png', url: '/assets/images/banner.png' },
  { name: 'project1.png', url: '/assets/images/project1.png' },
  { name: 'project2.png', url: '/assets/images/project2.png' },
  { name: 'project3.png', url: '/assets/images/project3.png' },
]);
</script>

<style src="./css/panels.css"></style>
