import http from './http';

export function fetchWfFlowTypes(params = {}) {
  return http.get('/wf/flow-types', { params });
}

export function fetchWfFlowTypeOptions() {
  return http.get('/wf/flow-types/options');
}

export function createWfFlowType(data) {
  return http.post('/wf/flow-types', data);
}

export function updateWfFlowType(id, data) {
  return http.put(`/wf/flow-types/${id}`, data);
}

export function updateWfFlowTypeSort(id, data) {
  return http.patch(`/wf/flow-types/${id}/sort`, data);
}

export function updateWfFlowTypeStatus(id, data) {
  return http.patch(`/wf/flow-types/${id}/status`, data);
}

export function deleteWfFlowType(id) {
  return http.delete(`/wf/flow-types/${id}`);
}
