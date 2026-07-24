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
import { ref, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '../components/layout/Sidebar.vue';
import Header from '../components/layout/Header.vue';
import Tabs from '../components/layout/Tabs.vue';
import Footer from '../components/layout/Footer.vue';
import { adminMenus } from '../config/menus';

const route = useRoute();
const router = useRouter();

const collapsed = ref(false);
const menus = adminMenus;
const viewKey = ref(0);
const tabs = ref([]);

const activeTab = computed(() => route.path);

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
}
</script>
