<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="短标题 / 标题 / 链接 / 备注"
        @keyup.enter="search"
      />
      <el-tree-select
        v-model="filters.category_id"
        :data="categoryFilterOptions"
        clearable
        check-strictly
        placeholder="分类"
        style="width: 180px"
        :props="{ label: 'category_name', value: 'id', children: 'children' }"
      />
      <el-select v-model="filters.status" clearable placeholder="状态" style="width: 120px">
        <el-option
          v-for="(label, value) in options.status"
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
      <el-table-column label="书签" min-width="220">
        <template #default="{ row }">
          <div class="bookmark-cell">
            <img
              v-if="row.book_favicon"
              :src="row.book_favicon"
              class="favicon"
              alt=""
              @error="onFaviconError"
            />
            <el-icon v-else class="favicon-fallback"><Link /></el-icon>
            <div>
              <a
                class="bookmark-title"
                :class="{ bold: row.is_bold === 0 }"
                :href="row.book_url"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ row.short_title || row.book_title }}
              </a>
              <div v-if="row.short_title && row.book_title" class="bookmark-sub">{{ row.book_title }}</div>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="category_name" label="分类" width="120" show-overflow-tooltip />
      <el-table-column label="链接" min-width="200" show-overflow-tooltip>
        <template #default="{ row }">
          <a :href="row.book_url" target="_blank" rel="noopener noreferrer" class="action-edit">
            {{ row.book_url }}
          </a>
        </template>
      </el-table-column>
      <el-table-column prop="book_desc" label="备注" min-width="140" show-overflow-tooltip />
      <el-table-column label="排序" width="100" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.sort_order"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="100" align="center">
        <template #default="{ row }">
          <el-tag :type="statusTag(row.status)" size="small">{{ row.status_label }}</el-tag>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改书签' : '添加书签'" width="620px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="所属分类" prop="category_id">
          <el-tree-select
            v-model="form.category_id"
            :data="categoryFormOptions"
            check-strictly
            clearable
            placeholder="未分类"
            style="width: 100%"
            :props="{ label: 'category_name', value: 'id', children: 'children' }"
          />
        </el-form-item>
        <el-form-item label="短标题" prop="short_title">
          <el-input v-model="form.short_title" maxlength="16" show-word-limit placeholder="列表展示用" />
        </el-form-item>
        <el-form-item label="长标题" prop="book_title">
          <el-input v-model="form.book_title" maxlength="128" show-word-limit />
        </el-form-item>
        <el-form-item label="链接地址" prop="book_url">
          <el-input v-model="form.book_url" maxlength="2048" placeholder="https://" />
        </el-form-item>
        <el-form-item label="图标URL" prop="book_favicon">
          <el-input v-model="form.book_favicon" maxlength="512" placeholder="favicon 地址，可选" />
        </el-form-item>
        <el-form-item label="排序" prop="sort_order">
          <el-input-number v-model="form.sort_order" :min="0" :max="999999" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio v-for="(label, value) in options.status" :key="value" :value="Number(value)">
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="字体" prop="is_bold">
          <el-radio-group v-model="form.is_bold">
            <el-radio v-for="(label, value) in options.is_bold" :key="value" :value="Number(value)">
              {{ label }}
            </el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="book_desc">
          <el-input v-model="form.book_desc" type="textarea" :rows="3" maxlength="1024" show-word-limit />
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
import { Link, Plus, Search } from '@element-plus/icons-vue';
import {
  createBookMark,
  deleteBookMark,
  fetchBookMarks,
  updateBookMark,
  updateBookMarkSort,
} from '../../api/bookMark';

const loading = ref(false);
const saving = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const formRef = ref();
const categoryTree = ref([]);

const filters = reactive({
  keyword: '',
  category_id: '',
  status: '',
});

const options = reactive({
  status: { 0: '隐藏', 1: '正常', 2: '失效' },
  is_bold: { 0: '加粗', 1: '正常' },
});

const form = reactive({
  id: null,
  category_id: '0',
  short_title: '',
  book_title: '',
  book_url: '',
  book_favicon: '',
  book_desc: '',
  sort_order: 0,
  status: 1,
  is_bold: 0,
});

const rules = {
  book_title: [{ required: true, message: '请输入书签标题', trigger: 'blur' }],
  book_url: [{ required: true, message: '请输入链接地址', trigger: 'blur' }],
};

function mapCategories(nodes) {
  return (nodes || []).map((n) => ({
    id: String(n.id),
    category_name: n.category_name,
    children: mapCategories(n.children || []),
  }));
}

const categoryFilterOptions = computed(() => mapCategories(categoryTree.value));

const categoryFormOptions = computed(() => [
  {
    id: '0',
    category_name: '未分类',
    children: mapCategories(categoryTree.value),
  },
]);

function statusTag(status) {
  return { 0: 'info', 1: 'success', 2: 'danger' }[status] || '';
}

function onFaviconError(e) {
  e.target.style.display = 'none';
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchBookMarks({
      keyword: filters.keyword || undefined,
      category_id: filters.category_id || undefined,
      status: filters.status === '' ? undefined : filters.status,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.status) options.status = res.data.options.status;
    if (res.data?.options?.is_bold) options.is_bold = res.data.options.is_bold;
    if (res.data?.options?.categories) categoryTree.value = res.data.options.categories;
  } catch (e) {
    ElMessage.error(e?.message || '加载书签失败');
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
  form.category_id = row ? String(row.category_id ?? '0') : '0';
  form.short_title = row?.short_title || '';
  form.book_title = row?.book_title || '';
  form.book_url = row?.book_url || '';
  form.book_favicon = row?.book_favicon || '';
  form.book_desc = row?.book_desc || '';
  form.sort_order = row?.sort_order ?? 0;
  form.status = row?.status ?? 1;
  form.is_bold = row?.is_bold ?? 0;
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      category_id: form.category_id === '0' || !form.category_id ? 0 : form.category_id,
      short_title: form.short_title || '',
      book_title: form.book_title,
      book_url: form.book_url,
      book_favicon: form.book_favicon || '',
      book_desc: form.book_desc || '',
      sort_order: form.sort_order ?? 0,
      status: form.status ?? 1,
      is_bold: form.is_bold ?? 0,
    };
    if (form.id) {
      await updateBookMark(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createBookMark(payload);
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
    await updateBookMarkSort(row.id, { sort_order: Number(row.sort_order) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除书签「${row.short_title || row.book_title}」吗？`, '提示', {
      type: 'warning',
    });
    await deleteBookMark(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.bookmark-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}
.favicon,
.favicon-fallback {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.favicon-fallback {
  color: #909399;
}
.bookmark-title {
  color: #303133;
  text-decoration: none;
}
.bookmark-title:hover {
  color: #409eff;
}
.bookmark-title.bold {
  font-weight: 700;
}
.bookmark-sub {
  margin-top: 2px;
  color: #909399;
  font-size: 12px;
}
</style>
