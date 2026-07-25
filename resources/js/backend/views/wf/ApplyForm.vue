<template>
  <div class="admin-page-card product-form-page">
    <div class="page-toolbar" style="justify-content: space-between">
      <h3 style="margin: 0; font-size: 18px">{{ isEdit ? '编辑申请' : '发起申请' }}</h3>
      <div>
        <el-button @click="goBack">返回</el-button>
        <el-button :loading="saving" @click="saveDraft">存草稿</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="saveAndSubmit">提交审批</el-button>
      </div>
    </div>

    <el-form ref="formRef" :model="form" :rules="rules" label-width="110px" style="max-width: 760px; padding-top: 12px">
      <el-form-item label="流程模板" prop="flow_def_id">
        <el-select
          v-model="form.flow_def_id"
          filterable
          placeholder="请选择已发布流程"
          style="width: 100%"
          :disabled="isEdit"
          @change="onDefChange"
        >
          <el-option
            v-for="d in defOptions"
            :key="d.id"
            :label="`${d.type_name || ''} - ${d.flow_name}`"
            :value="String(d.id)"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="单据标题" prop="title">
        <el-input v-model="form.title" maxlength="256" placeholder="如：张三-事假申请" />
      </el-form-item>
      <el-form-item label="备注">
        <el-input v-model="form.remark" type="textarea" :rows="2" maxlength="1024" />
      </el-form-item>
      <el-form-item label="抄送人">
        <el-select v-model="form.cc_uids" multiple filterable collapse-tags placeholder="可选" style="width: 100%">
          <el-option
            v-for="u in userOptions"
            :key="u.id"
            :label="u.nick_name || u.user_name"
            :value="String(u.id)"
          />
        </el-select>
      </el-form-item>

      <template v-if="fields.length">
        <el-divider content-position="left">表单内容</el-divider>
        <el-form-item
          v-for="f in fields"
          :key="f.field_key"
          :label="f.field_name"
          :required="f.is_required === 1"
        >
          <el-input-number
            v-if="f.field_type === 'number'"
            v-model="form.form_data[f.field_key]"
            style="width: 100%"
          />
          <el-input
            v-else-if="f.field_type === 'textarea'"
            v-model="form.form_data[f.field_key]"
            type="textarea"
            :rows="3"
          />
          <el-radio-group v-else-if="f.field_type === 'radio'" v-model="form.form_data[f.field_key]">
            <el-radio v-for="opt in f.field_options || []" :key="opt.value" :value="opt.value">{{ opt.label }}</el-radio>
          </el-radio-group>
          <el-select
            v-else-if="f.field_type === 'select'"
            v-model="form.form_data[f.field_key]"
            clearable
            style="width: 100%"
          >
            <el-option v-for="opt in f.field_options || []" :key="opt.value" :label="opt.label" :value="opt.value" />
          </el-select>
          <el-date-picker
            v-else-if="f.field_type === 'date'"
            v-model="form.form_data[f.field_key]"
            type="date"
            value-format="YYYY-MM-DD"
            style="width: 100%"
          />
          <el-input v-else v-model="form.form_data[f.field_key]" />
        </el-form-item>
      </template>
    </el-form>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import {
  createWfApply,
  fetchPublishedDefinition,
  fetchPublishedDefinitions,
  fetchWfApply,
  submitWfApply,
  updateWfApply,
} from '../../api/wfFlowApply';
import { fetchUsers } from '../../api/user';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => Boolean(route.params.id));
const saving = ref(false);
const formRef = ref();
const defOptions = ref([]);
const userOptions = ref([]);
const fields = ref([]);
const applyId = ref(route.params.id || '');

const form = reactive({
  flow_def_id: '',
  title: '',
  remark: '',
  form_data: {},
  cc_uids: [],
});

const rules = {
  flow_def_id: [{ required: true, message: '请选择流程模板', trigger: 'change' }],
  title: [{ required: true, message: '请填写标题', trigger: 'blur' }],
};

async function loadDefs() {
  const res = await fetchPublishedDefinitions();
  defOptions.value = res.data || [];
}

async function onDefChange(id) {
  if (!id) {
    fields.value = [];
    return;
  }
  const res = await fetchPublishedDefinition(id);
  fields.value = res.data?.forms || [];
  const data = {};
  fields.value.forEach((f) => {
    data[f.field_key] = form.form_data[f.field_key] ?? (f.field_type === 'number' ? undefined : '');
  });
  form.form_data = data;
}

async function loadDetail() {
  if (!isEdit.value) return;
  const res = await fetchWfApply(route.params.id);
  const data = res.data || {};
  applyId.value = data.id;
  form.flow_def_id = String(data.flow_def_id || '');
  form.title = data.title || '';
  form.remark = data.remark || '';
  form.form_data = data.form_data || {};
  form.cc_uids = (data.cc_users || []).map((c) => String(c.cc_uid));
  fields.value = data.forms || [];
  if (!fields.value.length && form.flow_def_id) {
    await onDefChange(form.flow_def_id);
  }
}

function payload() {
  return {
    flow_def_id: form.flow_def_id,
    title: form.title,
    remark: form.remark || '',
    form_data: form.form_data || {},
    cc_uids: form.cc_uids || [],
  };
}

async function saveDraft() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    if (applyId.value) {
      await updateWfApply(applyId.value, payload());
      ElMessage.success('草稿已保存');
    } else {
      const res = await createWfApply(payload());
      applyId.value = res.data?.id;
      ElMessage.success('草稿已保存');
      router.replace(`/backend/wf/applies/${applyId.value}/edit`);
    }
  } catch (e) {
    ElMessage.error(e?.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

async function saveAndSubmit() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    let id = applyId.value;
    if (!id) {
      const res = await createWfApply(payload());
      id = res.data?.id;
      applyId.value = id;
    } else {
      await updateWfApply(id, payload());
    }
    await submitWfApply(id, payload());
    ElMessage.success('提交成功');
    router.push(`/backend/wf/applies/${id}`);
  } catch (e) {
    ElMessage.error(e?.message || '提交失败');
  } finally {
    saving.value = false;
  }
}

function goBack() {
  router.push('/backend/wf/applies');
}

onMounted(async () => {
  try {
    const [, users] = await Promise.all([loadDefs(), fetchUsers({ per_page: 200 })]);
    userOptions.value = users.data?.list || [];
    await loadDetail();
  } catch (e) {
    ElMessage.error(e?.message || '加载失败');
  }
});
</script>
