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
      <el-table-column label="岗位名称" min-width="220">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            {{ row.post_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="post_code" label="编码" min-width="140" show-overflow-tooltip />
      <el-table-column prop="remark" label="备注" min-width="160" show-overflow-tooltip />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.post_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.post_status === 1"
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
      :title="form.id ? '修改岗位' : '添加岗位'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级岗位" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（顶级岗位）"
            style="width: 100%"
            :props="{ label: 'post_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="岗位名称" prop="post_name">
          <el-input v-model="form.post_name" placeholder="如：前端开发" />
        </el-form-item>
        <el-form-item label="岗位编码" prop="post_code">
          <el-input v-model="form.post_code" placeholder="如：FE_DEV" />
        </el-form-item>
        <el-form-item label="排序号" prop="post_sort">
          <el-input-number v-model="form.post_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="post_status">
          <el-radio-group v-model="form.post_status">
            <el-radio
              v-for="(label, value) in options.post_status"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
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
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createHrPost,
  deleteHrPost,
  fetchHrPosts,
  updateHrPost,
  updateHrPostSort,
  updateHrPostStatus,
} from '../../api/hrPost';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({ post_status: { 0: '禁用', 1: '启用' } });

const form = reactive({
  id: null,
  parent_id: '0',
  post_name: '',
  post_code: '',
  post_sort: 0,
  post_status: 1,
  remark: '',
});

const rules = {
  post_name: [{ required: true, message: '请输入岗位名称', trigger: 'blur' }],
  post_code: [{ required: true, message: '请输入岗位编码', trigger: 'blur' }],
};

const parentOptions = computed(() => [
  {
    id: '0',
    post_name: '无（顶级岗位）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .map((n) => ({
      id: String(n.id),
      post_name: n.post_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchHrPosts({ keyword: keyword.value || undefined });
    treeData.value = res.data?.list || [];
    if (res.data?.options?.post_status) options.post_status = res.data.options.post_status;
  } catch (e) {
    ElMessage.error(e?.message || '加载岗位失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.post_name = row?.post_name || '';
  form.post_code = row?.post_code || '';
  form.post_sort = row?.post_sort ?? 0;
  form.post_status = row?.post_status ?? 1;
  form.remark = row?.remark || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      parent_id: form.parent_id === '0' || !form.parent_id ? 0 : form.parent_id,
      post_name: form.post_name,
      post_code: form.post_code,
      post_sort: form.post_sort ?? 0,
      post_status: form.post_status ?? 1,
      remark: form.remark || '',
    };
    if (form.id) {
      await updateHrPost(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createHrPost(payload);
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
    await updateHrPostSort(row.id, { post_sort: Number(row.post_sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateHrPostStatus(row.id, { post_status: enabled ? 1 : 0 });
    row.post_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除岗位「${row.post_name}」吗？`, '提示', { type: 'warning' });
    await deleteHrPost(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
