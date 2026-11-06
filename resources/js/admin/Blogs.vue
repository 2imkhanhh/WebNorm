<template>
  <div class="sidebar-header">
    <h2 class="sidebar-title">Bài viết & Tin tức</h2>
    <p class="sidebar-subtitle">Các bài viết chia sẻ góc nhìn thương hiệu.</p>
  </div>
  <div class="sidebar-panel-content">
    <div class="panel-list-item" v-for="(n, idx) in news" :key="idx">
      <div class="item-thumb"><img :src="n.image" :alt="n.title" /></div>
      <div class="item-info">
        <div class="item-name">{{ n.title }}</div>
        <div class="item-meta">{{ n.date }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Danh sách dữ liệu bài viết & tin tức quản lý riêng trong module Blogs
const news = ref([
  { title: 'Tái định vị thương hiệu kỷ nguyên số', date: '24/10/2026', image: '/assets/images/project1.png' },
  { title: 'Thiết kế tối giản và sức hút thương hiệu', date: '18/10/2026', image: '/assets/images/project2.png' },
]);
</script>

<style src="./css/panels.css"></style>
