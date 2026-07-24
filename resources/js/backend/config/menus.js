export const adminMenus = [
  {
    key: 'dashboard',
    title: '控制台',
    icon: 'Odometer',
    path: '/backend/dashboard',
  },
  {
    key: 'permissions',
    title: '权限菜单分类管理',
    icon: 'Lock',
    children: [
      { key: 'permissions-list', title: '权限', path: '/backend/permissions' },
      { key: 'menus-list', title: '菜单管理', path: '/backend/menus' },
      { key: 'roles-list', title: '角色管理', path: '/backend/roles' },
    ],
  },
  {
    key: 'system',
    title: '系统设置',
    icon: 'Setting',
    children: [
      { key: 'system-settings', title: '网站设置', path: '/backend/system/settings' },
    ],
  },
  {
    key: 'users',
    title: '用户管理',
    icon: 'User',
    children: [
      { key: 'users-list', title: '用户管理', path: '/backend/users' },
    ],
  },
  {
    key: 'news',
    title: '新闻',
    icon: 'Document',
    children: [
      { key: 'news-articles', title: '文章管理', path: '/backend/news/articles' },
      { key: 'news-categories', title: '分类管理', path: '/backend/news/categories' },
    ],
  },
  {
    key: 'cases',
    title: '案例',
    icon: 'Collection',
    children: [
      { key: 'cases-list', title: '案例管理', path: '/backend/news/articles' },
    ],
  },
  {
    key: 'products',
    title: '产品',
    icon: 'Goods',
    children: [
      { key: 'products-list', title: '产品管理', path: '/backend/news/articles' },
    ],
  },
  {
    key: 'about',
    title: '关于',
    icon: 'InfoFilled',
    children: [
      { key: 'about-list', title: '关于我们', path: '/backend/news/articles' },
    ],
  },
  {
    key: 'services',
    title: '服务',
    icon: 'Headset',
    children: [
      { key: 'services-list', title: '服务管理', path: '/backend/news/articles' },
    ],
  },
  {
    key: 'others',
    title: '其它功能',
    icon: 'More',
    children: [
      { key: 'others-list', title: '其它', path: '/backend/news/articles' },
    ],
  },
  {
    key: 'files',
    title: '文件管理',
    icon: 'Folder',
    path: '/backend/news/articles',
  },
];
