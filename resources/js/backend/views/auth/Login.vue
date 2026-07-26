<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-brand">
        <div class="logo">8</div>
        <h1>名杨后台管理系统</h1>
        <p>登录您的管理员账号</p>
      </div>

      <el-form ref="formRef" :model="form" :rules="rules" size="large" @submit.prevent="onSubmit">
        <el-form-item prop="account">
          <el-input v-model="form.account" placeholder="用户名 / 手机号 / 邮箱" prefix-icon="User" />
        </el-form-item>
        <el-form-item prop="password">
          <el-input
            v-model="form.password"
            type="password"
            show-password
            placeholder="密码"
            prefix-icon="Lock"
            @keyup.enter="onSubmit"
          />
        </el-form-item>
        <el-button class="btn-primary-teal auth-submit" :loading="loading" native-type="submit" @click="onSubmit">
          登录
        </el-button>
      </el-form>

      <p class="auth-hint">演示账号：admin / admin123，或 super_admin / password</p>
      <div class="auth-footer">
        还没有账号？
        <router-link to="/backend/register">立即注册</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ElMessage } from 'element-plus';
import { useAuth } from '../../composables/useAuth';

const router = useRouter();
const route = useRoute();
const { login } = useAuth();

const formRef = ref();
const loading = ref(false);
const form = reactive({
  account: 'admin',
  password: 'admin123',
});

const rules = {
  account: [{ required: true, message: '请输入账号', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
};

async function onSubmit() {
  await formRef.value?.validate();
  loading.value = true;
  try {
    await login({ ...form });
    ElMessage.success('登录成功');
    router.replace(route.query.redirect || '/backend/dashboard');
  } catch (e) {
    ElMessage.error(e.message || '登录失败');
  } finally {
    loading.value = false;
  }
}
</script>
