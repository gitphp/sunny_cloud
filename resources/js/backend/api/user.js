import http from './http';

export function fetchUsers(params = {}) {
  return http.get('/users', { params });
}

export function createUser(data) {
  return http.post('/users', data);
}

export function updateUser(id, data) {
  return http.put(`/users/${id}`, data);
}

export function updateUserStatus(id, data) {
  return http.patch(`/users/${id}/status`, data);
}

export function deleteUser(id) {
  return http.delete(`/users/${id}`);
}
