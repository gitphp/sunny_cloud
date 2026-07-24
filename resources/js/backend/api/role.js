import http from './http';

export function fetchRoles(params = {}) {
  return http.get('/roles', { params });
}

export function createRole(data) {
  return http.post('/roles', data);
}

export function updateRole(id, data) {
  return http.put(`/roles/${id}`, data);
}

export function updateRoleSort(id, data) {
  return http.patch(`/roles/${id}/sort`, data);
}

export function updateRoleStatus(id, data) {
  return http.patch(`/roles/${id}/status`, data);
}

export function deleteRole(id) {
  return http.delete(`/roles/${id}`);
}

export function fetchRoleGrant(id) {
  return http.get(`/roles/${id}/grant`);
}

export function syncRoleGrant(id, data) {
  return http.put(`/roles/${id}/grant`, data);
}
