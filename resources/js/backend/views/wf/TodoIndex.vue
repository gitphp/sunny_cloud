<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="query.keyword" class="search-input" clearable placeholder="标题 / 单号" @keyup.enter="loadData" />
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column prop="apply_no" label="单号" min-width="140" />
      <el-table-column prop="title" label="标题" min-width="200" show-overflow-tooltip />
      <el-table-column prop="type_name" label="类型" width="120" />
      <el-table-column prop="apply_user_name" label="发起人" width="110" />
      <el-table-column prop="current_node_name" label="当前节点" width="120" />
      <el-table-column prop="created_at" label="发起时间" min-width="160" />
      <el-table-column label="操作" width="100" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="goDetail(row)">审批</a>
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
import { fetchTodoApplies } from '../../api/wfFlowApply';

const router = useRouter();
const loading = ref(false);
const list = ref([]);
const total = ref(0);
const query = reactive({ keyword: '', page: 1, per_page: 15 });

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchTodoApplies({
      keyword: query.keyword || undefined,
      page: query.page,
      per_page: query.per_page,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function goDetail(row) {
  router.push(`/backend/wf/applies/${row.id}`);
}

onMounted(loadData);
</script>
