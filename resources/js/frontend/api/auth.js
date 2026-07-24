import http from './http';

export function login(data) {
  return http.post('/auth/login', data);
}

export function register(data) {
  return http.post('/auth/register', data);
}

export function logout() {
  return http.post('/auth/logout');
}

export function fetchMe() {
  return http.get('/auth/me');
}
