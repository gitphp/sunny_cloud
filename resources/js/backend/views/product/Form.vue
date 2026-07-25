<template>
  <div class="admin-page-card product-form-page">
    <div class="page-toolbar" style="justify-content: space-between">
      <h3 style="margin: 0; font-size: 18px">{{ isEdit ? '编辑商品' : '新增商品' }}</h3>
      <div>
        <el-button @click="goBack">返回</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submit">保存</el-button>
      </div>
    </div>

    <el-tabs v-model="activeTab" class="product-tabs">
      <el-tab-pane label="基础信息" name="basic">
        <el-form ref="basicRef" :model="form" :rules="basicRules" label-width="100px" class="basic-form">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="商品名称" prop="product_name">
                <el-input v-model="form.product_name" maxlength="30" show-word-limit placeholder="请输入商品名称，尽量包含材质、风格、规格等信息" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="型号" prop="product_model">
                <el-input v-model="form.product_model" placeholder="请输入型号" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="商品分类" prop="category_id">
                <el-tree-select
                  v-model="form.category_id"
                  :data="categoryTree"
                  check-strictly
                  filterable
                  clearable
                  placeholder="请选择分类"
                  style="width: 100%"
                  :props="{ label: 'category_name', value: 'id', children: 'children' }"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="品牌" prop="brand_id">
                <el-select v-model="form.brand_id" filterable clearable placeholder="请选择品牌" style="width: 100%">
                  <el-option v-for="b in brandOptions" :key="b.id" :label="b.brand_name" :value="String(b.id)" />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="材质" prop="material_quality">
                <el-input v-model="form.material_quality" placeholder="请输入材质" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="填充" prop="filling">
                <el-input v-model="form.filling" placeholder="请输入填充" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="简短描述" prop="short_desc">
                <el-input v-model="form.short_desc" type="textarea" :rows="4" placeholder="请输入商品简短描述" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="商品状态" prop="product_status">
                <el-switch
                  v-model="form.product_status"
                  :active-value="1"
                  :inactive-value="0"
                  inline-prompt
                  active-text="上架"
                  inactive-text="下架"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
      </el-tab-pane>

      <el-tab-pane label="规格定价" name="sku">
        <div class="spec-builder">
          <div v-for="(row, idx) in specRows" :key="idx" class="spec-row">
            <span class="spec-label">规格</span>
            <el-select v-model="row.spec_id" placeholder="请选择" style="width: 180px" @change="onSpecChange(row)">
              <el-option
                v-for="s in availableSpecs(row.spec_id)"
                :key="s.id"
                :label="s.spec_name"
                :value="String(s.id)"
              />
            </el-select>
            <span class="spec-label">规格值</span>
            <el-select
              v-model="row.spec_value_ids"
              multiple
              collapse-tags
              collapse-tags-tooltip
              placeholder="请选择"
              style="width: 360px"
              @change="rebuildSkus"
            >
              <el-option
                v-for="v in valuesOf(row.spec_id)"
                :key="v.id"
                :label="v.value"
                :value="String(v.id)"
              />
            </el-select>
            <el-button link type="danger" @click="removeSpecRow(idx)">删除</el-button>
          </div>
          <el-button link type="primary" @click="addSpecRow">+ 新增一行</el-button>
        </div>

        <el-table :data="skuList" border style="width: 100%; margin-top: 16px">
          <el-table-column
            v-for="col in skuSpecColumns"
            :key="col.spec_id"
            :label="col.spec_name"
            min-width="140"
          >
            <template #default="{ row }">
              {{ row.spec_labels?.[col.spec_id] || '-' }}
            </template>
          </el-table-column>
          <el-table-column label="单价(元)" width="140">
            <template #default="{ row }">
              <el-input-number v-model="row.price" :min="0" :precision="2" controls-position="right" style="width: 120px" />
            </template>
          </el-table-column>
          <el-table-column label="划线价(元)" width="140">
            <template #default="{ row }">
              <el-input-number v-model="row.market_price" :min="0" :precision="2" controls-position="right" style="width: 120px" />
            </template>
          </el-table-column>
          <el-table-column label="重量(KG)" width="130">
            <template #default="{ row }">
              <el-input-number v-model="row.weight" :min="0" :precision="2" controls-position="right" style="width: 110px" />
            </template>
          </el-table-column>
          <el-table-column label="体积(m³)" width="140">
            <template #default="{ row }">
              <el-input-number v-model="row.volume" :min="0" :precision="4" controls-position="right" style="width: 120px" />
            </template>
          </el-table-column>
          <el-table-column label="操作" width="80" align="center">
            <template #default="{ $index }">
              <el-button link type="danger" :icon="Delete" @click="skuList.splice($index, 1)" />
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="图文资质" name="media">
        <el-form label-width="100px">
          <el-form-item label="主图">
            <el-upload
              class="media-uploader"
              drag
              :show-file-list="false"
              :http-request="(opt) => doUpload(opt, 1)"
              accept="image/*"
            >
              <div v-if="mainImage?.file_url" class="preview-box">
                <el-image :src="mainImage.file_url" fit="contain" style="width: 100%; height: 120px" />
              </div>
              <div v-else class="upload-placeholder">
                <el-icon :size="28"><UploadFilled /></el-icon>
                <div>点击上传主图</div>
              </div>
            </el-upload>
          </el-form-item>

          <el-form-item label="详情图">
            <div class="detail-list">
              <div v-for="(img, i) in detailImages" :key="i" class="detail-item">
                <el-image :src="img.file_url" fit="cover" style="width: 96px; height: 96px" />
                <el-button class="remove-btn" size="small" type="danger" circle @click="detailImages.splice(i, 1)">×</el-button>
              </div>
              <el-upload
                class="media-uploader small"
                drag
                :show-file-list="false"
                :http-request="(opt) => doUpload(opt, 2)"
                accept="image/*"
              >
                <div class="upload-placeholder">
                  <el-icon :size="24"><UploadFilled /></el-icon>
                  <div>点击上传详情图</div>
                </div>
              </el-upload>
            </div>
          </el-form-item>

          <el-form-item label="视频链接">
            <el-input v-model="videoUrl" placeholder="上传视频链接" />
          </el-form-item>

          <el-form-item label="资质文件">
            <div class="detail-list">
              <div v-for="(f, i) in qualifyFiles" :key="i" class="file-chip">
                <a :href="f.file_url" target="_blank">{{ f.file_name || f.file_url }}</a>
                <el-button link type="danger" @click="qualifyFiles.splice(i, 1)">删除</el-button>
              </div>
              <el-upload
                class="media-uploader"
                drag
                :show-file-list="false"
                :http-request="(opt) => doUpload(opt, 4)"
                accept=".pdf,.doc,.docx"
              >
                <div class="upload-placeholder">
                  <el-icon :size="28"><UploadFilled /></el-icon>
                  <div>上传资质文件 (PDF/DOC)</div>
                </div>
              </el-upload>
            </div>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Delete, UploadFilled } from '@element-plus/icons-vue';
import { createProduct, fetchProduct, updateProduct, uploadProductFile } from '../../api/product';
import { fetchProductBrands } from '../../api/productBrand';
import { fetchProductCategories } from '../../api/productCategory';
import { fetchProductSpecifications } from '../../api/productSpecification';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => Boolean(route.params.id));
const activeTab = ref('basic');
const saving = ref(false);
const basicRef = ref();

const brandOptions = ref([]);
const categoryTree = ref([]);
const specOptions = ref([]);

const form = reactive({
  product_name: '',
  product_model: '',
  category_id: '',
  brand_id: '',
  material_quality: '',
  filling: '',
  short_desc: '',
  product_status: 1,
  sort_order: 0,
});

const basicRules = {
  product_name: [{ required: true, message: '请输入商品名称', trigger: 'blur' }],
  product_model: [{ required: true, message: '请输入型号', trigger: 'blur' }],
  category_id: [{ required: true, message: '请选择商品分类', trigger: 'change' }],
  product_status: [{ required: true, message: '请设置商品状态', trigger: 'change' }],
};

const specRows = ref([]);
const skuList = ref([]);
const mainImage = ref(null);
const detailImages = ref([]);
const qualifyFiles = ref([]);
const videoUrl = ref('');

const skuSpecColumns = computed(() =>
  specRows.value
    .filter((r) => r.spec_id && (r.spec_value_ids || []).length)
    .map((r) => {
      const spec = specOptions.value.find((s) => String(s.id) === String(r.spec_id));
      return { spec_id: String(r.spec_id), spec_name: spec?.spec_name || '规格' };
    }),
);

function mapTree(nodes) {
  return (nodes || []).map((n) => ({
    id: String(n.id),
    category_name: n.category_name,
    children: mapTree(n.children || []),
  }));
}

function valuesOf(specId) {
  const spec = specOptions.value.find((s) => String(s.id) === String(specId));
  return (spec?.values || []).map((v) => ({ ...v, id: String(v.id) }));
}

function availableSpecs(currentId) {
  const used = new Set(specRows.value.map((r) => String(r.spec_id)).filter(Boolean));
  return specOptions.value.filter((s) => String(s.id) === String(currentId) || !used.has(String(s.id)));
}

function addSpecRow() {
  specRows.value.push({ spec_id: '', spec_value_ids: [] });
}

function removeSpecRow(idx) {
  specRows.value.splice(idx, 1);
  rebuildSkus();
}

function onSpecChange(row) {
  row.spec_value_ids = [];
  rebuildSkus();
}

function cartesian(arrays) {
  if (!arrays.length) return [[]];
  return arrays.reduce(
    (acc, cur) => acc.flatMap((a) => cur.map((b) => [...a, b])),
    [[]],
  );
}

function rebuildSkus() {
  const dims = specRows.value
    .filter((r) => r.spec_id && (r.spec_value_ids || []).length)
    .map((r) => {
      const values = valuesOf(r.spec_id).filter((v) => r.spec_value_ids.includes(String(v.id)));
      return values.map((v) => ({
        spec_id: String(r.spec_id),
        spec_name: specOptions.value.find((s) => String(s.id) === String(r.spec_id))?.spec_name || '',
        spec_value_id: String(v.id),
        value: v.value,
      }));
    });

  if (!dims.length) {
    skuList.value = [];
    return;
  }

  const combos = cartesian(dims);
  const oldMap = new Map(
    skuList.value.map((s) => [s._key, s]),
  );

  skuList.value = combos.map((combo) => {
    const key = combo.map((c) => c.spec_value_id).sort().join('_');
    const old = oldMap.get(key);
    const labels = {};
    combo.forEach((c) => {
      labels[c.spec_id] = c.value;
    });
    return {
      _key: key,
      price: old?.price ?? 0,
      market_price: old?.market_price ?? 0,
      weight: old?.weight ?? 0,
      volume: old?.volume ?? 0,
      sale_status: 1,
      spec_values: combo.map((c) => ({
        spec_id: c.spec_id,
        spec_value_id: c.spec_value_id,
      })),
      spec_labels: labels,
    };
  });
}

async function doUpload({ file }, mediaType) {
  try {
    const res = await uploadProductFile(file, mediaType);
    const meta = { ...res.data, media_type: mediaType };
    if (mediaType === 1) mainImage.value = meta;
    else if (mediaType === 2) detailImages.value.push(meta);
    else if (mediaType === 4) qualifyFiles.value.push(meta);
    ElMessage.success('上传成功');
  } catch (e) {
    ElMessage.error(e?.message || '上传失败');
  }
}

function buildMediaPayload() {
  const media = [];
  if (mainImage.value?.file_url) {
    media.push({ ...mainImage.value, media_type: 1, sort_order: 0 });
  }
  detailImages.value.forEach((img, i) => {
    media.push({ ...img, media_type: 2, sort_order: i + 1 });
  });
  if (videoUrl.value) {
    media.push({
      media_type: 3,
      file_url: videoUrl.value,
      file_name: '',
      file_key: '',
      storage_provider: 'local',
      extension: '',
      file_size: 0,
      file_type: '',
      sort_order: 0,
    });
  }
  qualifyFiles.value.forEach((f, i) => {
    media.push({ ...f, media_type: 4, sort_order: i });
  });
  return media;
}

async function loadOptions() {
  const [brands, cats, specs] = await Promise.all([
    fetchProductBrands({ per_page: 200 }),
    fetchProductCategories(),
    fetchProductSpecifications({ per_page: 200 }),
  ]);
  brandOptions.value = brands.data?.list || [];
  categoryTree.value = mapTree(cats.data?.list || []);
  specOptions.value = (specs.data?.list || []).map((s) => ({
    ...s,
    id: String(s.id),
    values: (s.values || []).map((v) => ({ ...v, id: String(v.id) })),
  }));
}

async function loadDetail() {
  if (!isEdit.value) {
    addSpecRow();
    return;
  }
  const res = await fetchProduct(route.params.id);
  const data = res.data || {};
  form.product_name = data.product_name || '';
  form.product_model = data.product_model || '';
  form.category_id = data.category_id ? String(data.category_id) : '';
  form.brand_id = data.brand_id && data.brand_id !== '0' ? String(data.brand_id) : '';
  form.material_quality = data.material_quality || '';
  form.filling = data.filling || '';
  form.short_desc = data.short_desc || '';
  form.product_status = data.product_status ?? 1;
  form.sort_order = data.sort_order ?? 0;

  specRows.value = (data.spec_rows || []).map((r) => ({
    spec_id: String(r.spec_id),
    spec_value_ids: (r.spec_value_ids || []).map(String),
  }));
  if (!specRows.value.length) addSpecRow();

  skuList.value = (data.skus || []).map((s) => {
    const labels = {};
    (s.spec_values || []).forEach((sv) => {
      labels[String(sv.spec_id)] = sv.value;
    });
    const key = (s.spec_values || []).map((sv) => String(sv.spec_value_id)).sort().join('_');
    return {
      _key: key,
      price: Number(s.price || 0),
      market_price: Number(s.market_price || 0),
      weight: Number(s.weight || 0),
      volume: Number(s.volume || 0),
      sale_status: s.sale_status ?? 1,
      spec_values: (s.spec_values || []).map((sv) => ({
        spec_id: String(sv.spec_id),
        spec_value_id: String(sv.spec_value_id),
      })),
      spec_labels: labels,
    };
  });

  const media = data.media || [];
  mainImage.value = media.find((m) => m.media_type === 1) || (data.main_image_url ? { file_url: data.main_image_url, media_type: 1 } : null);
  detailImages.value = media.filter((m) => m.media_type === 2);
  qualifyFiles.value = media.filter((m) => m.media_type === 4);
  videoUrl.value = media.find((m) => m.media_type === 3)?.file_url || '';
}

async function submit() {
  await basicRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      product_name: form.product_name,
      product_model: form.product_model,
      category_id: form.category_id,
      brand_id: form.brand_id || 0,
      material_quality: form.material_quality || '',
      filling: form.filling || '',
      short_desc: form.short_desc || '',
      product_status: form.product_status ?? 1,
      sort_order: form.sort_order ?? 0,
      main_image_url: mainImage.value?.file_url || '',
      skus: skuList.value.map((s, i) => ({
        price: s.price || 0,
        market_price: s.market_price || 0,
        weight: s.weight || 0,
        volume: s.volume || 0,
        sale_status: s.sale_status ?? 1,
        sort_order: i,
        spec_values: s.spec_values || [],
      })),
      media: buildMediaPayload(),
    };

    if (isEdit.value) {
      await updateProduct(route.params.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createProduct(payload);
      ElMessage.success('添加成功');
    }
    goBack();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

function goBack() {
  router.push('/backend/product/products');
}

onMounted(async () => {
  try {
    await loadOptions();
    await loadDetail();
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  }
});
</script>

<style scoped>
.product-tabs {
  margin-top: 8px;
}
.basic-form {
  max-width: 960px;
  padding-top: 12px;
}
.spec-builder {
  padding: 8px 0 4px;
}
.spec-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}
.spec-label {
  color: #666;
}
.media-uploader {
  width: 100%;
}
.media-uploader :deep(.el-upload),
.media-uploader :deep(.el-upload-dragger) {
  width: 100%;
}
.media-uploader.small {
  width: 180px;
}
.upload-placeholder {
  color: #909399;
  padding: 18px 0;
}
.detail-list {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: flex-start;
}
.detail-item {
  position: relative;
}
.remove-btn {
  position: absolute;
  top: -8px;
  right: -8px;
}
.file-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
}
.preview-box {
  padding: 8px;
}
</style>
