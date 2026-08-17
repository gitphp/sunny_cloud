<template>
  <div class="json-page">
    <header class="page-header">
      <div class="page-header-inner">
        <router-link class="brand" to="/">
          <img src="/uploads/logo/budff_logo.png" alt="logo" width="220" height="66" />
        </router-link>
        <div class="header-links">
          <router-link to="/tools/json" class="active">JSON 格式化</router-link>
          <router-link to="/tools/encode">编解码</router-link>
          <router-link to="/tools/mortgage">房贷计算器</router-link>
          <router-link to="/apply">申请收录</router-link>
          <router-link to="/">返回首页</router-link>
        </div>
      </div>
    </header>

    <div class="page-wrap">
      <div class="crumb">
        <router-link to="/">首页</router-link>
        <span> &gt; </span>
        <span>JSON 格式化</span>
      </div>

      <div class="page-head">
        <h1>JSON 格式化</h1>
        <p>粘贴、格式化、压缩，树形折叠查看；双击叶子节点可就地编辑</p>
      </div>

      <div class="toolbar">
        <div class="toolbar-left">
          <el-button type="primary" @click="formatJson">格式化</el-button>
          <el-button @click="compressJson">压缩</el-button>
          <el-button @click="expandAll">全部展开</el-button>
          <el-button @click="collapseAll">全部折叠</el-button>
          <el-button @click="copyText">复制</el-button>
          <el-button @click="clearAll">清空</el-button>
        </div>
        <div class="toolbar-right">
          <span class="status" :class="{ ok: !error, bad: !!error }">
            {{ error || metaText }}
          </span>
        </div>
      </div>

      <div class="path-bar" v-if="selectedPath">
        <span class="path-label">路径</span>
        <code class="path-value" @click="copyPath" title="点击复制">{{ selectedPath }}</code>
        <el-button link type="primary" size="small" @click="copyPath">复制路径</el-button>
      </div>

      <div class="layout">
        <section class="panel editor-panel">
          <div class="panel-title">
            <span>源文本</span>
            <span class="hint">Ctrl+Enter 格式化</span>
          </div>
          <textarea
            ref="textareaRef"
            v-model="rawText"
            class="json-textarea"
            spellcheck="false"
            placeholder="在此粘贴 JSON…"
            @keydown.ctrl.enter.prevent="formatJson"
            @input="onRawInput"
          />
        </section>

        <section class="panel tree-panel">
          <div class="panel-title">
            <span>树形视图</span>
            <span class="hint">点击选路径 · 双击编辑值</span>
          </div>
          <div class="tree-scroll">
            <div v-if="parsed === undefined" class="tree-empty">
              {{ error ? 'JSON 无效，请修正左侧内容' : '等待输入…' }}
            </div>
            <JsonNode
              v-else
              :value="parsed"
              path="$"
              :collapsed-paths="collapsedPaths"
              @toggle="togglePath"
              @select-path="selectedPath = $event"
              @update-value="onUpdateValue"
            />
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { ElMessage } from 'element-plus';
import JsonNode from './JsonNode.vue';

const SAMPLE = `{
  "name": "sunny_cloud",
  "version": 1,
  "enabled": true,
  "owner": null,
  "tags": ["portal", "tools", "json"],
  "meta": {
    "author": "budff",
    "features": {
      "format": true,
      "collapse": true,
      "edit": true
    }
  }
}`;

const rawText = ref(SAMPLE);
const parsed = ref(null);
const error = ref('');
const selectedPath = ref('$');
const collapsedPaths = ref(new Set());
const textareaRef = ref(null);
let parseTimer = null;

const metaText = computed(() => {
  if (parsed.value === undefined) return '';
  const chars = rawText.value.length;
  const type = Array.isArray(parsed.value)
    ? `Array(${parsed.value.length})`
    : parsed.value !== null && typeof parsed.value === 'object'
      ? `Object(${Object.keys(parsed.value).length})`
      : typeof parsed.value;
  return `${type} · ${chars} 字符`;
});

function tryParse(text) {
  const trimmed = text.trim();
  if (!trimmed) {
    error.value = '';
    parsed.value = undefined;
    return;
  }
  try {
    parsed.value = JSON.parse(trimmed);
    error.value = '';
  } catch (e) {
    error.value = e.message || '解析失败';
    parsed.value = undefined;
  }
}

function onRawInput() {
  clearTimeout(parseTimer);
  parseTimer = setTimeout(() => tryParse(rawText.value), 180);
}

function formatJson() {
  try {
    const data = JSON.parse(rawText.value.trim() || 'null');
    rawText.value = JSON.stringify(data, null, 2);
    parsed.value = data;
    error.value = '';
    ElMessage.success('已格式化');
  } catch (e) {
    error.value = e.message || '解析失败';
    ElMessage.error('JSON 无效，无法格式化');
  }
}

function compressJson() {
  try {
    const data = JSON.parse(rawText.value.trim() || 'null');
    rawText.value = JSON.stringify(data);
    parsed.value = data;
    error.value = '';
    ElMessage.success('已压缩');
  } catch (e) {
    error.value = e.message || '解析失败';
    ElMessage.error('JSON 无效，无法压缩');
  }
}

function collectExpandablePaths(value, path = '$', out = []) {
  if (value !== null && typeof value === 'object') {
    out.push(path);
    if (Array.isArray(value)) {
      value.forEach((item, i) => collectExpandablePaths(item, `${path}[${i}]`, out));
    } else {
      Object.keys(value).forEach((k) => collectExpandablePaths(value[k], `${path}.${k}`, out));
    }
  }
  return out;
}

function expandAll() {
  collapsedPaths.value = new Set();
}

function collapseAll() {
  if (parsed.value === undefined) return;
  const next = new Set();
  collectExpandablePaths(parsed.value).forEach((p) => {
    if (p !== '$') next.add(p);
  });
  collapsedPaths.value = next;
}

function togglePath(path) {
  const next = new Set(collapsedPaths.value);
  if (next.has(path)) next.delete(path);
  else next.add(path);
  collapsedPaths.value = next;
}

function setByPath(root, path, nextValue) {
  if (path === '$') return nextValue;
  const tokens = [];
  path.replace(/^\$/, '').replace(/\.([^.\[\]]+)|\[(\d+)\]/g, (_, key, idx) => {
    tokens.push(key !== undefined ? key : Number(idx));
    return '';
  });
  if (!tokens.length) return nextValue;
  let cur = root;
  for (let i = 0; i < tokens.length - 1; i++) {
    cur = cur[tokens[i]];
  }
  cur[tokens[tokens.length - 1]] = nextValue;
  return root;
}

function onUpdateValue({ path, value }) {
  if (parsed.value === undefined) return;
  const clone = JSON.parse(JSON.stringify(parsed.value));
  const next = setByPath(clone, path, value);
  parsed.value = next;
  rawText.value = JSON.stringify(next, null, 2);
  error.value = '';
}

async function copyText() {
  try {
    await navigator.clipboard.writeText(rawText.value);
    ElMessage.success('已复制到剪贴板');
  } catch {
    ElMessage.error('复制失败');
  }
}

async function copyPath() {
  if (!selectedPath.value) return;
  try {
    await navigator.clipboard.writeText(selectedPath.value);
    ElMessage.success('路径已复制');
  } catch {
    ElMessage.error('复制失败');
  }
}

function clearAll() {
  rawText.value = '';
  parsed.value = undefined;
  error.value = '';
  selectedPath.value = '';
  collapsedPaths.value = new Set();
}

tryParse(rawText.value);
</script>

<style scoped>
.json-page {
  --accent: #e74c3c;
  --bg: #f5f6f8;
  --card: #fff;
  --text: #1f2937;
  --muted: #6b7280;
  --line: #e5e7eb;
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
  font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
}

.page-header {
  background: var(--card);
  border-bottom: 1px solid var(--line);
}

.page-header-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 8px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.brand img {
  width: 220px;
  height: 66px;
  object-fit: contain;
  display: block;
}

.header-links {
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.header-links a {
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
}

.header-links a.active,
.header-links a:hover {
  color: var(--accent);
}

.page-wrap {
  max-width: 1280px;
  margin: 0 auto;
  padding: 20px 20px 48px;
}

.crumb {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 12px;
}

.crumb a {
  color: var(--muted);
  text-decoration: none;
}

.crumb a:hover {
  color: var(--accent);
}

.page-head {
  margin-bottom: 14px;
}

.page-head h1 {
  margin: 0 0 6px;
  font-size: 28px;
}

.page-head p {
  margin: 0;
  color: var(--muted);
  font-size: 14px;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.toolbar-left {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.status {
  font-size: 13px;
  color: var(--muted);
}

.status.ok {
  color: #059669;
}

.status.bad {
  color: var(--accent);
}

.path-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  padding: 8px 12px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 8px;
}

.path-label {
  font-size: 12px;
  color: var(--muted);
}

.path-value {
  flex: 1;
  font-size: 13px;
  color: #7c3aed;
  cursor: pointer;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  min-height: 560px;
}

.panel {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 520px;
}

.panel-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 14px;
  border-bottom: 1px solid var(--line);
  font-size: 14px;
  font-weight: 600;
}

.hint {
  font-size: 12px;
  font-weight: 400;
  color: var(--muted);
}

.json-textarea {
  flex: 1;
  width: 100%;
  border: 0;
  resize: none;
  padding: 12px 14px;
  font-family: Consolas, "Courier New", Monaco, monospace;
  font-size: 13px;
  line-height: 1.55;
  color: var(--text);
  background: #fafafa;
  outline: none;
  box-sizing: border-box;
}

.tree-scroll {
  flex: 1;
  overflow: auto;
  padding: 10px 12px 16px;
  background: #fcfcfd;
}

.tree-empty {
  color: var(--muted);
  font-size: 13px;
  padding: 24px 8px;
}

@media (max-width: 900px) {
  .layout {
    grid-template-columns: 1fr;
  }

  .panel {
    min-height: 360px;
  }
}
</style>
