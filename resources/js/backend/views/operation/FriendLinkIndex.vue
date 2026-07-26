<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="名称 / 链接 / 描述"
        @keyup.enter="search"
      />
      <el-select v-model="filters.link_status" clearable placeholder="状态" style="width: 120px">
        <el-option
          v-for="(label, value) in options.link_status"
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
      <el-table-column label="网站" min-width="180">
        <template #default="{ row }">
          <div class="link-cell">
            <img v-if="row.link_logo" :src="row.link_logo" class="logo" alt="" @error="onLogoError" />
            <a :href="row.link_url" target="_blank" rel="noopener noreferrer" class="action-edit">
              {{ row.link_name }}
            </a>
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="link_url" label="链接" min-width="220" show-overflow-tooltip />
      <el-table-column prop="link_desc" label="描述" min-width="160" show-overflow-tooltip />
      <el-table-column label="排序" width="100" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.link_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.link_status === 1"
            inline-prompt
            active-text="启"
            inactive-text="禁"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改友情链接' : '添加友情链接'" width="560px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="网站名称" prop="link_name">
          <el-input v-model="form.link_name" maxlength="32" show-word-limit />
        </el-form-item>
        <el-form-item label="网站链接" prop="link_url">
          <el-input v-model="form.link_url" maxlength="512" placeholder="https://" />
        </el-form-item>
        <el-form-item label="Logo" prop="link_logo">
          <el-input v-model="form.link_logo" maxlength="512" placeholder="Logo URL，可选" />
        </el-form-item>
        <el-form-item label="排序" prop="link_sort">
          <el-input-number v-model="form.link_sort" :min="0" :max="999999" />
        </el-form-item>
        <el-form-item label="状态" prop="link_status">
          <el-radio-group v-model="form.link_status">
            <el-radio v-for="(label, value) in options.link_status" :key="value" :value="Number(value)">
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="描述" prop="link_desc">
          <el-input v-model="form.link_desc" type="textarea" :rows="2" maxlength="255" show-word-limit />
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
  createFriendLink,
  deleteFriendLink,
  fetchFriendLinks,
  updateFriendLink,
  updateFriendLinkSort,
  updateFriendLinkStatus,
} from '../../api/friendLink';

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
  link_status: '',
});

const options = reactive({
  link_status: { 0: '禁用', 1: '启用' },
});

const form = reactive({
  id: null,
  link_name: '',
  link_url: '',
  link_logo: '',
  link_desc: '',
  link_sort: 0,
  link_status: 1,
});

const rules = {
  link_name: [{ required: true, message: '请输入网站名称', trigger: 'blur' }],
  link_url: [{ required: true, message: '请输入网站链接', trigger: 'blur' }],
};

function onLogoError(e) {
  e.target.style.display = 'none';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchFriendLinks({
      keyword: filters.keyword || undefined,
      link_status: filters.link_status === '' ? undefined : filters.link_status,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.link_status) options.link_status = res.data.options.link_status;
  } catch (e) {
    ElMessage.error(e?.message || '加载友情链接失败');
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
  form.link_name = row?.link_name || '';
  form.link_url = row?.link_url || '';
  form.link_logo = row?.link_logo || '';
  form.link_desc = row?.link_desc || '';
  form.link_sort = row?.link_sort ?? 0;
  form.link_status = row?.link_status ?? 1;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      link_name: form.link_name,
      link_url: form.link_url,
      link_logo: form.link_logo || '',
      link_desc: form.link_desc || '',
      link_sort: form.link_sort ?? 0,
      link_status: form.link_status ?? 1,
    };
    if (form.id) {
      await updateFriendLink(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createFriendLink(payload);
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
    await updateFriendLinkSort(row.id, { link_sort: Number(row.link_sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateFriendLinkStatus(row.id, { link_status: enabled ? 1 : 0 });
    row.link_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.link_name}」吗？`, '提示', { type: 'warning' });
    await deleteFriendLink(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.link-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}
.logo {
  width: 20px;
  height: 20px;
  object-fit: contain;
}
</style>
