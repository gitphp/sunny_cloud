<template>
  <div class="admin-page-card">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="query.keyword"
        class="search-input"
        clearable
        placeholder="角色名称 / 标识 / 备注"
        @keyup.enter="loadData"
      />
      <el-select v-model="query.role_type" clearable placeholder="角色类型" style="width: 130px">
        <el-option
          v-for="(label, value) in options.role_type"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="query.role_status" clearable placeholder="状态" style="width: 110px">
        <el-option
          v-for="(label, value) in options.role_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="query.data_scope" clearable placeholder="数据权限" style="width: 150px">
        <el-option
          v-for="(label, value) in options.data_scope"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border stripe style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="role_name" label="角色名称" min-width="140" />
      <el-table-column prop="role_code" label="角色标识" min-width="140" />
      <el-table-column label="类型" width="110" align="center">
        <template #default="{ row }">
          <el-tag :type="row.role_type === 1 ? 'danger' : 'info'" size="small">
            {{ row.role_type_label }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="data_scope_label" label="数据权限" min-width="130" />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.role_sort"
            class="sort-input"
            size="small"
            style="width: 72px"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.role_status === 1"
            inline-prompt
            active-text="启"
            inactive-text="禁"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="role_remark" label="备注" min-width="160" show-overflow-tooltip />
      <el-table-column prop="created_at" label="创建时间" min-width="160" />
      <el-table-column label="操作" width="220" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <a class="action-edit" @click="openGrant(row)">授权</a>
          <el-button
            class="btn-danger-orange"
            size="small"
            :disabled="row.role_type === 1"
            @click="handleDelete(row)"
          >
            删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display: flex; justify-content: flex-end">
      <el-pagination
        v-model:current-page="query.page"
        v-model:page-size="query.per_page"
        background
        layout="total, prev, pager, next"
        :total="meta.total"
        @current-change="loadData"
      />
    </div>

    <el-dialog
      v-model="dialogVisible"
      :title="form.id ? '修改角色' : '添加角色'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="角色名称" prop="role_name">
          <el-input v-model="form.role_name" placeholder="如：超级管理员" />
        </el-form-item>
        <el-form-item label="角色标识" prop="role_code">
          <el-input
            v-model="form.role_code"
            :disabled="isSystemRole"
            placeholder="如：finance_admin"
          />
        </el-form-item>
        <el-form-item label="角色类型" prop="role_type">
          <el-radio-group v-model="form.role_type" :disabled="isSystemRole">
            <el-radio
              v-for="(label, value) in options.role_type"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="数据权限" prop="data_scope">
          <el-select v-model="form.data_scope" style="width: 100%">
            <el-option
              v-for="(label, value) in options.data_scope"
              :key="value"
              :label="label"
              :value="Number(value)"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          v-if="form.data_scope === 5"
          label="指定部门"
          prop="scope_departments"
        >
          <el-select
            v-model="form.scope_departments"
            multiple
            filterable
            allow-create
            default-first-option
            placeholder="输入部门ID后回车"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="排序号" prop="role_sort">
          <el-input-number v-model="form.role_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="role_status">
          <el-radio-group v-model="form.role_status">
            <el-radio
              v-for="(label, value) in options.role_status"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="role_remark">
          <el-input v-model="form.role_remark" type="textarea" :rows="3" maxlength="512" show-word-limit />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>

    <el-dialog
      v-model="grantVisible"
      :title="`授权 - ${grantRoleName}`"
      width="720px"
      destroy-on-close
    >
      <el-tabs v-model="grantTab">
        <el-tab-pane label="菜单权限" name="menus">
          <el-tree
            ref="menuTreeRef"
            :data="menuTree"
            node-key="id"
            show-checkbox
            default-expand-all
            :props="{ label: 'menu_name', children: 'children' }"
            style="max-height: 420px; overflow: auto"
          />
        </el-tab-pane>
        <el-tab-pane label="功能权限" name="permissions">
          <el-tree
            ref="permTreeRef"
            :data="permissionTree"
            node-key="id"
            show-checkbox
            default-expand-all
            :props="{ label: 'per_name', children: 'children' }"
            style="max-height: 420px; overflow: auto"
          >
            <template #default="{ data }">
              <span>
                {{ data.per_name }}
                <el-tag size="small" style="margin-left: 8px">{{ data.per_type_label || data.per_type }}</el-tag>
              </span>
            </template>
          </el-tree>
        </el-tab-pane>
      </el-tabs>
      <template #footer>
        <el-button @click="grantVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="grantSaving" @click="submitGrant">保存授权</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createRole,
  deleteRole,
  fetchRoleGrant,
  fetchRoles,
  syncRoleGrant,
  updateRole,
  updateRoleSort,
  updateRoleStatus,
} from '../../api/role';
import { fetchMenus } from '../../api/menu';
import { fetchPermissionTree } from '../../api/permission';

const loading = ref(false);
const saving = ref(false);
const list = ref([]);
const meta = reactive({ total: 0 });
const options = reactive({
  role_type: { 1: '系统内置', 2: '用户自定义' },
  role_status: { 0: '禁用', 1: '启用' },
  data_scope: {
    1: '全部数据',
    2: '本部门及下级',
    3: '本部门',
    4: '仅本人数据',
    5: '自定义指定部门',
  },
});

const query = reactive({
  keyword: '',
  role_type: undefined,
  role_status: undefined,
  data_scope: undefined,
  page: 1,
  per_page: 15,
});

const dialogVisible = ref(false);
const formRef = ref();
const form = reactive({
  id: null,
  role_name: '',
  role_code: '',
  role_type: 2,
  role_sort: 0,
  data_scope: 1,
  scope_departments: [],
  role_status: 1,
  role_remark: '',
});

const grantVisible = ref(false);
const grantSaving = ref(false);
const grantTab = ref('menus');
const grantRoleId = ref(null);
const grantRoleName = ref('');
const menuTree = ref([]);
const permissionTree = ref([]);
const menuTreeRef = ref();
const permTreeRef = ref();

const isSystemRole = computed(() => form.role_type === 1 && !!form.id);

const rules = {
  role_name: [{ required: true, message: '请输入角色名称', trigger: 'blur' }],
  role_code: [
    { required: true, message: '请输入角色标识', trigger: 'blur' },
    { pattern: /^[a-zA-Z][a-zA-Z0-9_]*$/, message: '以字母开头，仅字母数字下划线', trigger: 'blur' },
  ],
  data_scope: [{ required: true, message: '请选择数据权限', trigger: 'change' }],
  scope_departments: [
    {
      validator: (_r, v, cb) => {
        if (form.data_scope === 5 && (!v || !v.length)) {
          cb(new Error('请指定至少一个部门ID'));
        } else {
          cb();
        }
      },
      trigger: 'change',
    },
  ],
};

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchRoles({ ...query });
    list.value = res.data.list || [];
    Object.assign(meta, res.data.meta || {});
    if (res.data.options) {
      Object.assign(options, res.data.options);
    }
  } catch (e) {
    ElMessage.error(e.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.role_name = row?.role_name || '';
  form.role_code = row?.role_code || '';
  form.role_type = row?.role_type ?? 2;
  form.role_sort = row?.role_sort ?? 0;
  form.data_scope = row?.data_scope ?? 1;
  form.scope_departments = [...(row?.scope_departments || [])];
  form.role_status = row?.role_status ?? 1;
  form.role_remark = row?.role_remark || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      role_name: form.role_name,
      role_code: form.role_code,
      role_type: form.role_type,
      role_sort: form.role_sort ?? 0,
      data_scope: form.data_scope,
      scope_departments: form.data_scope === 5 ? form.scope_departments : [],
      role_status: form.role_status,
      role_remark: form.role_remark || '',
    };
    if (form.id) {
      await updateRole(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createRole(payload);
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
    await updateRoleSort(row.id, { role_sort: Number(row.role_sort) || 0 });
  } catch (e) {
    ElMessage.error(e.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateRoleStatus(row.id, { role_status: enabled ? 1 : 0 });
    row.role_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  if (row.role_type === 1) {
    ElMessage.warning('系统内置角色不可删除');
    return;
  }
  try {
    await ElMessageBox.confirm(`确定删除角色「${row.role_name}」吗？`, '提示', { type: 'warning' });
    await deleteRole(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e.message || '删除失败');
  }
}

async function openGrant(row) {
  grantRoleId.value = row.id;
  grantRoleName.value = row.role_name;
  grantTab.value = 'menus';
  grantVisible.value = true;
  try {
    const [menusRes, permsRes, grantRes] = await Promise.all([
      fetchMenus(),
      fetchPermissionTree(),
      fetchRoleGrant(row.id),
    ]);
    menuTree.value = menusRes.data?.list || [];
    permissionTree.value = permsRes.data || [];
    await Promise.resolve();
    menuTreeRef.value?.setCheckedKeys(grantRes.data?.menu_ids || [], false);
    permTreeRef.value?.setCheckedKeys(grantRes.data?.permission_ids || [], false);
  } catch (e) {
    ElMessage.error(e.message || '加载授权数据失败');
  }
}

async function submitGrant() {
  grantSaving.value = true;
  try {
    const menuIds = [
      ...(menuTreeRef.value?.getCheckedKeys(false) || []),
      ...(menuTreeRef.value?.getHalfCheckedKeys() || []),
    ].map(String);
    const permissionIds = [
      ...(permTreeRef.value?.getCheckedKeys(false) || []),
      ...(permTreeRef.value?.getHalfCheckedKeys() || []),
    ].map(String);

    await syncRoleGrant(grantRoleId.value, {
      menu_ids: menuIds,
      permission_ids: permissionIds,
    });
    ElMessage.success('授权成功');
    grantVisible.value = false;
  } catch (e) {
    ElMessage.error(e.message || '授权失败');
  } finally {
    grantSaving.value = false;
  }
}

onMounted(loadData);
</script>
