<template>
  <div class="front-auth">
    <div class="panel wide">
      <h2>用户注册</h2>
      <p class="sub">填写账号信息完成注册</p>
      <el-form ref="formRef" :model="form" :rules="rules" label-position="top" @submit.prevent="onSubmit">
        <el-form-item label="用户名" prop="user_name">
          <el-input v-model="form.user_name" placeholder="字母数字下划线" />
        </el-form-item>
        <el-form-item label="昵称" prop="nick_name">
          <el-input v-model="form.nick_name" />
        </el-form-item>
        <el-form-item label="手机号" prop="user_mobile">
          <el-input v-model="form.user_mobile" />
        </el-form-item>
        <el-form-item label="邮箱" prop="user_email">
          <el-input v-model="form.user_email" />
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="form.password" type="password" show-password />
        </el-form-item>
        <el-form-item label="确认密码" prop="password_confirmation">
          <el-input v-model="form.password_confirmation" type="password" show-password />
        </el-form-item>
        <el-button type="primary" class="submit" :loading="loading" @click="onSubmit">注册</el-button>
      </el-form>
      <div class="foot">
        已有账号？<router-link to="/frontend/login">去登录</router-link>
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
const { register } = useAuth();
const formRef = ref();
const loading = ref(false);
const form = reactive({
  user_name: '',
  nick_name: '',
  user_mobile: '',
  user_email: '',
  password: '',
  password_confirmation: '',
  register_channel: 'web',
});

const rules = {
  user_name: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]{3,32}$/, message: '用户名格式不正确', trigger: 'blur' },
  ],
  nick_name: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
  user_mobile: [
    { required: true, message: '请输入手机号', trigger: 'blur' },
    { pattern: /^1[3-9]\d{9}$/, message: '手机号格式不正确', trigger: 'blur' },
  ],
  user_email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '邮箱格式不正确', trigger: 'blur' },
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, message: '密码至少6位', trigger: 'blur' },
  ],
  password_confirmation: [
    {
      validator: (_r, v, cb) => {
        if (v !== form.password) cb(new Error('两次密码不一致'));
        else cb();
      },
      trigger: 'blur',
    },
  ],
};

async function onSubmit() {
  await formRef.value?.validate();
  loading.value = true;
  try {
    await register({ ...form });
    ElMessage.success('注册成功，请登录');
    router.replace('/frontend/login');
  } catch (e) {
    ElMessage.error(e.message || '注册失败');
  } finally {
    loading.value = false;
  }
}
</script>
