<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="query.keyword" class="search-input" clearable placeholder="流程名称" @keyup.enter="loadData" />
      <el-select v-model="query.flow_type_id" clearable placeholder="流程类型" style="width: 160px; margin-right: 8px">
        <el-option v-for="t in typeOptions" :key="t.id" :label="t.type_name" :value="String(t.id)" />
      </el-select>
      <el-select v-model="query.is_publish" clearable placeholder="发布状态" style="width: 120px; margin-right: 8px">
        <el-option v-for="(label, value) in options.is_publish" :key="value" :label="label" :value="Number(value)" />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="goCreate">新增流程</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="flow_name" label="流程名称" min-width="180" show-overflow-tooltip />
      <el-table-column prop="type_name" label="流程类型" min-width="120" />
      <el-table-column prop="version" label="版本" width="80" align="center" />
      <el-table-column prop="is_publish_label" label="状态" width="100" align="center" />
      <el-table-column prop="created_at" label="创建时间" min-width="160" />
      <el-table-column label="操作" width="240" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="goEdit(row)">配置</a>
          <a v-if="row.is_publish !== 1" class="action-edit" @click="onPublish(row)">发布</a>
          <a v-else class="action-edit" @click="onUnpublish(row)">设草稿</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display: flex; justify-content: flex-end">
      <el-pagination
        v-model:current-page="query.page"
        v-model:page-size="query.per_page"
        background
        layout="total, prev, pager, next"
        :total="total"
        @current-change="loadData"
        @size-change="loadData"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  deleteWfFlowDefinition,
  fetchWfFlowDefinitions,
  publishWfFlowDefinition,
  unpublishWfFlowDefinition,
} from '../../api/wfFlowDefinition';
import { fetchWfFlowTypeOptions } from '../../api/wfFlowType';

const router = useRouter();
const loading = ref(false);
const list = ref([]);
const total = ref(0);
const typeOptions = ref([]);
const options = reactive({ is_publish: { 0: '草稿', 1: '已发布' } });
const query = reactive({
  keyword: '',
  flow_type_id: undefined,
  is_publish: undefined,
  page: 1,
  per_page: 15,
});

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchWfFlowDefinitions({
      keyword: query.keyword || undefined,
      flow_type_id: query.flow_type_id || undefined,
      is_publish: query.is_publish,
      page: query.page,
      per_page: query.per_page,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.is_publish) options.is_publish = res.data.options.is_publish;
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

async function loadTypes() {
  try {
    const res = await fetchWfFlowTypeOptions();
    typeOptions.value = res.data || [];
  } catch (_) {
    typeOptions.value = [];
  }
}

function goCreate() {
  router.push('/backend/wf/flow-definitions/create');
}

function goEdit(row) {
  router.push(`/backend/wf/flow-definitions/${row.id}/edit`);
}

async function onPublish(row) {
  try {
    await publishWfFlowDefinition(row.id);
    ElMessage.success('发布成功');
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '发布失败');
  }
}

async function onUnpublish(row) {
  try {
    await unpublishWfFlowDefinition(row.id);
    ElMessage.success('已设为草稿');
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '操作失败');
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除流程「${row.flow_name}」吗？`, '提示', { type: 'warning' });
    await deleteWfFlowDefinition(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(async () => {
  await loadTypes();
  await loadData();
});
</script>
