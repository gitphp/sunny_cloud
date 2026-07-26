import http from './http';

export function fetchHome() {
  return http.get('/home');
}

export function submitFeedback(data) {
  return http.post('/feedbacks', data);
}
