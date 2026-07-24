import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@frontend/composables/useAuth';

const routes = [
  {
    path: '/frontend',
    redirect: '/frontend/home',
  },
  {
    path: '/frontend/home',
    name: 'home',
    component: () => import('@frontend/views/Home.vue'),
    meta: { title: '首页' },
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
});

router.beforeEach(async (to) => {
  const { isLoggedIn, loaded, loadMe } = useAuth();
  if (!loaded.value) {
    await loadMe();
  }
  if (to.meta.guest && isLoggedIn.value) {
    return { path: '/frontend/home' };
  }
  return true;
});

export default router;
