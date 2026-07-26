import http from './http';

export function fetchSiteConfigs(params = {}) {
  return http.get('/site-configs', { params });
}

export function createSiteConfig(data) {
  return http.post('/site-configs', data);
}

export function updateSiteConfig(id, data) {
  return http.put(`/site-configs/${id}`, data);
}

export function batchUpdateSiteConfigs(items) {
  return http.post('/site-configs/batch', { items });
}

export function deleteSiteConfig(id) {
  return http.delete(`/site-configs/${id}`);
}
