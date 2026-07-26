<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="姓名 / 电话 / 邮箱 / 标题"
        @keyup.enter="search"
      />
      <el-select v-model="filters.fb_status" clearable placeholder="处理状态" style="width: 130px">
        <el-option
          v-for="(label, value) in options.fb_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="search">搜索</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="fb_title" label="标题" min-width="180" show-overflow-tooltip />
      <el-table-column prop="fb_name" label="联系人" width="100" />
      <el-table-column prop="fb_phone" label="电话" width="120" />
      <el-table-column prop="fb_email" label="邮箱" min-width="140" show-overflow-tooltip />
      <el-table-column prop="fb_company" label="公司" width="120" show-overflow-tooltip />
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-tag :type="row.fb_status === 1 ? 'success' : 'warning'" size="small">
            {{ row.fb_status_label }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="ip" label="IP" width="120" />
      <el-table-column prop="created_at" label="提交时间" width="170" />
      <el-table-column label="操作" width="180" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openDetail(row)">详情</a>
          <a class="action-edit" @click="openReply(row)">回复</a>
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

    <el-drawer v-model="detailVisible" title="留言详情" size="480px" destroy-on-close>
      <template v-if="current">
        <el-descriptions :column="1" border>
          <el-descriptions-item label="标题">{{ current.fb_title }}</el-descriptions-item>
          <el-descriptions-item label="联系人">{{ current.fb_name }}</el-descriptions-item>
          <el-descriptions-item label="电话">{{ current.fb_phone || '-' }}</el-descriptions-item>
          <el-descriptions-item label="邮箱">{{ current.fb_email || '-' }}</el-descriptions-item>
          <el-descriptions-item label="公司">{{ current.fb_company || '-' }}</el-descriptions-item>
          <el-descriptions-item label="状态">{{ current.fb_status_label }}</el-descriptions-item>
          <el-descriptions-item label="IP">{{ current.ip || '-' }}</el-descriptions-item>
          <el-descriptions-item label="提交时间">{{ current.created_at }}</el-descriptions-item>
          <el-descriptions-item label="留言内容">
            <div class="content-block">{{ current.fb_content }}</div>
          </el-descriptions-item>
          <el-descriptions-item label="回复内容">
            <div class="content-block">{{ current.reply_content || '-' }}</div>
          </el-descriptions-item>
          <el-descriptions-item label="回复时间">{{ current.replied_at || '-' }}</el-descriptions-item>
        </el-descriptions>
      </template>
    </el-drawer>

    <el-dialog v-model="replyVisible" title="回复留言" width="520px" destroy-on-close>
      <el-form label-width="80px">
        <el-form-item label="标题">
          <span>{{ replyForm.title }}</span>
        </el-form-item>
        <el-form-item label="回复" required>
          <el-input v-model="replyForm.reply_content" type="textarea" :rows="5" maxlength="5000" show-word-limit />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="replyVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="replySaving" @click="submitReply">提交回复</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search } from '@element-plus/icons-vue';
import { deleteFeedback, fetchFeedback, fetchFeedbacks, replyFeedback } from '../../api/feedback';

const loading = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const detailVisible = ref(false);
const replyVisible = ref(false);
const replySaving = ref(false);
const current = ref(null);
const currentId = ref(null);

const filters = reactive({
  keyword: '',
  fb_status: '',
});

const options = reactive({
  fb_status: { 0: '未处理', 1: '已处理' },
});

const replyForm = reactive({
  title: '',
  reply_content: '',
});

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchFeedbacks({
      keyword: filters.keyword || undefined,
      fb_status: filters.fb_status === '' ? undefined : filters.fb_status,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.fb_status) options.fb_status = res.data.options.fb_status;
  } catch (e) {
    ElMessage.error(e?.message || '加载留言失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

async function openDetail(row) {
  try {
    const res = await fetchFeedback(row.id);
    current.value = res.data;
    detailVisible.value = true;
  } catch (e) {
    ElMessage.error(e?.message || '加载详情失败');
  }
}

function openReply(row) {
  currentId.value = row.id;
  replyForm.title = row.fb_title;
  replyForm.reply_content = row.reply_content || '';
  replyVisible.value = true;
}

async function submitReply() {
  if (!replyForm.reply_content.trim()) {
    ElMessage.warning('请填写回复内容');
    return;
  }
  replySaving.value = true;
  try {
    await replyFeedback(currentId.value, { reply_content: replyForm.reply_content });
    ElMessage.success('回复成功');
    replyVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '回复失败');
  } finally {
    replySaving.value = false;
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除留言「${row.fb_title}」吗？`, '提示', { type: 'warning' });
    await deleteFeedback(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.content-block {
  white-space: pre-wrap;
  word-break: break-word;
  line-height: 1.6;
}
</style>
