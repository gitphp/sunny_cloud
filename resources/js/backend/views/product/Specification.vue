<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="keyword" class="search-input" clearable placeholder="名称 / 编码" @keyup.enter="loadData" />
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="openForm()">添加</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column prop="spec_code" label="编码" width="120" />
      <el-table-column prop="spec_name" label="规格名称" min-width="140" />
      <el-table-column label="规格值" min-width="220">
        <template #default="{ row }">
          <el-tag v-for="v in row.values || []" :key="v.id" size="small" style="margin: 2px">{{ v.value }}</el-tag>
          <span v-if="!(row.values || []).length" style="color: #999">暂无</span>
        </template>
      </el-table-column>
      <el-table-column label="排序号" width="110" align="center">
        <template #default="{ row }">
          <el-input v-model.number="row.sort_order" class="sort-input" size="small" @change="onSortChange(row)" />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="90" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.spec_status === 1"
            inline-prompt
            active-text="显"
            inactive-text="隐"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="220" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <a class="action-edit" @click="openValues(row)">规格值</a>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改规格' : '添加规格'" width="520px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="规格名称" prop="spec_name">
          <el-input v-model="form.spec_name" placeholder="如：颜色、材质" />
        </el-form-item>
        <el-form-item label="排序号" prop="sort_order">
          <el-input-number v-model="form.sort_order" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="spec_status">
          <el-radio-group v-model="form.spec_status">
            <el-radio v-for="(label, value) in options.spec_status" :key="value" :value="Number(value)">{{ label }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="备注" prop="spec_remark">
          <el-input v-model="form.spec_remark" type="textarea" :rows="2" maxlength="512" show-word-limit />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>

    <el-dialog v-model="valueVisible" :title="`规格值管理 - ${currentSpec?.spec_name || ''}`" width="720px" destroy-on-close>
      <div class="page-toolbar" style="margin-bottom: 12px">
        <el-button class="btn-primary-teal" size="small" :icon="Plus" @click="openValueForm()">添加规格值</el-button>
      </div>
      <el-table :data="valueList" border v-loading="valueLoading">
        <el-table-column prop="value_code" label="编码" width="120" />
        <el-table-column prop="value" label="规格值" min-width="140" />
        <el-table-column label="排序" width="110" align="center">
          <template #default="{ row }">
            <el-input v-model.number="row.sort_order" class="sort-input" size="small" @change="onValueSortChange(row)" />
          </template>
        </el-table-column>
        <el-table-column label="状态" width="90" align="center">
          <template #default="{ row }">
            <el-switch
              :model-value="row.value_status === 1"
              inline-prompt
              active-text="显"
              inactive-text="隐"
              @change="(val) => onValueStatusChange(row, val)"
            />
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" align="center">
          <template #default="{ row }">
            <a class="action-edit" @click="openValueForm(row)">修改</a>
            <el-button class="btn-danger-orange" size="small" @click="handleValueDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>

    <el-dialog v-model="valueFormVisible" :title="valueForm.id ? '修改规格值' : '添加规格值'" width="480px" destroy-on-close>
      <el-form ref="valueFormRef" :model="valueForm" :rules="valueRules" label-width="90px">
        <el-form-item label="规格值" prop="value">
          <el-input v-model="valueForm.value" placeholder="如：红色、实木" />
        </el-form-item>
        <el-form-item label="排序号" prop="sort_order">
          <el-input-number v-model="valueForm.sort_order" :min="0" :max="9999" />
        </el-form-item>
        <el-form-item label="状态" prop="value_status">
          <el-radio-group v-model="valueForm.value_status">
            <el-radio v-for="(label, value) in options.value_status" :key="value" :value="Number(value)">{{ label }}</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="valueFormVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="valueSaving" @click="submitValueForm">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  createProductSpecification,
  createSpecValue,
  deleteProductSpecification,
  deleteSpecValue,
  fetchProductSpecifications,
  fetchSpecValues,
  updateProductSpecification,
  updateProductSpecificationSort,
  updateProductSpecificationStatus,
  updateSpecValue,
  updateSpecValueSort,
  updateSpecValueStatus,
} from '../../api/productSpecification';

const loading = ref(false);
const saving = ref(false);
const valueLoading = ref(false);
const valueSaving = ref(false);
const keyword = ref('');
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const valueVisible = ref(false);
const valueFormVisible = ref(false);
const formRef = ref();
const valueFormRef = ref();
const currentSpec = ref(null);
const valueList = ref([]);
const options = reactive({
  spec_status: { 0: '隐藏', 1: '显示' },
  value_status: { 0: '隐藏', 1: '显示' },
});

const form = reactive({
  id: null,
  spec_name: '',
  sort_order: 0,
  spec_status: 1,
  spec_remark: '',
});
const valueForm = reactive({
  id: null,
  value: '',
  sort_order: 0,
  value_status: 1,
});
const rules = { spec_name: [{ required: true, message: '请输入规格名称', trigger: 'blur' }] };
const valueRules = { value: [{ required: true, message: '请输入规格值', trigger: 'blur' }] };

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchProductSpecifications({
      keyword: keyword.value || undefined,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options) Object.assign(options, res.data.options);
  } catch (e) {
    ElMessage.error(e?.message || '加载规格失败');
  } finally {
    loading.value = false;
  }
}

function openForm(row = null) {
  form.id = row?.id ?? null;
  form.spec_name = row?.spec_name || '';
  form.sort_order = row?.sort_order ?? 0;
  form.spec_status = row?.spec_status ?? 1;
  form.spec_remark = row?.spec_remark || '';
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      spec_name: form.spec_name,
      sort_order: form.sort_order ?? 0,
      spec_status: form.spec_status ?? 1,
      spec_remark: form.spec_remark || '',
    };
    if (form.id) {
      await updateProductSpecification(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createProductSpecification(payload);
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
    await updateProductSpecificationSort(row.id, { sort_order: Number(row.sort_order) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function onStatusChange(row, enabled) {
  try {
    await updateProductSpecificationStatus(row.id, { spec_status: enabled ? 1 : 0 });
    row.spec_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除规格「${row.spec_name}」及其规格值吗？`, '提示', { type: 'warning' });
    await deleteProductSpecification(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

async function openValues(row) {
  currentSpec.value = row;
  valueVisible.value = true;
  await loadValues();
}

async function loadValues() {
  if (!currentSpec.value?.id) return;
  valueLoading.value = true;
  try {
    const res = await fetchSpecValues(currentSpec.value.id);
    valueList.value = res.data || [];
  } catch (e) {
    ElMessage.error(e?.message || '加载规格值失败');
  } finally {
    valueLoading.value = false;
  }
}

function openValueForm(row = null) {
  valueForm.id = row?.id ?? null;
  valueForm.value = row?.value || '';
  valueForm.sort_order = row?.sort_order ?? 0;
  valueForm.value_status = row?.value_status ?? 1;
  valueFormVisible.value = true;
}

async function submitValueForm() {
  await valueFormRef.value?.validate();
  valueSaving.value = true;
  try {
    const payload = {
      value: valueForm.value,
      sort_order: valueForm.sort_order ?? 0,
      value_status: valueForm.value_status ?? 1,
    };
    if (valueForm.id) {
      await updateSpecValue(valueForm.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createSpecValue(currentSpec.value.id, payload);
      ElMessage.success('添加成功');
    }
    valueFormVisible.value = false;
    await loadValues();
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    valueSaving.value = false;
  }
}

async function onValueSortChange(row) {
  try {
    await updateSpecValueSort(row.id, { sort_order: Number(row.sort_order) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadValues();
  }
}

async function onValueStatusChange(row, enabled) {
  try {
    await updateSpecValueStatus(row.id, { value_status: enabled ? 1 : 0 });
    row.value_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadValues();
  }
}

async function handleValueDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除规格值「${row.value}」吗？`, '提示', { type: 'warning' });
    await deleteSpecValue(row.id);
    ElMessage.success('删除成功');
    await loadValues();
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
