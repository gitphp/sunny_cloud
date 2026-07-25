<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="keyword" class="search-input" clearable placeholder="名称 / 编码" @keyup.enter="loadData" />
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="type_name" label="类型名称" min-width="160" />
      <el-table-column prop="type_code" label="编码" min-width="140" />
      <el-table-column prop="icon" label="图标" width="120" />
      <el-table-column label="排序" width="110" align="center">
        <template #default="{ row }">
          <el-input v-model.number="row.sort" class="sort-input" size="small" @change="onSortChange(row)" />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.status === 1"
            inline-prompt
            active-text="启"
            inactive-text="禁"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="160" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改流程类型' : '添加流程类型'" width="520px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="类型名称" prop="type_name">
          <el-input v-model="form.type_name" placeholder="如：请假审批" maxlength="32" />
        </el-form-item>
        <el-form-item label="类型编码" prop="type_code">
          <el-input v-model="form.type_code" placeholder="如：leave" maxlength="32" />
        </el-form-item>
        <el-form-item label="图标" prop="icon">
          <el-input v-model="form.icon" placeholder="Element Plus 图标名，如 Calendar" />
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input-number v-model="form.sort" :min="0" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio v-for="(label, value) in options.status" :key="value" :value="Number(value)">{{ label }}</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createWfFlowType,
  deleteWfFlowType,
  fetchWfFlowTypes,
  updateWfFlowType,
  updateWfFlowTypeSort,
  updateWfFlowTypeStatus,
} from '../../api/wfFlowType';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({ status: { 0: '禁用', 1: '启用' } });
const form = reactive({ id: null, type_name: '', type_code: '', icon: '', sort: 0, status: 1 });
const rules = {
  type_name: [{ required: true, message: '请输入类型名称', trigger: 'blur' }],
  type_code: [
    { required: true, message: '请输入类型编码', trigger: 'blur' },
    { pattern: /^[a-z][a-z0-9_]*$/, message: '编码需小写字母开头', trigger: 'blur' },
  ],
};

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchWfFlowTypes({ keyword: keyword.value || undefined, page: page.value, per_page: perPage.value });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.status) options.status = res.data.options.status;
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.type_name = row?.type_name || '';
  form.type_code = row?.type_code || '';
  form.icon = row?.icon || '';
  form.sort = row?.sort ?? 0;
  form.status = row?.status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = { ...form };
    delete payload.id;
    if (form.id) {
      await updateWfFlowType(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createWfFlowType(payload);
      ElMessage.success('添加成功');
    }
    dialogVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

async function onSortChange(row) {
  try {
    await updateWfFlowTypeSort(row.id, { sort: Number(row.sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateWfFlowTypeStatus(row.id, { status: enabled ? 1 : 0 });
    row.status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除流程类型「${row.type_name}」吗？`, '提示', { type: 'warning' });
    await deleteWfFlowType(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
