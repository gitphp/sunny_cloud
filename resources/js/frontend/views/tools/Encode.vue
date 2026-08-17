<template>
  <div class="codec-page">
    <header class="page-header">
      <div class="page-header-inner">
        <router-link class="brand" to="/">
          <img src="/uploads/logo/budff_logo.png" alt="logo" width="220" height="66" />
        </router-link>
        <div class="header-links">
          <router-link to="/tools/encode" class="active">编解码</router-link>
          <router-link to="/tools/json">JSON 格式化</router-link>
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
        <span>编解码工具</span>
      </div>

      <div class="page-head">
        <h1>编解码工具</h1>
        <p>Base64、URL 编解码、MD5、时间戳互转，纯前端本地处理</p>
      </div>

      <el-tabs v-model="activeTab" class="codec-tabs" @tab-change="onTabChange">
        <el-tab-pane label="Base64" name="base64">
          <div class="tool-panel">
            <div class="actions">
              <el-button type="primary" @click="doBase64Encode">编码</el-button>
              <el-button @click="doBase64Decode">解码</el-button>
              <el-button @click="swapBase64">上下交换</el-button>
              <el-button @click="copyText(b64Out)">复制结果</el-button>
              <el-button @click="clearBase64">清空</el-button>
            </div>
            <label class="field-label">输入</label>
            <textarea v-model="b64In" class="area" rows="8" placeholder="输入原文或 Base64…" />
            <label class="field-label">输出</label>
            <textarea v-model="b64Out" class="area" rows="8" placeholder="结果" readonly />
            <p v-if="b64Error" class="err">{{ b64Error }}</p>
          </div>
        </el-tab-pane>

        <el-tab-pane label="URLEncode" name="url">
          <div class="tool-panel">
            <div class="actions">
              <el-radio-group v-model="urlMode" size="small">
                <el-radio-button value="component">encodeURIComponent</el-radio-button>
                <el-radio-button value="uri">encodeURI</el-radio-button>
              </el-radio-group>
              <el-button type="primary" @click="doUrlEncode">编码</el-button>
              <el-button @click="doUrlDecode">解码</el-button>
              <el-button @click="swapUrl">上下交换</el-button>
              <el-button @click="copyText(urlOut)">复制结果</el-button>
              <el-button @click="clearUrl">清空</el-button>
            </div>
            <label class="field-label">输入</label>
            <textarea v-model="urlIn" class="area" rows="8" placeholder="输入文本或 URL 编码串…" />
            <label class="field-label">输出</label>
            <textarea v-model="urlOut" class="area" rows="8" placeholder="结果" readonly />
            <p v-if="urlError" class="err">{{ urlError }}</p>
          </div>
        </el-tab-pane>

        <el-tab-pane label="MD5" name="md5">
          <div class="tool-panel">
            <div class="actions">
              <el-checkbox v-model="md5Upper">大写</el-checkbox>
              <el-checkbox v-model="md5Short">16 位</el-checkbox>
              <el-button type="primary" @click="doMd5">计算</el-button>
              <el-button @click="copyText(md5Out)">复制结果</el-button>
              <el-button @click="clearMd5">清空</el-button>
            </div>
            <label class="field-label">输入</label>
            <textarea
              v-model="md5In"
              class="area"
              rows="8"
              placeholder="输入要计算 MD5 的文本…"
              @input="doMd5"
            />
            <label class="field-label">输出</label>
            <textarea v-model="md5Out" class="area mono" rows="3" placeholder="MD5 结果" readonly />
          </div>
        </el-tab-pane>

        <el-tab-pane label="TimeStamp" name="timestamp">
          <div class="tool-panel">
            <div class="now-card">
              <div class="now-row">
                <span class="now-label">当前时间</span>
                <strong>{{ nowText }}</strong>
              </div>
              <div class="now-row">
                <span class="now-label">秒级时间戳</span>
                <code @click="copyText(String(nowSec))" title="点击复制">{{ nowSec }}</code>
              </div>
              <div class="now-row">
                <span class="now-label">毫秒时间戳</span>
                <code @click="copyText(String(nowMs))" title="点击复制">{{ nowMs }}</code>
              </div>
              <el-button size="small" @click="fillNowToTs">填入当前时间戳</el-button>
            </div>

            <div class="ts-grid">
              <div class="ts-box">
                <label class="field-label">时间戳 → 日期</label>
                <div class="inline-actions">
                  <el-radio-group v-model="tsUnit" size="small">
                    <el-radio-button value="s">秒</el-radio-button>
                    <el-radio-button value="ms">毫秒</el-radio-button>
                  </el-radio-group>
                  <el-button type="primary" size="small" @click="tsToDate">转换</el-button>
                  <el-button size="small" @click="copyText(dateOut)">复制</el-button>
                </div>
                <el-input v-model="tsIn" placeholder="Unix 时间戳" clearable @keyup.enter="tsToDate" />
                <el-input v-model="dateOut" class="mt" readonly placeholder="日期时间" />
                <p v-if="tsError" class="err">{{ tsError }}</p>
              </div>

              <div class="ts-box">
                <label class="field-label">日期 → 时间戳</label>
                <div class="inline-actions">
                  <el-button type="primary" size="small" @click="dateToTs">转换</el-button>
                  <el-button size="small" @click="copyText(tsOut)">复制</el-button>
                  <el-button size="small" @click="fillNowToDate">填入当前</el-button>
                </div>
                <el-input
                  v-model="dateIn"
                  placeholder="YYYY-MM-DD HH:mm:ss"
                  clearable
                  @keyup.enter="dateToTs"
                />
                <el-input v-model="tsOut" class="mt" readonly placeholder="时间戳（秒 / 毫秒）" />
                <p v-if="dateError" class="err">{{ dateError }}</p>
              </div>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import {
  base64Decode,
  base64Encode,
  formatLocalDateTime,
  md5,
  parseLocalDateTime,
  urlDecode,
  urlEncode,
} from '@frontend/utils/codec';

const TABS = ['base64', 'url', 'md5', 'timestamp'];

const route = useRoute();
const router = useRouter();

const activeTab = ref(normalizeTab(route.query.tab));

const b64In = ref('');
const b64Out = ref('');
const b64Error = ref('');

const urlIn = ref('');
const urlOut = ref('');
const urlMode = ref('component');
const urlError = ref('');

const md5In = ref('');
const md5Out = ref('');
const md5Upper = ref(false);
const md5Short = ref(false);

const nowMs = ref(Date.now());
const nowSec = computed(() => Math.floor(nowMs.value / 1000));
const nowText = computed(() => formatLocalDateTime(nowMs.value));

const tsUnit = ref('s');
const tsIn = ref('');
const dateOut = ref('');
const tsError = ref('');
const dateIn = ref('');
const tsOut = ref('');
const dateError = ref('');

let timer = null;

function normalizeTab(tab) {
  const t = String(tab || 'base64').toLowerCase();
  if (t === 'urlencode') return 'url';
  if (t === 'time' || t === 'ts') return 'timestamp';
  return TABS.includes(t) ? t : 'base64';
}

function onTabChange(name) {
  router.replace({ query: { ...route.query, tab: name } });
}

watch(
  () => route.query.tab,
  (tab) => {
    activeTab.value = normalizeTab(tab);
  },
);

async function copyText(text) {
  if (!text) {
    ElMessage.warning('没有可复制的内容');
    return;
  }
  try {
    await navigator.clipboard.writeText(String(text));
    ElMessage.success('已复制');
  } catch {
    ElMessage.error('复制失败');
  }
}

function doBase64Encode() {
  b64Error.value = '';
  try {
    b64Out.value = base64Encode(b64In.value);
  } catch (e) {
    b64Error.value = e.message || '编码失败';
  }
}

function doBase64Decode() {
  b64Error.value = '';
  try {
    b64Out.value = base64Decode(b64In.value);
  } catch (e) {
    b64Error.value = e.message || '解码失败，请检查 Base64 内容';
  }
}

function swapBase64() {
  const t = b64In.value;
  b64In.value = b64Out.value;
  b64Out.value = t;
}

function clearBase64() {
  b64In.value = '';
  b64Out.value = '';
  b64Error.value = '';
}

function doUrlEncode() {
  urlError.value = '';
  try {
    urlOut.value = urlEncode(urlIn.value, urlMode.value);
  } catch (e) {
    urlError.value = e.message || '编码失败';
  }
}

function doUrlDecode() {
  urlError.value = '';
  try {
    urlOut.value = urlDecode(urlIn.value);
  } catch (e) {
    urlError.value = e.message || '解码失败，请检查编码串';
  }
}

function swapUrl() {
  const t = urlIn.value;
  urlIn.value = urlOut.value;
  urlOut.value = t;
}

function clearUrl() {
  urlIn.value = '';
  urlOut.value = '';
  urlError.value = '';
}

function doMd5() {
  md5Out.value = md5(md5In.value, {
    upper: md5Upper.value,
    short16: md5Short.value,
  });
}

function clearMd5() {
  md5In.value = '';
  md5Out.value = '';
}

watch([md5Upper, md5Short], () => {
  if (md5In.value !== '') doMd5();
});

function tsToDate() {
  tsError.value = '';
  const raw = String(tsIn.value).trim();
  if (!raw) {
    tsError.value = '请输入时间戳';
    return;
  }
  const n = Number(raw);
  if (!Number.isFinite(n)) {
    tsError.value = '时间戳无效';
    return;
  }
  let ms = n;
  if (tsUnit.value === 's') ms = n * 1000;
  else if (String(Math.trunc(Math.abs(n))).length <= 10) ms = n * 1000;
  const d = new Date(ms);
  if (Number.isNaN(d.getTime())) {
    tsError.value = '无法转换';
    return;
  }
  dateOut.value = formatLocalDateTime(d);
}

function dateToTs() {
  dateError.value = '';
  const d = parseLocalDateTime(dateIn.value);
  if (!d) {
    dateError.value = '格式应为 YYYY-MM-DD HH:mm:ss';
    return;
  }
  const ms = d.getTime();
  tsOut.value = tsUnit.value === 'ms' ? String(ms) : String(Math.floor(ms / 1000));
}

function fillNowToTs() {
  tsIn.value = tsUnit.value === 'ms' ? String(nowMs.value) : String(nowSec.value);
  tsToDate();
}

function fillNowToDate() {
  dateIn.value = formatLocalDateTime(nowMs.value);
  dateToTs();
}

onMounted(() => {
  timer = setInterval(() => {
    nowMs.value = Date.now();
  }, 1000);
  if (route.query.tab) {
    activeTab.value = normalizeTab(route.query.tab);
  }
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<style scoped>
.codec-page {
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
  max-width: 1100px;
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
  max-width: 1100px;
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

.codec-tabs {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 8px 16px 20px;
}

.tool-panel {
  padding-top: 4px;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
  margin-bottom: 14px;
}

.field-label {
  display: block;
  font-size: 13px;
  color: var(--muted);
  margin: 0 0 6px;
}

.area {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 10px 12px;
  font-family: Consolas, "Courier New", Monaco, monospace;
  font-size: 13px;
  line-height: 1.5;
  resize: vertical;
  margin-bottom: 12px;
  background: #fafafa;
  color: var(--text);
  outline: none;
}

.area:focus {
  border-color: var(--accent);
  background: #fff;
}

.area.mono {
  letter-spacing: 0.02em;
}

.err {
  margin: 0;
  color: var(--accent);
  font-size: 13px;
}

.now-card {
  background: #fafafa;
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 14px 16px;
  margin-bottom: 16px;
}

.now-row {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 8px;
  font-size: 14px;
}

.now-label {
  width: 96px;
  color: var(--muted);
  flex-shrink: 0;
}

.now-row code {
  font-family: Consolas, Monaco, monospace;
  color: #7c3aed;
  cursor: pointer;
}

.ts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.ts-box {
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 14px;
  background: #fff;
}

.inline-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  margin-bottom: 10px;
}

.mt {
  margin-top: 10px;
}

@media (max-width: 800px) {
  .ts-grid {
    grid-template-columns: 1fr;
  }
}
</style>
