<template>
  <div class="admin-page-card product-form-page">
    <div class="page-toolbar" style="justify-content: space-between">
      <h3 style="margin: 0; font-size: 18px">{{ isEdit ? '配置流程模板' : '新增流程模板' }}</h3>
      <div>
        <el-button @click="goBack">返回</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submit">保存</el-button>
      </div>
    </div>

    <el-tabs v-model="activeTab">
      <el-tab-pane label="基本信息" name="basic">
        <el-form ref="basicRef" :model="form" :rules="basicRules" label-width="110px" style="max-width: 720px; padding-top: 12px">
          <el-form-item label="流程类型" prop="flow_type_id">
            <el-select v-model="form.flow_type_id" filterable placeholder="请选择" style="width: 100%">
              <el-option v-for="t in typeOptions" :key="t.id" :label="t.type_name" :value="String(t.id)" />
            </el-select>
          </el-form-item>
          <el-form-item label="流程名称" prop="flow_name">
            <el-input v-model="form.flow_name" placeholder="如：日常请假审批流程V2" maxlength="128" />
          </el-form-item>
          <el-form-item label="版本号" prop="version">
            <el-input-number v-model="form.version" :min="1" />
          </el-form-item>
          <el-form-item label="备注" prop="remark">
            <el-input v-model="form.remark" type="textarea" :rows="3" maxlength="512" show-word-limit />
          </el-form-item>
        </el-form>
      </el-tab-pane>

      <el-tab-pane label="表单字段" name="forms">
        <div class="page-toolbar" style="margin-bottom: 12px">
          <el-button class="btn-primary-teal" size="small" :icon="Plus" @click="addFormField">添加字段</el-button>
        </div>
        <el-table :data="forms" border>
          <el-table-column label="字段名称" min-width="140">
            <template #default="{ row }">
              <el-input v-model="row.field_name" placeholder="请假天数" />
            </template>
          </el-table-column>
          <el-table-column label="字段标识" min-width="140">
            <template #default="{ row }">
              <el-input v-model="row.field_key" placeholder="leave_days" />
            </template>
          </el-table-column>
          <el-table-column label="组件类型" width="150">
            <template #default="{ row }">
              <el-select v-model="row.field_type" style="width: 100%">
                <el-option v-for="(label, value) in options.field_type" :key="value" :label="label" :value="value" />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="选项(逗号分隔)" min-width="160">
            <template #default="{ row }">
              <el-input
                v-model="row._optionsText"
                :disabled="!['radio', 'select'].includes(row.field_type)"
                placeholder="选项A,选项B"
              />
            </template>
          </el-table-column>
          <el-table-column label="必填" width="80" align="center">
            <template #default="{ row }">
              <el-switch v-model="row.is_required" :active-value="1" :inactive-value="0" />
            </template>
          </el-table-column>
          <el-table-column label="排序" width="100" align="center">
            <template #default="{ row }">
              <el-input-number v-model="row.sort" :min="0" controls-position="right" style="width: 90px" />
            </template>
          </el-table-column>
          <el-table-column label="操作" width="90" align="center">
            <template #default="{ $index }">
              <el-button class="btn-danger-orange" size="small" @click="forms.splice($index, 1)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="审批节点" name="nodes">
        <div class="page-toolbar" style="margin-bottom: 12px">
          <el-button class="btn-primary-teal" size="small" :icon="Plus" @click="addNode">添加节点</el-button>
        </div>
        <el-table :data="nodes" border>
          <el-table-column label="顺序" width="90" align="center">
            <template #default="{ row }">
              <el-input-number v-model="row.node_sort" :min="1" controls-position="right" style="width: 80px" />
            </template>
          </el-table-column>
          <el-table-column label="节点名称" min-width="140">
            <template #default="{ row }">
              <el-input v-model="row.node_name" placeholder="直属领导审批" />
            </template>
          </el-table-column>
          <el-table-column label="审批人类型" width="180">
            <template #default="{ row }">
              <el-select v-model="row.approve_type" style="width: 100%">
                <el-option
                  v-for="(label, value) in options.approve_type"
                  :key="value"
                  :label="label"
                  :value="Number(value)"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="审批目标" min-width="200">
            <template #default="{ row }">
              <el-select
                v-if="row.approve_type === 2"
                v-model="row.approve_target"
                multiple
                filterable
                collapse-tags
                placeholder="选择用户"
                style="width: 100%"
              >
                <el-option
                  v-for="u in userOptions"
                  :key="u.id"
                  :label="`${u.nick_name || u.user_name}`"
                  :value="String(u.id)"
                />
              </el-select>
              <el-select
                v-else-if="row.approve_type === 3"
                v-model="row.approve_target"
                multiple
                filterable
                collapse-tags
                placeholder="选择角色"
                style="width: 100%"
              >
                <el-option v-for="r in roleOptions" :key="r.id" :label="r.role_name" :value="String(r.id)" />
              </el-select>
              <span v-else style="color: #999">按规则自动解析</span>
            </template>
          </el-table-column>
          <el-table-column label="审批模式" width="170">
            <template #default="{ row }">
              <el-select v-model="row.node_mode" style="width: 100%">
                <el-option
                  v-for="(label, value) in options.node_mode"
                  :key="value"
                  :label="label"
                  :value="Number(value)"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="可驳回" width="80" align="center">
            <template #default="{ row }">
              <el-switch v-model="row.can_reject" :active-value="1" :inactive-value="0" />
            </template>
          </el-table-column>
          <el-table-column label="可加签" width="80" align="center">
            <template #default="{ row }">
              <el-switch v-model="row.can_add_sign" :active-value="1" :inactive-value="0" />
            </template>
          </el-table-column>
          <el-table-column label="可转审" width="80" align="center">
            <template #default="{ row }">
              <el-switch v-model="row.can_transfer" :active-value="1" :inactive-value="0" />
            </template>
          </el-table-column>
          <el-table-column label="操作" width="90" align="center" fixed="right">
            <template #default="{ $index }">
              <el-button class="btn-danger-orange" size="small" @click="nodes.splice($index, 1)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="条件分支" name="conditions">
        <div class="page-toolbar" style="margin-bottom: 12px">
          <el-button class="btn-primary-teal" size="small" :icon="Plus" @click="addCondition">添加条件</el-button>
          <span style="margin-left: 12px; color: #888; font-size: 13px">按节点顺序引用；上一节点选「发起」表示提交时判断</span>
        </div>
        <el-table :data="conditions" border>
          <el-table-column label="上一节点" width="180">
            <template #default="{ row }">
              <el-select v-model="row.pre_node_sort" style="width: 100%">
                <el-option label="发起（开始）" :value="0" />
                <el-option
                  v-for="n in nodes.filter((x) => x.node_name)"
                  :key="`pre-${n.node_sort}`"
                  :label="`${n.node_sort}. ${n.node_name}`"
                  :value="n.node_sort"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="条件字段" min-width="140">
            <template #default="{ row }">
              <el-select v-model="row.condition_field" filterable allow-create default-first-option style="width: 100%">
                <el-option
                  v-for="f in forms.filter((x) => x.field_key)"
                  :key="f.field_key"
                  :label="`${f.field_name} (${f.field_key})`"
                  :value="f.field_key"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="运算符" width="130">
            <template #default="{ row }">
              <el-select v-model="row.condition_operator" style="width: 100%">
                <el-option
                  v-for="(label, value) in options.condition_operator"
                  :key="value"
                  :label="`${value} ${label}`"
                  :value="value"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="阈值" width="120">
            <template #default="{ row }">
              <el-input v-model="row.condition_value" placeholder="3" />
            </template>
          </el-table-column>
          <el-table-column label="跳转节点" min-width="180">
            <template #default="{ row }">
              <el-select v-model="row.jump_node_sort" style="width: 100%">
                <el-option
                  v-for="n in nodes.filter((x) => x.node_name)"
                  :key="`jump-${n.node_sort}`"
                  :label="`${n.node_sort}. ${n.node_name}`"
                  :value="n.node_sort"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="90" align="center">
            <template #default="{ $index }">
              <el-button class="btn-danger-orange" size="small" @click="conditions.splice($index, 1)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import {
  createWfFlowDefinition,
  fetchWfFlowDefinition,
  updateWfFlowDefinition,
} from '../../api/wfFlowDefinition';
import { fetchWfFlowTypeOptions } from '../../api/wfFlowType';
import { fetchUsers } from '../../api/user';
import { fetchRoles } from '../../api/role';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => Boolean(route.params.id));
const activeTab = ref('basic');
const saving = ref(false);
const basicRef = ref();
const typeOptions = ref([]);
const userOptions = ref([]);
const roleOptions = ref([]);
const forms = ref([]);
const nodes = ref([]);
const conditions = ref([]);

const options = reactive({
  field_type: {
    input: '单行文本',
    number: '数字',
    textarea: '多行文本',
    radio: '单选',
    select: '下拉',
    upload: '附件上传',
    date: '日期',
  },
  approve_type: {
    1: '发起人直属上级',
    2: '指定固定人员',
    3: '指定角色',
    4: '部门负责人',
    5: '发起人自选审批人',
  },
  node_mode: {
    1: '或签（一人通过即过）',
    2: '会签（全部必须通过）',
  },
  condition_operator: {
    '>': '大于',
    '>=': '大于等于',
    '<': '小于',
    '<=': '小于等于',
    '=': '等于',
    '!=': '不等于',
  },
});

const form = reactive({
  flow_type_id: '',
  flow_name: '',
  version: 1,
  remark: '',
  apply_scope: [],
});

const basicRules = {
  flow_type_id: [{ required: true, message: '请选择流程类型', trigger: 'change' }],
  flow_name: [{ required: true, message: '请输入流程名称', trigger: 'blur' }],
};

function addFormField() {
  forms.value.push({
    field_name: '',
    field_key: '',
    field_type: 'input',
    _optionsText: '',
    is_required: 1,
    sort: forms.value.length,
  });
}

function addNode() {
  nodes.value.push({
    node_name: '',
    node_sort: nodes.value.length + 1,
    approve_type: 1,
    approve_target: [],
    node_mode: 1,
    can_reject: 1,
    can_add_sign: 1,
    can_transfer: 1,
    back_node_id: 0,
  });
}

function addCondition() {
  conditions.value.push({
    pre_node_sort: 0,
    condition_field: forms.value.find((f) => f.field_key)?.field_key || '',
    condition_operator: '>',
    condition_value: '',
    jump_node_sort: nodes.value.find((n) => n.node_name)?.node_sort || 1,
  });
}

function parseOptions(text) {
  return String(text || '')
    .split(',')
    .map((s) => s.trim())
    .filter(Boolean)
    .map((label) => ({ label, value: label }));
}

async function loadOptions() {
  const [types, users, roles] = await Promise.all([
    fetchWfFlowTypeOptions(),
    fetchUsers({ per_page: 200 }),
    fetchRoles({ per_page: 200 }),
  ]);
  typeOptions.value = types.data || [];
  userOptions.value = users.data?.list || [];
  roleOptions.value = roles.data?.list || [];
}

async function loadDetail() {
  if (!isEdit.value) {
    addFormField();
    addNode();
    return;
  }
  const res = await fetchWfFlowDefinition(route.params.id);
  const data = res.data || {};
  form.flow_type_id = data.flow_type_id ? String(data.flow_type_id) : '';
  form.flow_name = data.flow_name || '';
  form.version = data.version || 1;
  form.remark = data.remark || '';
  form.apply_scope = data.apply_scope || [];

  forms.value = (data.forms || []).map((f) => ({
    field_name: f.field_name,
    field_key: f.field_key,
    field_type: f.field_type || 'input',
    _optionsText: Array.isArray(f.field_options)
      ? f.field_options.map((o) => o.label || o.value || o).join(',')
      : '',
    is_required: f.is_required ?? 1,
    sort: f.sort ?? 0,
  }));

  nodes.value = (data.nodes || []).map((n) => ({
    node_name: n.node_name,
    node_sort: n.node_sort || 1,
    approve_type: n.approve_type || 2,
    approve_target: (n.approve_target || []).map(String),
    node_mode: n.node_mode || 1,
    can_reject: n.can_reject ?? 1,
    can_add_sign: n.can_add_sign ?? 1,
    can_transfer: n.can_transfer ?? 1,
    back_node_id: n.back_node_id || 0,
  }));

  conditions.value = (data.conditions || []).map((c) => ({
    pre_node_sort: c.pre_node_sort ?? 0,
    condition_field: c.condition_field || '',
    condition_operator: c.condition_operator || '>',
    condition_value: c.condition_value || '',
    jump_node_sort: c.jump_node_sort || 1,
  }));

  if (!forms.value.length) addFormField();
  if (!nodes.value.length) addNode();
}

async function submit() {
  await basicRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      flow_type_id: form.flow_type_id,
      flow_name: form.flow_name,
      version: form.version || 1,
      remark: form.remark || '',
      apply_scope: form.apply_scope || [],
      forms: forms.value
        .filter((f) => f.field_name && f.field_key)
        .map((f, i) => ({
          field_name: f.field_name,
          field_key: f.field_key,
          field_type: f.field_type,
          field_options: ['radio', 'select'].includes(f.field_type) ? parseOptions(f._optionsText) : [],
          is_required: f.is_required ?? 1,
          sort: f.sort ?? i,
        })),
      nodes: nodes.value
        .filter((n) => n.node_name)
        .map((n, i) => ({
          node_name: n.node_name,
          node_sort: n.node_sort || i + 1,
          approve_type: n.approve_type,
          approve_target: n.approve_target || [],
          node_mode: n.node_mode || 1,
          can_reject: n.can_reject ?? 1,
          can_add_sign: n.can_add_sign ?? 1,
          can_transfer: n.can_transfer ?? 1,
          back_node_id: n.back_node_id || 0,
        })),
      conditions: conditions.value
        .filter((c) => c.condition_field && c.condition_value !== '' && c.jump_node_sort)
        .map((c) => ({
          pre_node_sort: c.pre_node_sort ?? 0,
          condition_field: c.condition_field,
          condition_operator: c.condition_operator || '>',
          condition_value: String(c.condition_value),
          jump_node_sort: c.jump_node_sort,
        })),
    };

    if (isEdit.value) {
      await updateWfFlowDefinition(route.params.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createWfFlowDefinition(payload);
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
  router.push('/backend/wf/flow-definitions');
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
