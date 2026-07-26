<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="编码 / 名称 / 描述"
        @keyup.enter="search"
      />
      <el-select v-model="filters.slot_status" clearable placeholder="状态" style="width: 120px">
        <el-option
          v-for="(label, value) in options.slot_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="filters.is_system" clearable placeholder="系统预设" style="width: 120px">
        <el-option
          v-for="(label, value) in options.is_system"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="search">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="slot_code" label="编码" min-width="160" show-overflow-tooltip />
      <el-table-column prop="slot_name" label="名称" min-width="160" show-overflow-tooltip />
      <el-table-column label="尺寸" width="120" align="center">
        <template #default="{ row }">{{ row.width }} × {{ row.height }}</template>
      </el-table-column>
      <el-table-column prop="max_items" label="最大数量" width="100" align="center" />
      <el-table-column label="系统" width="80" align="center">
        <template #default="{ row }">
          <el-tag :type="row.is_system === 1 ? 'warning' : 'info'" size="small">
            {{ row.is_system === 1 ? '是' : '否' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.slot_status === 1"
            inline-prompt
            active-text="启"
            inactive-text="禁"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="description" label="描述" min-width="160" show-overflow-tooltip />
      <el-table-column prop="updated_at" label="更新时间" width="170" />
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改广告位' : '添加广告位'" width="560px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="编码" prop="slot_code">
          <el-input
            v-model="form.slot_code"
            maxlength="32"
            show-word-limit
            placeholder="如 home_banner_top"
            :disabled="form.is_system === 1"
          />
        </el-form-item>
        <el-form-item label="名称" prop="slot_name">
          <el-input v-model="form.slot_name" maxlength="128" show-word-limit />
        </el-form-item>
        <el-form-item label="宽度" prop="width">
          <el-input-number v-model="form.width" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="高度" prop="height">
          <el-input-number v-model="form.height" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="最大数量" prop="max_items">
          <el-input-number v-model="form.max_items" :min="1" :max="99" />
        </el-form-item>
        <el-form-item label="系统预设" prop="is_system">
          <el-switch v-model="form.is_system" :active-value="1" :inactive-value="0" />
        </el-form-item>
        <el-form-item label="状态" prop="slot_status">
          <el-radio-group v-model="form.slot_status">
            <el-radio v-for="(label, value) in options.slot_status" :key="value" :value="Number(value)">
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="描述" prop="description">
          <el-input v-model="form.description" type="textarea" :rows="2" maxlength="255" show-word-limit />
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
  createAdSlot,
  deleteAdSlot,
  fetchAdSlots,
  updateAdSlot,
  updateAdSlotStatus,
} from '../../api/adSlot';

const loading = ref(false);
const saving = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const formRef = ref();

const filters = reactive({
  keyword: '',
  slot_status: '',
  is_system: '',
});

const options = reactive({
  slot_status: { 0: '禁用', 1: '启用' },
  is_system: { 0: '否', 1: '是' },
});

const form = reactive({
  id: null,
  slot_code: '',
  slot_name: '',
  description: '',
  width: 0,
  height: 0,
  max_items: 1,
  is_system: 0,
  slot_status: 1,
});

const rules = {
  slot_code: [{ required: true, message: '请输入广告位编码', trigger: 'blur' }],
  slot_name: [{ required: true, message: '请输入广告位名称', trigger: 'blur' }],
};

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchAdSlots({
      keyword: filters.keyword || undefined,
      slot_status: filters.slot_status === '' ? undefined : filters.slot_status,
      is_system: filters.is_system === '' ? undefined : filters.is_system,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.slot_status) options.slot_status = res.data.options.slot_status;
    if (res.data?.options?.is_system) options.is_system = res.data.options.is_system;
  } catch (e) {
    ElMessage.error(e?.message || '加载广告位失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.slot_code = row?.slot_code || '';
  form.slot_name = row?.slot_name || '';
  form.description = row?.description || '';
  form.width = row?.width ?? 0;
  form.height = row?.height ?? 0;
  form.max_items = row?.max_items ?? 1;
  form.is_system = row?.is_system ?? 0;
  form.slot_status = row?.slot_status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      slot_code: form.slot_code,
      slot_name: form.slot_name,
      description: form.description || '',
      width: form.width ?? 0,
      height: form.height ?? 0,
      max_items: form.max_items ?? 1,
      is_system: form.is_system ?? 0,
      slot_status: form.slot_status ?? 1,
    };
    if (form.id) {
      await updateAdSlot(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createAdSlot(payload);
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

async function onStatusChange(row, enabled) {
  try {
    await updateAdSlotStatus(row.id, { slot_status: enabled ? 1 : 0 });
    row.slot_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.slot_name}」吗？`, '提示', { type: 'warning' });
    await deleteAdSlot(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
