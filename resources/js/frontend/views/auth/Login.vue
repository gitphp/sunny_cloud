<template>
  <div class="front-auth">
    <div class="panel">
      <h2>用户登录</h2>
      <p class="sub">支持用户名 / 手机号 / 邮箱登录</p>
      <el-form ref="formRef" :model="form" :rules="rules" size="large" @submit.prevent="onSubmit">
        <el-form-item prop="account">
          <el-input v-model="form.account" placeholder="用户名 / 手机号 / 邮箱" />
        </el-form-item>
        <el-form-item prop="password">
          <el-input v-model="form.password" type="password" show-password placeholder="密码" @keyup.enter="onSubmit" />
        </el-form-item>
        <el-button type="primary" class="submit" :loading="loading" @click="onSubmit">登录</el-button>
      </el-form>
      <div class="foot">
        没有账号？<router-link to="/frontend/register">去注册</router-link>
        · <router-link to="/frontend/home">返回首页</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { useAuth } from '../../composables/useAuth';

const router = useRouter();
const { login } = useAuth();
const formRef = ref();
const loading = ref(false);
const form = reactive({ account: '', password: '' });
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
    router.replace('/frontend/home');
  } catch (e) {
    ElMessage.error(e.message || '登录失败');
  } finally {
    loading.value = false;
  }
}
</script>
