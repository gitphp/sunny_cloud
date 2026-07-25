import http from './http';

export function fetchHrPosts(params = {}) {
  return http.get('/hr/posts', { params });
}

export function createHrPost(data) {
  return http.post('/hr/posts', data);
}

export function updateHrPost(id, data) {
  return http.put(`/hr/posts/${id}`, data);
}

export function updateHrPostSort(id, data) {
  return http.patch(`/hr/posts/${id}/sort`, data);
}

export function updateHrPostStatus(id, data) {
  return http.patch(`/hr/posts/${id}/status`, data);
}

export function deleteHrPost(id) {
  return http.delete(`/hr/posts/${id}`);
}
