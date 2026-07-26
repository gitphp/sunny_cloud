<template>
  <div class="admin-page-card category-table">
    <div class="page-toolbar" style="flex-wrap: wrap; gap: 8px">
      <el-input
        v-model="filters.keyword"
        class="search-input"
        clearable
        placeholder="标题 / 副标题 / 广告位"
        @keyup.enter="search"
      />
      <el-select v-model="filters.position_code" clearable filterable placeholder="广告位" style="width: 180px">
        <el-option
          v-for="item in options.slots"
          :key="item.slot_code"
          :label="item.slot_name"
          :value="item.slot_code"
        />
      </el-select>
      <el-select v-model="filters.status" clearable placeholder="状态" style="width: 120px">
        <el-option
          v-for="(label, value) in options.status"
          :key="value"
          :label="label"
          :value="Number(value)"
        />
      </el-select>
      <el-select v-model="filters.platform" clearable placeholder="平台" style="width: 120px">
        <el-option
          v-for="(label, value) in options.platform"
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
      <el-table-column label="广告" min-width="220">
        <template #default="{ row }">
          <div class="ad-cell">
            <img v-if="row.cover_thumb" :src="row.cover_thumb" class="cover" alt="" @error="onImgError" />
            <div>
              <div>{{ row.ad_title }}</div>
              <div class="sub">{{ row.subtitle }}</div>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="广告位" min-width="140">
        <template #default="{ row }">
          <div>{{ row.slot_name || row.position_code }}</div>
          <div class="sub">{{ row.position_code }}</div>
        </template>
      </el-table-column>
      <el-table-column prop="platform_label" label="平台" width="90" align="center" />
      <el-table-column prop="link_type_label" label="跳转" width="100" align="center" />
      <el-table-column label="投放时间" min-width="170">
        <template #default="{ row }">
          <div>{{ row.start_time }}</div>
          <div class="sub">至 {{ row.end_time }}</div>
        </template>
      </el-table-column>
      <el-table-column label="排序" width="100" align="center">
        <template #default="{ row }">
          <el-input
            v-model.number="row.sort"
            class="sort-input"
            size="small"
            @change="onSortChange(row)"
          />
        </template>
      </el-table-column>
      <el-table-column label="状态" width="100" align="center">
        <template #default="{ row }">
          <el-tag size="small">{{ row.status_label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="数据" width="110" align="center">
        <template #default="{ row }">
          <div>展 {{ row.impression_count }}</div>
          <div class="sub">点 {{ row.click_count }} / CTR {{ row.click_rate }}</div>
        </template>
      </el-table-column>
      <el-table-column prop="updated_at" label="更新时间" width="170" />
      <el-table-column label="操作" width="220" align="center" fixed="right">
        <template #default="{ row }">
          <a class="action-edit" @click="openForm(row)">修改</a>
          <a class="action-edit" @click="openAudit(row)">审核</a>
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

    <el-dialog v-model="dialogVisible" :title="form.id ? '修改广告' : '添加广告'" width="820px" destroy-on-close>
      <el-form ref="formRef" :model="form" :rules="rules" label-width="110px">
        <el-tabs v-model="activeTab">
          <el-tab-pane label="基础信息" name="base">
            <el-row :gutter="12">
              <el-col :span="12">
                <el-form-item label="广告标题" prop="ad_title">
                  <el-input v-model="form.ad_title" maxlength="128" show-word-limit />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="广告位" prop="position_code">
                  <el-select v-model="form.position_code" filterable style="width: 100%">
                    <el-option
                      v-for="item in options.slots"
                      :key="item.slot_code"
                      :label="`${item.slot_name} (${item.slot_code})`"
                      :value="item.slot_code"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="副标题" prop="subtitle">
                  <el-input v-model="form.subtitle" maxlength="255" show-word-limit />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="封面图" prop="cover_url">
                  <el-input v-model="form.cover_url" maxlength="512" placeholder="封面 URL" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="移动端封面" prop="cover_mobile">
                  <el-input v-model="form.cover_mobile" maxlength="512" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="缩略图" prop="cover_thumb">
                  <el-input v-model="form.cover_thumb" maxlength="512" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="视频地址" prop="video_url">
                  <el-input v-model="form.video_url" maxlength="512" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="跳转类型" prop="link_type">
                  <el-select v-model="form.link_type" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.link_type"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="跳转链接" prop="link_url">
                  <el-input v-model="form.link_url" maxlength="512" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="小程序AppId" prop="app_id">
                  <el-input v-model="form.app_id" maxlength="128" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="小程序路径" prop="app_path">
                  <el-input v-model="form.app_path" maxlength="255" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="状态" prop="status">
                  <el-select v-model="form.status" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.status"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="排序" prop="sort">
                  <el-input-number v-model="form.sort" :min="0" :max="999999" />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <el-tab-pane label="投放设置" name="delivery">
            <el-row :gutter="12">
              <el-col :span="12">
                <el-form-item label="投放平台" prop="platform">
                  <el-select v-model="form.platform" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.platform"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="设备类型" prop="device_type">
                  <el-select v-model="form.device_type" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.device_type"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="开始时间" prop="start_time">
                  <el-date-picker
                    v-model="form.start_time"
                    type="datetime"
                    value-format="YYYY-MM-DD HH:mm:ss"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="结束时间" prop="end_time">
                  <el-date-picker
                    v-model="form.end_time"
                    type="datetime"
                    value-format="YYYY-MM-DD HH:mm:ss"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="展示时段" prop="show_time_type">
                  <el-select v-model="form.show_time_type" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.show_time_type"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="展示频率" prop="display_frequency">
                  <el-select v-model="form.display_frequency" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.display_frequency"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="投放星期" prop="weekdays">
                  <el-checkbox-group v-model="form.weekdays">
                    <el-checkbox
                      v-for="(label, value) in options.weekdays"
                      :key="value"
                      :value="Number(value)"
                    >
                      {{ label }}
                    </el-checkbox>
                  </el-checkbox-group>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="日展示上限" prop="daily_impression_limit">
                  <el-input-number v-model="form.daily_impression_limit" :min="0" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="日点击上限" prop="daily_click_limit">
                  <el-input-number v-model="form.daily_click_limit" :min="0" />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <el-tab-pane label="定向与计费" name="target">
            <el-row :gutter="12">
              <el-col :span="12">
                <el-form-item label="用户定向" prop="target_user_type">
                  <el-select v-model="form.target_user_type" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.target_user_type"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="计费方式" prop="cost_type">
                  <el-select v-model="form.cost_type" style="width: 100%">
                    <el-option
                      v-for="(label, value) in options.cost_type"
                      :key="value"
                      :label="label"
                      :value="Number(value)"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="预算" prop="budget">
                  <el-input-number v-model="form.budget" :min="0" :precision="2" :step="1" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="出价" prop="bid_price">
                  <el-input-number v-model="form.bid_price" :min="0" :precision="2" :step="0.1" />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="目标地区JSON" prop="target_region_text">
                  <el-input
                    v-model="form.target_region_text"
                    type="textarea"
                    :rows="2"
                    placeholder='如 {"province":["广东"],"city":["深圳"]}'
                  />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="跳转参数JSON" prop="link_params_text">
                  <el-input
                    v-model="form.link_params_text"
                    type="textarea"
                    :rows="2"
                    placeholder='如 {"utm_source":"home"}'
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="saving" @click="submitForm">确定</el-button>
      </template>
    </el-dialog>

    <el-dialog v-model="auditVisible" title="审核广告" width="480px" destroy-on-close>
      <el-form label-width="90px">
        <el-form-item label="审核结果">
          <el-radio-group v-model="auditForm.audit_status">
            <el-radio :value="2">通过</el-radio>
            <el-radio :value="3">驳回</el-radio>
            <el-radio :value="1">待审核</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-if="auditForm.audit_status === 3" label="驳回原因">
          <el-input v-model="auditForm.reject_reason" type="textarea" :rows="3" maxlength="512" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="auditVisible = false">取消</el-button>
        <el-button class="btn-primary-teal" :loading="auditing" @click="submitAudit">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Search } from '@element-plus/icons-vue';
import {
  auditAdPosition,
  createAdPosition,
  deleteAdPosition,
  fetchAdPosition,
  fetchAdPositions,
  updateAdPosition,
  updateAdPositionSort,
} from '../../api/adPosition';

const loading = ref(false);
const saving = ref(false);
const auditing = ref(false);
const list = ref([]);
const page = ref(1);
const perPage = ref(15);
const total = ref(0);
const dialogVisible = ref(false);
const auditVisible = ref(false);
const activeTab = ref('base');
const formRef = ref();

const filters = reactive({
  keyword: '',
  position_code: '',
  status: '',
  platform: '',
});

const options = reactive({
  slots: [],
  link_type: {},
  platform: {},
  device_type: {},
  target_user_type: {},
  show_time_type: {},
  display_frequency: {},
  cost_type: {},
  status: {},
  audit_status: {},
  weekdays: { 1: '周一', 2: '周二', 3: '周三', 4: '周四', 5: '周五', 6: '周六', 7: '周日' },
});

const form = reactive({
  id: null,
  ad_title: '',
  subtitle: '',
  cover_url: '',
  cover_mobile: '',
  cover_thumb: '',
  video_url: '',
  link_type: 1,
  link_url: '',
  link_params_text: '',
  app_id: '',
  app_path: '',
  position_code: '',
  platform: 1,
  device_type: 1,
  target_user_type: 0,
  target_region_text: '',
  start_time: '',
  end_time: '',
  show_time_type: 0,
  weekdays: [1, 2, 3, 4, 5],
  sort: 0,
  display_frequency: 1,
  daily_impression_limit: 0,
  daily_click_limit: 0,
  budget: null,
  cost_type: 1,
  bid_price: null,
  status: 1,
});

const auditForm = reactive({
  id: null,
  audit_status: 2,
  reject_reason: '',
});

const rules = {
  ad_title: [{ required: true, message: '请输入广告标题', trigger: 'blur' }],
  position_code: [{ required: true, message: '请选择广告位', trigger: 'change' }],
  start_time: [{ required: true, message: '请选择开始时间', trigger: 'change' }],
  end_time: [{ required: true, message: '请选择结束时间', trigger: 'change' }],
};

function onImgError(e) {
  e.target.style.display = 'none';
}

function parseJsonField(text, label) {
  const value = (text || '').trim();
  if (!value) return null;
  try {
    return JSON.parse(value);
  } catch {
    throw new Error(`${label} JSON 格式不正确`);
  }
}

function defaultRange() {
  const start = new Date();
  const end = new Date(Date.now() + 30 * 24 * 3600 * 1000);
  const fmt = (d) => {
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  };
  return { start: fmt(start), end: fmt(end) };
}

async function loadData() {
  loading.value = true;
  try {
    const res = await fetchAdPositions({
      keyword: filters.keyword || undefined,
      position_code: filters.position_code || undefined,
      status: filters.status === '' ? undefined : filters.status,
      platform: filters.platform === '' ? undefined : filters.platform,
      page: page.value,
      per_page: perPage.value,
    });
    list.value = res.data?.list || [];
    total.value = res.data?.meta?.total || 0;
    const opts = res.data?.options || {};
    Object.keys(opts).forEach((key) => {
      if (opts[key] !== undefined) options[key] = opts[key];
    });
  } catch (e) {
    ElMessage.error(e?.message || '加载广告失败');
  } finally {
    loading.value = false;
  }
}

function search() {
  page.value = 1;
  loadData();
}

function resetForm() {
  const range = defaultRange();
  form.id = null;
  form.ad_title = '';
  form.subtitle = '';
  form.cover_url = '';
  form.cover_mobile = '';
  form.cover_thumb = '';
  form.video_url = '';
  form.link_type = 1;
  form.link_url = '';
  form.link_params_text = '';
  form.app_id = '';
  form.app_path = '';
  form.position_code = options.slots[0]?.slot_code || '';
  form.platform = 1;
  form.device_type = 1;
  form.target_user_type = 0;
  form.target_region_text = '';
  form.start_time = range.start;
  form.end_time = range.end;
  form.show_time_type = 0;
  form.weekdays = [1, 2, 3, 4, 5];
  form.sort = 0;
  form.display_frequency = 1;
  form.daily_impression_limit = 0;
  form.daily_click_limit = 0;
  form.budget = null;
  form.cost_type = 1;
  form.bid_price = null;
  form.status = 1;
  activeTab.value = 'base';
}

async function openForm(row = null) {
  resetForm();
  if (row?.id) {
    try {
      const res = await fetchAdPosition(row.id);
      const data = res.data || {};
      form.id = data.id;
      form.ad_title = data.ad_title || '';
      form.subtitle = data.subtitle || '';
      form.cover_url = data.cover_url || '';
      form.cover_mobile = data.cover_mobile || '';
      form.cover_thumb = data.cover_thumb || '';
      form.video_url = data.video_url || '';
      form.link_type = data.link_type ?? 1;
      form.link_url = data.link_url || '';
      form.link_params_text = data.link_params ? JSON.stringify(data.link_params) : '';
      form.app_id = data.app_id || '';
      form.app_path = data.app_path || '';
      form.position_code = data.position_code || '';
      form.platform = data.platform ?? 1;
      form.device_type = data.device_type ?? 1;
      form.target_user_type = data.target_user_type ?? 0;
      form.target_region_text = data.target_region ? JSON.stringify(data.target_region) : '';
      form.start_time = data.start_time || '';
      form.end_time = data.end_time || '';
      form.show_time_type = data.show_time_type ?? 0;
      form.weekdays = Array.isArray(data.weekdays) ? data.weekdays.map(Number) : [];
      form.sort = data.sort ?? 0;
      form.display_frequency = data.display_frequency ?? 1;
      form.daily_impression_limit = data.daily_impression_limit ?? 0;
      form.daily_click_limit = data.daily_click_limit ?? 0;
      form.budget = data.budget != null ? Number(data.budget) : null;
      form.cost_type = data.cost_type ?? 1;
      form.bid_price = data.bid_price != null ? Number(data.bid_price) : null;
      form.status = data.status ?? 1;
    } catch (e) {
      ElMessage.error(e?.message || '加载详情失败');
      return;
    }
  }
  dialogVisible.value = true;
}

async function submitForm() {
  await formRef.value?.validate();
  saving.value = true;
  try {
    const payload = {
      ad_title: form.ad_title,
      subtitle: form.subtitle || '',
      cover_url: form.cover_url || '',
      cover_mobile: form.cover_mobile || '',
      cover_thumb: form.cover_thumb || '',
      video_url: form.video_url || '',
      link_type: form.link_type,
      link_url: form.link_url || '',
      link_params: parseJsonField(form.link_params_text, '跳转参数'),
      app_id: form.app_id || '',
      app_path: form.app_path || '',
      position_code: form.position_code,
      platform: form.platform,
      device_type: form.device_type,
      target_user_type: form.target_user_type,
      target_region: parseJsonField(form.target_region_text, '目标地区'),
      start_time: form.start_time,
      end_time: form.end_time,
      show_time_type: form.show_time_type,
      weekdays: form.weekdays || [],
      sort: form.sort ?? 0,
      display_frequency: form.display_frequency,
      daily_impression_limit: form.daily_impression_limit ?? 0,
      daily_click_limit: form.daily_click_limit ?? 0,
      budget: form.budget,
      cost_type: form.cost_type,
      bid_price: form.bid_price,
      status: form.status,
    };
    if (form.id) {
      await updateAdPosition(form.id, payload);
      ElMessage.success('修改成功');
    } else {
      await createAdPosition(payload);
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

function openAudit(row) {
  auditForm.id = row.id;
  auditForm.audit_status = 2;
  auditForm.reject_reason = '';
  auditVisible.value = true;
}

async function submitAudit() {
  auditing.value = true;
  try {
    await auditAdPosition(auditForm.id, {
      audit_status: auditForm.audit_status,
      reject_reason: auditForm.reject_reason || '',
    });
    ElMessage.success('审核成功');
    auditVisible.value = false;
    await loadData();
  } catch (e) {
    ElMessage.error(e?.message || '审核失败');
  } finally {
    auditing.value = false;
  }
}

async function onSortChange(row) {
  try {
    await updateAdPositionSort(row.id, { sort: Number(row.sort) || 0 });
  } catch (e) {
    ElMessage.error(e?.message || '排序更新失败');
    await loadData();
  }
}

async function handleDelete(row) {
  try {
    await ElMessageBox.confirm(`确定删除「${row.ad_title}」吗？`, '提示', { type: 'warning' });
    await deleteAdPosition(row.id);
    ElMessage.success('删除成功');
    await loadData();
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(e?.message || '删除失败');
  }
}

onMounted(loadData);
</script>

<style scoped>
.ad-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}
.cover {
  width: 56px;
  height: 32px;
  object-fit: cover;
  border-radius: 2px;
  background: #f5f5f5;
}
.sub {
  color: #909399;
  font-size: 12px;
  margin-top: 2px;
}
</style>
