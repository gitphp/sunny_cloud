<template>
  <div class="site">
    <header class="nav" :class="{ solid: navSolid }">
      <a class="brand" href="#top" @click.prevent="scrollTo('top')">
        <span class="brand-mark">MY</span>
        <span class="brand-text">
          <strong>名扬科技</strong>
          <small>MINGYANG TECH</small>
        </span>
      </a>
      <nav class="nav-links">
        <a href="#products" @click.prevent="scrollTo('products')">产品</a>
        <a href="#news" @click.prevent="scrollTo('news')">资讯</a>
        <a href="#careers" @click.prevent="scrollTo('careers')">招聘</a>
        <a href="#contact" @click.prevent="scrollTo('contact')">联系</a>
      </nav>
      <div class="nav-actions">
        <router-link to="/">导航首页</router-link>
        <template v-if="isLoggedIn">
          <span class="hello">{{ user?.nick_name || user?.user_name }}</span>
          <button type="button" class="link-btn" @click="onLogout">退出</button>
        </template>
        <template v-else>
          <router-link to="/frontend/login">登录</router-link>
          <router-link class="nav-cta" to="/frontend/register">注册</router-link>
        </template>
      </div>
    </header>

    <section id="top" class="hero">
      <div
        class="hero-media"
        :style="heroStyle"
        :class="{ animate: heroReady }"
      />
      <div class="hero-veil" />
      <div class="hero-content" :class="{ in: heroReady }">
        <p class="hero-brand">深圳市名扬科技</p>
        <h1>{{ heroTitle }}</h1>
        <p class="hero-desc">{{ site.site_description }}</p>
        <div class="hero-actions">
          <a class="btn primary" href="#products" @click.prevent="scrollTo('products')">了解产品</a>
          <a class="btn ghost" href="#contact" @click.prevent="scrollTo('contact')">联系我们</a>
        </div>
      </div>
      <div v-if="banners.length > 1" class="hero-dots">
        <button
          v-for="(item, idx) in banners"
          :key="item.id"
          type="button"
          :class="{ active: idx === bannerIndex }"
          @click="bannerIndex = idx"
        />
      </div>
    </section>

    <section id="products" class="section products">
      <div class="section-inner">
        <header class="section-head">
          <h2>产品与方案</h2>
          <p>面向企业数字化场景，持续打磨可落地的产品能力</p>
        </header>
        <div v-if="categories.length" class="cat-row">
          <span v-for="item in categories" :key="item.id">{{ item.category_name }}</span>
        </div>
        <div v-if="products.length" class="product-list">
          <article v-for="item in products" :key="item.id" class="product-row">
            <div class="product-visual">
              <img
                v-if="item.main_image_url"
                :src="item.main_image_url"
                :alt="item.product_name"
                @error="hideImg"
              />
              <div v-else class="img-fallback" />
            </div>
            <div class="product-body">
              <h3>{{ item.product_name }}</h3>
              <p>{{ item.short_desc || '为企业提供稳定高效的数字化能力。' }}</p>
              <div class="meta">
                <span v-if="item.category_name">{{ item.category_name }}</span>
                <span v-if="item.brand_name">{{ item.brand_name }}</span>
              </div>
            </div>
          </article>
        </div>
        <p v-else class="empty">产品即将上架，敬请期待</p>
      </div>
    </section>

    <section id="news" class="section news">
      <div class="section-inner">
        <header class="section-head">
          <h2>资讯动态</h2>
          <p>名扬观点、产品进展与行业观察</p>
        </header>
        <div v-if="articles.length" class="news-list">
          <article v-for="item in articles" :key="item.id" class="news-item">
            <time>{{ item.published_at || '近期' }}</time>
            <h3>{{ item.title }}</h3>
            <p>{{ item.summary }}</p>
          </article>
        </div>
        <p v-else class="empty">暂无已发布资讯</p>
      </div>
    </section>

    <section id="careers" class="section careers">
      <div class="section-inner">
        <header class="section-head light">
          <h2>加入名扬</h2>
          <p>与优秀的人一起，把复杂做成简单</p>
        </header>
        <div v-if="jobs.length" class="job-list">
          <a
            v-for="item in jobs"
            :key="item.id"
            class="job-item"
            href="#contact"
            @click.prevent="prefillJob(item)"
          >
            <div>
              <h3>
                {{ item.job_title }}
                <em v-if="item.is_hot === 1">急聘</em>
              </h3>
              <p>{{ item.department }} · {{ item.workplace }}</p>
            </div>
            <strong>{{ item.salary_range || '面议' }}</strong>
          </a>
        </div>
        <p v-else class="empty light">当前暂无开放职位</p>
      </div>
    </section>

    <section id="contact" class="section contact">
      <div class="section-inner contact-grid">
        <div>
          <header class="section-head">
            <h2>联系名扬</h2>
            <p>留下需求，我们会尽快与您沟通</p>
          </header>
          <ul class="contact-info">
            <li><span>电话</span>{{ site.phone }}</li>
            <li><span>邮箱</span>{{ site.email }}</li>
            <li><span>地址</span>{{ site.address }}</li>
            <li v-if="site.wechat"><span>微信</span>{{ site.wechat }}</li>
          </ul>
        </div>
        <form class="contact-form" @submit.prevent="onSubmitFeedback">
          <label>
            联系人
            <input v-model="form.fb_name" maxlength="32" required placeholder="您的姓名" />
          </label>
          <label>
            电话
            <input v-model="form.fb_phone" maxlength="16" placeholder="方便回拨的手机号" />
          </label>
          <label>
            公司
            <input v-model="form.fb_company" maxlength="32" placeholder="公司名称" />
          </label>
          <label>
            主题
            <input v-model="form.fb_title" maxlength="128" required placeholder="合作 / 产品咨询 / 应聘" />
          </label>
          <label class="full">
            内容
            <textarea v-model="form.fb_content" rows="4" maxlength="5000" required placeholder="请简要描述您的需求" />
          </label>
          <button class="btn primary" type="submit" :disabled="submitting">
            {{ submitting ? '提交中…' : '提交留言' }}
          </button>
        </form>
      </div>
    </section>

    <footer class="footer">
      <div class="footer-inner">
        <div class="footer-brand">
          <strong>深圳市名扬科技</strong>
          <p>{{ site.company_full_name || '深圳市名扬科技有限公司' }}</p>
        </div>
        <div v-if="friendLinks.length" class="friend-links">
          <span>合作伙伴</span>
          <a
            v-for="item in friendLinks"
            :key="item.id"
            :href="item.link_url"
            target="_blank"
            rel="noopener noreferrer"
          >
            {{ item.link_name }}
          </a>
        </div>
        <p class="copy">© {{ year }} {{ site.company_full_name || '深圳市名扬科技有限公司  粤ICP备2026110578号' }}</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { fetchHome, submitFeedback } from '../api/home';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const { user, isLoggedIn, logout } = useAuth();

const loading = ref(false);
const submitting = ref(false);
const navSolid = ref(false);
const heroReady = ref(false);
const bannerIndex = ref(0);
const banners = ref([]);
const products = ref([]);
const categories = ref([]);
const articles = ref([]);
const jobs = ref([]);
const friendLinks = ref([]);
const site = reactive({
  site_name: '名扬科技',
  site_title: '深圳市名扬科技 — 企业数字化与智能云服务',
  site_description: '深圳市名扬科技专注企业数字化建设，提供云平台、智能应用与行业解决方案，助力企业稳健增长。',
  phone: '0755-88886666',
  email: 'contact@mingyang.tech',
  address: '深圳市南山区科技园南区科苑路',
  wechat: 'mingyang_tech',
  company_full_name: '深圳市名扬科技有限公司',
});

const form = reactive({
  fb_name: '',
  fb_phone: '',
  fb_email: '',
  fb_company: '',
  fb_title: '',
  fb_content: '',
});

const year = new Date().getFullYear();
let bannerTimer = null;

const currentBanner = computed(() => banners.value[bannerIndex.value] || null);

const heroTitle = computed(() => {
  return currentBanner.value?.ad_title || '让数字化成为企业增长引擎';
});

const heroStyle = computed(() => {
  const url = currentBanner.value?.cover_url;
  if (url) {
    return { backgroundImage: `url(${url})` };
  }
  return {};
});

function hideImg(e) {
  e.target.style.display = 'none';
}

function scrollTo(id) {
  if (id === 'top') {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function onScroll() {
  navSolid.value = window.scrollY > 40;
}

function prefillJob(item) {
  form.fb_title = `应聘：${item.job_title}`;
  form.fb_content = `您好，我对「${item.job_title}」（${item.department} / ${item.workplace}）职位感兴趣，期待沟通。`;
  scrollTo('contact');
}

function startBannerRotate() {
  stopBannerRotate();
  if (banners.value.length <= 1) return;
  bannerTimer = window.setInterval(() => {
    bannerIndex.value = (bannerIndex.value + 1) % banners.value.length;
  }, 6000);
}

function stopBannerRotate() {
  if (bannerTimer) {
    clearInterval(bannerTimer);
    bannerTimer = null;
  }
}

async function loadHome() {
  loading.value = true;
  try {
    const res = await fetchHome();
    const data = res.data || {};
    Object.assign(site, data.site || {});
    banners.value = data.banners || [];
    products.value = data.products || [];
    categories.value = data.categories || [];
    articles.value = data.articles || [];
    jobs.value = data.jobs || [];
    friendLinks.value = data.friend_links || [];
    document.title = site.site_title || '深圳市名扬科技';
    startBannerRotate();
  } catch (e) {
    ElMessage.error(e?.message || '首页加载失败');
  } finally {
    loading.value = false;
    requestAnimationFrame(() => {
      heroReady.value = true;
    });
  }
}

async function onSubmitFeedback() {
  submitting.value = true;
  try {
    await submitFeedback({ ...form });
    ElMessage.success('提交成功，我们会尽快与您联系');
    form.fb_name = '';
    form.fb_phone = '';
    form.fb_email = '';
    form.fb_company = '';
    form.fb_title = '';
    form.fb_content = '';
  } catch (e) {
    ElMessage.error(e?.message || '提交失败');
  } finally {
    submitting.value = false;
  }
}

async function onLogout() {
  await logout();
  ElMessage.success('已退出');
  router.push('/frontend/login');
}

onMounted(() => {
  loadHome();
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
});

onUnmounted(() => {
  stopBannerRotate();
  window.removeEventListener('scroll', onScroll);
});
</script>
