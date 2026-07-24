<template>
  <div class="admin-tabs">
    <div
      v-for="tab in tabs"
      :key="tab.path"
      class="admin-tab"
      :class="{ 'is-active': active === tab.path }"
      @click="$emit('select', tab.path)"
    >
      <el-icon :size="14"><Document /></el-icon>
      <span>{{ tab.title }}</span>
      <span class="tab-close" @click.stop="$emit('close', tab.path)">
        <el-icon :size="10"><Close /></el-icon>
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tabs: { type: Array, default: () => [] },
  active: { type: String, default: '' },
});

defineEmits(['select', 'close']);
</script>
