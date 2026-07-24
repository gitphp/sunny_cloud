import http from './http';

export function fetchMenus(params = {}) {
  return http.get('/menus', { params });
}

export function fetchNavMenus() {
  return http.get('/menus/nav');
}

export function createMenu(data) {
  return http.post('/menus', data);
}

export function updateMenu(id, data) {
  return http.put(`/menus/${id}`, data);
}

export function updateMenuSort(id, data) {
  return http.patch(`/menus/${id}/sort`, data);
}

export function updateMenuStatus(id, data) {
  return http.patch(`/menus/${id}/status`, data);
}

export function deleteMenu(id) {
  return http.delete(`/menus/${id}`);
}
