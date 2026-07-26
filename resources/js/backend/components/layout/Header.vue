<template>
  <header class="admin-header">
    <div class="admin-header-left">
      <button class="header-btn" type="button" title="折叠菜单" @click="$emit('toggle-sidebar')">
        <el-icon><Fold v-if="!collapsed" /><Expand v-else /></el-icon>
      </button>
      <button class="header-btn" type="button" title="刷新" @click="$emit('refresh')">
        <el-icon><Refresh /></el-icon>
      </button>
      <div class="header-breadcrumb">
        <span>WMS</span>
        <span class="sep"> / </span>
        <span>EHR</span>
      </div>
    </div>

    <div class="admin-header-right">
      <button class="header-btn" type="button" title="语言">
        <el-icon><ChatDotRound /></el-icon>
      </button>
      <button class="header-btn" type="button" title="清理缓存">
        <el-icon><Delete /></el-icon>
      </button>
      <button class="header-btn" type="button" title="通知">
        <el-icon><Bell /></el-icon>
      </button>
      <button class="header-btn" type="button" title="安全">
        <el-icon><Key /></el-icon>
      </button>
      <button class="header-btn" type="button" title="全屏" @click="toggleFullscreen">
        <el-icon><FullScreen /></el-icon>
      </button>
      <button class="header-btn" type="button" title="锁屏">
        <el-icon><Lock /></el-icon>
      </button>
      <el-dropdown trigger="click" @command="onUserCommand">
        <div class="header-user">
          <div class="avatar">{{ avatarText }}</div>
          <span>{{ displayName }}</span>
          <el-icon><ArrowDown /></el-icon>
        </div>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item disabled>{{ user?.user_mobile || '-' }}</el-dropdown-item>
            <el-dropdown-item command="logout" divided>退出登录</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { useAuth } from '../../composables/useAuth';

defineProps({
  collapsed: { type: Boolean, default: false },
});

defineEmits(['toggle-sidebar', 'refresh']);

const router = useRouter();
const { user, logout } = useAuth();

const displayName = computed(() => user.value?.nick_name || user.value?.user_name || 'admin');
const avatarText = computed(() => (displayName.value || 'A').slice(0, 1).toUpperCase());

function toggleFullscreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen?.();
  } else {
    document.exitFullscreen?.();
  }
}

async function onUserCommand(command) {
  if (command === 'logout') {
    await logout();
    ElMessage.success('已退出登录');
    router.replace('/backend/login');
  }
}
</script>
