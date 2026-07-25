<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="keyword" class="search-input" clearable placeholder="名称 / 编码 / 别名" @keyup.enter="loadData" />
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="brand_code" label="编码" width="120" />
      <el-table-column prop="brand_name" label="品牌名称" min-width="140" />
      <el-table-column prop="alias" label="英文别名" min-width="140" show-overflow-tooltip />
      <el-table-column prop="is_system_label" label="类型" width="100" align="center" />
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input v-model.number="row.sort_order" class="sort-input" size="small" @change="onSortChange(row)" />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.is_show === 1"
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
          <el-button class="btn-danger-orange" size="small" :disabled="row.is_system === 1" @click="handleDelete(row)">删除</el-button>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改品牌' : '添加品牌'" width="520px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="品牌名称" prop="brand_name">
          <el-input v-model="form.brand_name" maxlength="32" show-word-limit />
        </el-form-item>
        <el-form-item label="英文别名" prop="alias">
          <el-input v-model="form.alias" maxlength="64" />
        </el-form-item>
        <el-form-item label="排序号" prop="sort_order">
          <el-input-number v-model="form.sort_order" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="is_show">
          <el-radio-group v-model="form.is_show">
            <el-radio v-for="(label, value) in options.is_show" :key="value" :value="Number(value)">{{ label }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="brand_remark">
          <el-input v-model="form.brand_remark" type="textarea" :rows="2" maxlength="512" show-word-limit />
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
  createProductBrand,
  deleteProductBrand,
  fetchProductBrands,
  updateProductBrand,
  updateProductBrandSort,
  updateProductBrandStatus,
} from '../../api/productBrand';

const loading = ref(false);
const saving = ref(false);
const keyword = ref('');
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const formRef = ref();
const options = reactive({ is_show: { 0: '隐藏', 1: '显示' } });
const form = reactive({
  id: null,
  brand_name: '',
  alias: '',
  sort_order: 0,
  is_show: 1,
  brand_remark: '',
});
const rules = {
  brand_name: [{ required: true, message: '请输入品牌名称', trigger: 'blur' }],
};

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchProductBrands({
      keyword: keyword.value || undefined,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.is_show) options.is_show = res.data.options.is_show;
  } catch (e) {
    ElMessage.error(e?.message || '加载品牌失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.brand_name = row?.brand_name || '';
  form.alias = row?.alias || '';
  form.sort_order = row?.sort_order ?? 0;
  form.is_show = row?.is_show ?? 1;
  form.brand_remark = row?.brand_remark || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      brand_name: form.brand_name,
      alias: form.alias || '',
      sort_order: form.sort_order ?? 0,
      is_show: form.is_show ?? 1,
      brand_remark: form.brand_remark || '',
    };
    if (form.id) {
      await updateProductBrand(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createProductBrand(payload);
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
    await updateProductBrandSort(row.id, { sort_order: Number(row.sort_order) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateProductBrandStatus(row.id, { is_show: enabled ? 1 : 0 });
    row.is_show = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除品牌「${row.brand_name}」吗？`, '提示', { type: 'warning' });
    await deleteProductBrand(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
