<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="输入关键字"
        @keyup.enter="handleSearch"
      />
      <el-button class="btn-primary-teal" :icon="Search" @click="handleSearch">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table
      v-loading="loading"
      :data="filteredTree"
      row-key="id"
      border
      default-expand-all
      :tree-props="{ children: 'children', hasChildren: 'has_children' }"
      style="width: 100%"
    >
      <el-table-column type="index" label="#" width="60" align="center" />
      <el-table-column label="名称" min-width="260">
        <template #default="{ row }">
          <span class="name-cell">
            <el-icon v-if="row.children?.length" class="folder-icon"><Folder /></el-icon>
            <el-icon v-else class="doc-icon"><Document /></el-icon>
            {{ row.name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="id" label="ID值" width="100" align="center" />
      <el-table-column label="排序号" width="120" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.sort"
            class="sort-input"
            size="small"
            @change="updateSort(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="160" align="center">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">
            删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog
      v-model="dialogVisible"
      :title="form.id ? '修改分类' : '添加分类'"
      width="480px"
      destroy-on-close
    >
      <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
        <el-form-item label="上级分类" prop="parent_id">
          <el-tree-select
            v-model="form.parent_id"
            :data="parentOptions"
            check-strictly
            clearable
            placeholder="无（顶级分类）"
            style="width: 100%"
            :props="{ label: 'name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="排序号" prop="sort">
          <el-input-number v-model="form.sort" :min="0" :max="9999" />
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
import { ref, computed, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search, Plus } from '@element-plus/icons-vue';
import {
  fetchCategories,
  createCategory,
  updateCategory,
  deleteCategory,
  updateCategorySort,
} from '../../api/category';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();

const form = reactive({
  id: null,
  parent_id: null,
  name: '',
  sort: 0,
});

const rules = {
  name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
};

const filteredTree = computed(() => {
  const kw = keyword.value.trim().toLowerCase();
  if (!kw) return treeData.value;
  return filterTree(treeData.value, kw);
});

const parentOptions = computed(() => [
  { id: 0, name: '无（顶级分类）', children: mapParents(treeData.value, form.id) },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => n.id !== excludeId)
    .map((n) => ({
      id: n.id,
      name: n.name,
      children: mapParents(n.children || [], excludeId),
    }));
}

function filterTree(nodes, kw) {
  const result = [];
  for (const node of nodes) {
    const children = filterTree(node.children || [], kw);
    if (node.name.toLowerCase().includes(kw) || children.length) {
      result.push({ ...node, children });
    }
  }
  return result;
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchCategories();
    treeData.value = res.data || [];
  } catch (e) {
    ElMessage.error(e?.message || '加载分类失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  // filteredTree 已响应 keyword
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row?.parent_id || 0;
  form.name = row?.name || '';
  form.sort = row?.sort ?? 0;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      name: form.name,
      parent_id: form.parent_id || 0,
      sort: form.sort ?? 0,
    };
    if (form.id) {
      await updateCategory(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createCategory(payload);
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

async function updateSort(row) {
  try {
    await updateCategorySort(row.id, { sort: Number(row.sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.name}」吗？`, '提示', {
      type: 'warning',
      confirmButtonText: '确定',
      cancelButtonText: '取消',
    });
    await deleteCategory(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') {
      ElMessage.error(e?.message || '删除失败');
    }
  }
}

onMounted(loadData);
</script>
