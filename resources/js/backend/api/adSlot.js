import http from './http';

export function fetchAdSlots(params = {}) {
  return http.get('/ad-slots', { params });
}

export function fetchAdSlotOptions() {
  return http.get('/ad-slots/options');
}

export function createAdSlot(data) {
  return http.post('/ad-slots', data);
}

export function updateAdSlot(id, data) {
  return http.put(`/ad-slots/${id}`, data);
}

export function updateAdSlotStatus(id, data) {
  return http.patch(`/ad-slots/${id}/status`, data);
}

export function deleteAdSlot(id) {
  return http.delete(`/ad-slots/${id}`);
}
