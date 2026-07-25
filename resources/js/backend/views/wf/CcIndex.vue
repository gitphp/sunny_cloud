<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="query.keyword" class="search-input" clearable placeholder="标题 / 单号" @keyup.enter="loadData" />
      <el-select v-model="query.is_read" clearable placeholder="已读状态" style="width: 120px; margin-right: 8px">
        <el-option v-for="(label, value) in options.is_read" :key="value" :label="label" :value="Number(value)" />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column prop="apply_no" label="单号" min-width="140" />
      <el-table-column prop="title" label="标题" min-width="200" show-overflow-tooltip />
      <el-table-column prop="type_name" label="类型" width="120" />
      <el-table-column prop="apply_user_name" label="发起人" width="110" />
      <el-table-column prop="apply_status_label" label="单据状态" width="100" align="center" />
      <el-table-column prop="is_read_label" label="阅读" width="80" align="center" />
      <el-table-column prop="created_at" label="抄送时间" min-width="160" />
      <el-table-column label="操作" width="140" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="goDetail(row)">查看</a>
          <a v-if="row.is_read !== 1" class="action-edit" @click="onRead(row)">标已读</a>
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
import { ElMessage } from 'element-plus';
import { Search } from '@element-plus/icons-vue';
import { fetchCcApplies, markCcRead } from '../../api/wfFlowApply';

const router = useRouter();
const loading = ref(false);
const list = ref([]);
const total = ref(0);
const options = reactive({ is_read: { 0: '未读', 1: '已读' } });
const query = reactive({ keyword: '', is_read: undefined, page: 1, per_page: 15 });

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchCcApplies({
      keyword: query.keyword || undefined,
      is_read: query.is_read,
      page: query.page,
      per_page: query.per_page,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.is_read) options.is_read = res.data.options.is_read;
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

async function onRead(row) {
  try {
    await markCcRead(row.id);
    ElMessage.success('已标记已读');
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '操作失败');
  }
}

function goDetail(row) {
  router.push(`/backend/wf/applies/${row.apply_id}`);
}

onMounted(loadData);
</script>
