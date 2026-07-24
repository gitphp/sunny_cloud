<template>
  <div class="admin-page-card">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="query.keyword"
        class="search-input"
        clearable
        placeholder="用户名/昵称/手机/邮箱"
        @keyup.enter="loadData"
      />
      <el-select v-model="query.user_status" clearable placeholder="账号状态" style="width: 130px">
        <el-option
          v-for="(label, value) in options.user_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="query.real_auth_status" clearable placeholder="实名状态" style="width: 130px">
        <el-option
          v-for="(label, value) in options.real_auth_status"
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
      <el-table-column prop="user_name" label="用户名" min-width="110" />
      <el-table-column prop="nick_name" label="昵称" min-width="100" />
      <el-table-column prop="user_mobile" label="手机号" min-width="120" />
      <el-table-column prop="user_email" label="邮箱" min-width="160" show-overflow-tooltip />
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-tag :type="statusTagType(row.user_status)" size="small">
            {{ row.user_status_label }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="real_auth_status_label" label="实名" width="90" align="center" />
      <el-table-column label="角色" min-width="140">
        <template #default="{ row }">
          <el-tag
            v-for="role in row.roles || []"
            :key="role.id"
            size="small"
            style="margin: 2px"
          >
            {{ role.role_name }}
          </el-tag>
          <span v-if="!(row.roles || []).length" style="color: #999">未分配</span>
        </template>
      </el-table-column>
      <el-table-column prop="last_login_at" label="最后登录" min-width="160" />
      <el-table-column prop="created_at" label="注册时间" min-width="160" />
      <el-table-column label="操作" width="260" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <a class="action-edit" @click="openRoles(row)">角色</a>
          <a class="action-edit" @click="openStatus(row)">状态</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">删除</el-button>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改用户' : '添加用户'" width="520px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
        <el-form-item label="用户名" prop="user_name">
          <el-input v-model="form.user_name" :disabled="!!form.id" />
        </el-form-item>
        <el-form-item label="昵称" prop="nick_name">
          <el-input v-model="form.nick_name" />
        </el-form-item>
        <el-form-item label="手机号" prop="user_mobile">
          <el-input v-model="form.user_mobile" />
        </el-form-item>
        <el-form-item label="邮箱" prop="user_email">
          <el-input v-model="form.user_email" />
        </el-form-item>
        <el-form-item :label="form.id ? '新密码' : '密码'" prop="password">
          <el-input v-model="form.password" type="password" show-password :placeholder="form.id ? '不修改请留空' : ''" />
        </el-form-item>
        <el-form-item label="账号状态" prop="user_status">
          <el-select v-model="form.user_status" style="width: 100%">
            <el-option
              v-for="(label, value) in options.user_status"
              :key="value"
              :label="label"
              :value="Number(value)"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="实名状态" prop="real_auth_status">
          <el-select v-model="form.real_auth_status" style="width: 100%">
            <el-option
              v-for="(label, value) in options.real_auth_status"
              :key="value"
              :label="label"
              :value="Number(value)"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>

    <el-dialog v-model="statusVisible" title="更新账号状态" width="420px" destroy-on-close>
      <el-form :model="statusForm" label-width="90px">
        <el-form-item label="账号状态">
          <el-select v-model="statusForm.user_status" style="width: 100%">
            <el-option
              v-for="(label, value) in options.user_status"
              :key="value"
              :label="label"
              :value="Number(value)"
            />
          </el-select>
        </el-form-item>
        <el-form-item v-if="statusForm.user_status === 2" label="冻结原因">
          <el-input v-model="statusForm.lock_reason" type="textarea" :rows="2" />
        </el-form-item>
        <el-form-item v-if="statusForm.user_status === 2" label="到期时间">
          <el-date-picker
            v-model="statusForm.lock_expire_time"
            type="datetime"
            value-format="YYYY-MM-DD HH:mm:ss"
            placeholder="空=永久冻结"
            style="width: 100%"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="statusVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitStatus">确定</el-button>
      </template>
    </el-dialog>

    <el-dialog v-model="roleVisible" :title="`分配角色 - ${roleUserName}`" width="480px" destroy-on-close>
      <el-select
        v-model="selectedRoleIds"
        multiple
        filterable
        placeholder="请选择角色"
        style="width: 100%"
      >
        <el-option
          v-for="role in roleOptions"
          :key="role.id"
          :label="`${role.role_name}（${role.role_code}）`"
          :value="role.id"
        />
      </el-select>
      <template #footer>
        <el-button @click="roleVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitRoles">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import { createUser, deleteUser, fetchUsers, syncUserRoles, updateUser, updateUserStatus } from '../../api/user';
import { fetchRoles } from '../../api/role';

const loading = ref(false);
const saving = ref(false);
const list = ref([]);
const meta = reactive({ total: 0 });
const options = reactive({
  user_status: {},
  real_auth_status: {},
});

const query = reactive({
  keyword: '',
  user_status: undefined,
  real_auth_status: undefined,
  page: 1,
  per_page: 15,
});

const dialogVisible = ref(false);
const statusVisible = ref(false);
const roleVisible = ref(false);
const formRef = ref();
const form = reactive({
  id: null,
  user_name: '',
  nick_name: '',
  user_mobile: '',
  user_email: '',
  password: '',
  user_status: 1,
  real_auth_status: 0,
});

const statusForm = reactive({
  id: null,
  user_status: 1,
  lock_reason: '',
  lock_expire_time: null,
});

const roleUserId = ref(null);
const roleUserName = ref('');
const selectedRoleIds = ref([]);
const roleOptions = ref([]);

const rules = {
  user_name: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
  nick_name: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
  user_mobile: [
    { required: true, message: '请输入手机号', trigger: 'blur' },
    { pattern: /^1[3-9]\d{9}$/, message: '手机号格式不正确', trigger: 'blur' },
  ],
  password: [
    {
      validator: (_r, v, cb) => {
        if (!form.id && !v) cb(new Error('请输入密码'));
        else if (v && v.length < 6) cb(new Error('密码至少6位'));
        else cb();
      },
      trigger: 'blur',
    },
  ],
};

function statusTagType(status) {
  return { 0: 'info', 1: 'success', 2: 'warning', 3: 'danger' }[status] || 'info';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchUsers({ ...query });
    list.value = res.data.list || [];
    Object.assign(meta, res.data.meta || {});
    Object.assign(options, res.data.options || {});
  } catch (e) {
    ElMessage.error(e.message || '加载失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.user_name = row?.user_name || '';
  form.nick_name = row?.nick_name || '';
  form.user_mobile = row?.user_mobile || '';
  form.user_email = row?.user_email || '';
  form.password = '';
  form.user_status = row?.user_status ?? 1;
  form.real_auth_status = row?.real_auth_status ?? 0;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = { ...form };
    delete payload.id;
    if (!payload.password) delete payload.password;
    if (form.id) {
      await updateUser(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createUser(payload);
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

function openStatus(row) {
  statusForm.id = row.id;
  statusForm.user_status = row.user_status;
  statusForm.lock_reason = row.lock_reason || '';
  statusForm.lock_expire_time = row.lock_expire_time || null;
  statusVisible.value = true;
}

async function submitStatus() {
  saving.value = true;
  try {
    await updateUserStatus(statusForm.id, {
      user_status: statusForm.user_status,
      lock_reason: statusForm.lock_reason,
      lock_expire_time: statusForm.lock_expire_time,
    });
    ElMessage.success('状态更新成功');
    statusVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e.message || '更新失败');
  } finally {
    saving.value = false;
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除用户「${row.user_name}」吗？`, '提示', { type: 'warning' });
    await deleteUser(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e.message || '删除失败');
  }
}

async function openRoles(row) {
  roleUserId.value = row.id;
  roleUserName.value = row.nick_name || row.user_name;
  selectedRoleIds.value = (row.role_ids || row.roles?.map((r) => r.id) || []).map(String);
  roleVisible.value = true;
  try {
    const res = await fetchRoles({ per_page: 100, role_status: 1 });
    roleOptions.value = res.data.list || [];
  } catch (e) {
    ElMessage.error(e.message || '加载角色失败');
  }
}

async function submitRoles() {
  saving.value = true;
  try {
    await syncUserRoles(roleUserId.value, { role_ids: selectedRoleIds.value });
    ElMessage.success('角色分配成功');
    roleVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e.message || '角色分配失败');
  } finally {
    saving.value = false;
  }
}

onMounted(loadData);
</script>
