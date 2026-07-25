<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input
        v-model="keyword"
        class="search-input"
        clearable
        placeholder="输入关键字"
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
            {{ row.category_name }}
          </span>
        </template>
      </el-table-column>
      <el-table-column prop="level_label" label="级别" width="80" align="center" />
      <el-table-column label="可见性" min-width="120" align="center">
        <template #default="{ row }">
          {{ row.show_type_label || options.show_type?.[row.show_type] || '-' }}
        </template>
      </el-table-column>
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.sort_order"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.cat_status === 1"
            inline-prompt
            active-text="显"
            inactive-text="隐"
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
            :props="{ label: 'category_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="分类名称" prop="category_name">
          <el-input v-model="form.category_name" placeholder="请输入分类名称" />
        </el-form-item>
        <el-form-item label="可见性" prop="show_type">
          <el-radio-group v-model="form.show_type">
            <el-radio
              v-for="(label, value) in options.show_type"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="排序号" prop="sort_order">
          <el-input-number v-model="form.sort_order" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="cat_status">
          <el-radio-group v-model="form.cat_status">
            <el-radio
              v-for="(label, value) in options.cat_status"
              :key="value"
              :value="Number(value)"
            >
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="分类描述" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="2"
            maxlength="512"
            show-word-limit
            placeholder="分类描述 / SEO 说明"
          />
        </el-form-item>
        <el-form-item label="备注" prop="cat_remark">
          <el-input
            v-model="form.cat_remark"
            type="textarea"
            :rows="2"
            maxlength="512"
            show-word-limit
            placeholder="备注"
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
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createCategory,
  deleteCategory,
  fetchCategories,
  updateCategory,
  updateCategorySort,
  updateCategoryStatus,
} from '../../api/category';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const treeData = ref([]);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({
  show_type: { 0: '全部可见', 1: '指定客户可见', 2: '指定客户不可见' },
  cat_status: { 0: '隐藏', 1: '显示' },
  level: { 1: '一级', 2: '二级', 3: '三级' },
});

const form = reactive({
  id: null,
  parent_id: '0',
  category_name: '',
  show_type: 0,
  cat_status: 1,
  sort_order: 0,
  description: '',
  cat_remark: '',
});

const rules = {
  category_name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
};

const parentOptions = computed(() => [
  {
    id: '0',
    category_name: '无（一级分类）',
    children: mapParents(treeData.value, form.id),
  },
]);

function mapParents(nodes, excludeId) {
  return (nodes || [])
    .filter((n) => String(n.id) !== String(excludeId))
    .filter((n) => Number(n.level || 1) < 3)
    .map((n) => ({
      id: String(n.id),
      category_name: n.category_name,
      children: mapParents(n.children || [], excludeId),
    }));
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchCategories({ keyword: keyword.value || undefined });
    treeData.value = res.data?.list || [];
    if (res.data?.options) {
      Object.assign(options, res.data.options);
    }
  } catch (e) {
    ElMessage.error(e?.message || '加载分类失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.parent_id = row ? String(row.parent_id ?? '0') : '0';
  form.category_name = row?.category_name || '';
  form.show_type = row?.show_type ?? 0;
  form.cat_status = row?.cat_status ?? 1;
  form.sort_order = row?.sort_order ?? 0;
  form.description = row?.description || '';
  form.cat_remark = row?.cat_remark || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      category_name: form.category_name,
      parent_id: form.parent_id === '0' || form.parent_id === 0 || !form.parent_id ? 0 : form.parent_id,
      show_type: form.show_type ?? 0,
      cat_status: form.cat_status ?? 1,
      sort_order: form.sort_order ?? 0,
      description: form.description || '',
      cat_remark: form.cat_remark || '',
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

async function onSortChange(row) {
  try {
    await updateCategorySort(row.id, { sort_order: Number(row.sort_order) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateCategoryStatus(row.id, { cat_status: enabled ? 1 : 0 });
    row.cat_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.category_name}」吗？`, '提示', {
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
