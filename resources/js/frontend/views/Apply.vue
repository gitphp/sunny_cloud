<template>
  <div class="apply-page">
    <header class="apply-header">
      <div class="apply-header-inner">
        <router-link class="brand" to="/">
          <img :src="logo" alt="logo" width="220" height="66" />
        </router-link>
        <router-link class="back-home" to="/">返回首页</router-link>
      </div>
    </header>

    <div class="apply-wrap" v-loading="loading">
      <div class="crumb">
        <router-link to="/">首页</router-link>
        <span> &gt; </span>
        <span>申请收录</span>
      </div>

      <div class="page-head">
        <h1>申请收录</h1>
        <div class="meta">
          <span>{{ publishedAt }}</span>
          <span>{{ viewCount }}</span>
        </div>
      </div>

      <div class="req-box" v-html="requirementsHtml" />

      <el-form
        ref="formRef"
        class="apply-form"
        :model="form"
        :rules="rules"
        label-width="120px"
        label-position="left"
      >
        <el-form-item label="网站标签" prop="site_tag" required>
          <el-input v-model="form.site_tag" maxlength="32" placeholder="必填" />
        </el-form-item>
        <el-form-item label="网站副标题" prop="site_subtitle">
          <el-input v-model="form.site_subtitle" maxlength="128" />
        </el-form-item>
        <el-form-item label="网站图标url" prop="site_favicon">
          <el-input v-model="form.site_favicon" maxlength="512" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="网站地址" prop="site_url" required>
          <div class="url-row">
            <el-input v-model="form.site_url" maxlength="2048" placeholder="(带http://或https://)" />
            <el-button class="tkd-btn" :loading="fetching" @click="onFetchTkd">获取TKD</el-button>
          </div>
        </el-form-item>
        <el-form-item label="所属分类" prop="category_id" required>
          <el-select v-model="form.category_id" filterable placeholder="请选择分类" style="width: 100%">
            <el-option
              v-for="c in categories"
              :key="c.id"
              :label="categoryLabel(c)"
              :value="String(c.id)"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="网站简介" prop="site_intro">
          <el-input
            v-model="form.site_intro"
            type="textarea"
            :rows="10"
            maxlength="1024"
            show-word-limit
            placeholder="请填写网站简介"
          />
        </el-form-item>
        <div class="submit-row">
          <el-button class="submit-btn" :loading="saving" @click="onSubmit">提交</el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage } from 'element-plus';
import { createApply, fetchApplyMeta, fetchApplyTkd } from '@frontend/api/apply';

const loading = ref(false);
const saving = ref(false);
const fetching = ref(false);
const formRef = ref();
const logo = ref('/uploads/logo/budff_logo.png');
const requirementsHtml = ref('');
const categories = ref([]);
const publishedAt = ref('2026-08-22');
const viewCount = ref(17233);
const siteName = ref('帮扶导航');

const form = reactive({
  site_tag: '',
  site_subtitle: '',
  site_favicon: '',
  site_url: '',
  category_id: '',
  site_intro: '',
});

const rules = {
  site_tag: [{ required: true, message: '请填写网站标签', trigger: 'blur' }],
  site_url: [{ required: true, message: '请填写网站地址', trigger: 'blur' }],
  category_id: [{ required: true, message: '请选择所属分类', trigger: 'change' }],
};

function categoryLabel(c) {
  const pad = c.level > 1 ? '　'.repeat(Math.max(0, (c.level || 1) - 1)) : '';
  return `${pad}${c.category_name}`;
}

async function loadMeta() {
  loading.value = true;
  try {
    const res = await fetchApplyMeta();
    const data = res.data || {};
    logo.value = data.logo || logo.value;
    requirementsHtml.value = data.requirements_html || '';
    categories.value = data.categories || [];
    publishedAt.value = data.published_at || publishedAt.value;
    viewCount.value = data.view_count || viewCount.value;
    siteName.value = data.site?.site_name || siteName.value;
    document.title = `申请收录 - ${siteName.value}`;
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

async function onFetchTkd() {
  if (!form.site_url) {
    ElMessage.warning('请先填写网站地址');
    return;
  }
  fetching.value = true;
  try {
    const res = await fetchApplyTkd({ site_url: form.site_url });
    const data = res.data || {};
    if (data.site_tag) form.site_tag = data.site_tag;
    if (data.site_favicon) form.site_favicon = data.site_favicon;
    if (data.site_intro) form.site_intro = data.site_intro;
    if (data.site_subtitle) form.site_subtitle = data.site_subtitle;
    ElMessage.success('获取成功');
  } catch (e) {
    ElMessage.error(e?.message || '获取失败');
  } finally {
    fetching.value = false;
  }
}

async function onSubmit() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    await createApply({ ...form });
    ElMessage.success('提交成功，请等待审核');
    formRef.value?.resetFields();
  } catch (e) {
    ElMessage.error(e?.message || '提交失败');
  } finally {
    saving.value = false;
  }
}

onMounted(loadMeta);
</script>

<style scoped>
.apply-page {
  --accent: #e74c3c;
  --bg: #f5f6f8;
  --card: #fff;
  --text: #222;
  --muted: #888;
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
  font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
}

.apply-header {
  background: var(--card);
  border-bottom: 1px solid #eee;
}

.apply-header-inner {
  max-width: 960px;
  margin: 0 auto;
  padding: 8px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand img {
  width: 220px;
  height: 66px;
  object-fit: contain;
  display: block;
}

.back-home {
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
}

.back-home:hover {
  color: var(--accent);
}

.apply-wrap {
  max-width: 960px;
  margin: 0 auto;
  padding: 20px 20px 60px;
}

.crumb {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 16px;
}

.crumb a {
  color: var(--muted);
  text-decoration: none;
}

.crumb a:hover {
  color: var(--accent);
}

.page-head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.page-head h1 {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
}

.meta {
  display: flex;
  gap: 16px;
  color: var(--muted);
  font-size: 13px;
}

.req-box {
  background: #fff8e6;
  border: 1px solid #ffe2a8;
  border-radius: 6px;
  padding: 16px 20px;
  margin-bottom: 24px;
  line-height: 1.8;
  font-size: 14px;
}

.req-box :deep(ul) {
  margin: 8px 0 0;
  padding-left: 20px;
}

.req-box :deep(a) {
  color: var(--accent);
}

.req-box :deep(strong) {
  color: #c0392b;
}

.apply-form {
  background: var(--card);
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 28px 32px 20px;
}

.url-row {
  display: flex;
  gap: 10px;
  width: 100%;
}

.tkd-btn {
  flex-shrink: 0;
  background: var(--accent) !important;
  border-color: var(--accent) !important;
  color: #fff !important;
}

.submit-row {
  text-align: center;
  padding: 12px 0 8px;
}

.submit-btn {
  min-width: 220px;
  height: 40px;
  background: var(--accent) !important;
  border-color: var(--accent) !important;
  color: #fff !important;
  font-size: 16px;
}

@media (max-width: 720px) {
  .apply-form {
    padding: 20px 14px;
  }

  .url-row {
    flex-direction: column;
  }

  .page-head {
    flex-direction: column;
    align-items: flex-start;
  }

  :deep(.el-form-item) {
    flex-direction: column;
  }

  :deep(.el-form-item__label) {
    justify-content: flex-start;
  }
}
</style>
