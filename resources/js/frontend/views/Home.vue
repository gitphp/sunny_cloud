<template>
  <div class="front-page">
    <header class="front-header">
      <div class="brand">Sunny Cloud</div>
      <div class="actions">
        <template v-if="isLoggedIn">
          <span class="hello">你好，{{ user?.nick_name || user?.user_name }}</span>
          <el-button text type="primary" @click="onLogout">退出</el-button>
        </template>
        <template v-else>
          <router-link to="/frontend/login">登录</router-link>
          <router-link class="cta" to="/frontend/register">注册</router-link>
        </template>
      </div>
    </header>

    <main class="front-hero">
      <h1>欢迎使用 Sunny Cloud</h1>
      <p>基于 Laravel 13 + Vue 3.5 + MySQL 8 的用户中心</p>
      <div class="hero-actions">
        <router-link v-if="!isLoggedIn" class="btn" to="/frontend/register">立即注册</router-link>
        <router-link class="btn ghost" to="/frontend/login">账号登录</router-link>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ElMessage } from 'element-plus';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const { user, isLoggedIn, logout } = useAuth();

async function onLogout() {
  await logout();
  ElMessage.success('已退出');
  router.push('/frontend/login');
}
</script>
