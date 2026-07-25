import http from './http';

export function fetchHrUserDeptPosts(params = {}) {
  return http.get('/hr/user-dept-posts', { params });
}

export function createHrUserDeptPost(data) {
  return http.post('/hr/user-dept-posts', data);
}

export function updateHrUserDeptPost(id, data) {
  return http.put(`/hr/user-dept-posts/${id}`, data);
}

export function deleteHrUserDeptPost(id) {
  return http.delete(`/hr/user-dept-posts/${id}`);
}
