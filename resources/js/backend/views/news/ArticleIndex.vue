<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="标题 / 副标题 / 作者"
        @keyup.enter="search"
      />
      <el-tree-select
        v-model="filters.category_id"
        :data="categoryOptions"
        clearable
        check-strictly
        placeholder="分类"
        style="width: 180px"
        :props="{ label: 'cat_name', value: 'id', children: 'children' }"
      />
      <el-select v-model="filters.art_status" clearable placeholder="状态" style="width: 130px">
        <el-option
          v-for="(label, value) in options.art_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="search">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="goCreate">写文章</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column label="标题" min-width="220" show-overflow-tooltip>
        <template #default="{ row }">
          <span v-if="row.is_top === 1" class="top-flag">顶</span>
          {{ row.title }}
        </template>
      </el-table-column>
      <el-table-column prop="category_name" label="分类" width="120" show-overflow-tooltip />
      <el-table-column prop="author_name" label="作者" width="100" />
      <el-table-column label="状态" width="100" align="center">
        <template #default="{ row }">
          <el-tag :type="statusTag(row.art_status)" size="small">{{ row.art_status_label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="置顶" width="80" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.is_top === 1"
            @change="(val) => onTopChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="view_count" label="浏览" width="80" align="center" />
      <el-table-column prop="published_at" label="发布时间" width="170" />
      <el-table-column prop="updated_at" label="更新时间" width="170" />
      <el-table-column label="操作" width="240" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="goEdit(row)">编辑</a>
          <a class="action-edit" @click="openStatus(row)">状态</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display: flex; justify-content: flex-end">
      <el-pagination
        v-model:current-page="page"
        v-model:page-size="perPage"
        background
        layout="total, prev, pager, next"
        :total="total"
        @current-change="loadData"
        @size-change="loadData"
      />
    </div>

    <el-dialog v-model="statusVisible" title="更新文章状态" width="420px" destroy-on-close>
      <el-form label-width="90px">
        <el-form-item label="状态">
          <el-select v-model="statusForm.art_status" style="width: 100%">
            <el-option
              v-for="(label, value) in options.art_status"
              :key="value"
              :label="label"
              :value="Number(value)"
            />
          </el-select>
        </el-form-item>
        <el-form-item v-if="statusForm.art_status === 6" label="驳回原因">
          <el-input v-model="statusForm.reject_reason" type="textarea" :rows="3" maxlength="512" show-word-limit />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="statusVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="statusSaving" @click="submitStatus">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  deleteArticle,
  fetchArticles,
  updateArticleStatus,
  updateArticleTop,
} from '../../api/article';

const router = useRouter();
const loading = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const categoryOptions = ref([]);
const statusVisible = ref(false);
const statusSaving = ref(false);
const currentId = ref(null);

const filters = reactive({
  keyword: '',
  category_id: '',
  art_status: '',
});

const options = reactive({
  art_status: {},
});

const statusForm = reactive({
  art_status: 1,
  reject_reason: '',
});

function statusTag(status) {
  return (
    {
      1: 'info',
      2: 'warning',
      3: 'success',
      4: 'success',
      5: 'info',
      6: 'danger',
      7: 'danger',
    }[status] || ''
  );
}

function flattenCategories(nodes) {
  return (nodes || []).map((n) => ({
    id: String(n.id),
    cat_name: n.cat_name,
    children: flattenCategories(n.children || []),
  }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchArticles({
      keyword: filters.keyword || undefined,
      category_id: filters.category_id || undefined,
      art_status: filters.art_status === '' ? undefined : filters.art_status,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.art_status) options.art_status = res.data.options.art_status;
    if (res.data?.options?.categories) {
      categoryOptions.value = flattenCategories(res.data.options.categories);
    }
  } catch (e) {
    ElMessage.error(e?.message || '加载文章失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

function goCreate() {
  router.push('/backend/news/articles/create');
}

function goEdit(row) {
  router.push(`/backend/news/articles/${row.id}/edit`);
}

function openStatus(row) {
  currentId.value = row.id;
  statusForm.art_status = row.art_status ?? 1;
  statusForm.reject_reason = row.reject_reason || '';
  statusVisible.value = true;
}

async function submitStatus() {
  statusSaving.value = true;
  try {
    await updateArticleStatus(currentId.value, {
      art_status: statusForm.art_status,
      reject_reason: statusForm.reject_reason || '',
    });
    ElMessage.success('状态更新成功');
    statusVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
  } finally {
    statusSaving.value = false;
  }
}

async function onTopChange(row, enabled) {
  try {
    await updateArticleTop(row.id, { is_top: enabled ? 1 : 0 });
    row.is_top = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '置顶更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除文章「${row.title}」吗？`, '提示', { type: 'warning' });
    await deleteArticle(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.top-flag {
  display: inline-block;
  margin-right: 6px;
  padding: 0 4px;
  font-size: 12px;
  color: #fff;
  background: #e6a23c;
  border-radius: 2px;
  line-height: 18px;
}
</style>
