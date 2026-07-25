import http from './http';

export function fetchWfFlowDefinitions(params = {}) {
  return http.get('/wf/flow-definitions', { params });
}

export function fetchWfFlowDefinition(id) {
  return http.get(`/wf/flow-definitions/${id}`);
}

export function createWfFlowDefinition(data) {
  return http.post('/wf/flow-definitions', data);
}

export function updateWfFlowDefinition(id, data) {
  return http.put(`/wf/flow-definitions/${id}`, data);
}

export function publishWfFlowDefinition(id) {
  return http.post(`/wf/flow-definitions/${id}/publish`);
}

export function unpublishWfFlowDefinition(id) {
  return http.post(`/wf/flow-definitions/${id}/unpublish`);
}

export function deleteWfFlowDefinition(id) {
  return http.delete(`/wf/flow-definitions/${id}`);
}
