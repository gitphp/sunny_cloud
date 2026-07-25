<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="名称 / 编码"
        @keyup.enter="loadData"
      />
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
      <el-table-column label="部门名称" min-width="200">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            {{ row.dept_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="dept_code" label="编码" min-width="120" show-overflow-tooltip />
      <el-table-column prop="dept_level" label="层级" width="70" align="center" />
      <el-table-column prop="dept_phone" label="电话" width="120" show-overflow-tooltip />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.dept_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.dept_status === 1"
            inline-prompt
            active-text="启"
            inactive-text="禁"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="220" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <a class="action-edit" @click="openLeaders(row)">负责人</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog
      v-model="dialogVisible"
      :title="form.id ? '修改部门' : '添加部门'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级部门" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（根部门）"
            style="width: 100%"
            :props="{ label: 'dept_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="部门名称" prop="dept_name">
          <el-input v-model="form.dept_name" placeholder="如：技术部" />
        </el-form-item>
        <el-form-item label="部门编码" prop="dept_code">
          <el-input v-model="form.dept_code" placeholder="如：TECH" />
        </el-form-item>
        <el-form-item label="联系电话" prop="dept_phone">
          <el-input v-model="form.dept_phone" placeholder="选填" />
        </el-form-item>
        <el-form-item label="排序号" prop="dept_sort">
          <el-input-number v-model="form.dept_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="dept_status">
          <el-radio-group v-model="form.dept_status">
            <el-radio
              v-for="(label, value) in options.dept_status"
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

    <el-dialog v-model="leaderVisible" title="部门负责人" width="640px" destroy-on-close>
      <div class="page-toolbar" style="margin-bottom: 12px">
        <el-button class="btn-primary-teal" size="small" :icon="Plus" @click="addLeaderRow">添加负责人</el-button>
      </div>
      <el-table :data="leaderRows" border>
        <el-table-column label="用户" min-width="200">
          <template #default="{ row }">
            <el-select v-model="row.user_id" filterable clearable placeholder="选择用户" style="width: 100%">
              <el-option
                v-for="u in userOptions"
                :key="u.id"
                :label="`${u.nick_name || u.user_name} (${u.user_name})`"
                :value="String(u.id)"
              />
            </el-select>
          </template>
        </el-table-column>
        <el-table-column label="类型" width="160">
          <template #default="{ row }">
            <el-select v-model="row.role_type" style="width: 100%">
              <el-option
                v-for="(label, value) in options.role_type"
                :key="value"
                :label="label"
                :value="Number(value)"
              />
            </el-select>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="90" align="center">
          <template #default="{ $index }">
            <el-button class="btn-danger-orange" size="small" @click="leaderRows.splice($index, 1)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <template #footer>
        <el-button @click="leaderVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="leaderSaving" @click="submitLeaders">保存</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createHrDepartment,
  deleteHrDepartment,
  fetchHrDepartmentLeaders,
  fetchHrDepartments,
  syncHrDepartmentLeaders,
  updateHrDepartment,
  updateHrDepartmentSort,
  updateHrDepartmentStatus,
} from '../../api/hrDepartment';
import { fetchUsers } from '../../api/user';

const loading = ref(false);
const saving = ref(false);
const leaderSaving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const leaderVisible = ref(false);
const formRef = ref();
const currentDeptId = ref(null);
const leaderRows = ref([]);
const userOptions = ref([]);
const options = reactive({
  dept_status: { 0: '禁用', 1: '启用' },
  role_type: { 1: '主要负责人', 2: '次要负责人' },
});

const form = reactive({
  id: null,
  parent_id: '0',
  dept_name: '',
  dept_code: '',
  dept_phone: '',
  dept_sort: 0,
  dept_status: 1,
});

const rules = {
  dept_name: [{ required: true, message: '请输入部门名称', trigger: 'blur' }],
  dept_code: [{ required: true, message: '请输入部门编码', trigger: 'blur' }],
};

const parentOptions = computed(() => [
  {
    id: '0',
    dept_name: '无（根部门）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .map((n) => ({
      id: String(n.id),
      dept_name: n.dept_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchHrDepartments({ keyword: keyword.value || undefined });
    treeData.value = res.data?.list || [];
    if (res.data?.options) Object.assign(options, res.data.options);
  } catch (e) {
    ElMessage.error(e?.message || '加载部门失败');
  } finally {
    loading.value = false;
  }
}

async function loadUsers() {
  try {
    const res = await fetchUsers({ per_page: 200 });
    userOptions.value = res.data?.list || [];
  } catch (_) {
    userOptions.value = [];
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.dept_name = row?.dept_name || '';
  form.dept_code = row?.dept_code || '';
  form.dept_phone = row?.dept_phone || '';
  form.dept_sort = row?.dept_sort ?? 0;
  form.dept_status = row?.dept_status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      parent_id: form.parent_id === '0' || !form.parent_id ? 0 : form.parent_id,
      dept_name: form.dept_name,
      dept_code: form.dept_code,
      dept_phone: form.dept_phone || '',
      dept_sort: form.dept_sort ?? 0,
      dept_status: form.dept_status ?? 1,
    };
    if (form.id) {
      await updateHrDepartment(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createHrDepartment(payload);
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
    await updateHrDepartmentSort(row.id, { dept_sort: Number(row.dept_sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateHrDepartmentStatus(row.id, { dept_status: enabled ? 1 : 0 });
    row.dept_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function openLeaders(row) {
  currentDeptId.value = row.id;
  await loadUsers();
  try {
    const res = await fetchHrDepartmentLeaders(row.id);
    leaderRows.value = (res.data || []).map((item) => ({
      user_id: String(item.user_id),
      role_type: item.role_type ?? 1,
    }));
  } catch (_) {
    leaderRows.value = [];
  }
  leaderVisible.value = true;
}

function addLeaderRow() {
  leaderRows.value.push({ user_id: '', role_type: 1 });
}

async function submitLeaders() {
  leaderSaving.value = true;
  try {
    const leaders = leaderRows.value
      .filter((r) => r.user_id)
      .map((r) => ({ user_id: r.user_id, role_type: Number(r.role_type) || 1 }));
    await syncHrDepartmentLeaders(currentDeptId.value, { leaders });
    ElMessage.success('负责人保存成功');
    leaderVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    leaderSaving.value = false;
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除部门「${row.dept_name}」吗？`, '提示', { type: 'warning' });
    await deleteHrDepartment(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
