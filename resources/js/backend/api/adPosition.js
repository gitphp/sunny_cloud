import http from './http';

export function fetchAdPositions(params = {}) {
  return http.get('/ad-positions', { params });
}

export function fetchAdPosition(id) {
  return http.get(`/ad-positions/${id}`);
}

export function createAdPosition(data) {
  return http.post('/ad-positions', data);
}

export function updateAdPosition(id, data) {
  return http.put(`/ad-positions/${id}`, data);
}

export function updateAdPositionSort(id, data) {
  return http.patch(`/ad-positions/${id}/sort`, data);
}

export function updateAdPositionStatus(id, data) {
  return http.patch(`/ad-positions/${id}/status`, data);
}

export function auditAdPosition(id, data) {
  return http.post(`/ad-positions/${id}/audit`, data);
}

export function deleteAdPosition(id) {
  return http.delete(`/ad-positions/${id}`);
}
