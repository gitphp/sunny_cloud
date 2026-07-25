import http from './http';

export function fetchCategories(params = {}) {
  return http.get('/categories', { params });
}

export function createCategory(data) {
  return http.post('/categories', data);
}

export function updateCategory(id, data) {
  return http.put(`/categories/${id}`, data);
}

export function updateCategorySort(id, data) {
  return http.patch(`/categories/${id}/sort`, data);
}

export function updateCategoryStatus(id, data) {
  return http.patch(`/categories/${id}/status`, data);
}

export function deleteCategory(id) {
  return http.delete(`/categories/${id}`);
}
