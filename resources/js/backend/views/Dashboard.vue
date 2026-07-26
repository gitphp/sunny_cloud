<template>
  <div v-loading="loading" class="dashboard">
    <div class="dash-header">
      <div>
        <h2>控制台</h2>
        <p>业务概览 · 更新于 {{ data.generated_at || '-' }}</p>
      </div>
      <el-button class="btn-primary-teal" :icon="Refresh" :loading="loading" @click="loadData">刷新</el-button>
    </div>

    <div class="metric-grid">
      <div
        v-for="item in data.summary"
        :key="item.key"
        class="metric-tile"
        :class="`tone-${item.tone}`"
        @click="go(item.path)"
      >
        <div class="metric-label">{{ item.label }}</div>
        <div class="metric-value">{{ item.value }}</div>
        <div class="metric-sub">
          {{ item.sub_label }} <strong>{{ item.sub_value }}</strong>
        </div>
      </div>
    </div>

    <div class="dash-row">
      <section class="panel todos-panel">
        <div class="panel-head">
          <h3>待办事项</h3>
          <span v-if="!data.todos.length" class="muted">暂无待办</span>
        </div>
        <div v-if="data.todos.length" class="todo-list">
          <a
            v-for="item in data.todos"
            :key="item.key"
            class="todo-item"
            :class="`level-${item.level}`"
            @click.prevent="go(item.path)"
          >
            <span class="todo-title">{{ item.title }}</span>
            <span class="todo-count">{{ item.count }}</span>
          </a>
        </div>
        <div v-else class="empty-block">当前业务队列空闲</div>
      </section>

      <section class="panel trend-panel">
        <div class="panel-head">
          <h3>近 7 日动态</h3>
          <div class="legend">
            <span v-for="s in data.trends.series" :key="s.key" class="legend-item">
              <i :class="`dot ${s.key}`" />{{ s.label }}
            </span>
          </div>
        </div>
        <div class="trend-chart">
          <div v-for="(date, idx) in data.trends.dates" :key="date" class="trend-col">
            <div class="bars">
              <div
                v-for="s in data.trends.series"
                :key="s.key"
                class="bar"
                :class="s.key"
                :style="{ height: barHeight(s.data[idx]) }"
                :title="`${s.label}: ${s.data[idx]}`"
              />
            </div>
            <div class="trend-date">{{ date.slice(5) }}</div>
          </div>
        </div>
      </section>
    </div>

    <div class="dash-row">
      <section class="panel">
        <div class="panel-head">
          <h3>广告投放</h3>
          <a class="action-edit" @click="go('/backend/ad-positions')">管理</a>
        </div>
        <div class="ad-metrics">
          <div class="ad-stat">
            <div class="n">{{ data.ad_metrics.ads_running }}</div>
            <div class="l">投放中 / {{ data.ad_metrics.ads_total }}</div>
          </div>
          <div class="ad-stat">
            <div class="n">{{ data.ad_metrics.slots_enabled }}</div>
            <div class="l">启用广告位 / {{ data.ad_metrics.slots_total }}</div>
          </div>
          <div class="ad-stat">
            <div class="n">{{ formatNum(data.ad_metrics.impression_count) }}</div>
            <div class="l">展示次数</div>
          </div>
          <div class="ad-stat">
            <div class="n">{{ formatNum(data.ad_metrics.click_count) }}</div>
            <div class="l">点击 · CTR {{ data.ad_metrics.click_rate }}</div>
          </div>
        </div>
      </section>

      <section class="panel">
        <div class="panel-head">
          <h3>组织与运营资产</h3>
        </div>
        <div class="asset-grid">
          <a
            v-for="item in data.quick_links.assets"
            :key="item.label"
            class="asset-item"
            @click.prevent="go(item.path)"
          >
            <strong>{{ item.value }}</strong>
            <span>{{ item.label }}</span>
          </a>
        </div>
        <div class="quick-grid">
          <a
            v-for="item in data.quick_links.items"
            :key="item.path"
            class="quick-item"
            @click.prevent="go(item.path)"
          >
            <div class="q-title">{{ item.title }}</div>
            <div class="q-desc">{{ item.desc }}</div>
          </a>
        </div>
      </section>
    </div>

    <div class="dash-row triple">
      <section class="panel">
        <div class="panel-head">
          <h3>最近操作</h3>
          <a class="action-edit" @click="go('/backend/system/operation-logs')">全部</a>
        </div>
        <el-table :data="data.recent_logs" size="small" border>
          <el-table-column prop="created_at" label="时间" width="150" />
          <el-table-column prop="operator_name" label="操作人" width="90" />
          <el-table-column prop="action" label="动作" width="70" />
          <el-table-column prop="biz_type" label="模块" width="90" />
          <el-table-column prop="biz_label" label="对象" min-width="100" show-overflow-tooltip />
        </el-table>
      </section>

      <section class="panel">
        <div class="panel-head">
          <h3>最新留言</h3>
          <a class="action-edit" @click="go('/backend/feedbacks')">全部</a>
        </div>
        <el-table :data="data.recent_feedbacks" size="small" border>
          <el-table-column prop="fb_name" label="联系人" width="90" />
          <el-table-column prop="fb_title" label="标题" min-width="120" show-overflow-tooltip />
          <el-table-column label="状态" width="80" align="center">
            <template #default="{ row }">
              <el-tag :type="row.fb_status === 0 ? 'warning' : 'success'" size="small">
                {{ row.fb_status_label }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>
      </section>

      <section class="panel">
        <div class="panel-head">
          <h3>最新文章</h3>
          <a class="action-edit" @click="go('/backend/news/articles')">全部</a>
        </div>
        <el-table :data="data.recent_articles" size="small" border>
          <el-table-column prop="title" label="标题" min-width="140" show-overflow-tooltip />
          <el-table-column prop="art_status_label" label="状态" width="90" />
          <el-table-column prop="view_count" label="浏览" width="70" align="center" />
        </el-table>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Refresh } from '@element-plus/icons-vue';
import { fetchDashboardOverview } from '../api/dashboard';

const router = useRouter();
const loading = ref(false);

const data = reactive({
  summary: [],
  todos: [],
  trends: { dates: [], series: [] },
  recent_logs: [],
  recent_feedbacks: [],
  recent_articles: [],
  ad_metrics: {
    slots_enabled: 0,
    slots_total: 0,
    ads_running: 0,
    ads_total: 0,
    impression_count: 0,
    click_count: 0,
    click_rate: '0.0000',
  },
  quick_links: { items: [], assets: [] },
  generated_at: '',
});

const maxTrend = computed(() => {
  let max = 0;
  (data.trends.series || []).forEach((s) => {
    (s.data || []).forEach((n) => {
      if (n > max) max = n;
    });
  });
  return max || 1;
});

function barHeight(value) {
  const h = Math.round((Number(value) / maxTrend.value) * 100);
  return `${Math.max(value > 0 ? 8 : 2, h)}%`;
}

function formatNum(n) {
  const num = Number(n || 0);
  if (num >= 10000) return `${(num / 10000).toFixed(1)}万`;
  return String(num);
}

function go(path) {
  if (path) router.push(path);
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchDashboardOverview();
    const payload = res.data || {};
    data.summary = payload.summary || [];
    data.todos = payload.todos || [];
    data.trends = payload.trends || { dates: [], series: [] };
    data.recent_logs = payload.recent_logs || [];
    data.recent_feedbacks = payload.recent_feedbacks || [];
    data.recent_articles = payload.recent_articles || [];
    data.ad_metrics = payload.ad_metrics || data.ad_metrics;
    data.quick_links = payload.quick_links || { items: [], assets: [] };
    data.generated_at = payload.generated_at || '';
  } catch (e) {
    ElMessage.error(e?.message || '加载控制台失败');
  } finally {
    loading.value = false;
  }
}

onMounted(loadData);
</script>

<style scoped>
.dashboard {
  min-height: calc(100% - 8px);
}

.dash-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.dash-header h2 {
  margin: 0 0 4px;
  font-size: 18px;
  font-weight: 600;
  color: #222;
}

.dash-header p {
  margin: 0;
  color: #888;
  font-size: 12px;
}

.metric-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  margin-bottom: 12px;
}

.metric-tile {
  background: #fff;
  border: 1px solid var(--admin-border);
  padding: 14px 16px;
  cursor: pointer;
  transition: border-color 0.15s ease, transform 0.15s ease;
  border-top: 3px solid var(--admin-primary);
}

.metric-tile:hover {
  border-color: var(--admin-primary);
  transform: translateY(-1px);
}

.metric-tile.tone-blue { border-top-color: #3498db; }
.metric-tile.tone-green { border-top-color: #27ae60; }
.metric-tile.tone-orange { border-top-color: #e67e22; }
.metric-tile.tone-cyan { border-top-color: #16a085; }
.metric-tile.tone-indigo { border-top-color: #3d5a80; }
.metric-tile.tone-amber { border-top-color: #f39c12; }
.metric-tile.tone-slate { border-top-color: #7f8c8d; }
.metric-tile.tone-teal { border-top-color: var(--admin-primary); }

.metric-label {
  color: #666;
  font-size: 13px;
}

.metric-value {
  margin-top: 6px;
  font-size: 28px;
  font-weight: 700;
  color: #222;
  line-height: 1.1;
}

.metric-sub {
  margin-top: 8px;
  color: #999;
  font-size: 12px;
}

.metric-sub strong {
  color: #555;
  font-weight: 600;
}

.dash-row {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 12px;
  margin-bottom: 12px;
}

.dash-row.triple {
  grid-template-columns: 1.3fr 1fr 1fr;
}

.panel {
  background: #fff;
  border: 1px solid var(--admin-border);
  padding: 14px 16px;
  min-height: 180px;
}

.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.panel-head h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.muted {
  color: #aaa;
  font-size: 12px;
}

.todo-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.todo-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  border: 1px solid #f0f0f0;
  background: #fafafa;
  cursor: pointer;
}

.todo-item:hover {
  border-color: var(--admin-primary);
}

.todo-title {
  color: #444;
  font-size: 13px;
}

.todo-count {
  min-width: 28px;
  height: 22px;
  padding: 0 8px;
  border-radius: 11px;
  background: var(--admin-primary);
  color: #fff;
  font-size: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.todo-item.level-danger .todo-count { background: #e74c3c; }
.todo-item.level-warning .todo-count { background: #e67e22; }
.todo-item.level-info .todo-count { background: #3498db; }

.empty-block {
  color: #bbb;
  font-size: 13px;
  padding: 28px 0;
  text-align: center;
}

.legend {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: #888;
  font-size: 12px;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

.dot.users { background: var(--admin-primary); }
.dot.feedbacks { background: #e67e22; }
.dot.articles { background: #27ae60; }
.dot.logs { background: #7f8c8d; }

.trend-chart {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
  height: 160px;
  align-items: end;
}

.trend-col {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  gap: 6px;
}

.bars {
  flex: 1;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 3px;
  min-height: 0;
}

.bar {
  width: 8px;
  min-height: 2px;
  border-radius: 2px 2px 0 0;
  background: #ddd;
}

.bar.users { background: var(--admin-primary); }
.bar.feedbacks { background: #e67e22; }
.bar.articles { background: #27ae60; }
.bar.logs { background: #7f8c8d; }

.trend-date {
  color: #999;
  font-size: 11px;
}

.ad-metrics {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.ad-stat {
  padding: 12px;
  background: linear-gradient(180deg, #f7fbfb 0%, #fff 100%);
  border: 1px solid #eef2f2;
}

.ad-stat .n {
  font-size: 22px;
  font-weight: 700;
  color: #222;
}

.ad-stat .l {
  margin-top: 4px;
  color: #888;
  font-size: 12px;
}

.asset-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 8px;
  margin-bottom: 12px;
}

.asset-item {
  text-align: center;
  padding: 10px 6px;
  border: 1px solid #f0f0f0;
  cursor: pointer;
}

.asset-item:hover {
  border-color: var(--admin-primary);
}

.asset-item strong {
  display: block;
  font-size: 18px;
  color: #222;
}

.asset-item span {
  color: #888;
  font-size: 12px;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.quick-item {
  padding: 10px;
  border: 1px solid #f0f0f0;
  cursor: pointer;
}

.quick-item:hover {
  border-color: var(--admin-primary);
  background: #f9fffe;
}

.q-title {
  color: #333;
  font-size: 13px;
  font-weight: 600;
}

.q-desc {
  margin-top: 2px;
  color: #999;
  font-size: 12px;
}

@media (max-width: 1200px) {
  .metric-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .dash-row,
  .dash-row.triple {
    grid-template-columns: 1fr;
  }
  .quick-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .asset-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>
