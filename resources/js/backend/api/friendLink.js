import http from './http';

export function fetchFriendLinks(params = {}) {
  return http.get('/friend-links', { params });
}

export function createFriendLink(data) {
  return http.post('/friend-links', data);
}

export function updateFriendLink(id, data) {
  return http.put(`/friend-links/${id}`, data);
}

export function updateFriendLinkSort(id, data) {
  return http.patch(`/friend-links/${id}/sort`, data);
}

export function updateFriendLinkStatus(id, data) {
  return http.patch(`/friend-links/${id}/status`, data);
}

export function deleteFriendLink(id) {
  return http.delete(`/friend-links/${id}`);
}
