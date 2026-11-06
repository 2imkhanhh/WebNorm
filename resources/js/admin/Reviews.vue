<template>
  <Layout>
    <div class="sidebar-header">
      <h2 class="sidebar-title">Đánh giá của đối tác</h2>
      <p class="sidebar-subtitle">Slider nhận xét của khách hàng tại trang chủ.</p>
    </div>
    <div class="sidebar-panel-content">
      <div class="panel-list-item" v-for="(r, idx) in reviews" :key="idx">
        <div class="item-avatar"><i class="bx bx-user"></i></div>
        <div class="item-info">
          <div class="item-name">{{ r.author }}</div>
          <div class="item-meta">{{ r.role }}</div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from './Layout.vue';

// Danh sách dữ liệu đánh giá đối tác quản lý riêng trong module Reviews
const reviews = ref([
  { author: 'Nguyễn Minh Anh', role: 'Giám đốc Marketing', text: 'Rất hài lòng với giải pháp nhận diện chuyên nghiệp của Norm.' },
  { author: 'Trần Văn B', role: 'CEO Founder', text: 'Quy trình bài bản, sáng tạo và hiểu thị trường sâu sắc.' },
]);
</script>

<style src="./css/panels.css"></style>
