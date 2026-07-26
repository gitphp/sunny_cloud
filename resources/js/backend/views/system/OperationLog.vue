<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar operation-log-toolbar">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="操作人 / 业务标签 / URL"
        @keyup.enter="search"
      />
      <el-select v-model="filters.biz_type" clearable placeholder="业务模块" style="width: 140px">
        <el-option v-for="(label, value) in options.biz_type" :key="value" :label="label" :value="value" />
      </el-select>
      <el-select v-model="filters.action" clearable placeholder="操作类型" style="width: 120px">
        <el-option v-for="(label, value) in options.action" :key="value" :label="label" :value="value" />
      </el-select>
      <el-select v-model="filters.operator_status" clearable placeholder="状态" style="width: 100px">
        <el-option v-for="(label, value) in options.operator_status" :key="value" :label="label" :value="Number(value)" />
      </el-select>
      <el-date-picker
        v-model="dateRange"
        type="daterange"
        value-format="YYYY-MM-DD"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        style="width: 260px"
      />
      <el-button class="btn-primary-teal" :icon="Search" @click="search">搜索</el-button>
      <el-button @click="resetFilters">重置</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="created_at" label="时间" width="170" />
      <el-table-column prop="operator_name" label="操作人" width="120" show-overflow-tooltip />
      <el-table-column prop="biz_type_label" label="模块" width="100" align="center" />
      <el-table-column prop="action_label" label="操作" width="80" align="center">
        <template #default="{ row }">
          <el-tag :type="actionTagType(row.action)" size="small">{{ row.action_label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="biz_label" label="业务对象" min-width="140" show-overflow-tooltip />
      <el-table-column prop="activity_type" label="活动类型" width="150" show-overflow-tooltip />
      <el-table-column label="状态" width="80" align="center">
        <template #default="{ row }">
          <el-tag :type="row.operator_status === 1 ? 'success' : 'danger'" size="small">
            {{ row.operator_status_label }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="client_ip" label="IP" width="130" show-overflow-tooltip />
      <el-table-column label="操作" width="90" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openDetail(row)">详情</a>
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

    <el-drawer v-model="detailVisible" title="操作日志详情" size="520px" destroy-on-close>
      <template v-if="current">
        <el-descriptions :column="1" border>
          <el-descriptions-item label="时间">{{ current.created_at || '-' }}</el-descriptions-item>
          <el-descriptions-item label="操作人">
            {{ current.operator_name || '-' }}（ID: {{ current.operator_id }}）
          </el-descriptions-item>
          <el-descriptions-item label="模块">{{ current.biz_type_label || current.biz_type }}</el-descriptions-item>
          <el-descriptions-item label="操作">{{ current.action_label }} / {{ current.activity_type }}</el-descriptions-item>
          <el-descriptions-item label="业务对象">
            {{ current.biz_label || '-' }}（ID: {{ current.biz_id }}）
          </el-descriptions-item>
          <el-descriptions-item label="状态">{{ current.operator_status_label }}</el-descriptions-item>
          <el-descriptions-item label="错误信息">{{ current.error_msg || '-' }}</el-descriptions-item>
          <el-descriptions-item label="IP">{{ current.client_ip || '-' }}</el-descriptions-item>
          <el-descriptions-item label="URL">{{ current.request_url || '-' }}</el-descriptions-item>
          <el-descriptions-item label="方法">{{ current.method_fun || '-' }}</el-descriptions-item>
          <el-descriptions-item label="UA">{{ current.user_agent || '-' }}</el-descriptions-item>
        </el-descriptions>

        <div class="json-block">
          <h4>修改前</h4>
          <pre>{{ formatJson(current.old_value) }}</pre>
        </div>
        <div class="json-block">
          <h4>修改后</h4>
          <pre>{{ formatJson(current.new_value) }}</pre>
        </div>
      </template>
    </el-drawer>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage } from 'element-plus';
import { Search } from '@element-plus/icons-vue';
import { fetchOperationLog, fetchOperationLogs } from '../../api/operationLog';

const loading = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dateRange = ref([]);
const detailVisible = ref(false);
const current = ref(null);

const filters = reactive({
  keyword: '',
  biz_type: '',
  action: '',
  operator_status: '',
});

const options = reactive({
  action: {},
  operator_status: {},
  biz_type: {},
});

function actionTagType(action) {
  return (
    {
      INSERT: 'success',
      UPDATE: 'warning',
      DELETE: 'danger',
      LOGIN: 'info',
    }[action] || ''
  );
}

function formatJson(value) {
  if (value == null || value === '') return '-';
  try {
    return JSON.stringify(value, null, 2);
  } catch {
    return String(value);
  }
}

async function loadData() {
  loading.value = true;
  try {
    const params = {
      keyword: filters.keyword || undefined,
      biz_type: filters.biz_type || undefined,
      action: filters.action || undefined,
      operator_status: filters.operator_status === '' ? undefined : filters.operator_status,
      date_from: dateRange.value?.[0] || undefined,
      date_to: dateRange.value?.[1] || undefined,
      page: page.value,
      per_page: perPage.value,
    };
    const res = await fetchOperationLogs(params);
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.action) options.action = res.data.options.action;
    if (res.data?.options?.operator_status) options.operator_status = res.data.options.operator_status;
    if (res.data?.options?.biz_type) options.biz_type = res.data.options.biz_type;
  } catch (e) {
    ElMessage.error(e?.message || '加载操作日志失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

function resetFilters() {
  filters.keyword = '';
  filters.biz_type = '';
  filters.action = '';
  filters.operator_status = '';
  dateRange.value = [];
  search();
}

async function openDetail(row) {
  try {
    const res = await fetchOperationLog(row.id);
    current.value = res.data;
    detailVisible.value = true;
  } catch (e) {
    ElMessage.error(e?.message || '加载详情失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.operation-log-toolbar {
  flex-wrap: wrap;
  gap: 8px;
}

.json-block {
  margin-top: 16px;
}

.json-block h4 {
  margin: 0 0 8px;
  font-size: 14px;
  color: #606266;
}

.json-block pre {
  margin: 0;
  padding: 12px;
  background: #f5f7fa;
  border-radius: 4px;
  font-size: 12px;
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-all;
  max-height: 240px;
  overflow: auto;
}
</style>
