import http from './http';

export function fetchMyApplies(params = {}) {
  return http.get('/wf/applies/mine', { params });
}

export function fetchTodoApplies(params = {}) {
  return http.get('/wf/applies/todo', { params });
}

export function fetchCcApplies(params = {}) {
  return http.get('/wf/applies/cc', { params });
}

export function fetchPublishedDefinitions(params = {}) {
  return http.get('/wf/applies/published-definitions', { params });
}

export function fetchPublishedDefinition(id) {
  return http.get(`/wf/applies/published-definitions/${id}`);
}

export function fetchWfApply(id) {
  return http.get(`/wf/applies/${id}`);
}

export function createWfApply(data) {
  return http.post('/wf/applies', data);
}

export function updateWfApply(id, data) {
  return http.put(`/wf/applies/${id}`, data);
}

export function submitWfApply(id, data = {}) {
  return http.post(`/wf/applies/${id}/submit`, data);
}

export function withdrawWfApply(id) {
  return http.post(`/wf/applies/${id}/withdraw`);
}

export function voidWfApply(id) {
  return http.post(`/wf/applies/${id}/void`);
}

export function deleteWfApply(id) {
  return http.delete(`/wf/applies/${id}`);
}

export function agreeWfApply(id, data = {}) {
  return http.post(`/wf/applies/${id}/agree`, data);
}

export function rejectWfApply(id, data = {}) {
  return http.post(`/wf/applies/${id}/reject`, data);
}

export function transferWfApply(id, data = {}) {
  return http.post(`/wf/applies/${id}/transfer`, data);
}

export function addSignWfApply(id, data = {}) {
  return http.post(`/wf/applies/${id}/add-sign`, data);
}

export function markCcRead(id) {
  return http.post(`/wf/cc/${id}/read`);
}
