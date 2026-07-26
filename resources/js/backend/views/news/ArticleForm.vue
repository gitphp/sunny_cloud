<template>
  <div class="admin-page-card">
    <div class="page-toolbar" style="justify-content: space-between">
      <strong>{{ isEdit ? '编辑文章' : '写文章' }}</strong>
      <div>
        <el-button @click="goBack">返回</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submit(1)">存草稿</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submit(4)">发布</el-button>
      </div>
    </div>

    <el-form ref="formRef" v-loading="loading" :model="form" :rules="rules" label-width="100px" style="max-width: 960px">
      <el-form-item label="标题" prop="title">
        <el-input v-model="form.title" maxlength="255" show-word-limit />
      </el-form-item>
      <el-form-item label="副标题" prop="subtitle">
        <el-input v-model="form.subtitle" maxlength="128" show-word-limit />
      </el-form-item>
      <el-form-item label="分类" prop="category_id">
        <el-tree-select
          v-model="form.category_id"
          :data="categoryOptions"
          check-strictly
          clearable
          placeholder="请选择分类"
          style="width: 100%"
          :props="{ label: 'cat_name', value: 'id', children: 'children' }"
        />
      </el-form-item>
      <el-form-item label="封面图" prop="art_cover">
        <el-input v-model="form.art_cover" placeholder="封面图 URL" />
      </el-form-item>
      <el-form-item label="内容类型" prop="content_type">
        <el-radio-group v-model="form.content_type">
          <el-radio
            v-for="(label, value) in options.content_type"
            :key="value"
            :value="Number(value)"
          >
            {{ label }}
          </el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="正文" prop="art_content">
        <el-input v-model="form.art_content" type="textarea" :rows="14" placeholder="支持富文本 HTML / Markdown / 纯文本" />
      </el-form-item>
      <el-form-item label="摘要" prop="summary">
        <el-input v-model="form.summary" type="textarea" :rows="2" maxlength="512" show-word-limit />
      </el-form-item>
      <el-form-item label="作者" prop="author_name">
        <el-input v-model="form.author_name" maxlength="16" />
      </el-form-item>
      <el-form-item label="来源" prop="source">
        <el-input v-model="form.source" maxlength="64" placeholder="原创 / 转载 / 翻译" />
      </el-form-item>
      <el-form-item label="原文链接" prop="source_url">
        <el-input v-model="form.source_url" maxlength="512" />
      </el-form-item>
      <el-form-item label="状态" prop="art_status">
        <el-select v-model="form.art_status" style="width: 220px">
          <el-option
            v-for="(label, value) in options.art_status"
            :key="value"
            :label="label"
            :value="Number(value)"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="选项">
        <el-checkbox v-model="form.is_top" :true-value="1" :false-value="0">置顶</el-checkbox>
        <el-checkbox v-model="form.is_original" :true-value="1" :false-value="0">原创</el-checkbox>
        <el-checkbox v-model="form.is_commentable" :true-value="1" :false-value="0">允许评论</el-checkbox>
      </el-form-item>
      <el-divider content-position="left">SEO</el-divider>
      <el-form-item label="SEO标题" prop="seo_title">
        <el-input v-model="form.seo_title" maxlength="255" />
      </el-form-item>
      <el-form-item label="SEO关键词" prop="seo_keywords">
        <el-input v-model="form.seo_keywords" maxlength="255" />
      </el-form-item>
      <el-form-item label="SEO描述" prop="seo_description">
        <el-input v-model="form.seo_description" type="textarea" :rows="2" maxlength="512" show-word-limit />
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { createArticle, fetchArticle, fetchArticles, updateArticle } from '../../api/article';
import { fetchArticleCategories } from '../../api/articleCategory';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const saving = ref(false);
const formRef = ref();
const categoryOptions = ref([]);
const options = reactive({
  art_status: {},
  content_type: { 1: '富文本', 2: 'Markdown', 3: '纯文本' },
});

const isEdit = computed(() => !!route.params.id && route.name === 'news-articles-edit');

const form = reactive({
  title: '',
  subtitle: '',
  category_id: '',
  art_cover: '',
  content_type: 1,
  art_content: '',
  summary: '',
  author_name: '',
  source: '',
  source_url: '',
  art_status: 1,
  is_top: 0,
  is_original: 1,
  is_commentable: 1,
  seo_title: '',
  seo_keywords: '',
  seo_description: '',
});

const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  category_id: [{ required: true, message: '请选择分类', trigger: 'change' }],
};

function mapCategories(nodes) {
  return (nodes || []).map((n) => ({
    id: String(n.id),
    cat_name: n.cat_name,
    children: mapCategories(n.children || []),
  }));
}

async function loadOptions() {
  const [catRes, listRes] = await Promise.all([
    fetchArticleCategories(),
    fetchArticles({ per_page: 1 }),
  ]);
  categoryOptions.value = mapCategories(catRes.data?.list || []);
  if (listRes.data?.options?.art_status) options.art_status = listRes.data.options.art_status;
  if (listRes.data?.options?.content_type) options.content_type = listRes.data.options.content_type;
}

async function loadDetail() {
  if (!isEdit.value) return;
  loading.value = true;
  try {
    const res = await fetchArticle(route.params.id);
    const data = res.data || {};
    Object.assign(form, {
      title: data.title || '',
      subtitle: data.subtitle || '',
      category_id: data.category_id ? String(data.category_id) : '',
      art_cover: data.art_cover || '',
      content_type: data.content_type ?? 1,
      art_content: data.art_content || '',
      summary: data.summary || '',
      author_name: data.author_name || '',
      source: data.source || '',
      source_url: data.source_url || '',
      art_status: data.art_status ?? 1,
      is_top: data.is_top ?? 0,
      is_original: data.is_original ?? 1,
      is_commentable: data.is_commentable ?? 1,
      seo_title: data.seo_title || '',
      seo_keywords: data.seo_keywords || '',
      seo_description: data.seo_description || '',
    });
  } catch (e) {
    ElMessage.error(e?.message || '加载文章失败');
  } finally {
    loading.value = false;
  }
}

function goBack() {
  router.push('/backend/news/articles');
}

async function submit(forceStatus = null) {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      title: form.title,
      subtitle: form.subtitle || '',
      category_id: form.category_id,
      art_cover: form.art_cover || '',
      content_type: form.content_type ?? 1,
      art_content: form.art_content || '',
      summary: form.summary || '',
      author_name: form.author_name || '',
      source: form.source || '',
      source_url: form.source_url || '',
      art_status: forceStatus ?? form.art_status ?? 1,
      is_top: form.is_top ?? 0,
      is_original: form.is_original ?? 1,
      is_commentable: form.is_commentable ?? 1,
      seo_title: form.seo_title || '',
      seo_keywords: form.seo_keywords || '',
      seo_description: form.seo_description || '',
    };
    if (isEdit.value) {
      await updateArticle(route.params.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createArticle(payload);
      ElMessage.success('添加成功');
    }
    goBack();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  await loadOptions();
  await loadDetail();
});
</script>
