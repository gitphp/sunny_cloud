import http from './http';

export function fetchApplyMeta() {
  return http.get('/apply/meta');
}

export function fetchApplyTkd(data) {
  return http.post('/apply/fetch-tkd', data);
}

export function createApply(data) {
  return http.post('/apply', data);
}
