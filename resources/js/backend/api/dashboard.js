import http from './http';

export function fetchDashboardOverview() {
  return http.get('/dashboard');
}
