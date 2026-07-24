<template>
  <div class="admin-layout">
    <Sidebar
      :collapsed="collapsed"
      :menus="menus"
      :active-path="route.path"
      @toggle="collapsed = !collapsed"
      @navigate="onNavigate"
    />
    <div class="admin-main">
      <Header
        :collapsed="collapsed"
        @toggle-sidebar="collapsed = !collapsed"
        @refresh="onRefresh"
      />
      <Tabs
        :tabs="tabs"
        :active="activeTab"
        @select="selectTab"
        @close="closeTab"
      />
      <div class="admin-content">
        <router-view :key="viewKey" />
      </div>
      <Footer />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '../components/layout/Sidebar.vue';
import Header from '../components/layout/Header.vue';
import Tabs from '../components/layout/Tabs.vue';
import Footer from '../components/layout/Footer.vue';
import { adminMenus } from '../config/menus';
import { fetchNavMenus } from '../api/menu';

const route = useRoute();
const router = useRouter();

const collapsed = ref(false);
const menus = ref([...adminMenus]);
const viewKey = ref(0);
const tabs = ref([]);

const activeTab = computed(() => route.path);

function mapMenuTree(nodes) {
  return (nodes || []).map((node) => {
    const children = mapMenuTree(node.children || []);
    return {
      key: String(node.id),
      title: node.menu_name,
      icon: node.menu_icon || 'Menu',
      path: node.menu_path || undefined,
      children: children.length ? children : undefined,
    };
  });
}

async function loadMenus() {
  try {
    const res = await fetchNavMenus();
    const mapped = mapMenuTree(res.data || []);
    if (mapped.length) {
      menus.value = mapped;
    }
  } catch {
    // 接口失败时保留本地 menus 兜底
  }
}

function ensureTab(r) {
  if (!r.meta?.title || r.path === '/backend') return;
  const exists = tabs.value.find((t) => t.path === r.path);
  if (!exists) {
    tabs.value.push({
      path: r.path,
      title: r.meta.title,
      name: r.name,
    });
  }
}

watch(
  () => route.fullPath,
  () => ensureTab(route),
  { immediate: true },
);

function onNavigate(path) {
  router.push(path);
}

function selectTab(path) {
  router.push(path);
}

function closeTab(path) {
  const idx = tabs.value.findIndex((t) => t.path === path);
  if (idx === -1) return;
  tabs.value.splice(idx, 1);
  if (route.path === path) {
    const next = tabs.value[idx] || tabs.value[idx - 1] || tabs.value[0];
    router.push(next?.path || '/backend/dashboard');
  }
}

function onRefresh() {
  viewKey.value += 1;
  loadMenus();
}

onMounted(loadMenus);
</script>
