import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@frontend/composables/useAuth';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@frontend/views/Home.vue'),
    meta: { title: '首页' },
  },
  {
    path: '/company',
    name: 'company',
    component: () => import('@frontend/views/Company.vue'),
    meta: { title: '企业官网' },
  },
  {
    path: '/apply',
    name: 'apply',
    component: () => import('@frontend/views/Apply.vue'),
    meta: { title: '申请收录' },
  },
  {
    path: '/tools/mortgage',
    name: 'mortgage',
    component: () => import('@frontend/views/tools/Mortgage.vue'),
    meta: { title: '房贷计算器' },
  },
  {
    path: '/tools/json',
    name: 'json-format',
    component: () => import('@frontend/views/tools/Json.vue'),
    meta: { title: 'JSON 格式化' },
  },
  {
    path: '/tools/encode',
    name: 'encode',
    component: () => import('@frontend/views/tools/Encode.vue'),
    meta: { title: '编解码工具' },
  },
  {
    path: '/frontend',
    redirect: '/',
  },
  {
    path: '/frontend/home',
    redirect: '/',
  },
  {
    path: '/frontend/login',
    name: 'frontend-login',
    component: () => import('@frontend/views/auth/Login.vue'),
    meta: { guest: true, title: '登录' },
  },
  {
    path: '/frontend/register',
    name: 'frontend-register',
    component: () => import('@frontend/views/auth/Register.vue'),
    meta: { guest: true, title: '注册' },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach(async (to) => {
  const { isLoggedIn, loaded, loadMe } = useAuth();
  if (!loaded.value) {
    await loadMe();
  }
  if (to.meta.guest && isLoggedIn.value) {
    return { path: '/' };
  }
  if (to.meta.title) {
    document.title = to.meta.title;
  }
  return true;
});

export default router;
