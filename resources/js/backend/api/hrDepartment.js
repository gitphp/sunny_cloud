import http from './http';

export function fetchHrDepartments(params = {}) {
  return http.get('/hr/departments', { params });
}

export function createHrDepartment(data) {
  return http.post('/hr/departments', data);
}

export function updateHrDepartment(id, data) {
  return http.put(`/hr/departments/${id}`, data);
}

export function updateHrDepartmentSort(id, data) {
  return http.patch(`/hr/departments/${id}/sort`, data);
}

export function updateHrDepartmentStatus(id, data) {
  return http.patch(`/hr/departments/${id}/status`, data);
}

export function fetchHrDepartmentLeaders(id) {
  return http.get(`/hr/departments/${id}/leaders`);
}

export function syncHrDepartmentLeaders(id, data) {
  return http.put(`/hr/departments/${id}/leaders`, data);
}

export function deleteHrDepartment(id) {
  return http.delete(`/hr/departments/${id}`);
}
