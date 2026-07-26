<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="分类名称 / URL别名"
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
      <el-table-column label="分类名称" min-width="220">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            {{ row.cat_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="cat_url" label="URL别名" min-width="140" show-overflow-tooltip />
      <el-table-column prop="description" label="描述" min-width="160" show-overflow-tooltip />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.cat_sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.status === 1"
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
      :title="form.id ? '修改分类' : '添加分类'"
      width="560px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="上级分类" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（一级分类）"
            style="width: 100%"
            :props="{ label: 'cat_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="分类名称" prop="cat_name">
          <el-input v-model="form.cat_name" maxlength="32" show-word-limit placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="URL别名" prop="cat_url">
          <el-input v-model="form.cat_url" maxlength="32" placeholder="如 company-news" />
        </el-form-item>
        <el-form-item label="排序号" prop="cat_sort">
          <el-input-number v-model="form.cat_sort" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio v-for="(label, value) in options.status" :key="value" :value="Number(value)">
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="分类描述" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="2"
            maxlength="255"
            show-word-limit
          />
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
import { Document, Folder, Plus, Search } from '@element-plus/icons-vue';
import {
  createArticleCategory,
  deleteArticleCategory,
  fetchArticleCategories,
  updateArticleCategory,
  updateArticleCategorySort,
  updateArticleCategoryStatus,
} from '../../api/articleCategory';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({
  status: { 0: '禁用', 1: '启用' },
});

const form = reactive({
  id: null,
  parent_id: '0',
  cat_name: '',
  cat_url: '',
  cat_sort: 0,
  status: 1,
  description: '',
});

const rules = {
  cat_name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
  cat_url: [
    {
      pattern: /^[a-z0-9\-]*$/,
      message: '仅支持小写字母、数字和短横线',
      trigger: 'blur',
    },
  ],
};

const parentOptions = computed(() => [
  {
    id: '0',
    cat_name: '无（一级分类）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .map((n) => ({
      id: String(n.id),
      cat_name: n.cat_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchArticleCategories({ keyword: keyword.value || undefined });
    treeData.value = res.data?.list || [];
    if (res.data?.options?.status) options.status = res.data.options.status;
  } catch (e) {
    ElMessage.error(e?.message || '加载分类失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.cat_name = row?.cat_name || '';
  form.cat_url = row?.cat_url || '';
  form.cat_sort = row?.cat_sort ?? 0;
  form.status = row?.status ?? 1;
  form.description = row?.description || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      cat_name: form.cat_name,
      cat_url: form.cat_url || '',
      parent_id: form.parent_id === '0' || form.parent_id === 0 || !form.parent_id ? 0 : form.parent_id,
      cat_sort: form.cat_sort ?? 0,
      status: form.status ?? 1,
      description: form.description || '',
    };
    if (form.id) {
      await updateArticleCategory(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createArticleCategory(payload);
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
    await updateArticleCategorySort(row.id, { cat_sort: Number(row.cat_sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateArticleCategoryStatus(row.id, { status: enabled ? 1 : 0 });
    row.status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.cat_name}」吗？`, '提示', { type: 'warning' });
    await deleteArticleCategory(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
