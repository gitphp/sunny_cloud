import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from '@backend/layouts/AdminLayout.vue';
import { useAuth } from '@backend/composables/useAuth';

const routes = [
    {
        path: '/backend/login',
        name: 'login',
        component: () => import('@backend/views/auth/Login.vue'),
        meta: { guest: true, title: '登录' },
    },
    {
        path: '/backend/register',
        name: 'register',
        component: () => import('@backend/views/auth/Register.vue'),
        meta: { guest: true, title: '注册' },
    },
    {
        path: '/backend',
        component: AdminLayout,
        meta: { requiresAuth: true },
        redirect: '/backend/dashboard',
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: () => import('@backend/views/Dashboard.vue'),
                meta: { title: '控制台', icon: 'Odometer' },
            },
            {
                path: 'news/articles',
                name: 'news-articles',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '文章管理', parent: '新闻' },
            },
            {
                path: 'news/categories',
                name: 'news-categories',
                component: () => import('@backend/views/news/Category.vue'),
                meta: { title: '分类管理', parent: '新闻' },
            },
            {
                path: 'system/settings',
                name: 'system-settings',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '网站设置', parent: '系统设置' },
            },
            {
                path: 'users',
                name: 'users',
                component: () => import('@backend/views/users/Index.vue'),
                meta: { title: '用户管理', parent: '用户管理' },
            },
            {
                path: 'permissions',
                name: 'permissions',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '权限', parent: '权限菜单分类管理' },
            },
        ],
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

    if (to.meta.requiresAuth && !isLoggedIn.value) {
        return { path: '/backend/login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guest && isLoggedIn.value) {
        return { path: '/backend/dashboard' };
    }

    return true;
});

export default router;
