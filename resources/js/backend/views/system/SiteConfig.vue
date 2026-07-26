<template>
  <div class="admin-page-card">
    <div class="page-toolbar" style="justify-content: space-between">
      <strong>网站设置</strong>
      <div>
        <el-button @click="openAdd">新增配置项</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="saveBatch">保存当前分组</el-button>
      </div>
    </div>

    <el-tabs v-model="activeGroup" v-loading="loading" @tab-change="onTabChange">
      <el-tab-pane
        v-for="(label, value) in groupLabels"
        :key="value"
        :label="label"
        :name="value"
      >
        <el-form label-width="140px" style="max-width: 760px; margin-top: 12px">
          <el-form-item
            v-for="item in currentItems"
            :key="item.id"
            :label="item.conf_desc || item.conf_key"
          >
            <el-input
              v-if="item.input_type === 'textarea' || item.input_type === 'json'"
              v-model="formMap[item.id]"
              type="textarea"
              :rows="item.input_type === 'json' ? 4 : 3"
            />
            <el-input v-else v-model="formMap[item.id]" :placeholder="item.conf_key" />
            <div class="config-key">{{ item.conf_key }} · {{ item.input_type_label || item.input_type }}</div>
          </el-form-item>
          <el-empty v-if="!currentItems.length" description="当前分组暂无配置项" />
        </el-form>
      </el-tab-pane>
    </el-tabs>

    <el-dialog v-model="dialogVisible" title="新增配置项" width="520px" destroy-on-close>
      <el-form ref="formRef" :model="createForm" :rules="rules" label-width="100px">
        <el-form-item label="分组" prop="conf_group">
          <el-select v-model="createForm.conf_group" style="width: 100%">
            <el-option v-for="(label, value) in groupLabels" :key="value" :label="label" :value="value" />
          </el-select>
        </el-form-item>
        <el-form-item label="键名" prop="conf_key">
          <el-input v-model="createForm.conf_key" placeholder="如 site_name" />
        </el-form-item>
        <el-form-item label="说明" prop="conf_desc">
          <el-input v-model="createForm.conf_desc" />
        </el-form-item>
        <el-form-item label="输入类型" prop="input_type">
          <el-select v-model="createForm.input_type" style="width: 100%">
            <el-option v-for="(label, value) in inputTypes" :key="value" :label="label" :value="value" />
          </el-select>
        </el-form-item>
        <el-form-item label="默认值" prop="conf_value">
          <el-input v-model="createForm.conf_value" type="textarea" :rows="2" />
        </el-form-item>
        <el-form-item label="排序" prop="conf_sort">
          <el-input-number v-model="createForm.conf_sort" :min="0" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="creating" @click="submitCreate">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage } from 'element-plus';
import { batchUpdateSiteConfigs, createSiteConfig, fetchSiteConfigs } from '../../api/siteConfig';

const loading = ref(false);
const saving = ref(false);
const creating = ref(false);
const dialogVisible = ref(false);
const formRef = ref();
const activeGroup = ref('basic');
const list = ref([]);
const formMap = reactive({});

const groupLabels = reactive({
  basic: '基础设置',
  seo: 'SEO设置',
  contact: '联系方式',
  social: '社交账号',
});

const inputTypes = reactive({
  text: '单行文本',
  textarea: '多行文本',
  image: '图片',
  file: '文件',
  json: 'JSON',
});

const createForm = reactive({
  conf_group: 'basic',
  conf_key: '',
  conf_desc: '',
  input_type: 'text',
  conf_value: '',
  conf_sort: 0,
});

const rules = {
  conf_group: [{ required: true, message: '请选择分组', trigger: 'change' }],
  conf_key: [{ required: true, message: '请输入键名', trigger: 'blur' }],
  input_type: [{ required: true, message: '请选择输入类型', trigger: 'change' }],
};

const currentItems = computed(() =>
  (list.value || []).filter((item) => item.conf_group === activeGroup.value),
);

function syncFormMap(items) {
  Object.keys(formMap).forEach((key) => delete formMap[key]);
  items.forEach((item) => {
    formMap[item.id] = item.conf_value ?? '';
  });
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchSiteConfigs();
    list.value = res.data?.list || [];
    if (res.data?.options?.conf_group) Object.assign(groupLabels, res.data.options.conf_group);
    if (res.data?.options?.input_type) Object.assign(inputTypes, res.data.options.input_type);
    syncFormMap(list.value);
    if (!groupLabels[activeGroup.value]) {
      activeGroup.value = Object.keys(groupLabels)[0] || 'basic';
    }
  } catch (e) {
    ElMessage.error(e?.message || '加载配置失败');
  } finally {
    loading.value = false;
  }
}

function onTabChange() {
  // keep formMap values across tabs
}

async function saveBatch() {
  const items = currentItems.value.map((item) => ({
    id: item.id,
    conf_value: formMap[item.id] ?? '',
  }));
  if (!items.length) {
    ElMessage.warning('当前分组没有可保存的配置');
    return;
  }
  saving.value = true;
  try {
    await batchUpdateSiteConfigs(items);
    ElMessage.success('保存成功');
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

function openAdd() {
  createForm.conf_group = activeGroup.value || 'basic';
  createForm.conf_key = '';
  createForm.conf_desc = '';
  createForm.input_type = 'text';
  createForm.conf_value = '';
  createForm.conf_sort = 0;
  dialogVisible.value = true;
}

async function submitCreate() {
  await formRef.value?.validate();
  creating.value = true;
  try {
    await createSiteConfig({ ...createForm });
    ElMessage.success('添加成功');
    dialogVisible.value = false;
    await loadData();
    activeGroup.value = createForm.conf_group;
  } catch (e) {
    ElMessage.error(e?.message || '添加失败');
  } finally {
    creating.value = false;
  }
}

onMounted(loadData);
</script>

<style scoped>
.config-key {
  margin-top: 4px;
  color: #909399;
  font-size: 12px;
}
</style>
