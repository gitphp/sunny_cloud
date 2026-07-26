<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="职位 / 部门 / 地点 / 薪资"
        @keyup.enter="search"
      />
      <el-select v-model="filters.job_status" clearable placeholder="状态" style="width: 120px">
        <el-option
          v-for="(label, value) in options.job_status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="filters.is_hot" clearable placeholder="急聘" style="width: 100px">
        <el-option
          v-for="(label, value) in options.is_hot"
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
      <el-table-column label="职位" min-width="180">
        <template #default="{ row }">
          <span v-if="row.is_hot === 1" class="hot-flag">急</span>
          {{ row.job_title }}
        </template>
      </el-table-column>
      <el-table-column prop="department" label="部门" width="120" show-overflow-tooltip />
      <el-table-column prop="workplace" label="地点" width="120" show-overflow-tooltip />
      <el-table-column prop="salary_range" label="薪资" width="120" />
      <el-table-column prop="experience" label="经验" width="100" />
      <el-table-column prop="education" label="学历" width="100" />
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-tag :type="statusTag(row.job_status)" size="small">{{ row.job_status_label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="急聘" width="80" align="center">
        <template #default="{ row }">
          <el-switch :model-value="row.is_hot === 1" @change="(val) => onHotChange(row, val)" />
        </template>
      </el-table-column>
      <el-table-column label="排序" width="100" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.job_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="view_count" label="浏览" width="80" align="center" />
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改职位' : '添加职位'" width="720px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-row :gutter="12">
          <el-col :span="12">
            <el-form-item label="职位名称" prop="job_title">
              <el-input v-model="form.job_title" maxlength="64" show-word-limit />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="所属部门" prop="department">
              <el-input v-model="form.department" maxlength="64" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="工作地点" prop="workplace">
              <el-input v-model="form.workplace" maxlength="128" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="薪资范围" prop="salary_range">
              <el-input v-model="form.salary_range" maxlength="64" placeholder="如 15k-25k" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="经验要求" prop="experience">
              <el-input v-model="form.experience" maxlength="64" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="学历要求" prop="education">
              <el-input v-model="form.education" maxlength="64" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="状态" prop="job_status">
              <el-select v-model="form.job_status" style="width: 100%">
                <el-option
                  v-for="(label, value) in options.job_status"
                  :key="value"
                  :label="label"
                  :value="Number(value)"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="过期时间" prop="expire_at">
              <el-date-picker
                v-model="form.expire_at"
                type="datetime"
                value-format="YYYY-MM-DD HH:mm:ss"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="排序" prop="job_sort">
              <el-input-number v-model="form.job_sort" :min="0" :max="999999" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="急聘" prop="is_hot">
              <el-switch v-model="form.is_hot" :active-value="1" :inactive-value="0" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="职位描述" prop="description">
          <el-input v-model="form.description" type="textarea" :rows="3" />
        </el-form-item>
        <el-form-item label="任职要求" prop="requirements">
          <el-input v-model="form.requirements" type="textarea" :rows="3" />
        </el-form-item>
        <el-form-item label="福利待遇" prop="benefits">
          <el-input v-model="form.benefits" type="textarea" :rows="2" />
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
  createBossJob,
  deleteBossJob,
  fetchBossJob,
  fetchBossJobs,
  updateBossJob,
  updateBossJobHot,
  updateBossJobSort,
} from '../../api/bossJob';

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
  job_status: '',
  is_hot: '',
});

const options = reactive({
  job_status: { 1: '待发布', 2: '发布中', 3: '已关闭' },
  is_hot: { 0: '否', 1: '急聘' },
});

const form = reactive({
  id: null,
  job_title: '',
  department: '',
  workplace: '',
  experience: '',
  education: '',
  salary_range: '',
  description: '',
  requirements: '',
  benefits: '',
  is_hot: 0,
  job_status: 1,
  expire_at: '',
  job_sort: 0,
});

const rules = {
  job_title: [{ required: true, message: '请输入职位名称', trigger: 'blur' }],
};

function statusTag(status) {
  return { 1: 'info', 2: 'success', 3: 'danger' }[status] || '';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchBossJobs({
      keyword: filters.keyword || undefined,
      job_status: filters.job_status === '' ? undefined : filters.job_status,
      is_hot: filters.is_hot === '' ? undefined : filters.is_hot,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.job_status) options.job_status = res.data.options.job_status;
    if (res.data?.options?.is_hot) options.is_hot = res.data.options.is_hot;
  } catch (e) {
    ElMessage.error(e?.message || '加载职位失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

async function openForm(row = null) {
  if (row?.id) {
    try {
      const res = await fetchBossJob(row.id);
      const data = res.data || {};
      Object.assign(form, {
        id: data.id,
        job_title: data.job_title || '',
        department: data.department || '',
        workplace: data.workplace || '',
        experience: data.experience || '',
        education: data.education || '',
        salary_range: data.salary_range || '',
        description: data.description || '',
        requirements: data.requirements || '',
        benefits: data.benefits || '',
        is_hot: data.is_hot ?? 0,
        job_status: data.job_status ?? 1,
        expire_at: data.expire_at || '',
        job_sort: data.job_sort ?? 0,
      });
    } catch (e) {
      ElMessage.error(e?.message || '加载职位失败');
      return;
    }
  } else {
    Object.assign(form, {
      id: null,
      job_title: '',
      department: '',
      workplace: '',
      experience: '',
      education: '',
      salary_range: '',
      description: '',
      requirements: '',
      benefits: '',
      is_hot: 0,
      job_status: 1,
      expire_at: '',
      job_sort: 0,
    });
  }
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      job_title: form.job_title,
      department: form.department || '',
      workplace: form.workplace || '',
      experience: form.experience || '',
      education: form.education || '',
      salary_range: form.salary_range || '',
      description: form.description || '',
      requirements: form.requirements || '',
      benefits: form.benefits || '',
      is_hot: form.is_hot ?? 0,
      job_status: form.job_status ?? 1,
      expire_at: form.expire_at || null,
      job_sort: form.job_sort ?? 0,
    };
    if (form.id) {
      await updateBossJob(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createBossJob(payload);
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
    await updateBossJobSort(row.id, { job_sort: Number(row.job_sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onHotChange(row, enabled) {
  try {
    await updateBossJobHot(row.id, { is_hot: enabled ? 1 : 0 });
    row.is_hot = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '急聘状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除职位「${row.job_title}」吗？`, '提示', { type: 'warning' });
    await deleteBossJob(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.hot-flag {
  display: inline-block;
  margin-right: 6px;
  padding: 0 4px;
  font-size: 12px;
  color: #fff;
  background: #f56c6c;
  border-radius: 2px;
  line-height: 18px;
}
</style>
