<template>
  <div v-loading="loading" class="admin-page-card product-form-page">
    <div class="page-toolbar" style="justify-content: space-between">
      <h3 style="margin: 0; font-size: 18px">申请详情</h3>
      <div>
        <el-button @click="goBack">返回</el-button>
        <el-button v-if="detail.permissions?.can_edit" @click="goEdit">编辑</el-button>
        <el-button v-if="detail.permissions?.can_withdraw" @click="onWithdraw">撤回</el-button>
        <el-button v-if="detail.permissions?.can_approve" class="btn-primary-teal" @click="openAction('agree')">同意</el-button>
        <el-button v-if="detail.permissions?.can_reject" class="btn-danger-orange" @click="openAction('reject')">驳回</el-button>
        <el-button v-if="detail.permissions?.can_transfer" @click="openAction('transfer')">转审</el-button>
        <el-button v-if="detail.permissions?.can_add_sign" @click="openAction('add_sign')">加签</el-button>
      </div>
    </div>

    <el-descriptions :column="2" border style="margin-top: 12px">
      <el-descriptions-item label="单号">{{ detail.apply_no }}</el-descriptions-item>
      <el-descriptions-item label="状态">{{ detail.apply_status_label }}</el-descriptions-item>
      <el-descriptions-item label="标题" :span="2">{{ detail.title }}</el-descriptions-item>
      <el-descriptions-item label="类型">{{ detail.type_name }}</el-descriptions-item>
      <el-descriptions-item label="发起人">{{ detail.apply_user_name }}</el-descriptions-item>
      <el-descriptions-item label="当前节点">{{ detail.current_node_name || '-' }}</el-descriptions-item>
      <el-descriptions-item label="当前审批人">{{ detail.current_approve_name || '-' }}</el-descriptions-item>
      <el-descriptions-item label="备注" :span="2">{{ detail.remark || '-' }}</el-descriptions-item>
    </el-descriptions>

    <el-divider content-position="left">表单内容</el-divider>
    <el-descriptions :column="1" border>
      <el-descriptions-item v-for="f in detail.forms || []" :key="f.field_key" :label="f.field_name">
        {{ formatValue(detail.form_data?.[f.field_key]) }}
      </el-descriptions-item>
    </el-descriptions>

    <el-divider content-position="left">审批记录</el-divider>
    <el-timeline v-if="(detail.records || []).length">
      <el-timeline-item
        v-for="r in detail.records"
        :key="r.id"
        :timestamp="r.operate_at"
        placement="top"
      >
        <div>
          <strong>{{ r.action_type_label }}</strong>
          · {{ r.approve_user_name }}
          <span v-if="r.node_name">（{{ r.node_name }}）</span>
          <span v-if="r.target_user_name"> → {{ r.target_user_name }}</span>
        </div>
        <div v-if="r.approve_opinion" style="color: #666; margin-top: 4px">{{ r.approve_opinion }}</div>
      </el-timeline-item>
    </el-timeline>
    <el-empty v-else description="暂无审批记录" :image-size="60" />

    <el-divider content-position="left">抄送人</el-divider>
    <div v-if="(detail.cc_users || []).length">
      <el-tag v-for="c in detail.cc_users" :key="c.id" style="margin-right: 8px; margin-bottom: 8px">
        {{ c.cc_user_name }}（{{ c.is_read_label }}）
      </el-tag>
    </div>
    <span v-else style="color: #999">无</span>

    <el-dialog v-model="actionVisible" :title="actionTitle" width="480px" destroy-on-close>
      <el-form label-width="90px">
        <el-form-item v-if="['transfer', 'add_sign'].includes(actionType)" label="目标人" required>
          <el-select v-model="actionForm.target_user_id" filterable style="width: 100%">
            <el-option
              v-for="u in userOptions"
              :key="u.id"
              :label="u.nick_name || u.user_name"
              :value="String(u.id)"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="审批意见">
          <el-input v-model="actionForm.approve_opinion" type="textarea" :rows="3" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="actionVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="acting" @click="confirmAction">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import {
  addSignWfApply,
  agreeWfApply,
  fetchWfApply,
  rejectWfApply,
  transferWfApply,
  withdrawWfApply,
} from '../../api/wfFlowApply';
import { fetchUsers } from '../../api/user';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const acting = ref(false);
const detail = ref({});
const userOptions = ref([]);
const actionVisible = ref(false);
const actionType = ref('agree');
const actionForm = reactive({ approve_opinion: '', target_user_id: '' });

const actionTitle = computed(() =>
  ({ agree: '同意', reject: '驳回', transfer: '转审', add_sign: '加签' })[actionType.value] || '操作'
);

function formatValue(v) {
  if (v === null || v === undefined || v === '') return '-';
  if (Array.isArray(v)) return v.join(', ');
  if (typeof v === 'object') return JSON.stringify(v);
  return String(v);
}

async function loadDetail() {
  loading.value = true;
  try {
    const res = await fetchWfApply(route.params.id);
    detail.value = res.data || {};
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function openAction(type) {
  actionType.value = type;
  actionForm.approve_opinion = '';
  actionForm.target_user_id = '';
  actionVisible.value = true;
}

async function confirmAction() {
  if (['transfer', 'add_sign'].includes(actionType.value) && !actionForm.target_user_id) {
    ElMessage.warning('请选择目标人');
    return;
  }
  acting.value = true;
  try {
    const id = route.params.id;
    const data = {
      approve_opinion: actionForm.approve_opinion || '',
      target_user_id: actionForm.target_user_id || undefined,
    };
    let res;
    if (actionType.value === 'agree') res = await agreeWfApply(id, data);
    else if (actionType.value === 'reject') res = await rejectWfApply(id, data);
    else if (actionType.value === 'transfer') res = await transferWfApply(id, data);
    else res = await addSignWfApply(id, data);
    detail.value = res.data || {};
    actionVisible.value = false;
    ElMessage.success('操作成功');
  } catch (e) {
    ElMessage.error(e?.message || '操作失败');
  } finally {
    acting.value = false;
  }
}

async function onWithdraw() {
  try {
    await ElMessageBox.confirm('确定撤回该申请吗？', '提示', { type: 'warning' });
    const res = await withdrawWfApply(route.params.id);
    detail.value = res.data || {};
    ElMessage.success('已撤回');
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '撤回失败');
  }
}

function goEdit() {
  router.push(`/backend/wf/applies/${route.params.id}/edit`);
}

function goBack() {
  if (window.history.length > 1) router.back();
  else router.push('/backend/wf/applies');
}

onMounted(async () => {
  try {
    const users = await fetchUsers({ per_page: 200 });
    userOptions.value = users.data?.list || [];
  } catch (_) {}
  await loadDetail();
});
</script>
