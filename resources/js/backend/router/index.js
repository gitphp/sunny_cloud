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
                path: 'menus',
                name: 'menus',
                component: () => import('@backend/views/menus/Index.vue'),
                meta: { title: '菜单管理', parent: '权限菜单分类管理' },
            },
            {
                path: 'roles',
                name: 'roles',
                component: () => import('@backend/views/roles/Index.vue'),
                meta: { title: '角色管理', parent: '权限菜单分类管理' },
            },
            {
                path: 'permissions',
                name: 'permissions',
                component: () => import('@backend/views/permissions/Index.vue'),
                meta: { title: '权限管理', parent: '权限菜单分类管理' },
            },
            {
                path: 'hr/departments',
                name: 'hr-departments',
                component: () => import('@backend/views/hr/Department.vue'),
                meta: { title: '部门管理', parent: '人事管理' },
            },
            {
                path: 'hr/posts',
                name: 'hr-posts',
                component: () => import('@backend/views/hr/Post.vue'),
                meta: { title: '岗位管理', parent: '人事管理' },
            },
            {
                path: 'hr/user-dept-posts',
                name: 'hr-user-dept-posts',
                component: () => import('@backend/views/hr/UserDeptPost.vue'),
                meta: { title: '任职管理', parent: '人事管理' },
            },
            {
                path: 'cases',
                name: 'cases',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '案例管理' },
            },
            {
                path: 'product/products',
                name: 'product-products',
                component: () => import('@backend/views/product/Index.vue'),
                meta: { title: '商品管理', parent: '产品' },
            },
            {
                path: 'product/products/create',
                name: 'product-products-create',
                component: () => import('@backend/views/product/Form.vue'),
                meta: { title: '新增商品', parent: '产品' },
            },
            {
                path: 'product/products/:id/edit',
                name: 'product-products-edit',
                component: () => import('@backend/views/product/Form.vue'),
                meta: { title: '编辑商品', parent: '产品' },
            },
            {
                path: 'product/brands',
                name: 'product-brands',
                component: () => import('@backend/views/product/Brand.vue'),
                meta: { title: '品牌管理', parent: '产品' },
            },
            {
                path: 'product/categories',
                name: 'product-categories',
                component: () => import('@backend/views/product/Category.vue'),
                meta: { title: '商品分类', parent: '产品' },
            },
            {
                path: 'product/specifications',
                name: 'product-specifications',
                component: () => import('@backend/views/product/Specification.vue'),
                meta: { title: '规格管理', parent: '产品' },
            },
            {
                path: 'wf/flow-types',
                name: 'wf-flow-types',
                component: () => import('@backend/views/wf/FlowType.vue'),
                meta: { title: '流程类型', parent: '流程管理' },
            },
            {
                path: 'wf/flow-definitions',
                name: 'wf-flow-definitions',
                component: () => import('@backend/views/wf/FlowDefinitionIndex.vue'),
                meta: { title: '流程模板', parent: '流程管理' },
            },
            {
                path: 'wf/flow-definitions/create',
                name: 'wf-flow-definitions-create',
                component: () => import('@backend/views/wf/FlowDefinitionForm.vue'),
                meta: { title: '新增流程模板', parent: '流程管理' },
            },
            {
                path: 'wf/flow-definitions/:id/edit',
                name: 'wf-flow-definitions-edit',
                component: () => import('@backend/views/wf/FlowDefinitionForm.vue'),
                meta: { title: '配置流程模板', parent: '流程管理' },
            },
            {
                path: 'wf/applies',
                name: 'wf-applies',
                component: () => import('@backend/views/wf/ApplyIndex.vue'),
                meta: { title: '我的申请', parent: '流程管理' },
            },
            {
                path: 'wf/applies/create',
                name: 'wf-applies-create',
                component: () => import('@backend/views/wf/ApplyForm.vue'),
                meta: { title: '发起申请', parent: '流程管理' },
            },
            {
                path: 'wf/applies/:id/edit',
                name: 'wf-applies-edit',
                component: () => import('@backend/views/wf/ApplyForm.vue'),
                meta: { title: '编辑申请', parent: '流程管理' },
            },
            {
                path: 'wf/applies/:id',
                name: 'wf-applies-detail',
                component: () => import('@backend/views/wf/ApplyDetail.vue'),
                meta: { title: '申请详情', parent: '流程管理' },
            },
            {
                path: 'wf/todo',
                name: 'wf-todo',
                component: () => import('@backend/views/wf/TodoIndex.vue'),
                meta: { title: '待我审批', parent: '流程管理' },
            },
            {
                path: 'wf/cc',
                name: 'wf-cc',
                component: () => import('@backend/views/wf/CcIndex.vue'),
                meta: { title: '抄送我的', parent: '流程管理' },
            },
            {
                path: 'about',
                name: 'about',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '关于我们' },
            },
            {
                path: 'services',
                name: 'services',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '服务管理' },
            },
            {
                path: 'others',
                name: 'others',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '其它' },
            },
            {
                path: 'files',
                name: 'files',
                component: () => import('@backend/views/Placeholder.vue'),
                meta: { title: '文件管理' },
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
