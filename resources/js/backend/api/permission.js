import http from './http';

export function fetchPermissions(params = {}) {
  return http.get('/permissions', { params });
}

export function fetchPermissionTree() {
  return http.get('/permissions/tree');
}

export function createPermission(data) {
  return http.post('/permissions', data);
}

export function updatePermission(id, data) {
  return http.put(`/permissions/${id}`, data);
}

export function updatePermissionSort(id, data) {
  return http.patch(`/permissions/${id}/sort`, data);
}

export function updatePermissionStatus(id, data) {
  return http.patch(`/permissions/${id}/status`, data);
}

export function deletePermission(id) {
  return http.delete(`/permissions/${id}`);
}
