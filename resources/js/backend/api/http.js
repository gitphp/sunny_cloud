import axios from 'axios';

const csrfToken = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute('content');

const http = axios.create({
  baseURL: '/backend/api',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
  },
});

http.interceptors.response.use(
  (response) => {
    const body = response.data;
    if (body && typeof body.code !== 'undefined' && body.code !== 0) {
      if (body.code === 2001003 && !window.location.pathname.includes('/login')) {
        window.location.href = '/backend/login';
      }
      return Promise.reject(new Error(body.message || '请求失败'));
    }
    return body;
  },
  (error) => {
    const status = error.response?.status;
    const message = error.response?.data?.message || error.message || '网络异常';
    if (status === 401 && !window.location.pathname.includes('/login')) {
      window.location.href = '/backend/login';
    }
    return Promise.reject(new Error(message));
  },
);

export default http;
