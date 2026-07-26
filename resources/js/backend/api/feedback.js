import http from './http';

export function fetchFeedbacks(params = {}) {
  return http.get('/feedbacks', { params });
}

export function fetchFeedback(id) {
  return http.get(`/feedbacks/${id}`);
}

export function replyFeedback(id, data) {
  return http.post(`/feedbacks/${id}/reply`, data);
}

export function updateFeedbackStatus(id, data) {
  return http.patch(`/feedbacks/${id}/status`, data);
}

export function deleteFeedback(id) {
  return http.delete(`/feedbacks/${id}`);
}
