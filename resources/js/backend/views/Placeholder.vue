<template>
  <div class="admin-page-card placeholder-page">
    {{ title }} 功能开发中…
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const title = computed(() => route.meta?.title || '页面');
</script>
