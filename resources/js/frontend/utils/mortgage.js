/**
 * 房贷计算工具
 * @param {number} principal 贷款本金（元）
 * @param {number} annualRate 年利率（如 3.6 表示 3.6%）
 * @param {number} months 贷款月数
 * @param {'interest'|'principal'} method 等额本息 | 等额本金
 */
export function calcLoan(principal, annualRate, months, method = 'interest') {
  const P = Number(principal) || 0;
  const n = Math.max(0, Math.floor(Number(months) || 0));
  const yearly = Number(annualRate) || 0;
  const r = yearly / 100 / 12;

  if (P <= 0 || n <= 0) {
    return emptyResult();
  }

  if (method === 'principal') {
    return calcEqualPrincipal(P, r, n);
  }

  return calcEqualInterest(P, r, n);
}

function emptyResult() {
  return {
    monthlyPayment: 0,
    firstMonthPayment: 0,
    lastMonthPayment: 0,
    totalPayment: 0,
    totalInterest: 0,
    schedule: [],
  };
}

/** 等额本息 */
function calcEqualInterest(P, r, n) {
  let monthly;
  if (r === 0) {
    monthly = P / n;
  } else {
    const pow = (1 + r) ** n;
    monthly = (P * r * pow) / (pow - 1);
  }

  const schedule = [];
  let balance = P;
  let totalInterest = 0;

  for (let m = 1; m <= n; m += 1) {
    const interest = r === 0 ? 0 : balance * r;
    let principal = monthly - interest;
    if (m === n) {
      principal = balance;
    }
    const payment = principal + interest;
    balance = Math.max(0, balance - principal);
    totalInterest += interest;
    schedule.push({
      month: m,
      payment: round2(payment),
      principal: round2(principal),
      interest: round2(interest),
      balance: round2(balance),
    });
  }

  const totalPayment = P + totalInterest;

  return {
    monthlyPayment: round2(monthly),
    firstMonthPayment: schedule[0]?.payment || 0,
    lastMonthPayment: schedule[n - 1]?.payment || 0,
    totalPayment: round2(totalPayment),
    totalInterest: round2(totalInterest),
    schedule,
  };
}

/** 等额本金 */
function calcEqualPrincipal(P, r, n) {
  const principalPart = P / n;
  const schedule = [];
  let balance = P;
  let totalInterest = 0;

  for (let m = 1; m <= n; m += 1) {
    const interest = balance * r;
    const principal = m === n ? balance : principalPart;
    const payment = principal + interest;
    balance = Math.max(0, balance - principal);
    totalInterest += interest;
    schedule.push({
      month: m,
      payment: round2(payment),
      principal: round2(principal),
      interest: round2(interest),
      balance: round2(balance),
    });
  }

  return {
    monthlyPayment: schedule[0]?.payment || 0,
    firstMonthPayment: schedule[0]?.payment || 0,
    lastMonthPayment: schedule[n - 1]?.payment || 0,
    totalPayment: round2(P + totalInterest),
    totalInterest: round2(totalInterest),
    schedule,
  };
}

/** 合并两笔贷款还款计划（按月相加） */
export function mergeLoanResults(a, b) {
  const len = Math.max(a.schedule.length, b.schedule.length);
  const schedule = [];
  for (let i = 0; i < len; i += 1) {
    const x = a.schedule[i] || { payment: 0, principal: 0, interest: 0, balance: 0 };
    const y = b.schedule[i] || { payment: 0, principal: 0, interest: 0, balance: 0 };
    schedule.push({
      month: i + 1,
      payment: round2(x.payment + y.payment),
      principal: round2(x.principal + y.principal),
      interest: round2(x.interest + y.interest),
      balance: round2(x.balance + y.balance),
    });
  }

  return {
    monthlyPayment: round2((a.monthlyPayment || 0) + (b.monthlyPayment || 0)),
    firstMonthPayment: schedule[0]?.payment || 0,
    lastMonthPayment: schedule[len - 1]?.payment || 0,
    totalPayment: round2(a.totalPayment + b.totalPayment),
    totalInterest: round2(a.totalInterest + b.totalInterest),
    schedule,
  };
}

export function round2(n) {
  return Math.round((Number(n) + Number.EPSILON) * 100) / 100;
}

export function formatMoney(n) {
  const v = Number(n) || 0;
  return v.toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/** 常见利率预设（可按需调整） */
export const RATE_PRESETS = {
  commercial: [
    { label: '最新商贷基准 3.60%', value: 3.6 },
    { label: 'LPR 3.95% - 20BP ≈ 3.75%', value: 3.75 },
    { label: 'LPR 3.95%', value: 3.95 },
    { label: '4.10%', value: 4.1 },
    { label: '4.20%', value: 4.2 },
    { label: '自定义', value: -1 },
  ],
  fund: [
    { label: '公积金首套 2.85%', value: 2.85 },
    { label: '公积金二套 3.325%', value: 3.325 },
    { label: '自定义', value: -1 },
  ],
};

export const YEAR_OPTIONS = [5, 10, 15, 20, 25, 30].map((y) => ({
  label: `${y}年（${y * 12}期）`,
  value: y,
}));
