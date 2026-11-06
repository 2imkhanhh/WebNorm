<template>
  <div class="sidebar-header">
    <h2 class="sidebar-title">Dịch vụ cốt lõi</h2>
    <p class="sidebar-subtitle">Các gói dịch vụ hiển thị tại trang chủ.</p>
  </div>
  <div class="sidebar-panel-content">
    <div class="panel-list-item" v-for="(s, idx) in services" :key="idx">
      <div class="item-badge">{{ s.number }}</div>
      <div class="item-info">
        <div class="item-name">{{ s.title }}</div>
        <div class="item-meta">{{ s.desc }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Danh sách dữ liệu gói dịch vụ quản lý riêng trong module Services
const services = ref([
  { number: '01', title: 'Thiết kế từ chiến lược', desc: 'Định hình nền tảng bản sắc thương hiệu vững chắc.' },
  { number: '02', title: 'Hệ thống nhận diện toàn diện', desc: 'Thiết kế hệ sinh thái nhận diện đồng bộ và khác biệt.' },
  { number: '03', title: 'Trải nghiệm số & Tương tác', desc: 'Website và sản phẩm số tối ưu trải nghiệm người dùng.' },
]);
</script>

<style src="./css/panels.css"></style>
