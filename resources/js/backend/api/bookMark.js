import http from './http';

export function fetchBookMarks(params = {}) {
  return http.get('/bookmarks', { params });
}

export function createBookMark(data) {
  return http.post('/bookmarks', data);
}

export function updateBookMark(id, data) {
  return http.put(`/bookmarks/${id}`, data);
}

export function updateBookMarkSort(id, data) {
  return http.patch(`/bookmarks/${id}/sort`, data);
}

export function updateBookMarkStatus(id, data) {
  return http.patch(`/bookmarks/${id}/status`, data);
}

export function deleteBookMark(id) {
  return http.delete(`/bookmarks/${id}`);
}
