<template>
  <Layout>
    <div class="sidebar-header">
      <h2 class="sidebar-title">Quản lý Dự án (Work)</h2>
      <p class="sidebar-subtitle">Danh sách các dự án hiển thị tại trang Portfolio.</p>
    </div>
    <div class="sidebar-panel-content">
      <div class="panel-list-item" v-for="(p, idx) in projects" :key="idx">
        <div class="item-thumb"><img :src="p.image" :alt="p.name" /></div>
        <div class="item-info">
          <div class="item-name">{{ p.name }}</div>
          <div class="item-meta">{{ p.category }}</div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from './Layout.vue';

// Danh sách dữ liệu dự án quản lý riêng trong module Projects
const projects = ref([
  { name: 'Tiên Phong Identity', category: 'Brand identity', image: '/assets/images/project1.png' },
  { name: 'Norm Digital Experience', category: 'UI/UX Design', image: '/assets/images/project2.png' },
  { name: 'Heritage Brand Strategy', category: 'Packaging', image: '/assets/images/project3.png' },
  { name: 'E-commerce Platform', category: 'Web Development', image: '/assets/images/project4.png' },
]);
</script>

<style src="./css/panels.css"></style>
