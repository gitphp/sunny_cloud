import http from './http';

export function fetchPortal(params = {}) {
  return http.get('/portal', { params });
}
