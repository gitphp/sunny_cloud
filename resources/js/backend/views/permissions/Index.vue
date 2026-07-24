<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="名称 / 标识 / 路径"
        @keyup.enter="loadData"
      />
      <el-select v-model="perType" clearable placeholder="权限类型" style="width: 120px">
        <el-option
          v-for="(label, value) in options.per_type"
          :key="value"
          :label="label"
          :value="value"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table
      v-loading="loading"
      :data="treeData"
      row-key="id"
      border
      default-expand-all
      :tree-props="{ children: 'children' }"
      style="width: 100%"
    >
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column label="权限名称" min-width="200">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            <el-icon v-if="row.per_icon" class="menu-row-icon">
              <component :is="row.per_icon" />
            </el-icon>
            {{ row.per_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="per_code" label="权限标识" min-width="150" show-overflow-tooltip />
      <el-table-column label="类型" width="90" align="center">
        <template #default="{ row }">
          <el-tag :type="typeTag(row.per_type)" size="small">{{ row.per_type_label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="per_path" label="路径" min-width="160" show-overflow-tooltip />
      <el-table-column prop="per_method" label="方法" width="90" align="center" />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.per_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.per_status === 1"
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

    <el-dialog
      v-model="dialogVisible"
      :title="form.id ? '修改权限' : '添加权限'"
      width="580px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级权限" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（顶级权限）"
            style="width: 100%"
            :props="{ label: 'per_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="权限名称" prop="per_name">
          <el-input v-model="form.per_name" placeholder="如：用户删除" />
        </el-form-item>
        <el-form-item label="权限标识" prop="per_code">
          <el-input v-model="form.per_code" placeholder="如：user:delete" />
        </el-form-item>
        <el-form-item label="权限类型" prop="per_type">
          <el-radio-group v-model="form.per_type">
            <el-radio
              v-for="(label, value) in options.per_type"
              :key="value"
              :value="value"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="路径" prop="per_path">
          <el-input v-model="form.per_path" placeholder="路由或 API 路径" />
        </el-form-item>
        <el-form-item v-if="form.per_type === 'api'" label="HTTP方法" prop="per_method">
          <el-select v-model="form.per_method" placeholder="请选择" style="width: 100%">
            <el-option
              v-for="method in options.per_method"
              :key="method"
              :label="method"
              :value="method"
            />
          </el-select>
        </el-form-item>
        <el-form-item v-if="form.per_type === 'menu'" label="图标" prop="per_icon">
          <el-input v-model="form.per_icon" placeholder="Element Plus 图标名，如 User" />
        </el-form-item>
        <el-form-item label="排序号" prop="per_sort">
          <el-input-number v-model="form.per_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="per_status">
          <el-radio-group v-model="form.per_status">
            <el-radio
              v-for="(label, value) in options.per_status"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
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
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createPermission,
  deletePermission,
  fetchPermissions,
  updatePermission,
  updatePermissionSort,
  updatePermissionStatus,
} from '../../api/permission';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const perType = ref();
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({
  per_type: { menu: '菜单', button: '按钮', api: '接口' },
  per_status: { 0: '禁用', 1: '启用' },
  per_method: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
});

const form = reactive({
  id: null,
  parent_id: '0',
  per_name: '',
  per_code: '',
  per_type: 'api',
  per_path: '',
  per_method: 'GET',
  per_icon: '',
  per_sort: 0,
  per_status: 1,
});

const rules = {
  per_name: [{ required: true, message: '请输入权限名称', trigger: 'blur' }],
  per_code: [
    { required: true, message: '请输入权限标识', trigger: 'blur' },
    { pattern: /^[a-zA-Z][a-zA-Z0-9_:.]*$/, message: '格式如 user:delete', trigger: 'blur' },
  ],
  per_type: [{ required: true, message: '请选择类型', trigger: 'change' }],
  per_method: [
    {
      validator: (_r, v, cb) => {
        if (form.per_type === 'api' && !v) cb(new Error('请选择 HTTP 方法'));
        else cb();
      },
      trigger: 'change',
    },
  ],
};

const parentOptions = computed(() => [
  {
    id: '0',
    per_name: '无（顶级权限）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .map((n) => ({
      id: String(n.id),
      per_name: n.per_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

function typeTag(type) {
  return { menu: 'success', button: 'warning', api: 'info' }[type] || 'info';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchPermissions({
      keyword: keyword.value || undefined,
      per_type: perType.value || undefined,
    });
    treeData.value = res.data?.list || [];
    if (res.data?.options) {
      Object.assign(options, res.data.options);
    }
  } catch (e) {
    ElMessage.error(e.message || '加载权限失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.per_name = row?.per_name || '';
  form.per_code = row?.per_code || '';
  form.per_type = row?.per_type || 'api';
  form.per_path = row?.per_path || '';
  form.per_method = row?.per_method || 'GET';
  form.per_icon = row?.per_icon || '';
  form.per_sort = row?.per_sort ?? 0;
  form.per_status = row?.per_status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      parent_id: form.parent_id === '0' || !form.parent_id ? 0 : form.parent_id,
      per_name: form.per_name,
      per_code: form.per_code,
      per_type: form.per_type,
      per_path: form.per_path || '',
      per_method: form.per_type === 'api' ? form.per_method : '',
      per_icon: form.per_type === 'menu' ? form.per_icon || '' : '',
      per_sort: form.per_sort ?? 0,
      per_status: form.per_status ?? 1,
    };
    if (form.id) {
      await updatePermission(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createPermission(payload);
      ElMessage.success('添加成功');
    }
    dialogVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

async function onSortChange(row) {
  try {
    await updatePermissionSort(row.id, { per_sort: Number(row.per_sort) || 0 });
  } catch (e) {
    ElMessage.error(e.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updatePermissionStatus(row.id, { per_status: enabled ? 1 : 0 });
    row.per_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除权限「${row.per_name}」吗？`, '提示', { type: 'warning' });
    await deletePermission(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e.message || '删除失败');
  }
}

onMounted(loadData);
</script>
