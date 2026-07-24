<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="名称 / 路径 / 权限标识"
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
      <el-table-column label="菜单名称" min-width="200">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            <el-icon v-if="row.menu_icon" class="menu-row-icon">
              <component :is="row.menu_icon" />
            </el-icon>
            {{ row.menu_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="menu_path" label="路由路径" min-width="180" show-overflow-tooltip />
      <el-table-column prop="component" label="组件" min-width="140" show-overflow-tooltip />
      <el-table-column prop="permission_code" label="权限标识" min-width="140" show-overflow-tooltip />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.menu_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.menu_status === 1"
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
      :title="form.id ? '修改菜单' : '添加菜单'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级菜单" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（顶级菜单）"
            style="width: 100%"
            :props="{ label: 'menu_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="菜单名称" prop="menu_name">
          <el-input v-model="form.menu_name" placeholder="如：用户管理" />
        </el-form-item>
        <el-form-item label="菜单图标" prop="menu_icon">
          <el-input v-model="form.menu_icon" placeholder="Element Plus 图标名，如 User" />
        </el-form-item>
        <el-form-item label="路由路径" prop="menu_path">
          <el-input v-model="form.menu_path" placeholder="如：/backend/users" />
        </el-form-item>
        <el-form-item label="组件路径" prop="component">
          <el-input v-model="form.component" placeholder="如：users/Index" />
        </el-form-item>
        <el-form-item label="权限标识" prop="permission_code">
          <el-input v-model="form.permission_code" placeholder="如：user.view" />
        </el-form-item>
        <el-form-item label="排序号" prop="menu_sort">
          <el-input-number v-model="form.menu_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="menu_status">
          <el-radio-group v-model="form.menu_status">
            <el-radio
              v-for="(label, value) in options.menu_status"
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
  createMenu,
  deleteMenu,
  fetchMenus,
  updateMenu,
  updateMenuSort,
  updateMenuStatus,
} from '../../api/menu';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({ menu_status: { 0: '禁用', 1: '启用' } });

const form = reactive({
  id: null,
  parent_id: '0',
  menu_name: '',
  menu_icon: '',
  menu_path: '',
  component: '',
  permission_code: '',
  menu_sort: 0,
  menu_status: 1,
});

const rules = {
  menu_name: [{ required: true, message: '请输入菜单名称', trigger: 'blur' }],
};

const parentOptions = computed(() => [
  {
    id: '0',
    menu_name: '无（顶级菜单）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .map((n) => ({
      id: String(n.id),
      menu_name: n.menu_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchMenus({ keyword: keyword.value || undefined });
    treeData.value = res.data?.list || [];
    if (res.data?.options?.menu_status) {
      options.menu_status = res.data.options.menu_status;
    }
  } catch (e) {
    ElMessage.error(e.message || '加载菜单失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.menu_name = row?.menu_name || '';
  form.menu_icon = row?.menu_icon || '';
  form.menu_path = row?.menu_path || '';
  form.component = row?.component || '';
  form.permission_code = row?.permission_code || '';
  form.menu_sort = row?.menu_sort ?? 0;
  form.menu_status = row?.menu_status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      parent_id: form.parent_id === '0' || form.parent_id === 0 || !form.parent_id ? 0 : form.parent_id,
      menu_name: form.menu_name,
      menu_icon: form.menu_icon || '',
      menu_path: form.menu_path || '',
      component: form.component || '',
      permission_code: form.permission_code || '',
      menu_sort: form.menu_sort ?? 0,
      menu_status: form.menu_status ?? 1,
    };
    if (form.id) {
      await updateMenu(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createMenu(payload);
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
    await updateMenuSort(row.id, { menu_sort: Number(row.menu_sort) || 0 });
  } catch (e) {
    ElMessage.error(e.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateMenuStatus(row.id, { menu_status: enabled ? 1 : 0 });
    row.menu_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除菜单「${row.menu_name}」吗？`, '提示', { type: 'warning' });
    await deleteMenu(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e.message || '删除失败');
  }
}

onMounted(loadData);
</script>
