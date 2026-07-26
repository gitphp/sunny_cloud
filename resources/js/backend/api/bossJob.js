import http from './http';

export function fetchBossJobs(params = {}) {
  return http.get('/boss-jobs', { params });
}

export function fetchBossJob(id) {
  return http.get(`/boss-jobs/${id}`);
}

export function createBossJob(data) {
  return http.post('/boss-jobs', data);
}

export function updateBossJob(id, data) {
  return http.put(`/boss-jobs/${id}`, data);
}

export function updateBossJobSort(id, data) {
  return http.patch(`/boss-jobs/${id}/sort`, data);
}

export function updateBossJobStatus(id, data) {
  return http.patch(`/boss-jobs/${id}/status`, data);
}

export function updateBossJobHot(id, data) {
  return http.patch(`/boss-jobs/${id}/hot`, data);
}

export function deleteBossJob(id) {
  return http.delete(`/boss-jobs/${id}`);
}
