<template>
  <div class="mortgage-page">
    <header class="page-header">
      <div class="page-header-inner">
        <router-link class="brand" to="/">
          <img src="/uploads/logo/budff_logo.png" alt="logo" width="220" height="66" />
        </router-link>
        <div class="header-links">
          <router-link to="/tools/mortgage" class="active">房贷计算器</router-link>
          <router-link to="/tools/json">JSON 格式化</router-link>
          <router-link to="/apply">申请收录</router-link>
          <router-link to="/">返回首页</router-link>
        </div>
      </div>
    </header>

    <div class="page-wrap">
      <div class="crumb">
        <router-link to="/">首页</router-link>
        <span> &gt; </span>
        <span>房贷计算器</span>
      </div>

      <div class="page-head">
        <h1>房贷计算器</h1>
        <p>支持商业贷款、公积金贷款、组合贷款，等额本息 / 等额本金一键测算</p>
      </div>

      <div class="layout">
        <section class="panel form-panel">
          <el-form label-width="100px" label-position="left">
            <el-form-item label="贷款类型">
              <el-radio-group v-model="loanType" @change="recalc">
                <el-radio-button value="commercial">商业贷款</el-radio-button>
                <el-radio-button value="fund">公积金贷款</el-radio-button>
                <el-radio-button value="combo">组合贷款</el-radio-button>
              </el-radio-group>
            </el-form-item>

            <el-form-item label="还款方式">
              <el-radio-group v-model="method" @change="recalc">
                <el-radio value="interest">等额本息</el-radio>
                <el-radio value="principal">等额本金</el-radio>
              </el-radio-group>
            </el-form-item>

            <template v-if="loanType !== 'fund'">
              <el-divider content-position="left">商业贷款</el-divider>
              <el-form-item label="贷款金额">
                <div class="input-with-unit">
                  <el-input-number
                    v-model="commercial.amount"
                    :min="0"
                    :max="100000"
                    :precision="2"
                    :step="10"
                    controls-position="right"
                    @change="recalc"
                  />
                  <span class="unit">万元</span>
                </div>
              </el-form-item>
              <el-form-item label="贷款年限">
                <el-select v-model="commercial.years" style="width: 100%" @change="recalc">
                  <el-option v-for="y in YEAR_OPTIONS" :key="y.value" :label="y.label" :value="y.value" />
                </el-select>
              </el-form-item>
              <el-form-item label="贷款利率">
                <el-select
                  v-model="commercial.ratePreset"
                  style="width: 100%; margin-bottom: 8px"
                  @change="onCommercialRatePreset"
                >
                  <el-option
                    v-for="item in RATE_PRESETS.commercial"
                    :key="item.label"
                    :label="item.label"
                    :value="item.value"
                  />
                </el-select>
                <div class="input-with-unit">
                  <el-input-number
                    v-model="commercial.rate"
                    :min="0"
                    :max="20"
                    :precision="3"
                    :step="0.05"
                    controls-position="right"
                    @change="recalc"
                  />
                  <span class="unit">%</span>
                </div>
              </el-form-item>
            </template>

            <template v-if="loanType !== 'commercial'">
              <el-divider content-position="left">公积金贷款</el-divider>
              <el-form-item label="贷款金额">
                <div class="input-with-unit">
                  <el-input-number
                    v-model="fund.amount"
                    :min="0"
                    :max="100000"
                    :precision="2"
                    :step="5"
                    controls-position="right"
                    @change="recalc"
                  />
                  <span class="unit">万元</span>
                </div>
              </el-form-item>
              <el-form-item label="贷款年限">
                <el-select v-model="fund.years" style="width: 100%" @change="recalc">
                  <el-option v-for="y in YEAR_OPTIONS" :key="`f-${y.value}`" :label="y.label" :value="y.value" />
                </el-select>
              </el-form-item>
              <el-form-item label="贷款利率">
                <el-select
                  v-model="fund.ratePreset"
                  style="width: 100%; margin-bottom: 8px"
                  @change="onFundRatePreset"
                >
                  <el-option
                    v-for="item in RATE_PRESETS.fund"
                    :key="item.label"
                    :label="item.label"
                    :value="item.value"
                  />
                </el-select>
                <div class="input-with-unit">
                  <el-input-number
                    v-model="fund.rate"
                    :min="0"
                    :max="20"
                    :precision="3"
                    :step="0.05"
                    controls-position="right"
                    @change="recalc"
                  />
                  <span class="unit">%</span>
                </div>
              </el-form-item>
            </template>

            <div class="actions">
              <el-button class="primary-btn" @click="recalc">开始计算</el-button>
              <el-button @click="resetForm">重置</el-button>
            </div>
          </el-form>
        </section>

        <section class="panel result-panel">
          <h2>计算结果</h2>
          <div class="summary-grid">
            <div class="summary-card accent">
              <div class="label">{{ method === 'interest' ? '每月月供' : '首月月供' }}</div>
              <div class="value">{{ formatMoney(result.firstMonthPayment) }}<small>元</small></div>
            </div>
            <div class="summary-card">
              <div class="label">{{ method === 'interest' ? '还款总额' : '末月月供' }}</div>
              <div class="value">
                {{
                  method === 'interest'
                    ? formatMoney(result.totalPayment)
                    : formatMoney(result.lastMonthPayment)
                }}<small>元</small>
              </div>
            </div>
            <div class="summary-card">
              <div class="label">支付利息</div>
              <div class="value">{{ formatMoney(result.totalInterest) }}<small>元</small></div>
            </div>
            <div class="summary-card">
              <div class="label">贷款总额</div>
              <div class="value">{{ formatMoney(totalPrincipalYuan) }}<small>元</small></div>
            </div>
          </div>

          <div v-if="method === 'principal'" class="tip">
            等额本金每月递减，上方展示首月与末月月供；还款总额为
            <strong>{{ formatMoney(result.totalPayment) }}</strong> 元。
          </div>

          <div class="bar-box">
            <div class="bar-title">本息构成</div>
            <div class="bar">
              <div class="bar-principal" :style="{ width: principalPercent + '%' }" />
              <div class="bar-interest" :style="{ width: interestPercent + '%' }" />
            </div>
            <div class="bar-legend">
              <span><i class="dot principal" />本金 {{ principalPercent }}%</span>
              <span><i class="dot interest" />利息 {{ interestPercent }}%</span>
            </div>
          </div>
        </section>
      </div>

      <section class="panel schedule-panel">
        <div class="schedule-head">
          <h2>还款明细</h2>
          <el-radio-group v-model="scheduleView" size="small">
            <el-radio-button value="month">按月</el-radio-button>
            <el-radio-button value="year">按年汇总</el-radio-button>
          </el-radio-group>
        </div>

        <el-table
          v-if="scheduleView === 'month'"
          :data="pagedSchedule"
          border
          stripe
          max-height="480"
          style="width: 100%"
        >
          <el-table-column prop="month" label="期数" width="80" align="center" />
          <el-table-column label="月供（元）" min-width="120" align="right">
            <template #default="{ row }">{{ formatMoney(row.payment) }}</template>
          </el-table-column>
          <el-table-column label="本金（元）" min-width="120" align="right">
            <template #default="{ row }">{{ formatMoney(row.principal) }}</template>
          </el-table-column>
          <el-table-column label="利息（元）" min-width="120" align="right">
            <template #default="{ row }">{{ formatMoney(row.interest) }}</template>
          </el-table-column>
          <el-table-column label="剩余本金（元）" min-width="140" align="right">
            <template #default="{ row }">{{ formatMoney(row.balance) }}</template>
          </el-table-column>
        </el-table>

        <el-table v-else :data="yearlySchedule" border stripe style="width: 100%">
          <el-table-column prop="year" label="年份" width="100" align="center" />
          <el-table-column label="还款额（元）" min-width="140" align="right">
            <template #default="{ row }">{{ formatMoney(row.payment) }}</template>
          </el-table-column>
          <el-table-column label="本金（元）" min-width="140" align="right">
            <template #default="{ row }">{{ formatMoney(row.principal) }}</template>
          </el-table-column>
          <el-table-column label="利息（元）" min-width="140" align="right">
            <template #default="{ row }">{{ formatMoney(row.interest) }}</template>
          </el-table-column>
        </el-table>

        <div v-if="scheduleView === 'month' && result.schedule.length > pageSize" class="pager">
          <el-pagination
            v-model:current-page="page"
            background
            layout="total, prev, pager, next"
            :page-size="pageSize"
            :total="result.schedule.length"
          />
        </div>
      </section>

      <section class="panel note-panel">
        <h3>说明</h3>
        <ul>
          <li>等额本息：每月还款金额固定，前期利息占比高，适合收入稳定的家庭。</li>
          <li>等额本金：每月本金固定，月供逐月递减，总利息更少，前期压力较大。</li>
          <li>利率仅供参考，实际以银行/公积金中心批复为准；LPR 加点政策各地可能不同。</li>
          <li>本工具计算结果仅供参考，不构成任何贷款承诺或金融建议。</li>
        </ul>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import {
  RATE_PRESETS,
  YEAR_OPTIONS,
  calcLoan,
  formatMoney,
  mergeLoanResults,
} from '@frontend/utils/mortgage';

const loanType = ref('commercial');
const method = ref('interest');
const scheduleView = ref('month');
const page = ref(1);
const pageSize = 12;

const commercial = reactive({
  amount: 100,
  years: 30,
  ratePreset: 3.6,
  rate: 3.6,
});

const fund = reactive({
  amount: 50,
  years: 30,
  ratePreset: 2.85,
  rate: 2.85,
});

const result = ref({
  monthlyPayment: 0,
  firstMonthPayment: 0,
  lastMonthPayment: 0,
  totalPayment: 0,
  totalInterest: 0,
  schedule: [],
});

const totalPrincipalYuan = computed(() => {
  let wan = 0;
  if (loanType.value !== 'fund') wan += Number(commercial.amount) || 0;
  if (loanType.value !== 'commercial') wan += Number(fund.amount) || 0;
  return wan * 10000;
});

const principalPercent = computed(() => {
  const total = result.value.totalPayment || 0;
  if (total <= 0) return 100;
  return Math.round(((totalPrincipalYuan.value / total) * 1000) / 10);
});

const interestPercent = computed(() => Math.max(0, 100 - principalPercent.value));

const pagedSchedule = computed(() => {
  const start = (page.value - 1) * pageSize;
  return result.value.schedule.slice(start, start + pageSize);
});

const yearlySchedule = computed(() => {
  const map = new Map();
  result.value.schedule.forEach((row) => {
    const year = Math.ceil(row.month / 12);
    const cur = map.get(year) || { year: `第${year}年`, payment: 0, principal: 0, interest: 0 };
    cur.payment += row.payment;
    cur.principal += row.principal;
    cur.interest += row.interest;
    map.set(year, cur);
  });
  return [...map.values()].map((row) => ({
    ...row,
    payment: Math.round(row.payment * 100) / 100,
    principal: Math.round(row.principal * 100) / 100,
    interest: Math.round(row.interest * 100) / 100,
  }));
});

function onCommercialRatePreset(val) {
  if (val !== -1) commercial.rate = val;
  recalc();
}

function onFundRatePreset(val) {
  if (val !== -1) fund.rate = val;
  recalc();
}

function recalc() {
  page.value = 1;
  const m = method.value;

  if (loanType.value === 'commercial') {
    result.value = calcLoan((commercial.amount || 0) * 10000, commercial.rate, commercial.years * 12, m);
    return;
  }
  if (loanType.value === 'fund') {
    result.value = calcLoan((fund.amount || 0) * 10000, fund.rate, fund.years * 12, m);
    return;
  }

  const a = calcLoan((commercial.amount || 0) * 10000, commercial.rate, commercial.years * 12, m);
  const b = calcLoan((fund.amount || 0) * 10000, fund.rate, fund.years * 12, m);
  result.value = mergeLoanResults(a, b);
}

function resetForm() {
  loanType.value = 'commercial';
  method.value = 'interest';
  commercial.amount = 100;
  commercial.years = 30;
  commercial.ratePreset = 3.6;
  commercial.rate = 3.6;
  fund.amount = 50;
  fund.years = 30;
  fund.ratePreset = 2.85;
  fund.rate = 2.85;
  scheduleView.value = 'month';
  recalc();
}

watch(loanType, () => {
  page.value = 1;
});

onMounted(() => {
  document.title = '房贷计算器';
  recalc();
});
</script>

<style scoped>
.mortgage-page {
  --accent: #e74c3c;
  --bg: #f5f6f8;
  --card: #fff;
  --text: #1f2937;
  --muted: #6b7280;
  --line: #e5e7eb;
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
  font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
}

.page-header {
  background: var(--card);
  border-bottom: 1px solid var(--line);
}

.page-header-inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 8px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.brand img {
  width: 220px;
  height: 66px;
  object-fit: contain;
  display: block;
}

.header-links {
  display: flex;
  gap: 16px;
  align-items: center;
}

.header-links a {
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
}

.header-links a.active,
.header-links a:hover {
  color: var(--accent);
}

.page-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: 20px 20px 48px;
}

.crumb {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 12px;
}

.crumb a {
  color: var(--muted);
  text-decoration: none;
}

.crumb a:hover {
  color: var(--accent);
}

.page-head {
  margin-bottom: 18px;
}

.page-head h1 {
  margin: 0 0 6px;
  font-size: 28px;
}

.page-head p {
  margin: 0;
  color: var(--muted);
  font-size: 14px;
}

.layout {
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 16px;
  margin-bottom: 16px;
}

.panel {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 20px;
}

.form-panel :deep(.el-divider__text) {
  font-weight: 600;
  color: var(--text);
}

.input-with-unit {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.input-with-unit :deep(.el-input-number) {
  flex: 1;
  width: auto;
}

.unit {
  color: var(--muted);
  flex-shrink: 0;
}

.actions {
  display: flex;
  gap: 10px;
  padding-top: 8px;
}

.primary-btn {
  background: var(--accent) !important;
  border-color: var(--accent) !important;
  color: #fff !important;
  min-width: 120px;
}

.result-panel h2,
.schedule-panel h2 {
  margin: 0 0 16px;
  font-size: 18px;
}

.summary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.summary-card {
  background: #f8fafc;
  border-radius: 10px;
  padding: 14px 16px;
}

.summary-card.accent {
  background: linear-gradient(135deg, #ffe8e6, #fff5f4);
}

.summary-card .label {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 6px;
}

.summary-card .value {
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}

.summary-card.accent .value {
  color: var(--accent);
}

.summary-card .value small {
  font-size: 13px;
  font-weight: 500;
  margin-left: 2px;
}

.tip {
  margin-top: 12px;
  font-size: 13px;
  color: var(--muted);
  line-height: 1.6;
}

.bar-box {
  margin-top: 18px;
}

.bar-title {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 8px;
}

.bar {
  height: 12px;
  border-radius: 999px;
  overflow: hidden;
  display: flex;
  background: #eee;
}

.bar-principal {
  background: #3b82f6;
}

.bar-interest {
  background: var(--accent);
}

.bar-legend {
  display: flex;
  gap: 16px;
  margin-top: 8px;
  font-size: 12px;
  color: var(--muted);
}

.dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 6px;
}

.dot.principal {
  background: #3b82f6;
}

.dot.interest {
  background: var(--accent);
}

.schedule-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.schedule-head h2 {
  margin: 0;
}

.pager {
  margin-top: 14px;
  display: flex;
  justify-content: flex-end;
}

.note-panel h3 {
  margin: 0 0 10px;
  font-size: 16px;
}

.note-panel ul {
  margin: 0;
  padding-left: 18px;
  color: var(--muted);
  font-size: 13px;
  line-height: 1.8;
}

@media (max-width: 900px) {
  .layout {
    grid-template-columns: 1fr;
  }

  .brand img {
    width: 160px;
    height: 48px;
  }

  .summary-grid {
    grid-template-columns: 1fr;
  }
}
</style>
