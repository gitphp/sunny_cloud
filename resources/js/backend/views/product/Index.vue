<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar">
      <span class="search-label">搜索：</span>
      <el-input v-model="query.keyword" class="search-input" clearable placeholder="名称 / 型号 / 编码" @keyup.enter="loadData" />
      <el-select v-model="query.product_status" clearable placeholder="状态" style="width: 120px; margin-right: 8px">
        <el-option v-for="(label, value) in options.product_status" :key="value" :label="label" :value="Number(value)" />
      </el-select>
      <el-button class="btn-primary-teal" :icon="Search" @click="loadData">搜索</el-button>
      <el-button class="btn-primary-teal" :icon="Plus" @click="goCreate">新增商品</el-button>
    </div>

    <el-table v-loading="loading" :data="list" border style="width: 100%">
      <el-table-column type="index" label="#" width="55" align="center" />
      <el-table-column label="主图" width="80" align="center">
        <template #default="{ row }">
          <el-image v-if="row.main_image_url" :src="row.main_image_url" style="width: 48px; height: 48px" fit="cover" />
          <span v-else style="color: #bbb">-</span>
        </template>
      </el-table-column>
      <el-table-column prop="auto_code" label="编码" width="120" />
      <el-table-column prop="product_name" label="商品名称" min-width="160" show-overflow-tooltip />
      <el-table-column prop="product_model" label="型号" min-width="120" show-overflow-tooltip />
      <el-table-column prop="category_name" label="分类" min-width="100" />
      <el-table-column prop="brand_name" label="品牌" min-width="100" />
      <el-table-column label="状态" width="100" align="center">
        <template #default="{ row }">
          <el-switch
            :model-value="row.product_status === 1"
            inline-prompt
            active-text="上"
            inactive-text="下"
            @change="(val) => onStatusChange(row, val)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="160" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="goEdit(row)">修改</a>
          <el-button class="btn-danger-orange" size="small" @click="handleDelete(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 16px; display: flex; justify-content: flex-end">
      <el-pagination
        v-model:current-page="query.page"
        v-model:page-size="query.per_page"
        background
        layout="total, prev, pager, next"
        :total="total"
        @current-change="loadData"
        @size-change="loadData"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import { deleteProduct, fetchProducts, updateProductStatus } from '../../api/product';

const router = useRouter();
const loading = ref(false);
const list = ref([]);
const total = ref(0);
const options = reactive({ product_status: { 0: '已下架', 1: '已上架' } });
const query = reactive({
  keyword: '',
  product_status: undefined,
  page: 1,
  per_page: 15,
});

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchProducts({
      keyword: query.keyword || undefined,
      product_status: query.product_status,
      page: query.page,
      per_page: query.per_page,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    if (res.data?.options?.product_status) options.product_status = res.data.options.product_status;
  } catch (e) {
    ElMessage.error(e?.message || '加载商品失败');
  } finally {
    loading.value = false;
  }
}

function goCreate() {
  router.push('/backend/product/products/create');
}

function goEdit(row) {
  router.push(`/backend/product/products/${row.id}/edit`);
}

async function onStatusChange(row, enabled) {
  try {
    await updateProductStatus(row.id, { product_status: enabled ? 1 : 0 });
    row.product_status = enabled ? 1 : 0;
  } catch (e) {
    ElMessage.error(e?.message || '状态更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除商品「${row.product_name}」吗？`, '提示', { type: 'warning' });
    await deleteProduct(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>
