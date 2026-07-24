<template>
  <aside class="admin-sidebar" :class="{ 'is-collapsed': collapsed }">
    <div class="admin-sidebar-brand">
      <div class="brand-logo">8</div>
      <span class="brand-title">名杨 后台管理</span>
    </div>

    <div class="quick-actions">
      <button class="qa-btn blue" type="button" title="快捷入口">
        <el-icon><Monitor /></el-icon>
      </button>
      <button class="qa-btn green" type="button" title="新增">
        <el-icon><Plus /></el-icon>
      </button>
      <button class="qa-btn yellow" type="button" title="消息">
        <el-icon><Bell /></el-icon>
      </button>
      <button class="qa-btn orange" type="button" title="设置">
        <el-icon><Setting /></el-icon>
      </button>
    </div>

    <nav class="admin-menu">
      <div
        v-for="menu in menus"
        :key="menu.key"
        class="menu-item"
        :class="{
          'is-open': openKeys.includes(menu.key),
          'is-active': isMenuActive(menu),
        }"
      >
        <div class="menu-item-title" @click="onMenuClick(menu)">
          <el-icon class="menu-icon">
            <component :is="menu.icon" />
          </el-icon>
          <span class="menu-label">{{ menu.title }}</span>
          <el-icon v-if="menu.children?.length" class="menu-arrow">
            <ArrowRight />
          </el-icon>
        </div>

        <div v-if="menu.children?.length && openKeys.includes(menu.key)" class="submenu">
          <div
            v-for="child in menu.children"
            :key="child.key"
            class="submenu-item"
            :class="{ 'is-active': activePath === child.path }"
            @click.stop="$emit('navigate', child.path)"
          >
            <span>{{ child.title }}</span>
          </div>
        </div>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  collapsed: { type: Boolean, default: false },
  menus: { type: Array, default: () => [] },
  activePath: { type: String, default: '' },
});

const emit = defineEmits(['toggle', 'navigate']);

const openKeys = ref([]);

function findParentKey(path) {
  for (const menu of props.menus) {
    if (menu.children?.some((c) => c.path === path)) {
      return menu.key;
    }
  }
  return null;
}

watch(
  () => props.activePath,
  (path) => {
    const parent = findParentKey(path);
    if (parent && !openKeys.value.includes(parent)) {
      openKeys.value.push(parent);
    }
  },
  { immediate: true },
);

function isMenuActive(menu) {
  if (menu.path && menu.path === props.activePath) return true;
  return menu.children?.some((c) => c.path === props.activePath);
}

function onMenuClick(menu) {
  if (menu.children?.length) {
    const idx = openKeys.value.indexOf(menu.key);
    if (idx >= 0) {
      openKeys.value.splice(idx, 1);
    } else {
      openKeys.value.push(menu.key);
    }
    return;
  }
  if (menu.path) {
    emit('navigate', menu.path);
  }
}
</script>
