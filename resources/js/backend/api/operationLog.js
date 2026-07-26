import http from './http';

export function fetchOperationLogs(params = {}) {
  return http.get('/operation-logs', { params });
}

export function fetchOperationLog(id) {
  return http.get(`/operation-logs/${id}`);
}
