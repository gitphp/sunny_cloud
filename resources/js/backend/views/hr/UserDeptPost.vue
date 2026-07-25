<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="员工 / 部门 / 岗位"
        @keyup.enter="loadData"
      />
      <el-select v-model="filters.is_main" clearable placeholder="主岗" style="width: 120px; margin-right: 8px">
        <el-option
          v-for="(label, value) in options.is_main"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column label="员工" min-width="140">
        <template #default="{ row }">
          {{ row.nick_name || row.user_name || row.user_id }}
        </template>
      </el-table-column>
      <el-table-column prop="dept_name" label="部门" min-width="140" show-overflow-tooltip />
      <el-table-column prop="post_name" label="岗位" min-width="140" show-overflow-tooltip />
      <el-table-column prop="is_main_label" label="类型" width="90" align="center" />
      <el-table-column prop="start_at" label="开始时间" min-width="160" />
      <el-table-column prop="end_at" label="结束时间" min-width="160" />
      <el-table-column prop="remark" label="备注" min-width="140" show-overflow-tooltip />
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

    <el-dialog
      v-model="dialogVisible"
      :title="form.id ? '修改任职' : '添加任职'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="员工" prop="user_id">
          <el-select v-model="form.user_id" filterable clearable placeholder="选择员工" style="width: 100%">
            <el-option
              v-for="u in userOptions"
              :key="u.id"
              :label="`${u.nick_name || u.user_name} (${u.user_name})`"
              :value="String(u.id)"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="部门" prop="dept_id">
          <el-tree-select
            v-model="form.dept_id"
            :data="deptTree"
            check-strictly
            filterable
            clearable
            placeholder="选择部门"
            style="width: 100%"
            :props="{ label: 'dept_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="岗位" prop="post_id">
          <el-tree-select
            v-model="form.post_id"
            :data="postTree"
            check-strictly
            filterable
            clearable
            placeholder="选择岗位"
            style="width: 100%"
            :props="{ label: 'post_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="任职类型" prop="is_main">
          <el-radio-group v-model="form.is_main">
            <el-radio
              v-for="(label, value) in options.is_main"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="开始时间" prop="start_at">
          <el-date-picker
            v-model="form.start_at"
            type="datetime"
            value-format="YYYY-MM-DD HH:mm:ss"
            placeholder="选填"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="结束时间" prop="end_at">
          <el-date-picker
            v-model="form.end_at"
            type="datetime"
            value-format="YYYY-MM-DD HH:mm:ss"
            placeholder="选填"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" type="textarea" :rows="2" maxlength="512" show-word-limit />
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
  createHrUserDeptPost,
  deleteHrUserDeptPost,
  fetchHrUserDeptPosts,
  updateHrUserDeptPost,
} from '../../api/hrUserDeptPost';
import { fetchHrDepartments } from '../../api/hrDepartment';
import { fetchHrPosts } from '../../api/hrPost';
import { fetchUsers } from '../../api/user';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const formRef = ref();
const userOptions = ref([]);
const deptTree = ref([]);
const postTree = ref([]);
const filters = reactive({ is_main: undefined });
const options = reactive({ is_main: { 0: '兼职', 1: '主岗' } });

const form = reactive({
  id: null,
  user_id: '',
  dept_id: '',
  post_id: '',
  is_main: 0,
  remark: '',
  start_at: '',
  end_at: '',
});

const rules = {
  user_id: [{ required: true, message: '请选择员工', trigger: 'change' }],
  dept_id: [{ required: true, message: '请选择部门', trigger: 'change' }],
  post_id: [{ required: true, message: '请选择岗位', trigger: 'change' }],
};

function mapTreeIds(nodes, labelKey) {
  return (nodes || []).map((n) => ({
    id: String(n.id),
    [labelKey]: n[labelKey],
    children: mapTreeIds(n.children || [], labelKey),
  }));
}

async function loadOptions() {
  const [usersRes, deptsRes, postsRes] = await Promise.all([
    fetchUsers({ per_page: 200 }),
    fetchHrDepartments(),
    fetchHrPosts(),
  ]);
  userOptions.value = usersRes.data?.list || [];
  deptTree.value = mapTreeIds(deptsRes.data?.list || [], 'dept_name');
  postTree.value = mapTreeIds(postsRes.data?.list || [], 'post_name');
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchHrUserDeptPosts({
      keyword: keyword.value || undefined,
      is_main: filters.is_main,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.is_main) options.is_main = res.data.options.is_main;
  } catch (e) {
    ElMessage.error(e?.message || '加载任职失败');
  } finally {
    loading.value = false;
  }
}

async function openForm(row = null) {
  await loadOptions();
  form.id = row?.id ?? null;
  form.user_id = row ? String(row.user_id) : '';
  form.dept_id = row ? String(row.dept_id) : '';
  form.post_id = row ? String(row.post_id) : '';
  form.is_main = row?.is_main ?? 0;
  form.remark = row?.remark || '';
  form.start_at = row?.start_at || '';
  form.end_at = row?.end_at || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      user_id: form.user_id,
      dept_id: form.dept_id,
      post_id: form.post_id,
      is_main: form.is_main ?? 0,
      remark: form.remark || '',
      start_at: form.start_at || null,
      end_at: form.end_at || null,
    };
    if (form.id) {
      await updateHrUserDeptPost(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createHrUserDeptPost(payload);
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

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm('确定删除该任职记录吗？', '提示', { type: 'warning' });
    await deleteHrUserDeptPost(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
