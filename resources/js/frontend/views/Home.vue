<template>
  <div class="portal" :class="{ dark: isDark }">
    <header class="portal-header">
      <div class="portal-header-inner">
        <router-link class="brand" to="/">
          <img class="brand-logo" :src="logo" alt="logo" width="220" height="66" />
        </router-link>

        <nav class="top-nav">
          <a
            class="nav-item"
            :class="{ active: !channelId }"
            href="/"
            @click.prevent="goHome"
          >首页</a>
          <div
            v-for="item in nav"
            :key="item.id"
            class="nav-item-wrap"
            @mouseenter="openMenu = item.id"
            @mouseleave="openMenu = ''"
          >
            <a
              class="nav-item"
              :class="{ active: channelId === item.id }"
              href="#"
              @click.prevent="selectChannel(item.id)"
            >
              {{ item.category_name }}
              <span v-if="item.children?.length" class="chev">▾</span>
            </a>
            <div v-if="item.children?.length && openMenu === item.id" class="nav-drop">
              <a
                v-for="child in item.children"
                :key="child.id"
                href="#"
                @click.prevent="scrollToSection(child.id)"
              >{{ child.category_name }}</a>
            </div>
          </div>
        </nav>

        <div class="header-actions">
          <a class="apply-btn" href="mailto:githup@163.com?subject=申请收录">
            ❤ 申请收录
          </a>
          <button type="button" class="icon-btn" title="切换主题" @click="isDark = !isDark">
            <el-icon><Moon /></el-icon>
          </button>
          <button type="button" class="icon-btn" title="搜索" @click="focusSearch">
            <el-icon><Search /></el-icon>
          </button>
        </div>
      </div>
    </header>

    <div class="portal-body">
      <aside class="side-nav">
        <a
          v-for="sec in sections"
          :key="sec.id"
          class="side-item"
          :class="{ active: activeSection === sec.id }"
          href="#"
          @click.prevent="scrollToSection(sec.id)"
        >{{ sec.category_name }}</a>
      </aside>

      <main class="main-area">
        <div class="toolbar">
          <el-input
            ref="searchRef"
            v-model="keyword"
            clearable
            placeholder="搜索站点名称 / 描述"
            class="search-box"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
        </div>

        <section
          v-for="sec in filteredSections"
          :id="`sec-${sec.id}`"
          :key="sec.id"
          class="block"
        >
          <header class="block-head">
            <h2>{{ sec.category_name }}</h2>
            <a class="more" href="#" @click.prevent>更多 +</a>
          </header>

          <div v-if="sec.layout === 'text'" class="text-links">
            <a
              v-for="bm in sec.bookmarks"
              :key="bm.id"
              :href="bm.book_url"
              target="_blank"
              rel="noopener noreferrer"
            >{{ bm.book_title || bm.short_title }}</a>
          </div>

          <div v-else class="card-grid">
            <a
              v-for="bm in sec.bookmarks"
              :key="bm.id"
              class="link-card"
              :href="bm.book_url"
              target="_blank"
              rel="noopener noreferrer"
            >
              <div class="card-icon">
                <img v-if="bm.book_favicon" :src="bm.book_favicon" alt="" @error="hideImg" />
                <span v-else>{{ initial(bm.book_title || bm.short_title) }}</span>
              </div>
              <div class="card-body">
                <div class="card-title-row">
                  <strong :class="{ bold: bm.is_bold === 0 }">{{ bm.book_title || bm.short_title }}</strong>
                  <span class="arrow">›</span>
                </div>
                <p>{{ bm.book_desc || bm.book_url }}</p>
              </div>
            </a>
          </div>
        </section>

        <el-empty v-if="!loading && !filteredSections.length" description="暂无导航内容" />

        <section v-if="friendLinks.length" class="friend-block">
          <h3>☆ 友链</h3>
          <div class="friend-list">
            <a
              v-for="link in friendLinks"
              :key="link.id"
              :href="link.link_url"
              target="_blank"
              rel="noopener noreferrer"
            >{{ link.link_name }}</a>
          </div>
        </section>
      </main>
    </div>

    <footer class="portal-footer">
      <div class="footer-inner">
        <div class="footer-text">
          <p>
            本站内容仅供学习交流，如有侵权请联系邮箱：
            <a :href="`mailto:${site.email || 'githup@163.com'}`">{{ site.email || 'githup@163.com' }}</a>
          </p>
          <p>{{ site.copyright || 'Copyright © 2022 - 2026 帮扶导航 All Rights Reserved' }}</p>
          <p v-if="site.icp">{{ site.icp }}</p>
        </div>
        <router-link class="footer-brand" to="/">
          <img :src="logo" alt="logo" />
          <span>{{ site.site_name || '帮扶导航' }}</span>
        </router-link>
      </div>
    </footer>

    <div class="fab">
      <router-link class="fab-btn" to="/company" title="企业官网">
        <el-icon><EditPen /></el-icon>
      </router-link>
      <button type="button" class="fab-btn" title="回到顶部" @click="toTop">
        <el-icon><ArrowUp /></el-icon>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { ArrowUp, EditPen, Moon, Search } from '@element-plus/icons-vue';
import { fetchPortal } from '@frontend/api/portal';

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const site = ref({});
const logo = ref('/uploads/logo/budff_logo.png');
const nav = ref([]);
const sections = ref([]);
const friendLinks = ref([]);
const channelId = ref('');
const activeSection = ref('');
const openMenu = ref('');
const keyword = ref('');
const isDark = ref(false);
const searchRef = ref();

const filteredSections = computed(() => {
  const kw = keyword.value.trim().toLowerCase();
  if (!kw) return sections.value;
  return sections.value
    .map((sec) => ({
      ...sec,
      bookmarks: (sec.bookmarks || []).filter((b) => {
        const text = `${b.book_title || ''} ${b.short_title || ''} ${b.book_desc || ''}`.toLowerCase();
        return text.includes(kw);
      }),
    }))
    .filter((sec) => sec.bookmarks.length);
});

function initial(title) {
  const t = String(title || '?').trim();
  return t.slice(0, 1).toUpperCase();
}

function hideImg(e) {
  e.target.style.display = 'none';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchPortal({
      channel_id: channelId.value || undefined,
    });
    const data = res.data || {};
    site.value = data.site || {};
    logo.value = data.logo || '/uploads/logo/budff_logo.png';
    nav.value = data.nav || [];
    sections.value = data.sections || [];
    friendLinks.value = data.friend_links || [];
    if (site.value.site_title) {
      document.title = site.value.site_title;
    }
    await nextTick();
    observeSections();
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function goHome() {
  channelId.value = '';
  router.replace({ path: '/', query: {} });
  loadData();
}

function selectChannel(id) {
  channelId.value = String(id);
  router.replace({ path: '/', query: { channel_id: id } });
  loadData();
}

function scrollToSection(id) {
  activeSection.value = String(id);
  const el = document.getElementById(`sec-${id}`);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function toTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function focusSearch() {
  searchRef.value?.focus?.();
}

let observer;
function observeSections() {
  observer?.disconnect();
  observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((e) => e.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio);
      if (visible[0]) {
        activeSection.value = visible[0].target.id.replace(/^sec-/, '');
      }
    },
    { rootMargin: '-20% 0px -60% 0px', threshold: [0.1, 0.4] }
  );
  sections.value.forEach((sec) => {
    const el = document.getElementById(`sec-${sec.id}`);
    if (el) observer.observe(el);
  });
}

watch(
  () => route.query.channel_id,
  (val) => {
    const next = val ? String(val) : '';
    if (next !== channelId.value) {
      channelId.value = next;
      loadData();
    }
  }
);

onMounted(() => {
  channelId.value = route.query.channel_id ? String(route.query.channel_id) : '';
  loadData();
});

onUnmounted(() => observer?.disconnect());
</script>

<style scoped>
.portal {
  --accent: #e74c3c;
  --accent-soft: #ffe8e6;
  --bg: #f3f4f6;
  --card: #ffffff;
  --text: #1f2937;
  --muted: #6b7280;
  --line: #e5e7eb;
  --header-h: 78px;
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
  font-family: "PingFang SC", "Microsoft YaHei", "Noto Sans SC", sans-serif;
}

.portal.dark {
  --bg: #111827;
  --card: #1f2937;
  --text: #f3f4f6;
  --muted: #9ca3af;
  --line: #374151;
  --accent-soft: #3f1d1d;
}

.portal-header {
  position: sticky;
  top: 0;
  z-index: 50;
  height: var(--header-h);
  background: var(--card);
  border-bottom: 1px solid var(--line);
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.02);
}

.portal-header-inner {
  max-width: 1280px;
  margin: 0 auto;
  height: 100%;
  padding: 0 20px;
  display: flex;
  align-items: center;
  gap: 24px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: var(--text);
  flex-shrink: 0;
}

.brand-logo {
  width: 220px;
  height: 66px;
  object-fit: contain;
  display: block;
}

.top-nav {
  display: flex;
  align-items: center;
  gap: 4px;
  flex: 1;
  min-width: 0;
  overflow-x: auto;
}

.nav-item-wrap {
  position: relative;
}

.nav-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 8px 14px;
  color: var(--text);
  text-decoration: none;
  font-size: 15px;
  border-bottom: 2px solid transparent;
  white-space: nowrap;
}

.nav-item.active {
  color: var(--accent);
  border-bottom-color: var(--accent);
  font-weight: 600;
}

.nav-item:hover {
  color: var(--accent);
}

.chev {
  font-size: 10px;
  opacity: 0.7;
}

.nav-drop {
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 140px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  padding: 6px;
  z-index: 20;
}

.nav-drop a {
  display: block;
  padding: 8px 12px;
  color: var(--text);
  text-decoration: none;
  border-radius: 6px;
  font-size: 13px;
}

.nav-drop a:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.apply-btn {
  display: inline-flex;
  align-items: center;
  height: 34px;
  padding: 0 14px;
  border-radius: 18px;
  background: var(--accent);
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

.icon-btn {
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: var(--muted);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.icon-btn:hover {
  background: var(--accent-soft);
  color: var(--accent);
}

.portal-body {
  max-width: 1280px;
  margin: 0 auto;
  padding: 20px;
  display: grid;
  grid-template-columns: 160px 1fr;
  gap: 20px;
  align-items: start;
}

.side-nav {
  position: sticky;
  top: calc(var(--header-h) + 20px);
  background: var(--card);
  border-radius: 12px;
  padding: 12px 0;
  border: 1px solid var(--line);
}

.side-item {
  display: block;
  padding: 10px 16px 10px 20px;
  color: var(--text);
  text-decoration: none;
  font-size: 14px;
  position: relative;
}

.side-item.active {
  color: var(--accent);
  font-weight: 600;
  background: linear-gradient(90deg, var(--accent-soft), transparent);
}

.side-item.active::before {
  content: "";
  position: absolute;
  left: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 14px;
  border-radius: 2px;
  background: var(--accent);
}

.main-area {
  min-width: 0;
}

.toolbar {
  margin-bottom: 16px;
}

.search-box {
  max-width: 360px;
}

.block {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 18px 20px 20px;
  margin-bottom: 16px;
  scroll-margin-top: calc(var(--header-h) + 16px);
}

.block-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.block-head h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
}

.more {
  color: var(--muted);
  text-decoration: none;
  font-size: 13px;
}

.more:hover {
  color: var(--accent);
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.link-card {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 10px;
  text-decoration: none;
  color: inherit;
  transition: background 0.15s ease, transform 0.15s ease;
}

.link-card:hover {
  background: var(--accent-soft);
  transform: translateY(-1px);
}

.card-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
  color: var(--accent);
  font-weight: 700;
}

.portal.dark .card-icon {
  background: #111827;
}

.card-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-body {
  min-width: 0;
  flex: 1;
}

.card-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
}

.card-title-row strong {
  font-size: 14px;
  font-weight: 600;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.card-title-row strong.bold {
  font-weight: 800;
}

.arrow {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 1px solid var(--line);
  color: var(--muted);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
}

.card-body p {
  margin: 4px 0 0;
  font-size: 12px;
  color: var(--muted);
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.text-links {
  display: flex;
  flex-wrap: wrap;
  gap: 10px 18px;
}

.text-links a {
  color: var(--text);
  text-decoration: none;
  font-size: 14px;
}

.text-links a:hover {
  color: var(--accent);
}

.friend-block {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 16px 20px;
  margin-bottom: 16px;
}

.friend-block h3 {
  margin: 0 0 12px;
  font-size: 15px;
}

.friend-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px 16px;
}

.friend-list a {
  color: var(--muted);
  text-decoration: none;
  font-size: 13px;
}

.friend-list a:hover {
  color: var(--accent);
}

.portal-footer {
  border-top: 1px solid var(--line);
  background: var(--card);
  margin-top: 24px;
}

.footer-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 28px 20px;
  display: flex;
  justify-content: space-between;
  gap: 24px;
  align-items: flex-end;
}

.footer-text {
  color: var(--muted);
  font-size: 13px;
  line-height: 1.8;
}

.footer-text p {
  margin: 0;
}

.footer-text a {
  color: var(--accent);
  text-decoration: none;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: var(--text);
  opacity: 0.85;
}

.footer-brand img {
  width: 28px;
  height: 28px;
  object-fit: contain;
}

.fab {
  position: fixed;
  right: 24px;
  bottom: 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 40;
}

.fab-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: 1px solid var(--line);
  background: var(--card);
  color: var(--text);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  text-decoration: none;
}

.fab-btn:hover {
  color: var(--accent);
  border-color: var(--accent);
}

@media (max-width: 1100px) {
  .card-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 900px) {
  .portal-body {
    grid-template-columns: 1fr;
  }

  .side-nav {
    position: static;
    display: flex;
    overflow-x: auto;
    padding: 8px;
    gap: 4px;
  }

  .side-item {
    white-space: nowrap;
    padding: 8px 12px;
  }

  .side-item.active::before {
    display: none;
  }

  .card-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .apply-btn {
    padding: 0 10px;
    font-size: 12px;
  }

  .brand-logo {
    width: 160px;
    height: 48px;
  }

  .top-nav {
    display: none;
  }
}

@media (max-width: 560px) {
  .card-grid {
    grid-template-columns: 1fr;
  }

  .footer-inner {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
