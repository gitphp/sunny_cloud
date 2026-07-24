import { computed, ref } from 'vue';
import { fetchMe, login as loginApi, logout as logoutApi, register as registerApi } from '../api/auth';

const user = ref(null);
const loaded = ref(false);

export function useAuth() {
  const isLoggedIn = computed(() => !!user.value);

  async function loadMe() {
    try {
      const res = await fetchMe();
      user.value = res.data;
      return user.value;
    } catch {
      user.value = null;
      return null;
    } finally {
      loaded.value = true;
    }
  }

  async function login(form) {
    const res = await loginApi(form);
    user.value = res.data;
    loaded.value = true;
    return user.value;
  }

  async function register(form) {
    const res = await registerApi(form);
    return res.data;
  }

  async function logout() {
    try {
      await logoutApi();
    } finally {
      user.value = null;
    }
  }

  return { user, loaded, isLoggedIn, loadMe, login, register, logout };
}
